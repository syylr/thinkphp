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

/**
 +------------------------------------------------------------------------------
 * ThinkPHP 高级模型类
 +------------------------------------------------------------------------------
 * @category   Think
 * @package  Think
 * @subpackage  Core
 * @author    liu21st <liu21st@gmail.com>
 * @version   $Id$
 +------------------------------------------------------------------------------
 */
class AdvModel extends Model {
    const MODEL_INSERT      =   1;      //  插入模型数据
    const MODEL_UPDATE    =   2;      //  更新模型数据
    const MODEL_BOTH      =   3;      //  包含上面两种方式
    const MUST_VALIDATE         =   1;// 必须验证
    const EXISTS_VAILIDATE      =   2;// 表单存在字段则验证
    const VALUE_VAILIDATE       =   3;// 表单值不为空则验证
    // 字段信息
    protected $fields = array();
    // 字段类型信息
    protected $type  =   array();
    // 数据库连接对象列表
    private $_db = array();
    // 自动写入时间戳的字段名称
    protected $autoCreateTimestamps = 'create_time';
    protected $autoUpdateTimestamps = 'update_time';
    // 自动写入的时间格式
    protected $autoTimeFormat = '';

    public function __construct() {
        parent::__construct();
        // 设置默认的数据库连接
        $this->_db[0]   =   $this->db;
        // 数据表字段检测
        $this->_checkTableInfo();
    }

    protected function _checkTableInfo() {
        // 如果不是Model类 自动记录数据表信息
        // 只在第一次执行记录
        if(empty($this->fields)) {
            // 如果数据表字段没有定义则自动获取
            if(C('DB_FIELDS_CACHE')) {
                $identify   =   $this->name.'_fields';
                $this->fields = F($identify);
                if(!$this->fields) {
                    $this->flush();
                }
            }else{
                // 每次都会读取数据表信息
                $this->flush();
            }
        }
    }

    public function flush() {
        // 缓存不存在则查询数据表信息
        $fields =   $this->db->getFields($this->getTableName());
        $this->fields   =   array_keys($fields);
        $this->fields['_autoinc'] = false;
        foreach ($fields as $key=>$val){
            // 记录字段类型
            $this->type[$key]    =   $val['type'];
            if($val['primary']) {
                $this->pk    =   $key;
                if($val['autoinc']) $this->fields['_autoinc']   =   true;
            }
        }
        // 2008-3-7 增加缓存开关控制
        if(C('DB_FIELDS_CACHE')) {
            // 永久缓存数据表信息
            // 2007-10-31 更改为F方法保存，保存在项目的Data目录，并且始终采用文件形式
            $identify   =   $this->name.'_fields';
            F($identify,$this->fields);
        }
    }

    public function getDbFields(){
        return $this->fields;
    }

    // 查询成功后的回调方法
    protected function _after_find(&$result,$options='') {
        // 检查序列化字段
        $result   =  $this->checkSerializeField($result);
        // 检查字段过滤
        $result   =  $this->filterFields($result);
    }

    // 查询数据集成功后的回调方法
    protected function _after_select(&$resultSet,$options='') {
        // 检查序列化字段
        $resultSet   =  $this->checkListSerializeField($resultSet);
        // 检查列表字段过滤
        //$resultSet   =  $this->filterListFields($resultSet);
    }

    // 写入前的回调方法
    protected function _before_insert(&$data,$options='') {
        $data = $this->serializeField($data);
    }

    // 更新前的回调方法
    protected function _before_update(&$data,$options='') {
        // 检查只读字段
        $data = $this->checkReadonlyField($data);
        // 检查序列化字段
        $data = $this->serializeField($data);
    }

    // 创建数据前的回调方法
    protected function _before_create($data,$type){
        // 自动验证
        return $this->autoValidation($data,$type);
    }

    // 创建数据后的回调方法
    protected function _after_create(&$data,$type) {
        // 自动保存时间戳
        $this->autoSaveTime($data,$type);
        // 自动完成
        $this->autoOperation($data,$type);
    }

    /**
     +----------------------------------------------------------
     * 检查序列化数据字段
     +----------------------------------------------------------
     * @access protected
     +----------------------------------------------------------
     * @param array $data 数据
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
     */
     protected function serializeField(&$data) {
        // 检查序列化字段
        if(!empty($this->serializeField)) {
            // 定义方式  $this->serializeField = array('ser'=>array('name','email'));
            foreach ($this->serializeField as $key=>$val){
                if(empty($data[$key])) {
                    $serialize  =   array();
                    foreach ($val as $name){
                        if(isset($data[$name])) {
                            $serialize[$name]   =   $data[$name];
                            unset($data[$name]);
                        }
                    }
                    $data[$key] =   serialize($serialize);
                }
            }
        }
        return $data;
     }

    // 检查返回数据的序列化字段
    protected function checkSerializeField(&$result) {
        // 检查序列化字段
        if(!empty($this->serializeField)) {
            foreach ($this->serializeField as $key=>$val){
                if(isset($result[$key])) {
                    $serialize   =   unserialize($result[$key]);
                    foreach ($serialize as $name=>$value){
                        $result[$name]  =   $value;
                    }
                    unset($serialize,$result[$key]);
                }
            }
        }
        return $result;
    }

