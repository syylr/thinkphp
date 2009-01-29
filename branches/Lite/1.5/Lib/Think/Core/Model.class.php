<?php
// +----------------------------------------------------------------------
// | ThinkPHP Lite
// +----------------------------------------------------------------------
// | Copyright (c) 2008 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// $Id$

/**
 +------------------------------------------------------------------------------
 * ThinkPHP Model模型类 抽象类
 * 实现了ORM和ActiveRecords模式
 +------------------------------------------------------------------------------
 * @category   Think
 * @package  Think
 * @subpackage  Core
 * @author    liu21st <liu21st@gmail.com>
 * @version   $Id$
 +------------------------------------------------------------------------------
 */
define('MUST_TO_VALIDATE',1);    // 必须验证
define('EXISTS_TO_VAILIDATE',0);        // 表单存在字段则验证
define('VALUE_TO_VAILIDATE',2);     // 表单值不为空则验证
class Model extends Base implements IteratorAggregate
{
    // 数据库连接对象列表
    private $_db = array();

    // 当前数据库操作对象
    protected $db = null;

    // 主键名称
    protected $pk  = 'id';

    // 数据表前缀
    protected $tablePrefix  =   '';

    // 模型名称
    protected $name = '';

    // 数据库名称
    protected $dbName  = '';

    // 数据表名（不包含表前缀）
    protected $tableName = '';

    // 实际数据表名（包含表前缀）
    protected $trueTableName ='';

    // 最近错误信息
    private $error = '';

    // 数据信息
    protected $data =   array();

    // 查询表达式参数
    protected $options  =   array();

    // 数据列表信息
    protected $dataList =   array();

    /**
     +----------------------------------------------------------
     * 架构函数
     * 取得DB类的实例对象 数据表字段检查
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param mixed $data 要创建的数据对象内容
     +----------------------------------------------------------
     */
    public function __construct()
    {
        // 模型初始化
        $this->_initialize();
        // 模型名称获取
        $this->name =   $this->getModelName();
        // 数据库初始化操作
        import("Think.Db.Db");
        // 获取数据库操作对象
        if(!empty($this->connection)) {
            // 当前模型有独立的数据库连接信息
            $this->db = Db::getInstance($this->connection);
        }else{
            $this->db = Db::getInstance();
        }
        // 设置默认的数据库连接
        $this->_db[0]   =   &$this->db;
        // 设置表前缀
        $this->tablePrefix = $this->tablePrefix?$this->tablePrefix:C('DB_PREFIX');
    }

    /**
     +----------------------------------------------------------
     * 取得模型实例对象
     +----------------------------------------------------------
     * @static
     * @access public
     +----------------------------------------------------------
     * @return Model 返回数据模型实例
     +----------------------------------------------------------
     */
    public static function getInstance()
    {
        return get_instance_of(__CLASS__);
    }

    /**
     +----------------------------------------------------------
     * 获取Iterator因子
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return Iterate
     +----------------------------------------------------------
     */
    public function getIterator()
    {
        if(!empty($this->dataList)) {
            // 存在数据集则返回数据集
            return new ArrayObject($this->dataList);
        }elseif(!empty($this->data)){
            // 存在数据对象则返回对象的Iterator
            return new ArrayObject($this->data);
        }
    }

    /**
     +----------------------------------------------------------
     * 设置数据对象的值 （魔术方法）
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $name 名称
     * @param mixed $value 值
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    public function __set($name,$value) {
        // 设置数据对象属性
        $this->data[$name]  =   $value;
    }

    /**
     +----------------------------------------------------------
     * 获取数据对象的值 （魔术方法）
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $name 名称
     +----------------------------------------------------------
     * @return mixed
     +----------------------------------------------------------
     */
    public function __get($name) {
        if(isset($this->data[$name])) {
            return $this->data[$name];
        }else{
            return null;
        }
    }

