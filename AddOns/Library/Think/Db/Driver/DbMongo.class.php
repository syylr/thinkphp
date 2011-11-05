<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
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
 * Mongo数据库驱动类 需要配合MongoModel使用
 +------------------------------------------------------------------------------
 * @category   Think
 * @package  Think
 * @subpackage  Db
 * @author    liu21st <liu21st@gmail.com>
 * @version   $Id$
 +------------------------------------------------------------------------------
 */
class DbMongo extends Db{

    protected $_mongo = null;
    protected $_collection    = null;
    protected $_collectionName = '';
    protected $_cursor   =  null;
    protected $comparison      = array('neq'=>'ne','ne'=>'ne','gt'=>'gt','egt'=>'gte','gte'=>'gte','lt'=>'lt','elt'=>'lte','lte'=>'lte','in'=>'in','not in'=>'nin','nin'=>'nin');

    /**
     +----------------------------------------------------------
     * 架构函数 读取数据库配置信息
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param array $config 数据库配置数组
     +----------------------------------------------------------
     */
    public function __construct($config=''){
        if ( !class_exists('mongo') ) {
            throw_exception(L('_NOT_SUPPERT_').':mongo');
        }
        if(!empty($config)) {
            $this->config   =   $config;
            if(empty($this->config['params'])) {
                $this->config['params'] =   array();
            }
        }
    }

    /**
     +----------------------------------------------------------
     * 连接数据库方法
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    public function connect($config='',$linkNum=0) {
        if ( !isset($this->linkID[$linkNum]) ) {
            if(empty($config))  $config =   $this->config;
            $host = 'mongodb://'.($config['username']?"{$config['username']}":'').($config['password']?":{$config['password']}@":'').$config['hostname'].($config['hostport']?":{$config['hostport']}":'');
            try{
                $this->_mongo = new mongo( $host,$config['params']);
            }catch (MongoConnectionException $e){
                throw_exception($e->getmessage());
            }
            if ( !empty($config['database']) ) {
                $this->linkID[$linkNum] =  $this->_mongo->selectDB($config['database']);
            }
            // 标记连接成功
            $this->connected    =   true;
            // 注销数据库连接配置信息
            if(1 != C('DB_DEPLOY_TYPE')) unset($this->config);
        }
        return $this->linkID[$linkNum];
    }

    /**
     +----------------------------------------------------------
     * 切换当前操作的Collection
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $collection  collection
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    public function switchCollection($collection){
        if ( !$this->_linkID ) $this->initConnect(false);
        $this->_collectionName  = $collection;
        $this->_collection =  $this->_linkID->selectCollection($collection);
    }

    /**
     +----------------------------------------------------------
     * 释放查询结果
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     */
    public function free() {
        $this->_cursor = null;
    }

    /**
     +----------------------------------------------------------
     * 执行命令
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param array $command  指令
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    public function command($command=array()) {
        $result   = $this->_linkID->command($command);
        if(!$result['ok']) {
            throw_exception($result['errmsg']);
        }
        return $result;
    }

    /**
     +----------------------------------------------------------
     * 执行语句
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $code  sql指令
     * @param array $args  参数
     +----------------------------------------------------------
     * @return mixed
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    public function execute($code,$args=array()) {
        $this->queryStr = $code;
        $result   = $this->_linkID->execute($code,$args);
        if($result['ok']) {
            return $result['retval'];
        }else{
            throw_exception($result['errmsg']);
        }
    }

    /**
     +----------------------------------------------------------
     * 关闭数据库
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     */
    public function close() {
        if($this->_linkID) {
            $this->_linkID = null;
            $this->_collection =  null;
            $this->_mongo->close();
        }
    }

    /**
     +----------------------------------------------------------
     * 数据库错误信息
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    public function error() {
        $this->error = $this->_mongo->lastError();
        return $this->error;
    }

    /**
     +----------------------------------------------------------
     * 插入记录
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param mixed $data 数据
     * @param array $options 参数表达式
     * @param boolean $replace 是否replace
     +----------------------------------------------------------
     * @return false | integer
     +----------------------------------------------------------
     */
    public function insert($data,$options=array(),$replace=false) {
        if(isset($options['table'])) {
            $this->switchCollection($options['table']);
        }
        N('db_write',1);
        try{
           $result =  $replace?   $this->_collection->save($data,true):  $this->_collection->insert($data,true);
           if($result) {
               $_id    = $data['_id'];
                if(is_object($_id)) {
                    $_id = $_id->__toString();
                }
               $this->lastInsID    = $_id;
           }
           return $result;
        } catch (MongoCursorException $e) {
            throw_exception($e->getMessage());
        }
    }