    // 检查数据集的序列化字段
    protected function checkListSerializeField(&$resultSet) {
        // 检查序列化字段
        if(!empty($this->serializeField)) {
            foreach ($this->serializeField as $key=>$val){
                foreach ($resultSet as $k=>$result){
                    if(isset($result[$key])) {
                        $serialize   =   unserialize($result[$key]);
                        foreach ($serialize as $name=>$value){
                            $result[$name]  =   $value;
                        }
                        unset($serialize,$result[$key]);
                        $resultSet[$k] =   $result;
                    }
                }
            }
        }
        return $resultSet;
    }

    /**
     +----------------------------------------------------------
     * 获取数据的时候过滤数据字段
     +----------------------------------------------------------
     * @access pubic
     +----------------------------------------------------------
     * @param mixed $result 查询的数据
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    public function filterFields(&$result) {
        if(!empty($this->_filter)) {
            foreach ($this->_filter as $field=>$filter){
                if(isset($result[$field])) {
                    $fun  =  $filter[1];
                    if(isset($filter[2]) && $filter[2]){
                        // 传递整个数据对象作为参数
                        $result[$field]  =  call_user_func($fun,$result);
                    }else{
                        // 传递字段的值作为参数
                        $result[$field]  =  call_user_func($fun,$result[$field]);
                    }
                }
            }
        }
        return $result;
    }

    /**
     +----------------------------------------------------------
     * 检查只读字段
     +----------------------------------------------------------
     * @access protected
     +----------------------------------------------------------
     * @param array $data 数据
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
     */
    protected function checkReadonlyField(&$data) {
        if(!empty($this->readonlyField)) {
            foreach ($this->readonlyField as $key=>$field){
                if(isset($data[$field])) {
                    unset($data[$field]);
                }
            }
        }
        return $data;
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

    // 自动保存时间戳字段
    protected function autoSaveTime(&$data,$type) {
        switch($type) {
            case self::INSERT_STATUS:
                $name   = $this->autoCreateTimestamps;
                break;
            case self::UPDATE_STATUS:
                $name   = $this->autoUpdateTimestamps;
        }
        // 自动保存时间戳
        if(!empty($this->autoTimeFormat)) {
            // 用指定日期格式记录时间戳
            $data[$name] =    date($this->autoTimeFormat);
        }else{
            // 默认记录时间戳
            $data[$name] = time();
        }
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
                if(empty($auto[2])) $auto[2] = self::MODEL_INSERT;// 默认为新增的时候自动填充
                else $auto[2]   =   strtoupper($auto[2]);
                if( ($type ==self::INSERT_STATUS  && $auto[2] == self::MODEL_INSERT) ||   ($type == self::UPDATE_STATUS  && $auto[2] == self::MODEL_UPDATE) || $auto[2] == self::MODEL_BOTH)
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
                if(empty($val[5]) || $val[5]== self::ALL_STATUS || $val[5]== $type ) {
                    if(0==strpos($val[2],'{%') && strpos($val[2],'}')) {
                        // 支持提示信息的多语言 使用 {%语言定义} 方式
                        $val[2]  =  L(substr($val[2],2,-1));
                    }
                    // 判断验证条件
                    switch($val[3]) {
                        case self::MUST_VALIDATE:   // 必须验证 不管表单是否有设置该字段
                            if(!$this->_validationField($data,$val)){
                                $this->error    =   $val[2];
                                return false;
                            }
                            break;
                        case self::VALUE_VAILIDATE:    // 值不为空的时候才验证
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
                if(isset($validate[strtolower($rule)])) {
                    $rule   =   $validate[strtolower($rule)];
                }
                return preg_match($rule,$value)===1;
        }
    }

    /**
     +----------------------------------------------------------
     * 得到分表的的数据表名
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param array $data 操作的数据
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    public function getPartitionTableName($data=array()) {
        // 对数据表进行分区
        if(isset($data[$this->partition['field']])) {
            $field   =   $data[$this->partition['field']];
            switch($this->partition['type']) {
                case 'id':
                    // 按照id范围分表
                    $step    =   $this->partition['expr'];
                    $seq    =   floor($field / $step)+1;
                    break;
                case 'year':
                    // 按照年份分表
                    if(!is_numeric($field)) {
                        $field   =   strtotime($field);
                    }
                    $seq    =   date('Y',$field)-$this->partition['expr']+1;
                    break;
                case 'mod':
                    // 按照id的模数分表
                    $seq    =   ($field % $this->partition['num'])+1;
                    break;
                case 'md5':
                    // 按照md5的序列分表
                    $seq    =   (ord(substr(md5($field),0,1)) % $this->partition['num'])+1;
                    break;
                default :
                    if(function_exists($this->partition['type'])) {
                        // 支持指定函数哈希
                        $fun    =   $this->partition['type'];
                        $seq    =   (ord(substr($fun($field),0,1)) % $this->partition['num'])+1;
                    }else{
                        // 按照字段的首字母的值分表
                        $seq    =   (ord($field{0}) % $this->partition['num'])+1;
                    }
            }
            return $this->getTableName().'_'.$seq;
        }else{
            // 当设置的分表字段不在查询条件或者数据中
            // 进行联合查询，必须设定 partition['num']
            $tableName  =   array();
            for($i=0;$i<$this->partition['num'];$i++) {
                $tableName[] = 'SELECT * FROM '.$this->getTableName().'_'.$i;
            }
            $tableName = '( '.implode(" UNION ",$tableName).') AS '.$this->name;
            return $tableName;
        }
    }

}
?>