    /**
     +----------------------------------------------------------
     * 利用__call方法实现一些特殊的Model方法 （魔术方法）
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $method 方法名称
     * @param array $args 调用参数
     +----------------------------------------------------------
     * @return mixed
     +----------------------------------------------------------
     */
    public function __call($method,$args) {
        if(in_array(strtolower($method),array('field','table','where','order','limit','having','group','distinct','lazy'),true)) {
            // 连贯操作的实现
            $this->options[strtolower($method)] =   $args[0];
            return $this;
        }elseif(in_array(strtolower($method),array('count','sum','min','max','avg'),true)){
            // 统计查询的实现
            $field =  isset($args[0])?$args[0]:'*';
            return $this->getField($method.'('.$field.') AS tp_'.$method);
        }elseif(strtolower(substr($method,0,5))=='getby') {
            // 根据某个字段获取记录
            $field   =   $this->parseName(substr($method,5));
            $options['where'] =  $field.'=\''.$args[0].'\'';
            return $this->find($options);
        }else{
            throw_exception(__CLASS__.':'.$method.L('_METHOD_NOT_EXIST_'));
            return;
        }
    }

    // 回调方法 初始化模型
    protected function _initialize() {}

    /**
     +----------------------------------------------------------
     * 对保存到数据库的数据进行处理
     +----------------------------------------------------------
     * @access protected
     +----------------------------------------------------------
     * @param mixed $data 要操作的数据
     +----------------------------------------------------------
     * @return boolean
     +----------------------------------------------------------
     */
     protected function _facade($data) {
        // 检查非数据字段
        if(isset($this->fields)) {
            foreach ($data as $key=>$val){
                if(!in_array($key,$this->fields,true)) {
                    unset($data[$key]);
                }
            }
        }
        return $data;
     }

    /**
     +----------------------------------------------------------
     * 新增数据
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param mixed $data 数据
     * @param array $options 表达式
     +----------------------------------------------------------
     * @return mixed
     +----------------------------------------------------------
     */
    public function add($data='',$options=array()) {
        if(empty($data)) {
            // 没有传递数据，获取当前数据对象的值
            if(!empty($this->data)) {
                $data    =   $this->data;
            }else{
                $this->error = L('_DATA_TYPE_INVALID_');
                return false;
            }
        }
        // 分析表达式
        $options =  $this->_parseOptions($options);
        // 数据处理
        $data = $this->_facade($data);
        // 写入数据到数据库
        if(false === $result = $this->db->insert($data,$options)){
            // 数据库插入操作失败
            $this->error = L('_OPERATION_WRONG_');
            return false;
        }else {
            $insertId   =   $this->getLastInsID();
            if($insertId) {
                $data[$this->getPk()]  = $insertId;
                $this->_after_insert($data,$options);
                return $insertId;
            }
            //成功后返回插入ID
            return $result;
        }
    }
    // 插入成功后的回调方法
    protected function _after_insert(&$data,$options='') {}

    /**
     +----------------------------------------------------------
     * 保存数据
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param mixed $data 数据
     * @param array $options 表达式
     +----------------------------------------------------------
     * @return boolean
     +----------------------------------------------------------
     */
    public function save($data='',$options=array()) {
        if(empty($data)) {
            // 没有传递数据，获取当前数据对象的值
            if(!empty($this->data)) {
                $data    =   $this->data;
            }else{
                $this->error = L('_DATA_TYPE_INVALID_');
                return false;
            }
        }
        // 数据处理
        $data = $this->_facade($data);
        // 如果存在主键数据 则自动作为更新条件
        if(empty($options['where']) && isset($data[$this->getPk()])) {
            $pk   =  $this->getPk();
            $options['where']  =  $pk.'=\''.$data[$pk].'\'';
            $pkValue = $data[$pk];
            unset($data[$pk]);
        }
        // 分析表达式
        $options =  $this->_parseOptions($options);
        if(false === $this->db->update($data,$options)){
            $this->error = L('_OPERATION_WRONG_');
            return false;
        }else {
            if(isset($pkValue)) {
                $data[$this->getPk()]   =  $pkValue;
            }
            $this->_after_update($data,$options);
            return true;
        }
    }
    // 更新成功后的回调方法
    protected function _after_update($data,$options) {}