    /**
     +----------------------------------------------------------
     * 插入多条记录
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param array $dataList 数据
     * @param array $options 参数表达式
     +----------------------------------------------------------
     * @return bool
     +----------------------------------------------------------
     */
    public function insertAll($dataList,$options=array()) {
        if(isset($options['table'])) {
            $this->switchCollection($options['table']);
        }
        N('db_write',1);
        try{
           $result =  $this->_collection->batchInsert($dataList);
           return $result;
        } catch (MongoCursorException $e) {
            throw_exception($e->getMessage());
        }
    }

    /**
     +----------------------------------------------------------
     * 生成下一条记录ID 用于自增非MongoId主键
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $pk 主键名
     +----------------------------------------------------------
     * @return integer
     +----------------------------------------------------------
     */
    public function mongo_next_id($pk) {
        N('db_read',1);
        $result   =  $this->_collection->find(array(),array($pk=>1))->sort(array($pk=>-1))->limit(1);
        $data = $result->getNext();
        return isset($data[$pk])?$data[$pk]+1:1;
    }

    /**
     +----------------------------------------------------------
     * 更新记录
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param mixed $data 数据
     * @param array $options 表达式
     +----------------------------------------------------------
     * @return bool
     +----------------------------------------------------------
     */
    public function update($data,$options) {
        if(isset($options['table'])) {
            $this->switchCollection($options['table']);
        }
        N('db_write',1);
        $query   = $this->parseWhere($options['where']);
        $set  =  $this->parseSet($data);
        try{
            return $this->_collection->update($query,$set,array("multiple" => true));
        } catch (MongoCursorException $e) {
            throw_exception($e->getMessage());
        }
    }

    /**
     +----------------------------------------------------------
     * 删除记录
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param array $options 表达式
     +----------------------------------------------------------
     * @return false | integer
     +----------------------------------------------------------
     */
    public function delete($options=array()) {
        if(isset($options['table'])) {
            $this->switchCollection($options['table']);
        }
        N('db_write',1);
        $result   = $this->_collection->remove($options);
        return $result;
    }

    /**
     +----------------------------------------------------------
     * 清空记录
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param array $options 表达式
     +----------------------------------------------------------
     * @return false | integer
     +----------------------------------------------------------
     */
    public function clear($options=array()){
        if(isset($options['table'])) {
            $this->switchCollection($options['table']);
        }
        N('db_write',1);
        $result   =   $this->_collection->drop();
        return $result;
    }

    /**
     +----------------------------------------------------------
     * 查找记录
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param array $options 表达式
     +----------------------------------------------------------
     * @return iterator
     +----------------------------------------------------------
     */
    public function select($options=array()) {
        if(isset($options['table'])) {
            $this->switchCollection($options['table']);
        }
        N('db_query',1);
        $query  =  $this->parseWhere($options['where']);
        $field =  $this->parseField($options['field']);
        $_cursor   = $this->_collection->find($query,$field);
        if($options['order']) {
            $order   =  $this->parseOrder($options['order']);
            $_cursor =  $_cursor->sort($order);
        }
        if(isset($options['page'])) { // 根据页数计算limit
            list($page,$length) =  explode(',',$options['page']);
            $page    = $page?$page:1;
            $length = $length?$length:($options['limit']?$options['limit']:20);
            $offset  =  $length*((int)$page-1);
            $options['limit'] =  $offset.','.$length;
        }
        if(isset($options['limit'])) {
            list($offset,$length) =  $this->parseLimit($options['limit']);
            if(!empty($offset)) {
                $_cursor =  $_cursor->skip(intval($offset));
            }
            $_cursor =  $_cursor->limit(intval($length));
        }
        $_cursor->snapshot();
        $this->_cursor =  $_cursor;
        return $_cursor;
    }

    /**
     +----------------------------------------------------------
     * 查找某个记录
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param array $options 表达式
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
     */
    public function find($options=array()){
        if(isset($options['table'])) {
            $this->switchCollection($options['table']);
        }
        N('db_query',1);
        $query  =  $this->parseWhere($options['where']);
        $fields    = $this->parseField($options['field']);
        $result   = $this->_collection->findOne($query,$fields);
        return $result;
    }

