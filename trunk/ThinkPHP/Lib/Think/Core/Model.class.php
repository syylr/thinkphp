<?php
// +----------------------------------------------------------------------
// | ThinkPHP
// +----------------------------------------------------------------------
// | Copyright (c) 2009 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// $Id$

define('HAS_ONE',1);
define('BELONGS_TO',2);
define('HAS_MANY',3);
define('MANY_TO_MANY',4);
/**
 +------------------------------------------------------------------------------
 * ThinkPHP Model模型类
 * 实现了ORM和ActiveRecords模式
 +------------------------------------------------------------------------------
 * @category   Think
 * @package  Think
 * @subpackage  Core
 * @author    liu21st <liu21st@gmail.com>
 * @version   $Id$
 +------------------------------------------------------------------------------
 */
class Model extends Think implements IteratorAggregate
{
    // 操作状态
    const MODEL_INSERT      =   1;      //  插入模型数据
    const MODEL_UPDATE    =   2;      //  更新模型数据
    const MODEL_BOTH      =   3;      //  包含上面两种方式
    // 当前使用的扩展模型
    private $_extModel =  null;
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
    protected $error = '';
    // 字段信息
    protected $fields = array();
    // 字段类型信息
    protected $type  =   array();
    // 数据信息
    protected $data =   array();
    // 查询表达式参数
    protected $options  =   array();
    // 数据列表信息
    protected $dataList =   array();
    // 自动写入时间戳的字段名称
    protected $autoRecordTime   =  true;
    protected $autoCreateTimestamps = 'create_time';
    protected $autoUpdateTimestamps = 'update_time';
    // 自动写入的时间格式
    protected $autoTimeFormat = '';

    /**
     +----------------------------------------------------------
     * 架构函数
     * 取得DB类的实例对象 字段检查
     +----------------------------------------------------------
     * @param string $name 模型名称
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     */
    public function __construct($name='')
    {
        // 模型初始化
        $this->_initialize();
        // 获取模型名称
        if(!empty($name)) {
            $this->name   =  $name;
        }elseif(empty($this->name)){
            $this->name =   $this->getModelName();
        }
        // 数据库初始化操作
        // 获取数据库操作对象
        // 当前模型有独立的数据库连接信息
        $this->db = Db::getInstance(empty($this->connection)?'':$this->connection);
        // 设置表前缀
        $this->tablePrefix = $this->tablePrefix?$this->tablePrefix:C('DB_PREFIX');
        // 字段检测
        $this->_checkTableInfo();
    }

    /**
     +----------------------------------------------------------
     * 自动检测数据表信息
     +----------------------------------------------------------
     * @access protected
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    protected function _checkTableInfo() {
        // 如果不是Model类 自动记录数据表信息
        // 只在第一次执行记录
        if(empty($this->fields)) {
            // 如果数据表字段没有定义则自动获取
            if(C('DB_FIELDS_CACHE')) {
                $this->fields = simple_file_read('_fields/'.$this->name);
                if(!$this->fields)   $this->flush();
            }else{
                // 每次都会读取数据表信息
                $this->flush();
            }
        }
    }

    /**
     +----------------------------------------------------------
     * 获取字段信息并缓存
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    public function flush() {
        // 缓存不存在则查询数据表信息
        $fields =   $this->db->getFields($this->getTableName());
        $this->fields   =   array_keys($fields);
        $this->fields['_autoinc'] = false;
        foreach ($fields as $key=>$val){
            // 记录字段类型
            $this->type[$key]    =   $val['type'];
            if($val['primary']) {
                $this->fields['_pk'] = $key;
                if($val['autoinc']) $this->fields['_autoinc']   =   true;
            }
        }
        // 2008-3-7 增加缓存开关控制
        if(C('DB_FIELDS_CACHE'))
            // 永久缓存数据表信息
            simple_file_save('_fields/'.$this->name,$this->fields);
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
     * 动态切换到其他模型
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $name 模型名称
     +----------------------------------------------------------
     * @return Model
     +----------------------------------------------------------
     */
    public function switchModel($name) {
        return M($name);
    }