    /**
     +----------------------------------------------------------
     * 删除数据
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param mixed $options 表达式
     +----------------------------------------------------------
     * @return mixed
     +----------------------------------------------------------
     */
    public function delete($options=array()) {
        if(empty($options) && empty($this->options)) {
            // 如果删除条件为空 则删除当前数据对象所对应的记录
            if(!empty($this->data) && isset($this->data[$this->getPk()])) {
                return $this->delete($this->data[$this->getPk()]);
            }else{
                return false;
            }
        }
        if(is_numeric($options)  || is_string($options)) {
            // 根据主键删除记录
            $where  =  $this->getPk().'=\''.$options.'\'';
            $pkValue = $options;
            $options =  array();
            $options['where'] =  $where;
        }
        // 分析表达式
        $options =  $this->_parseOptions($options);
        $result=    $this->db->delete($options);
        if(false === $result ){
            $this->error =  L('_OPERATION_WRONG_');
            return false;
        }else {
            $data = array();
            if(isset($pkValue)) {
                $data[$this->getPk()]   =  $pkValue;
            }
            $this->_after_delete($data,$options);
            // 返回删除记录个数
            return $result;
        }
    }
    // 删除成功后的回调方法
    protected function _after_delete($data,$options) {}

    /**
     +----------------------------------------------------------
     * 查询数据集
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param array $options 表达式参数
     +----------------------------------------------------------
     * @return mixed
     +----------------------------------------------------------
     */
    public function select($options=array()) {
        // 分析表达式
        $options =  $this->_parseOptions($options);
        if($result = $this->db->select($options)) {
            $this->dataList = $result;
            $this->_after_select($result,$options);
            return $result;
        }else{
            return false;
        }
    }
    // 查询成功后的回调方法
    protected function _after_select(&$result,$options) {}

    public function findAll($options=array()) {
        return $this->select($options);
    }

    /**
     +----------------------------------------------------------
     * 分析表达式
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param array $options 表达式参数
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
     */
    private function _parseOptions($options) {
        if(is_array($options)) {
            $options =  array_merge($this->options,$options);
        }
        // 查询过后清空sql表达式组装 避免影响下次查询
        $this->options  =   array();
        if(!isset($options['table'])) {
            // 自动获取表名
            $options['table'] =$this->getTableName();
        }
        $this->_options_filter($options);
        return $options;
    }
    // 表达式过滤回调方法
    protected function _options_filter(&$options) {}

    /**
     +----------------------------------------------------------
     * 查询数据
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param mixed $options 表达式参数
     +----------------------------------------------------------
     * @return mixed
     +----------------------------------------------------------
     */
     public function find($options=array()) {
         if(is_numeric($options) || is_string($options)) {
             $where = $this->getPk().'=\''.$options.'\'';
             $options = array();
             $options['where'] = $where;
         }
        $options['limit'] = 1;
        // 分析表达式
        $options =  $this->_parseOptions($options);
        if($result = $this->db->select($options)) {
            $this->data = $result[0];
            $this->_after_find($this->data,$options);
            return $this->data;
        }else{
            return false;
        }
     }
     // 查询成功的回调方法
     protected function _after_find(&$result,$options) {}

    /**
     +----------------------------------------------------------
     * 设置记录的某个字段值
     * 支持使用数据库字段和方法
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string|array $field  字段名
     * @param string|array $value  字段值
     * @param mixed $condition  条件
     +----------------------------------------------------------
     * @return boolean
     +----------------------------------------------------------
     */
    public function setField($field,$value,$condition='') {
        if(empty($condition) && isset($this->options['where'])) {
            $condition   =  $this->options['where'];
        }
        $options['where'] =  $condition;
        if(is_array($field)) {
            foreach ($field as $key=>$val){
                $data[$val]    = $value[$key];
            }
        }else{
            $data[$field]   =  $value;
        }
        return $this->save($data,$options);
    }

    /**
     +----------------------------------------------------------
     * 字段值增长
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $field  字段名
     * @param mixed $condition  条件
     * @param integer $step  增长值
     +----------------------------------------------------------
     * @return boolean
     +----------------------------------------------------------
     */
    public function setInc($field,$condition='',$step=1) {
        if(empty($condition) && isset($this->options['where'])) {
            $condition   =  $this->options['where'];
        }
        return $this->setField($field,array('exp',$field.'+'.$step),$condition);
    }

    /**
     +----------------------------------------------------------
     * 字段值减少
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $field  字段名
     * @param mixed $condition  条件
     * @param integer $step  减少值
     +----------------------------------------------------------
     * @return boolean
     +----------------------------------------------------------
     */
    public function setDec($field,$condition='',$step=1) {
        if(empty($condition) && isset($this->options['where'])) {
            $condition   =  $this->options['where'];
        }
        return $this->setField($field,array('exp',$field.'-'.$step),$condition);
    }