    /**
     +----------------------------------------------------------
     * 统计记录数
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param array $options 表达式
     +----------------------------------------------------------
     * @return iterator
     +----------------------------------------------------------
     */
    public function count($options=array()){
        if(isset($options['table'])) {
            $this->switchCollection($options['table']);
        }
        $query  =  $this->parseWhere($options['where']);
        $count   = $this->_collection->count($query);
        return $count;
    }

    public function group($keys,$initial,$reduce,$options=array()){
        $this->_collection->group($keys,$initial,$reduce,$options);
    }

    /**
     +----------------------------------------------------------
     * 取得数据表的字段信息
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
     */
    public function getFields($collection=''){
        if(!empty($collection) && $collection != $this->_collectionName) {
            $this->switchCollection($collection);
        }
        $result   =  $this->_collection->findOne();
        if($result) { // 存在数据则分析字段
            $info =  array();
            foreach ($result as $key=>$val){
                $info[$key] =  array(
                    'name'=>$key,
                    'type'=>getType($val),
                    );
            }
            return $info;
        }
        // 暂时没有数据 返回false
        return false;
    }

    /**
     +----------------------------------------------------------
     * 取得当前数据库的collection信息
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     */
    public function getTables(){
        $list   = $this->_linkID->listCollections();
        $info =  array();
        foreach ($list as $collection){
            $info[]   =  $collection->getName();
        }
        return $info;
    }

    /**
     +----------------------------------------------------------
     * set分析
     +----------------------------------------------------------
     * @access protected
     +----------------------------------------------------------
     * @param array $data
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    protected function parseSet($data) {
        $result   =  array();
        foreach ($data as $key=>$val){
            if(is_array($val)) {
                switch($val[0]) {
                    case 'inc':
                        $result['$inc'][$key]  =  (int)$val[1];
                        break;
                    case 'set':
                    case 'unset':
                    case 'push':
                    case 'pushall':
                    case 'addtoset':
                    case 'pop':
                    case 'pull':
                    case 'pullall':
                        $result['$'.$val[0]][$key] = $val[1];
                        break;
                    default:
                        $result['$set'][$key] =  $val;
                }
            }else{
                $result['$set'][$key]    = $val;
            }
        }
        return $result;
    }

    /**
     +----------------------------------------------------------
     * order分析
     +----------------------------------------------------------
     * @access protected
     +----------------------------------------------------------
     * @param mixed $order
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
     */
    protected function parseOrder($order) {
        if(is_string($order)) {
            $array   =  explode(',',$order);
            $order   =  array();
            foreach ($array as $key=>$val){
                $arr  =  explode(' ',trim($val));
                if(isset($arr[1])) {
                    $arr[1]  =  $arr[1]=='asc'?1:-1;
                }else{
                    $arr[1]  =  1;
                }
                $order[$arr[0]]    = $arr[1];
            }
        }
        return $order;
    }

    /**
     +----------------------------------------------------------
     * limit分析
     +----------------------------------------------------------
     * @access protected
     +----------------------------------------------------------
     * @param mixed $limit
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
     */
    protected function parseLimit($limit) {
        if(strpos($limit,',')) {
            $array  =  explode(',',$limit);
        }else{
            $array   =  array(0,$limit);
        }
        return $array;
    }

    /**
     +----------------------------------------------------------
     * field分析
     +----------------------------------------------------------
     * @access protected
     +----------------------------------------------------------
     * @param mixed $fields
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
     */
    public function parseField($fields){
        if(empty($fields)) {
            $fields    = array();
        }
        if(is_string($fields)) {
            $fields    = explode(',',$fields);
        }
        return $fields;
    }

