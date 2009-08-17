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
    // 数据库连接对象列表
    private $_db = array();
    // 返回数据类型
    public $returnType  =  'array';
    public $blobFields     =   array();
    public $blobValues    = null;
    public $serializeField   = array();
    public $readonlyField  = array();

    public function __construct($name='') {
        if('' === $name && 'AdvModel' == get_class() )
            $this->autoCheckFields = false;
        parent::__construct($name);
        // 设置默认的数据库连接
        $this->_db[0]   =   $this->db;
    }

    /**
     +----------------------------------------------------------
     * 利用__call方法重载 实现一些特殊的Model方法 （魔术方法）
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $method 方法名称
     * @param mixed $args 调用参数
     +----------------------------------------------------------
     * @return mixed
     +----------------------------------------------------------
     */
    public function __call($method,$args) {
        if(strtolower(substr($method,0,3))=='top'){
            // 获取前N条记录
            $count = substr($method,3);
            array_unshift($args,$count);
            return call_user_func_array(array(&$this, 'topN'), $args);
        }else{
            return parent::__call($method,$args);
        }
    }

    // 查询成功后的回调方法
    protected function _after_find(&$result,$options='') {
        // 检查序列化字段
        $this->checkSerializeField($result);
        // 获取文本字段
        $this->getBlobFields($result);
    }

    // 查询数据集成功后的回调方法
    protected function _after_select(&$resultSet,$options='') {
        // 检查序列化字段
        $resultSet   =  $this->checkListSerializeField($resultSet);
        // 获取文本字段
        $resultSet   =  $this->getListBlobFields($resultSet);
    }

    // 写入前的回调方法
    protected function _before_insert(&$data,$options='') {
        // 检查文本字段
        $data = $this->checkBlobFields($data);
        $data = $this->serializeField($data);
    }

    protected function _after_insert($data,$options) {
        // 保存文本字段
        $this->saveBlobFields($data);
    }

    // 更新前的回调方法
    protected function _before_update(&$data,$options='') {
        // 检查文本字段
        $data = $this->checkBlobFields($data);
        // 检查只读字段
        $data = $this->checkReadonlyField($data);
        // 检查序列化字段
        $data = $this->serializeField($data);
    }

    protected function _after_update($data,$options) {
        // 保存文本字段
        $this->saveBlobFields($data);
    }

    protected function _after_delete($data,$options) {
        // 删除Blob数据
        $this->delBlobFields($data);
    }

    // 创建数据前的回调方法
    protected function _before_create($data,$type){
        // 自动验证
        return $this->autoValidation($data,$type);
    }

    // 创建数据后的回调方法
    protected function _after_create(&$data,$type) {
        // 自动完成
        $this->autoOperation($data,$type);
    }

    /**
     +----------------------------------------------------------
     * 查找前N个记录
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param integer $count 记录个数
     * @param array $options 查询表达式
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
     */
    public function topN($count,$options=array()) {
        $options['limit'] =  $count;
        return $this->select($options);
    }

    /**
     +----------------------------------------------------------
     * 查询符合条件的第N条记录
     * 0 表示第一条记录 -1 表示最后一条记录
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param integer $position 记录位置
     * @param array $options 查询表达式
     +----------------------------------------------------------
     * @return mixed
     +----------------------------------------------------------
     */
    public function getN($position=0,$options=array()) {
        if($position>=0) { // 正向查找
            $options['limit'] = $position.',1';
            $list   =  $this->select($options);
            return $list?$list[0]:false;
        }else{ // 逆序查找
            $list   =  $this->select($options);
            return $list?$list[count($list)-abs($position)]:false;
        }
    }

    /**
     +----------------------------------------------------------
     * 获取满足条件的第一条记录
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param array $options 查询表达式
     +----------------------------------------------------------
     * @return mixed
     +----------------------------------------------------------
     */
    public function first($options=array()) {
        return $this->getN(0,$options);
    }

    /**
     +----------------------------------------------------------
     * 获取满足条件的最后一条记录
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param array $options 查询表达式
     +----------------------------------------------------------
     * @return mixed
     +----------------------------------------------------------
     */
    public function last($options=array()) {
        return $this->getN(-1,$options);
    }

    /**
     +----------------------------------------------------------
     * 返回数据
     +----------------------------------------------------------
     * @access protected
     +----------------------------------------------------------
     * @param array $data 数据
     * @param string $type 返回类型 默认为数组
     +----------------------------------------------------------
     * @return mixed
     +----------------------------------------------------------
     */
    protected function returnResult($data,$type='') {
        if('' === $type)
            $type = $this->returnType;
        switch($type) {
            case 'array' :  return $data;
            case 'object':  return (object)$data;
            default:// 允许用户自定义返回类型
                if(class_exists($type))
                    return new $type($data);
                else
                    throw_exception(L('_CLASS_NOT_EXIST_').':'.$type);
        }
    }

    /**
     +----------------------------------------------------------
     * 返回数据列表
     +----------------------------------------------------------
     * @access protected
     +----------------------------------------------------------
     * @param array $resultSet 数据
     * @param string $type 返回类型 默认为数组
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    protected function returnResultSet(&$resultSet,$type='') {
        foreach ($resultSet as $key=>$data)
            $resultSet[$key]  =  $this->returnResult($data,$type);
        return $resultSet;
    }

    public function checkBlobFields(&$data) {
        // 检查Blob文件保存字段
        if(!empty($this->blobFields)) {
            foreach ($this->blobFields as $field){
                if(isset($data[$field])) {
                    if(isset($data[$this->getPk()]))
                        $this->blobValues[$this->name.'/'.$data[$this->getPk()].'_'.$field] =   $data[$field];
                    else
                        $this->blobValues[$this->name.'/@?id@_'.$field] =   $data[$field];
                    unset($data[$field]);
                }
            }
        }
        return $data;
    }

    /**
     +----------------------------------------------------------
     * 获取数据集的文本字段
     +----------------------------------------------------------
     * @access pubic
     +----------------------------------------------------------
     * @param mixed $resultSet 查询的数据
     * @param string $field 查询的字段
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    public function getListBlobFields(&$resultSet,$field='') {
        if(!empty($this->blobFields)) {
            foreach ($resultSet as $key=>$result){
                $result =   $this->getBlobFields($result,$field);
                $resultSet[$key]    =   $result;
            }
        }
        return $resultSet;
    }

    /**
     +----------------------------------------------------------
     * 获取数据的文本字段
     +----------------------------------------------------------
     * @access pubic
     +----------------------------------------------------------
     * @param mixed $data 查询的数据
     * @param string $field 查询的字段
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    public function getBlobFields(&$data,$field='') {
        if(!empty($this->blobFields)) {
            $pk =   $this->getPk();
            $id =   $data[$pk];
            if(empty($field)) {
                foreach ($this->blobFields as $field){
                    $identify   =   $this->name.'/'.$id.'_'.$field;
                    $data[$field]   =   F($identify);
                }
                return $data;
            }else{
                $identify   =   $this->name.'/'.$id.'_'.$field;
                return F($identify);
            }
        }
    }

    /**
     +----------------------------------------------------------
     * 保存File方式的字段
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param mixed $data 保存的数据
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    public function saveBlobFields(&$data) {
        if(!empty($this->blobFields)) {
            foreach ($this->blobValues as $key=>$val){
                if(strpos($key,'@?id@'))
                    $key    =   str_replace('@?id@',$data[$this->getPk()],$key);
                F($key,$val);
            }
        }
    }

    /**
     +----------------------------------------------------------
     * 删除File方式的字段
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param mixed $data 保存的数据
     * @param string $field 查询的字段
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    public function delBlobFields(&$data,$field='') {
        if(!empty($this->blobFields)) {
            $pk =   $this->getPk();
            $id =   $data[$pk];
            if(empty($field)) {
                foreach ($this->blobFields as $field){
                    $identify   =   $this->name.'/'.$id.'_'.$field;
                    F($identify,null);
                }
            }else{
                $identify   =   $this->name.'/'.$id.'_'.$field;
                F($identify,null);
            }
        }
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
     * @param integer $lazyTime  延时时间(s)
     +----------------------------------------------------------
     * @return boolean
     +----------------------------------------------------------
     */
    public function setInc($field,$condition='',$step=1,$lazyTime=0) {
        if(empty($condition) && isset($this->options['where']))
            $condition   =  $this->options['where'];
        if($lazyTime>0) {// 延迟写入
            $guid =  md5($this->name.'_'.$field);
            $step = $this->lazyWrite($guid,$step,$lazyTime);
            if(false === $step )
                // 等待下次写入
                return true;
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
     * @param integer $lazyTime  延时时间(s)
     +----------------------------------------------------------
     * @return boolean
     +----------------------------------------------------------
     */
    public function setDec($field,$condition='',$step=1,$lazyTime=0) {
        if(empty($condition) && isset($this->options['where']))
            $condition   =  $this->options['where'];
        if($lazyTime>0) {// 延迟写入
            $guid =  md5($this->name.'_'.$field);
            $step = $this->lazyWrite($guid,$step,$lazyTime);
            if(false === $step )
                // 等待下次写入
                return true;
        }
        return $this->setField($field,array('exp',$field.'-'.$step),$condition);
    }

    /**
     +----------------------------------------------------------
     * 延时写入检查 返回false表示需要延时
     * 否则返回实际写入的数值
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $guid  写入标识
     * @param integer $step  写入步进值
     * @param integer $lazyTime  延时时间(s)
     +----------------------------------------------------------
     * @return false|integer
     +----------------------------------------------------------
     */
    protected function lazyWrite($guid,$step,$lazyTime) {
        if(false !== ($value = F($guid))) { // 存在缓存写入数据
            if(time()>F($guid.'_time')+$lazyTime) {
                // 延时写入时间到了，删除缓存数据 并实际写入数据库
                F($guid,NULL);
                F($guid.'_time',NULL);
                return $value+$step;
            }else{
                // 追加数据到缓存
                F($guid,$value+$step);
                return false;
            }
        }else{ // 没有缓存数据
            F($guid,$step);
            // 计时开始
            F($guid.'_time',time());
            return false;
        }
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
                    foreach ($serialize as $name=>$value)
                        $result[$name]  =   $value;
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
                        foreach ($serialize as $name=>$value)
                            $result[$name]  =   $value;
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
                if(isset($data[$field]))
                    unset($data[$field]);
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
        if(isset($this->_db[$linkNum]))
            return false;
        if(NULL === $linkNum && is_array($config)) {
            // 支持批量增加数据库连接
            foreach ($config as $key=>$val)
                $this->_db[$key]            =    Db::getInstance($val);
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
     * 把返回的数据集转换成Tree
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param array $list 要转换的数据集
     * @param string $pid parent标记字段
     * @param string $level level标记字段
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
     */
    public function toTree($list=null, $pk='id',$pid = 'pid',$child = '_child',$root=0)
    {
        if(null === $list)
            // 默认直接取查询返回的结果集合
            $list   =   &$this->dataList;
        // 创建Tree
        $tree = array();
        if(is_array($list)) {
            // 创建基于主键的数组引用
            $refer = array();
            foreach ($list as $key => $data) {
                $refer[$data[$pk]] =& $list[$key];
            }
            foreach ($list as $key => $data) {
                // 判断是否存在parent
                $parentId = $data[$pid];
                if ($root == $parentId) {
                    $tree[] =& $list[$key];
                }else{
                    if (isset($refer[$parentId])) {
                        $parent =& $refer[$parentId];
                        $parent[$child][] =& $list[$key];
                    }
                }
            }
        }
        return $tree;
    }

    /**
     +----------------------------------------------------------
     * 对查询结果集进行排序
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $field 排序的字段名
     * @param array $sortby 排序类型 asc arsort natcaseror
     * @param array $list 查询结果
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
     */
    public function sortBy($field, $sortby='asc', $list='' ) {
       if(empty($list) && !empty($this->dataList))
           $list     =   $this->dataList;
       if(is_array($list)){
           $refer = $resultSet = array();
           foreach ($list as $i => $data)
               $refer[$i] = &$data[$field];
           switch ($sortby) {
               case 'asc': // 正向排序
                    asort($refer);
                    break;
               case 'desc':// 逆向排序
                    arsort($refer);
                    break;
               case 'nat': // 自然排序
                    natcasesort($refer);
                    break;
           }
           foreach ( $refer as $key=> $val)
               $resultSet[] = &$list[$key];
           return $resultSet;
       }
       return false;
    }

    /**
     +----------------------------------------------------------
     * 在数据列表中搜索
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param mixed $condition 查询条件
     * 支持 array('name'=>$value) 或者 name=$value
     * @param array $list 数据列表
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
     */
    public function search($condition,$list=null) {
        if(null === $list)
            // 默认直接在查询返回的结果集中搜索
            $list   =   &$this->dataList;
        if(is_string($condition))
            parse_str($condition,$condition);
        // 返回的结果集合
        $resultSet = array();
        foreach ($list as $key=>$data){
            $find   =   false;
            foreach ($condition as $field=>$value){
                if(isset($data[$field])) {
                    if(0 === strpos($value,'/')) {
                        $find   =   preg_match($value,$data[$field]);
                    }elseif($data[$field]==$value){
                        $find = true;
                    }
                }
            }
            if($find)
                $resultSet[]     =   &$list[$key];
        }
        return $resultSet;
    }

}
?>