    /**
     +----------------------------------------------------------
     * 获取一条记录的某个字段值
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $field  字段名
     * @param mixed $condition  查询条件
     +----------------------------------------------------------
     * @return mixed
     +----------------------------------------------------------
     */
    public function getField($field,$condition='') {
        if(empty($condition) && isset($this->options['where'])) {
            $condition   =  $this->options['where'];
        }
        $options['where'] =  $condition;
        $options['field']    =  $field;
        $result   =  $this->find($options);
        if($result) {
            return reset($result);
        }else{
            return null;
        }
    }

    /**
     +----------------------------------------------------------
     * 创建数据对象 但不保存到数据库
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param mixed $data 创建数据
     +----------------------------------------------------------
     * @return mixed
     +----------------------------------------------------------
     */
     public function create($data='') {
        // 如果没有传值默认取POST数据
        if(empty($data)) {
            $data    =   $_POST;
        }elseif(is_object($data)){
            $data   =   get_object_vars($data);
        }elseif(!is_array($data)){
            $this->error = L('_DATA_TYPE_INVALID_');
            return false;
        }
        $type = 'add';
        if(isset($data[$this->getPk()])) {
            $pk   =  $this->getPk();
            if($this->field($pk)->where($pk.'=\''.$data[$pk].'\'')->find()) {
                // 编辑状态
                $type = 'edit';
            }
        }
        // 自动验证数据
        if(!$this->autoValidation($data,$type)) {
            return false;
        }
        // 检查字段映射
        if(isset($this->_map)) {
            foreach ($this->_map as $key=>$val){
                if(isset($data[$key])) {
                    $data[$val] =   $data[$key];
                    unset($data[$key]);
                }
            }
        }
        // 自动完成数据
        $this->autoOperation($data,$type);
        // 赋值当前数据对象
        $this->data =   $data;
        return $data;
     }

    /**
     +----------------------------------------------------------
     * 自动表单处理
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param array $data 创建数据
     * @param string $type 创建类型
     +----------------------------------------------------------
     * @return mixed
     +----------------------------------------------------------
     */
    private function autoOperation(&$data,$type) {
        // 自动填充
        if(!empty($this->_auto)) {
            foreach ($this->_auto as $auto){
                // 填充因子定义格式
                // array('field','填充内容','填充条件','附加规则',[额外参数])
                if(empty($auto[2])) $auto[2] = 'ADD';// 默认为新增的时候自动填充
                else $auto[2]   =   strtoupper($auto[2]);
                if( (strtolower($type) == "add"  && $auto[2] == 'ADD') ||   (strtolower($type) == "edit"  && $auto[2] == 'UPDATE') || $auto[2] == 'ALL')
                {
                    switch($auto[3]) {
                        case 'function':    //  使用函数进行填充 字段的值作为参数
                        case 'callback': // 使用回调方法
                            if(isset($auto[4])) {
                                $args = $auto[4];
                            }else{
                                $args = array();
                            }
                            array_unshift($args,$data[$auto[0]]);
                            if('function'==$auto[3]) {
                                $data[$auto[0]]  = call_user_func_array($auto[1], $args);
                            }else{
                                $data[$auto[0]]  =  call_user_func_array(array(&$this,$auto[1]), $args);
                            }
                            break;
                        case 'field':    // 用其它字段的值进行填充
                            $data[$auto[0]] = $data[$auto[1]];
                            break;
                        case 'string':
                        default: // 默认作为字符串填充
                            $data[$auto[0]] = $auto[1];
                    }
                    if(false === $data[$auto[0]] ) {
                        unset($data[$auto[0]]);
                    }
                }
            }
        }
        return $data;
    }