    /**
     +----------------------------------------------------------
     * where分析
     +----------------------------------------------------------
     * @access protected
     +----------------------------------------------------------
     * @param mixed $where
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
     */
    public function parseWhere($where){
        $query   = array();
        foreach ($where as $key=>$val){
            if(0===strpos($key,'_')) {
                // 解析特殊条件表达式
                $query   = $this->parseThinkWhere($key,$val);
            }else{
                // 查询字段的安全过滤
                if(!preg_match('/^[A-Z_\|\&\-.a-z0-9]+$/',trim($key))){
                    throw_exception(L('_ERROR_QUERY_').':'.$key);
                }
                $key = trim($key);
                if(strpos($key,'|')) {
                    $array   =  explode('|',$key);
                    $str   = array();
                    foreach ($array as $k){
                        $str[]   = $this->parseWhereItem($k,$val);
                    }
                    $query['$or'] =    $str;
                }elseif(strpos($key,'&')){
                    $array   =  explode('&',$key);
                    $str   = array();
                    foreach ($array as $k){
                        $str[]   = $this->parseWhereItem($k,$val);
                    }
                    $query   = array_merge($query,$str);
                }else{
                    $str   = $this->parseWhereItem($key,$val);
                    $query   = array_merge($query,$str);
                }
            }
        }
        return $query;
    }

    /**
     +----------------------------------------------------------
     * 特殊条件分析
     +----------------------------------------------------------
     * @access protected
     +----------------------------------------------------------
     * @param string $key
     * @param mixed $val
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    protected function parseThinkWhere($key,$val) {
        $query   = array();
        switch($key) {
            case '_query': // 字符串模式查询条件
                parse_str($val,$query);
                if(array_key_exists('_logic',$query) && strtolower($query['_logic']) == 'or' ) {
                    unset($query['_logic']);
                    $query['$or']   =  $query;
                }
                break;
            case '_string':// MongoCode查询
                $query['$where']  = new MongoCode($val);
                break;
        }
        return $query;
    }

    /**
     +----------------------------------------------------------
     * where子单元分析
     +----------------------------------------------------------
     * @access protected
     +----------------------------------------------------------
     * @param string $key
     * @param mixed $val
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
     */
    protected function parseWhereItem($key,$val) {
        $query   = array();
        if(is_array($val)) {
            if(is_string($val[0])) {
                $con  =  strtolower($val[0]);
                if(in_array($con,array('neq','ne','gt','egt','gte','lt','lte','elt'))) { // 比较运算
                    $k = '$'.$this->comparison[$con];
                    $query[$key]  =  array($k=>$val[1]);
                }elseif('like'== $con){ // 模糊查询 采用正则方式
                    $query[$key]  =  new MongoRegex("/".$val[1]."/");  
                }elseif('mod'==$con){ // mod 查询
                    $query[$key]   =  array('$mod'=>$val[1]);
                }elseif('regex'==$con){ // 正则查询
                    $query[$key]  =  new MongoRegex($val[1]);
                }elseif(in_array($con,array('in','nin','not in'))){ // IN NIN 运算
                    $data = is_string($val[1])? explode(',',$val[1]):$val[1];
                    $k = '$'.$this->comparison[$con];
                    $query[$key]  =  array($k=>$data);
                }elseif('all'==$con){ // 满足所有指定条件
                    $data = is_string($val[1])? explode(',',$val[1]):$val[1];
                    $query[$key]  =  array('$all'=>$data);
                }elseif('between'==$con){ // BETWEEN运算
                    $data = is_string($val[1])? explode(',',$val[1]):$val[1];
                    $query[$key]  =  array('$gte'=>$data[0],'$lte'=>$data[1]);
                }elseif('not between'==$con){
                    $data = is_string($val[1])? explode(',',$val[1]):$val[1];
                    $query[$key]  =  array('$lt'=>$data[0],'$gt'=>$data[1]);
                }elseif('exp'==$con){ // 表达式查询
                    $query['$where']  = new MongoCode($val[1]);
                }elseif('exists'==$con){ // 字段是否存在
                    $query[$key]  =array('$exists'=>(bool)$val[1]);
                }elseif('size'==$con){ // 限制属性大小
                    $query[$key]  =array('$size'=>intval($val[1]));
                }elseif('type'==$con){ // 限制字段类型 1 浮点型 2 字符型 3 对象或者MongoDBRef 5 MongoBinData 7 MongoId 8 布尔型 9 MongoDate 10 NULL 15 MongoCode 16 32位整型 17 MongoTimestamp 18 MongoInt64 如果是数组的话判断元素的类型
                    $query[$key]  =array('$type'=>intval($val[1]));
                }else{
                    $query[$key]  =  $val;
                }
                return $query;
            }
        }
        $query[$key]  =  $val;
        return $query;
    }
}//类定义结束
?>