    /**
     +----------------------------------------------------------
     * 动态切换扩展模型类型
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $type 模型类型名称
     * @param mixed $vars 要传入扩展模型的属性变量
     +----------------------------------------------------------
     * @return Model
     +----------------------------------------------------------
     */
    public function extendModel($type,$vars=array()) {
        $class = ucwords(strtolower($type)).'Model';
        require_cache(dirname(__FILE__).'/Model/'.$class.'.class.php');
        if(!class_exists($class))
            throw_exception($class.L('_MODEL_NOT_EXIST_'));
        // 实例化扩展模型
        $this->_extModel   = new $class($this->name);
        if(!empty($vars)) {
            // 传入当前模型的属性到扩展模型
            foreach ($vars as $var)
                $this->_extModel->$var  = $this->$var;
        }
        return $this->_extModel;
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
        return isset($this->data[$name])?$this->data[$name]:null;
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
        if(in_array(strtolower($method),array('field','table','where','order','limit','page','having','group','distinct','lazy'),true)) {
            // 连贯操作的实现
            $this->options[strtolower($method)] =   $args[0];
            return $this;
        }elseif(in_array(strtolower($method),array('count','sum','min','max','avg'),true)){
            // 统计查询的实现
            $field =  isset($args[0])?$args[0]:'*';
            return $this->getField($method.'('.$field.') AS tp_'.$method);
        }elseif(strtolower(substr($method,0,5))=='getby') {
            // 根据某个字段获取记录
            $field   =   parse_name(substr($method,5));
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
                if(!in_array($key,$this->fields,true))
                    unset($data[$key]);
            }
        }
        $this->_before_write($data);
        return $data;
     }

    // 写入数据前的回调方法 包括新增和更新
    protected function _before_write(&$data) {}

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
        $this->_before_insert($data,$options);
        // 写入数据到数据库
        if(false === ($result = $this->db->insert($data,$options))){
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
    // 插入数据前的回调方法
    protected function _before_insert(&$data,$options) {}
    // 插入成功后的回调方法
    protected function _after_insert($data,$options) {}

    /**
     +----------------------------------------------------------
     * 新增数据
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param array $dataList 数据
     * @param array $options 表达式
     +----------------------------------------------------------
     * @return mixed
     +----------------------------------------------------------
     */
    public function addAll($dataList='',$options=array()) {
        // 分析表达式
        $options =  $this->_parseOptions($options);
        // 写入数据到数据库
        if(false === $result = $this->db->insertAll($dataList,$options)){
            // 数据库插入操作失败
            $this->error = L('_OPERATION_WRONG_');
            return false;
        }else {
            return $result;
        }
    }

    /**
     +----------------------------------------------------------
     * 通过Select方式添加记录
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $fields 要插入的数据表字段名
     * @param string $table 要插入的数据表名
     * @param array $options 表达式
     +----------------------------------------------------------
     * @return boolean
     +----------------------------------------------------------
     */
    public function selectAdd($fields='',$table='',$options=array()) {
        // 分析表达式
        $options =  $this->_parseOptions($options);
        // 写入数据到数据库
        if(false === $result = $this->db->selectInsert($fields?$fields:$options['field'],$table?$table:$this->getTableName(),$options)){
            // 数据库插入操作失败
            $this->error = L('_OPERATION_WRONG_');
            return false;
        }else {
            // 插入成功
            return $result;
        }
    }

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
        // 分析表达式
        $options =  $this->_parseOptions($options);
        if(!isset($options['where']) ) {
            // 如果存在主键数据 则自动作为更新条件
            if(isset($data[$this->getPk()])) {
                $pk   =  $this->getPk();
                $options['where']  =  $pk.'=\''.$data[$pk].'\'';
                $pkValue = $data[$pk];
                unset($data[$pk]);
            }else{
                // 如果没有任何更新条件则不执行
                $this->error = L('_OPERATION_WRONG_');
                return false;
            }
        }
        $this->_before_update($data,$options);
        if(false === ($result = $this->db->update($data,$options))){
            $this->error = L('_OPERATION_WRONG_');
            return false;
        }else {
            if(isset($pkValue)) $data[$this->getPk()]   =  $pkValue;
            $this->_after_update($data,$options);
            return $result;
        }
    }
    // 更新数据前的回调方法
    protected function _before_update(&$data,$options) {}
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
            if(!empty($this->data) && isset($this->data[$this->getPk()]))
                return $this->delete($this->data[$this->getPk()]);
            else
                return false;
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
            if(isset($pkValue))
                $data[$this->getPk()]   =  $pkValue;
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
        if($resultSet = $this->db->select($options)) {
            $this->dataList = $resultSet;
            $this->_after_select($resultSet,$options);
            return $resultSet;
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
     * @access private
     +----------------------------------------------------------
     * @param array $options 表达式参数
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
     */
    private function _parseOptions($options) {
        if(is_array($options))
            $options =  array_merge($this->options,$options);
        // 查询过后清空sql表达式组装 避免影响下次查询
        $this->options  =   array();
        if(!isset($options['table']))
            // 自动获取表名
            $options['table'] =$this->getTableName();
        // 表达式过滤
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
         // 总是查找一条记录
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
        if(empty($condition) && isset($this->options['where']))
            $condition   =  $this->options['where'];
        $options['where'] =  $condition;
        if(is_array($field)) {
            foreach ($field as $key=>$val)
                $data[$val]    = $value[$key];
        }else{
            $data[$field]   =  $value;
        }
        return $this->save($data,$options);
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
        if(empty($condition) && isset($this->options['where']))
            $condition   =  $this->options['where'];
        $options['where'] =  $condition;
        $options['field']    =  $field;
        $result   =  $this->find($options);
        if($result) {
            // 2009-6-24 解决getField方法和add等方法冲突的问题
            $this->data=  array();
            return reset($result);
        }else{
            return null;
        }
    }

    /**
     +----------------------------------------------------------
     * 获取数据集的个别字段值
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $field 字段名称
     * @param mixed $condition  条件
     * @param string $spea  多字段分割符号
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
     */
    public function getFields($field,$condition='',$sepa=' ')
    {
        if(empty($condition) && isset($this->options['where']))
            $condition   =  $this->options['where'];
        $options['where'] =  $condition;
        $options['field']    =  $field;
        $rs = $this->select($options);
        if($rs) {
            $field  =   explode(',',$field);
            $cols    =   array();
            $length  = count($field);
            foreach ($rs as $result){
                if($length>1) {
                    $cols[$result[$field[0]]]   =   '';
                    for($i=1; $i<$length; $i++) {
                        if($i+1<$length){
                            $cols[$result[$field[0]]] .= $result[$field[$i]].$sepa;
                        }else{
                            $cols[$result[$field[0]]] .= $result[$field[$i]];
                        }
                    }
                }else{
                    $cols[]  =   $result[$field[0]];
                }
            }
            return $cols;
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
        $type = self::MODEL_INSERT;// 新增数据
        if(isset($data[$this->getPk()])) {
            $pk   =  $this->getPk();
            if($this->field($pk)->where($pk.'=\''.$data[$pk].'\'')->find())
                // 编辑状态
                $type = self::MODEL_UPDATE; // 编辑数据
        }
        // 表单令牌验证
        if(C('TOKEN_ON') && !$this->autoCheckToken($data)) {
            $this->error = L('_TOKEN_ERROR_');
            return false;
        }
        // 验证回调接口
        if(!$this->_before_create($data,$type))
            return false;
        // 检查字段映射
        if(isset($this->_map)) {
            foreach ($this->_map as $key=>$val){
                if(isset($data[$key])) {
                    $data[$val] =   $data[$key];
                    unset($data[$key]);
                }
            }
        }
        if($this->autoRecordTime) {
            // 自动保存时间戳
            switch($type) {
                case self::MODEL_INSERT:
                    $name   = $this->autoCreateTimestamps;
                    break;
                case self::MODEL_UPDATE:
                    $name   = $this->autoUpdateTimestamps;
            }
            if(!empty($this->autoTimeFormat)) {
                // 用指定日期格式记录时间戳
                $data[$name] =    date($this->autoTimeFormat);
            }else{
                // 默认记录时间戳
                $data[$name] = time();
            }
        }
        // 创建完成后回调接口
        $this->_after_create($data,$type);
        // 赋值当前数据对象
        $this->data =   $data;
        return $data;
     }
     // 数据创建成功前的验证方法
     protected function _before_create($data,$type) {return true;}
     // 数据创建成功后的回调方法
     protected function _after_create(&$data,$type) {}

    // 自动表单令牌验证
    public function autoCheckToken($data) {
        $name   = C('TOKEN_NAME');
        return substr($data[$name],32) == $_SESSION[substr($data[$name],0,32).$name];
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
        if(!empty($sql)) {
            if(strpos($sql,'__TABLE__'))
                $sql    =   str_replace('__TABLE__',$this->getTableName(),$sql);
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
            if(strpos($sql,'__TABLE__'))
                $sql    =   str_replace('__TABLE__',$this->getTableName(),$sql);
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
        if(!is_array($sql)) return false;
        // 自动启动事务支持
        $this->startTrans();
        try{
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
        } catch (ThinkException $e) {
            $this->rollback();
        }
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
        if(empty($this->name))
            $this->name =   substr(get_class($this),0,-5);
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
                $tableName .= parse_name($this->name);
            }else{
                $tableName .= $this->name;
            }
            if(!empty($this->dbName))
                $tableName    =  $this->dbName.'.'.$tableName;
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
     * 返回模型的错误信息
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
     * 返回数据库的错误信息
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    public function getDbError() {
        return $this->db->getError();
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
     * 获取主键名称
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    public function getPk() {
        return isset($this->fields['_pk'])?$this->fields['_pk']:$this->pk;
    }

    /**
     +----------------------------------------------------------
     * 获取数据表字段信息
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
     */
    public function getDbFields(){
        return $this->fields;
    }

    /**
     +----------------------------------------------------------
     * 查询SQL组装 join
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param mixed $join
     +----------------------------------------------------------
     * @return Model
     +----------------------------------------------------------
     */
    public function join($join) {
        if(is_array($join))
            $this->options['join'] =  $join;
        else
            $this->options['join'][]  =   $join;
        return $this;
    }

    /**
     +----------------------------------------------------------
     * 验证数据由Create方法或者手动创建的数据
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param array $rule 验证规则
     +----------------------------------------------------------
     * @return boolean
     +----------------------------------------------------------
     */
    public function validate($rule='') {
        if(empty($this->data)) {
            $this->error = L('_DATA_TYPE_INVALID_');
            return false;
        }
        foreach($rule as $key=>$val) {
            // 验证因子定义格式
            // array(field,rule,message,type)
            if(0==strpos($val[2],'{%') && strpos($val[2],'}'))
                // 支持提示信息的多语言 使用 {%语言定义} 方式
                $val[2]  =  L(substr($val[2],2,-1));
            if(false === $this->_validationField($this->data,$val)){
                $this->error    =   $val[2];
                return false;
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
        $args = array_slice($val,4);
        switch($val[3]) {
            case 'function':// 使用函数进行验证
                return call_user_func_array($val[1], $args);
            case 'callback':// 调用方法进行验证
                return call_user_func_array(array(&$this, $val[1]), $args);
            case 'confirm': // 验证两个字段是否相同
                if($data[$val[0]] != $data[$val[1]] )
                    return false;
                break;
            case 'in': // 验证是否在某个数组范围之内
                if(!in_array($data[$val[0]] ,$val[1]) )
                    return false;
                break;
            case 'equal': // 验证是否等于某个值
                if($data[$val[0]] != $val[1])
                    return false;
                break;
            case 'unique': // 验证某个值是否唯一
                if(is_string($val[0]) && strpos($val[0],','))
                    $val[0]  =  explode(',',$val[0]);
                $map = array();
                if(is_array($val[0])) {
                    // 支持多个字段验证
                    foreach ($val[0] as $field)
                        $map[$field]   =  $data[$field];
                }else{
                    $map[$val[0]] = $data[$val[0]];
                }
                if($this->where($map)->find())
                    return false;
                break;
            case 'regex':
            default:    // 默认使用正则验证 可以使用验证类中定义的验证名称
                if(!$this->regex($data[$val[0]],$val[1]))
                    return false;
        }
        return true;
    }

    /**
     +----------------------------------------------------------
     * 使用正则验证数据
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $value  要验证的数据
     * @param string $rule 验证规则
     +----------------------------------------------------------
     * @return boolean
     +----------------------------------------------------------
     */
    public function regex($value,$rule) {
        $validate = array(
            'require'=> '/.+/',
            'email' => '/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/',
            'url' => '/^http:\/\/[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\':+!]*([^<>\"\"])*$/',
            'currency' => '/^\d+(\.\d+)?$/',
            'number' => '/\d+$/',
            'zip' => '/^[1-9]\d{5}$/',
            'integer' => '/^[-\+]?\d+$/',
            'double' => '/^[-\+]?\d+(\.\d+)?$/',
            'english' => '/^[A-Za-z]+$/',
        );
        // 检查是否有内置的正则表达式
        if(isset($validate[strtolower($rule)]))
            $rule   =   $validate[strtolower($rule)];
        return preg_match($rule,$value)===1;
    }

    /**
     +----------------------------------------------------------
     * 自动填充数据
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param array $rule 验证规则
     +----------------------------------------------------------
     * @return Model
     +----------------------------------------------------------
     */
    public function filter($data) {
        if(empty($this->data)) return false;
        foreach ($data as $key=>$auto){
            // 填充因子定义格式
            // array('field','填充内容','填充条件',[额外参数])
            $args = array_slice($auto,3);
            switch($auto[2]) {
                case 'function':    //  使用函数进行填充 字段的值作为参数
                    $this->data[$auto[0]]  = call_user_func_array($auto[1], $args);
                    break;
                case 'callback': // 使用回调方法
                    $this->data[$auto[0]]  =  call_user_func_array(array(&$this,$auto[1]), $args);
                    break;
                case 'field':    // 用其它字段的值进行填充
                    $this->data[$auto[0]] = $this->data[$auto[1]];
                    break;
                case 'string':
                default: // 默认作为字符串填充
                    $this->data[$auto[0]] = $auto[1];
            }
            if(false === $this->data[$auto[0]] )
                unset($this->data[$auto[0]]);
        }
        return $this;
    }
};
?>