    /**
     +----------------------------------------------------------
     * 自动表单验证
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param array $data 创建数据
     * @param string $type 创建类型
     +----------------------------------------------------------
     * @return boolean
     +----------------------------------------------------------
     */
    private function autoValidation($data,$type) {
        // 属性验证
        if(!empty($this->_validate)) {
            // 如果设置了数据自动验证
            // 则进行数据验证
            // 重置验证错误信息
            foreach($this->_validate as $key=>$val) {
                // 判断是否需要执行验证
                if(empty($val[5]) || $val[5]=='all' || strtolower($val[5])==strtolower($type) ) {
                    if(0==strpos($val[2],'{%') && strpos($val[2],'}')) {
                        // 支持提示信息的多语言 使用 {%语言定义} 方式
                        $val[2]  =  L(substr($val[2],2,-1));
                    }
                    // 判断验证条件
                    switch($val[3]) {
                        case MUST_TO_VALIDATE:   // 必须验证 不管表单是否有设置该字段
                            if(!$this->_validationField($data,$val)){
                                $this->error    =   $val[2];
                                return false;
                            }
                            break;
                        case VALUE_TO_VAILIDATE:    // 值不为空的时候才验证
                            if('' != trim($data[$val[0]])){
                                if(!$this->_validationField($data,$val)){
                                    $this->error    =   $val[2];
                                    return false;
                                }
                            }
                            break;
                        default:    // 默认表单存在该字段就验证
                            if(isset($data[$val[0]])){
                                if(!$this->_validationField($data,$val)){
                                    $this->error    =   $val[2];
                                    return false;
                                }
                            }
                    }
                }
            }
        }
        return true;
    }

    /**
     +----------------------------------------------------------
     * 根据验证因子验证字段
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param array $data 创建数据
     * @param string $val 验证规则
     +----------------------------------------------------------
     * @return boolean
     +----------------------------------------------------------
     */
    private function _validationField($data,$val) {
        // 检查附加规则
        if(!$this->validate($data[$val[0]],$val[1],$val[4])) {
            return false;
        }
        return true;
    }

    /**
     +----------------------------------------------------------
     * 验证数据
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $value  要验证的数据
     * @param string $rule 验证规则
     * @param string $type   验证方式
     * 包含 regex function callback 默认为regex
     +----------------------------------------------------------
     * @return boolean
     +----------------------------------------------------------
     */
    protected function validate($value,$rule,$type='regex') {
        switch(strtolower($type)) {
            case 'function':
                return $rule($value);
            case 'callback':
                return $this->$rule($value);
            case 'regex':
            default:
                return preg_match($rule,$value)===1;
        }
    }

    /**
     +----------------------------------------------------------
     * SQL查询
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param mixed $sql  SQL指令
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
     */
    public function query($sql)
    {
        if(is_array($sql)) {
            return $this->patchQuery($sql);
        }
        if(!empty($sql)) {
            if(strpos($sql,'__TABLE__')) {
                $sql    =   str_replace('__TABLE__',$this->getTableName(),$sql);
            }
            return $this->db->query($sql);
        }else{
            return false;
        }
    }

    /**
     +----------------------------------------------------------
     * 执行SQL语句
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $sql  SQL指令
     +----------------------------------------------------------
     * @return false | integer
     +----------------------------------------------------------
     */
    public function execute($sql='')
    {
        if(!empty($sql)) {
            if(strpos($sql,'__TABLE__')) {
                $sql    =   str_replace('__TABLE__',$this->getTableName(),$sql);
            }
            $result =   $this->db->execute($sql);
            return $result;
        }else {
            return false;
        }
    }

    /**
     +----------------------------------------------------------
     * 批处理执行SQL语句
     * 批处理的指令都认为是execute操作
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param array $sql  SQL批处理指令
     +----------------------------------------------------------
     * @return boolean
     +----------------------------------------------------------
     */
    public function patchQuery($sql=array()) {
        if(!is_array($sql)) {
            return false;
        }
        // 自动启动事务支持
        $this->startTrans();
        foreach ($sql as $_sql){
            $result   =  $this->execute($_sql);
            if(false === $result) {
                // 发生错误自动回滚事务
                $this->rollback();
                return false;
            }
        }
        // 提交事务
        $this->commit();
        return true;
    }

    /**
     +----------------------------------------------------------
     * 得到当前的数据对象名称
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    public function getModelName()
    {
        if(empty($this->name)) {
            $this->name =   substr(get_class($this),0,-5);
        }
        return $this->name;
    }

    /**
     +----------------------------------------------------------
     * 得到完整的数据表名
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    public function getTableName()
    {
        if(empty($this->trueTableName)) {
            $tableName  = !empty($this->tablePrefix) ? $this->tablePrefix : '';
            if(!empty($this->tableName)) {
                $tableName .= $this->tableName;
            }elseif(C('AUTO_NAME_IDENTIFY')){
                // 智能识别表名
                $tableName .= $this->parseName($this->name);
            }else{
                $tableName .= $this->name;
            }
            if(!empty($this->dbName)) {
                $tableName    =  $this->dbName.'.'.$tableName;
            }
            $this->trueTableName    =   strtolower($tableName);
        }
        return $this->trueTableName;
    }

    /**
     +----------------------------------------------------------
     * 启动事务
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    public function startTrans()
    {
        $this->commit();
        $this->db->startTrans();
        return ;
    }

    /**
     +----------------------------------------------------------
     * 提交事务
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return boolean
     +----------------------------------------------------------
     */
    public function commit()
    {
        return $this->db->commit();
    }

    /**
     +----------------------------------------------------------
     * 事务回滚
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return boolean
     +----------------------------------------------------------
     */
    public function rollback()
    {
        return $this->db->rollback();
    }

    /**
     +----------------------------------------------------------
     * 返回当前错误信息
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    public function getError(){
        return $this->error;
    }

    /**
     +----------------------------------------------------------
     * 返回最后插入的ID
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    public function getLastInsID() {
        return $this->db->lastInsID;
    }

    /**
     +----------------------------------------------------------
     * 返回最后执行的sql语句
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    public function getLastSql() {
        return $this->db->getLastSql();
    }

    /**
     +----------------------------------------------------------
     * 增加数据库连接
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param mixed $config 数据库连接信息
     * 支持批量添加 例如 array(1=>$config1,2=>$config2)
     * @param mixed $linkNum  创建的连接序号
     +----------------------------------------------------------
     * @return boolean
     +----------------------------------------------------------
     */
    public function addConnect($config,$linkNum=NULL) {
        if(isset($this->_db[$linkNum])) {
            return false;
        }
        if(NULL === $linkNum && is_array($config)) {
            // 支持批量增加数据库连接
            foreach ($config as $key=>$val){
                $this->_db[$key]            =    Db::getInstance($val);
            }
            return true;
        }
        // 创建一个新的实例
        $this->_db[$linkNum]            =    Db::getInstance($config);
        return true;
    }

    /**
     +----------------------------------------------------------
     * 删除数据库连接
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param integer $linkNum  创建的连接序号
     +----------------------------------------------------------
     * @return boolean
     +----------------------------------------------------------
     */
    public function delConnect($linkNum) {
        if(isset($this->_db[$linkNum])) {
            $this->_db[$linkNum]->close();
            unset($this->_db[$linkNum]);
            return true;
        }
        return false;
    }

    /**
     +----------------------------------------------------------
     * 关闭数据库连接
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param integer $linkNum  创建的连接序号
     +----------------------------------------------------------
     * @return boolean
     +----------------------------------------------------------
     */
    public function closeConnect($linkNum) {
        if(isset($this->_db[$linkNum])) {
            $this->_db[$linkNum]->close();
            return true;
        }
        return false;
    }

    /**
     +----------------------------------------------------------
     * 切换数据库连接
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param integer $linkNum  创建的连接序号
     +----------------------------------------------------------
     * @return boolean
     +----------------------------------------------------------
     */
    public function switchConnect($linkNum) {
        if(isset($this->_db[$linkNum])) {
            // 在不同实例直接切换
            $this->db   =   $this->_db[$linkNum];
            return true;
        }else{
            return false;
        }
    }

    /**
     +----------------------------------------------------------
     * 查询SQL组装 join
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param array $join
     +----------------------------------------------------------
     * @return Model
     +----------------------------------------------------------
     */
    public function join($join) {
        if(is_array($join)) {
            $this->options['join'] =  $join;
        }else{
            $this->options['join'][]  =   $join;
        }
        return $this;
    }

    /**
     +----------------------------------------------------------
     * 是否返回执行的SQL
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param boolean $fetch
     +----------------------------------------------------------
     * @return Model
     +----------------------------------------------------------
     */
    public function fetchSql($fetch=true) {
        if(in_array(strtolower($fetch),array('find','findall','save','add','delete'))) {
            $this->options['fetch'] =   true;
            return $this->{$fetch}();
        }else{
            $this->options['fetch'] =   $fetch;
        }
        return $this;
    }

    public function getPk() {
        return $this->pk?$this->pk:'id';
    }
};
?>