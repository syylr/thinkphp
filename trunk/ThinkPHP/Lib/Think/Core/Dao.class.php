<?php 
// +----------------------------------------------------------------------+
// | ThinkPHP                                                             |
// +----------------------------------------------------------------------+
// | Copyright (c) 2006 liu21st.com All rights reserved.                  |
// +----------------------------------------------------------------------+
// | Licensed under the Apache License, Version 2.0 (the 'License');      |
// | you may not use this file except in compliance with the License.     |
// | You may obtain a copy of the License at                              |
// | http://www.apache.org/licenses/LICENSE-2.0                           |
// | Unless required by applicable law or agreed to in writing, software  |
// | distributed under the License is distributed on an 'AS IS' BASIS,    |
// | WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or      |
// | implied. See the License for the specific language governing         |
// | permissions and limitations under the License.                       |
// +----------------------------------------------------------------------+
// | Author: liu21st <liu21st@gmail.com>                                  |
// +----------------------------------------------------------------------+
// $Id$

import("Think.Db.Db");
import("Think.Util.Cache");
import("Think.Core.VoList");

/**
 +------------------------------------------------------------------------------
 * 数据访问基础类
 +------------------------------------------------------------------------------
 * @package   core
 * @author    liu21st <liu21st@gmail.com>
 * @version   $Id$
 +------------------------------------------------------------------------------
 */
class Dao extends Base
{
    /**
     +----------------------------------------------------------
     * 数据库底层操作对象
     +----------------------------------------------------------
     * @var Db
     * @access protected
     +----------------------------------------------------------
     */
    var $db ;                

    /**
     +----------------------------------------------------------
     * 主键名
     +----------------------------------------------------------
     * @var string
     * @access protected
     +----------------------------------------------------------
     */
    var $pk =   'id';

    /**
     +----------------------------------------------------------
     * 主键是否自动增加
     +----------------------------------------------------------
     * @var string
     * @access protected
     +----------------------------------------------------------
     */
    var $autoIncrement =   true;

    /**
     +----------------------------------------------------------
     * 父键名
     +----------------------------------------------------------
     * @var string
     * @access protected
     +----------------------------------------------------------
     */
    var $cacheQuery =   true;

    // 返回元数据类型
    var $resultType =  DATA_RESULT_TYPE;

    //+----------------------------------------
    //|    数据表
    //| 数据表由<项目名_模块名_表名> 三部分组成
    //| appPrefix_modPrefix_table
    //+----------------------------------------

    /**
     +----------------------------------------------------------
     * 项目前缀
     +----------------------------------------------------------
     * @var string
     * @access protected
     +----------------------------------------------------------
     */
    var $appPrefix	=	DB_PREFIX;            

    /**
     +----------------------------------------------------------
     * 模块前缀
     +----------------------------------------------------------
     * @var string
     * @access protected
     +----------------------------------------------------------
     */
    var $modPrefix;            

    /**
     +----------------------------------------------------------
     * 数据表名
     +----------------------------------------------------------
     * @var string
     * @access protected
     +----------------------------------------------------------
     */
    var $tableName;            

    /**
     +----------------------------------------------------------
     * 真实表名
     +----------------------------------------------------------
     * @var string
     * @access protected
     +----------------------------------------------------------
     */
    var $realTableName;        

    /**
     +----------------------------------------------------------
     * 错误信息
     +----------------------------------------------------------
     * @var string
     * @access protected
     +----------------------------------------------------------
     */
    var $error;        


    /**
     +----------------------------------------------------------
     * 架构函数 取得DB类的实例对象
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $appPrefix 数据库前缀
     * @param string $tableName 数据表名
     * @param string $pk  主键名
     * @param boolean $autoIncrement  是否自动增长
     +----------------------------------------------------------
     */
    function __construct($appPrefix='',$tableName='',$pk='',$autoIncrement=true,$returnType='')
    {
        $this->db = DB::getInstance();
        if(!empty($appPrefix))  $this->appPrefix    =   $appPrefix;
        if(!empty($tableName))  $this->tableName    =   $tableName;
        if(!empty($pk))         $this->pk = $pk;
        if(!empty($returnType))         $this->returnType = $returnType;
        if(is_bool($autoIncrement))   $this->autoIncrement = $autoIncrement;

        //自动加载所需的Vo类文件
        import(APP_NAME.'.Vo.'.$this->getVo());

        //如果存在initialize方法，就首先调用
        if(method_exists($this,'_initialize')) {
            $this->_initialize();
        }
    }


    /**
     +----------------------------------------------------------
     * 新增数据
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param mixed $data 数据
     * @param string $table  数据表名
     * @param string $pk 主键名
     +----------------------------------------------------------
     * @return boolen
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function add($data,$table=NULL,$pk='')
    {
        if(is_array($data)){
            $map = new HashMap($data);
        }else if(is_instance_of($data,'Vo')){
            $map = $data->toMap();
        }else if(is_instance_of($data,'HashMap')){
            $map = $data;
        }else {
            $this->error = _DATA_TYPE_INVALID_;
            return false;
        }
        if($this->autoIncrement) {
            //如果主键为自动增长类型
            //删除主键属性 由数据库自动生成
            $map->remove($pk?$pk:$this->pk); 
        }
        $table = empty($table)?$this->getRealTableName():$table;
        if(FALSE === $this->db->add($map,$table)){
            
            $this->error = _OPERATION_WRONG_;
            return false;
        }else {
            //成功后返回插入ID
            return $this->db->getLastInsID();
        }
    }

    /**
     +----------------------------------------------------------
     * 批量新增数据
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param mixed $dataList 数据
     * @param string $table  数据表名
     * @param string $pk 主键名
     +----------------------------------------------------------
     * @return boolen
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function addAll($dataList,$table=null,$pk='')
    {
        if(is_instance_of($dataList,'VoList')){
            $it = $dataList->getIterator();
        }
        //启用事务操作
        $this->startTrans();
        foreach ($it as $data){
            $this->add($data,$table,$pk);
        }
        //提交
        $this->commit();
        return true;
    }

    /**
     +----------------------------------------------------------
     * 更新数据
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param mixed $data 要更新的数据
     * @param string $table  数据表名
     * @param mixed $where 更新数据的条件
     * @param string $pk 主键名
     * @param integer $limit 要更新的记录数
     * @param string $order  更新的顺序
     +----------------------------------------------------------
     * @return boolen
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function save($data,$table=NULL,$where='',$limit=0,$order='',$pk='')
    {
        if(is_array($data)){
            $map = new HashMap($data);
        }
        elseif(is_instance_of($data,'Vo')){
            $map = $data->toMap();
        }
        elseif(is_instance_of($data,'HashMap')) {
            $map    = $data;
        }
        else {
            $this->error = _DATA_TYPE_INVALID_;
            return false;
        }

        $pk     = $pk?$pk:$this->pk;
        if($map->containsKey($pk)) {
            $where  = $pk."=".$map->get($pk);
            $map->remove($pk);         	
        }
        $table = empty($table)?$this->getRealTableName():$table;
        if(FALSE === $this->db->save($map,$table,$where,$limit,$order)){
            $this->error = _OPERATION_WRONG_;
            return false;
        }else {
            return True;
        }
    }

    /**
     +----------------------------------------------------------
     * 删除数据
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param integer $id 主键值
     * @param string $table  数据表名
     +----------------------------------------------------------
     * @return boolen
     +----------------------------------------------------------
     */
    function deleteById($id,$table='',$pk='')
    {
        $table  = empty($table)?$this->getRealTableName():$table;
        $pk     = $pk?$pk:$this->pk;
        if(FALSE === $this->db->remove($pk."='$id'",$table)){
            $this->error =  _OPERATION_WRONG_;
            return false;
        }else {
            return True;
        }
    }

    /**
     +----------------------------------------------------------
     * 根据条件删除表数据
     * 如果成功返回删除记录个数
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param mixed $condition 删除条件
     * @param string $table  数据表名
     * @param integer $limit 要删除的记录数
     * @param string $order  删除的顺序
     +----------------------------------------------------------
     * @return boolen
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function delete($data,$table='',$limit='',$order='')
    {
        $table = empty($table)?$this->getRealTableName():$table;
        if(is_array($data)){
            $map = new HashMap($data);
        }
        elseif(is_instance_of($data,'Vo')){
            $map = $data->toMap();
        }
        elseif(is_instance_of($data,'HashMap')) {
            $map    = $data;
        }
        if(!empty($map)) {
            $pk     = $pk?$pk:$this->pk;
            $where  = $pk."=".$map->get($pk);            
        }else {
            $where  =   $data;
        }
        $result=    $this->db->remove($where,$table,$limit,$order);
        if(FALSE === $result ){
            $this->error =  _OPERATION_WRONG_;
            return false;
        }else {
            //返回删除记录个数
            return $result;
        }
    }

    /**
     +----------------------------------------------------------
     * 根据条件删除表数据
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param mixed $condition 删除条件
     * @param string $table  数据表名
     +----------------------------------------------------------
     * @return boolen
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function deleteAll($condition,$table='')
    {
        $table = empty($table)?$this->getRealTableName():$table;
        if(FALSE === $this->db->remove($condition,$table)){
            $this->error =  _OPERATION_WRONG_;
            return false;
        }else {
            return True;
        }
    }

    /**
     +----------------------------------------------------------
     * 清空表数据
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $table  数据表名
     +----------------------------------------------------------
     * @return boolen
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function clear($table='')
    {
        $table = empty($table)?$this->getRealTableName():$table;
        if(FALSE === $this->db->clear($table)){
            $this->error =  _OPERATION_WRONG_;
            return false;
        }else {
            return True;
        }
    }

    /**
     +----------------------------------------------------------
     * 根据主键得到一条记录
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param int $id 主键的值
     * @param string $table  数据表名
     * @param string $fields 字段名，默认为*
     +----------------------------------------------------------
     * @return Vo
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function getById($id,$table='',$fields='*',$pk='')
    {
        $table  = empty($table)?$this->getRealTableName():$table;
        if(DATA_CACHE_ON) {//启用动态数据缓存
        	$vo  =  $this->getCacheVo('',$id);
            if(false !== $vo) {
            	return $vo;
            }
        }
        $pk     = $pk?$pk:$this->pk;
        $rs     = $this->db->find($pk."='{$id}'",$table,$fields);
        if($rs->size()>0) {
                $vo  =  $this->rsToVo($rs->get(0));
                if(DATA_CACHE_ON) 
                    $this->CacheVo($vo,$id);
                return $vo;
        }else {
            return false;
        }        	

    }

    /**
     +----------------------------------------------------------
     * 根据某个字段得到一条记录
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $field 字段名称
     * @param mixed $value 字段的值
     * @param string $table  数据表名
     * @param string $fields 字段名，默认为*
     +----------------------------------------------------------
     * @return Vo
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function getBy($field,$value,$table='',$fields='*')
    {
        $table  = empty($table)?$this->getRealTableName():$table;
        if(DATA_CACHE_ON) {//启用动态数据缓存
        	$vo  =  $this->getCacheVo('',$field.'_'.$value);
            if(false !== $vo) {
            	return $vo;
            }
        }
        $rs     = $this->db->find($field."='{$value}'",$table,$fields);
        if($rs->size()>0) {
                $vo  =  $this->rsToVo($rs->get(0));
                if(DATA_CACHE_ON) 
                    $this->CacheVo($vo,$field.'_'.$value);
                return $vo;
        }else {
            return false;
        }        	
    }

    /**
     +----------------------------------------------------------
     * 根据条件得到一条记录
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param mixed $condition 条件
     * @param string $table  数据表名
     * @param string $fields 字段名，默认为*
     * @param boolean $cache 是否读取缓存
     +----------------------------------------------------------
     * @return Vo
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function find($condition='',$table=NULL,$fields='*',$cache=true)
    {
        $table  = empty($table)?$this->getRealTableName():$table;
        $identify   = to_guid_string($condition);
        if(DATA_CACHE_ON && $cache) {//启用动态数据缓存
        	$vo  =  $this->getCacheVo('',$identify);
            if(false !== $vo) {
            	return $vo;
            }
        }
        $rs = $this->db->find($condition,$table,$fields,NULL,NULL,NULL,NULL,$cache);
        if($rs->size()>0) {
            $vo  =  $this->rsToVo($rs->get(0));
            if(DATA_CACHE_ON)
                $this->cacheVo($vo,$identify);
            return $vo;
        }else {
            return false;
        }
    }

    /**
     +----------------------------------------------------------
     * 查找记录
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param mixed $condition  条件
     * @param string $table  数据表名
     * @param string $fields  需要显示的字段
     * @param string $order  排序字段
     * @param string $limit  
     * @param string $group  
     * @param string $having 
     +----------------------------------------------------------
     * @return VoList
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function findAll($condition='',$table='',$fields='*',$order='',$limit='',$group='',$having='')
    {
        $table = empty($table)?$this->getRealTableName():$table;
        //数据集唯一标识符
        $identify   =   to_guid_string(func_get_args());
        if(DATA_CACHE_ON && $this->cacheQuery) { //启用数据动态缓存
            $voList = $this->getCacheVoList('',$identify);
            if (false !== $voList) {
                return $voList;
            }
        }
        $rs = $this->db->find($condition,$table,$fields,$order,$limit,$group,$having,$this->cacheQuery);
        $voList  = $this->rsToVoList($rs);
        if(DATA_CACHE_ON)
            $this->cacheVoList($voList,$identify);
        return $voList;
    }

    /**
     +----------------------------------------------------------
     * 查询SQL语句
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $sql  SQL指令
     +----------------------------------------------------------
     * @return false | integer
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function query($sql)
    {
        if(empty($sql)) return false;
        $identify   = md5($sql);
        if(DATA_CACHE_ON && $this->cacheQuery) {//启用动态数据缓存
        	$result =   $this->getCacheResultSet($identify);
            if(false !== $result) {
            	return $result;
            }
        }
        $result =   $this->db->query($sql);
        if(DATA_CACHE_ON)
            $this->cacheResultSet($result,$identify);
        return $result;
    }

    /**
     +----------------------------------------------------------
     * 执行SQL语句
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $sql  SQL指令
     +----------------------------------------------------------
     * @return false | integer
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function execute($sql)
    {
        if(empty($sql)) {
            $result =   $this->db->execute($sql);
            return $result;
        }else {
        	return false;
        }
    }

    /**
     +----------------------------------------------------------
     * 获取一条记录的某个字段值
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $field  字段名
     * @param mixed $condition  条件
     * @param string $table  数据表名
     +----------------------------------------------------------
     * @return mixed
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function getOne($field,$condition='',$table='')
    {
        $result    =   $this->find($condition,$table);
        if(!empty($result)) {
            return $result->$field;
        }else {
            return null;
        }
    }

    /**
     +----------------------------------------------------------
     * 获取没有vo对象的查询结果中的某个字段值
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param ResultSet $rs  查询结果
     * @param string $col  列名
     +----------------------------------------------------------
     * @return integer
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function getCol($rs,$col) 
    {
        $result =   $rs->get(0);
        $count  =   is_array($result)? $result[$col]:$result->{$col};
        return empty($count)?0:$count;    	
    }

    /**
     +----------------------------------------------------------
     * 统计满足条件的记录个数
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param mixed $condition  条件
     * @param string $table  数据表名
     +----------------------------------------------------------
     * @return integer
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function getCount($condition='',$table='')
    {
        $table = empty($table)?$this->getRealTableName():$table;
        $fields = 'count(*) as count';
        $rs = $this->db->find($condition,$table,$fields);
        return $this->getCol($rs,'count');
    }

    /**
     +----------------------------------------------------------
     * 取得某个字段的最大值
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $field  字段名
     * @param mixed $condition  条件
     * @param string $table  数据表名
     +----------------------------------------------------------
     * @return integer
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function getMax($field,$condition='',$table='')
    {
        $table = empty($table)?$this->getRealTableName():$table;
        $fields = 'MAX('.$field.') as max';
        $rs = $this->db->find($condition,$table,$fields);
        return $this->getCol($rs,'max');
    }

    /**
     +----------------------------------------------------------
     * 取得某个字段的最小值
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $field  字段名
     * @param mixed $condition  条件
     * @param string $table  数据表名
     +----------------------------------------------------------
     * @return integer
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function getMin($field,$condition='',$table='')
    {
        $table = empty($table)?$this->getRealTableName():$table;
        $fields = 'MIN('.$field.') as min';
        $rs = $this->db->find($condition,$table,$fields);
        return $this->getCol($rs,'min');
    }

    /**
     +----------------------------------------------------------
     * 统计某个字段的总和
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $field  字段名
     * @param mixed $condition  条件
     * @param string $table  数据表名
     +----------------------------------------------------------
     * @return integer
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function getSum($field,$condition='',$table='')
    {
        $table = empty($table)?$this->getRealTableName():$table;
        $fields = 'SUM('.$field.') as sum';
        $rs = $this->db->find($condition,$table,$fields);
        return $this->getCol($rs,'sum');
    }

    /**
     +----------------------------------------------------------
     * 根据条件禁用表数据
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param mixed $condition 删除条件
     * @param string $table  数据表名
     +----------------------------------------------------------
     * @return boolen
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function forbid($condition,$table='')
    {
        $table = empty($table)?$this->getRealTableName():$table;
        if(FALSE === $this->db->execute('update '.$table.' set status=0 where status=1 and ('.$condition.')')){
            $this->error =  _OPERATION_WRONG_;
            return false;
        }else {
            return True;
        }
    }

    /**
     +----------------------------------------------------------
     * 根据条件禁用表数据
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param mixed $condition 删除条件
     * @param string $table  数据表名
     +----------------------------------------------------------
     * @return boolen
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function resume($condition,$table='')
    {
        $table = empty($table)?$this->getRealTableName():$table;
        if(FALSE === $this->db->execute('update '.$table.' set status=1 where status=0 and ('.$condition.')')){
            $this->error =  _OPERATION_WRONG_;
            return false;
        }else {
            return True;
        }
    }

    /**
     +----------------------------------------------------------
     * 缓存VoList对象
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param VoList $voList VoList对象实例
     * @param string $identify 缓存标识
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function cacheVoList($voList,$identify='') 
    {
        $guid   = strtoupper($voList->getVoClass()).'List_'.$identify;
        $vo  =  $voList->get(0);
        if(empty($vo->_info)) {
            $cache = Cache::getInstance();
            $cache->set($guid,$voList);        	
        }else {
            // 读取vo对象的缓存属性
            // 进行相应的缓存
            switch($vo->_info['cache']) {
            	case Think_CACHE_STATIC:
                     // 永久缓存
                    file_put_contents(TEMP_PATH.$guid,serialize($voList));
                    break;
                case Think_CACHE_NO:
                	// 禁止缓存
                	break;
                case Think_CACHE_DYNAMIC:
                    // 动态缓存
                    $cache  =  Cache::getInstance();
                    $cache->set($guid,$voList);                	
                	break;
            }        	
        }
    }

    /**
     +----------------------------------------------------------
     * 缓存Vo对象
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param Vo $vo Vo对象实例
     * @param string $identify 缓存标识
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function cacheVo($vo,$identify='') 
    {
        if(empty($identify)) {
        	$identify   =  $vo->{$this->pk};
        }
    	$guid    = strtoupper(get_class ($vo)).'_'.$identify;
        if(empty($vo->_info)) {
            $cache  =  Cache::getInstance();
            $cache->set($guid,$vo);        	
        }else {
            // 读取vo对象的缓存属性
            // 进行相应的缓存
            switch($vo->_info['cache']) {
            	case Think_CACHE_STATIC:
                     // 永久缓存
                    file_put_contents(TEMP_PATH.$guid,serialize($vo));
                    break;
                case Think_CACHE_NO:
                	// 禁止缓存
                	break;
                case Think_CACHE_DYNAMIC:
                    // 动态缓存
                    $cache  =  Cache::getInstance();
                    $cache->set($guid,$vo);                	
                	break;
            }
        }
    }

    /**
     +----------------------------------------------------------
     * 缓存ResultSet对象
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param ResultSet $resultSet 插件结果集
     * @param string $identify 缓存标识
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function cacheResultSet($resultSet,$identify) 
    {
        $cache  =  Cache::getInstance();
        $guid    = 'ResultSet_'.$identify;
        $cache->set($guid,$resultSet);        	
    }

    /**
     +----------------------------------------------------------
     * 获取缓存ResultSet对象
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $identify 缓存标识
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function getCacheResultSet($identify) 
    {
        $cache  =  Cache::getInstance();
        $guid    = 'ResultSet_'.$identify;
        return $cache->get($guid);   	
    }

    /**
     +----------------------------------------------------------
     * 获取缓存的Vo对象
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $voClass Vo对象名
     * @param string $identify 缓存标识
     +----------------------------------------------------------
     * @return Vo
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function getCacheVo($voClass,$identify='') 
    {
        $voClass    = !empty($voClass)? $voClass : $this->getVo();
        $guid    = strtoupper($voClass).'_'.$identify;
        //判断是否存在永久缓存
        if(file_exists(TEMP_PATH.$guid)) {
            $vo =   unserialize(file_get_contents(TEMP_PATH.$guid));
        }else {
            $cache  =  Cache::getInstance();
            $vo       = $cache->get($guid);        	
        }
        return $vo;
    }

    /**
     +----------------------------------------------------------
     * 获取缓存的VoList对象
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $voClass Vo对象名
     * @param string $identify 缓存标识
     +----------------------------------------------------------
     * @return VoList
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function getCacheVoList($voClass='',$identify='') 
    {
        $voClass    = !empty($voClass)? $voClass : $this->getVo();
        $guid    = strtoupper($voClass).'List_'.$identify;
        $cache  =  Cache::getInstance();
        $voList  = $cache->get($guid);   
        return $voList;
    }

    /**
     +----------------------------------------------------------
     * 把一条查询结果转换为Vo对象
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param Result $result 记录
     * @param string $voClass Vo对象名
     +----------------------------------------------------------
     * @return Vo
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function rsToVo($result,$voClass='',$resultType='') 
    {
        $resultType = !empty($resultType)? $resultType : $this->resultType ;
        if($resultType== DATA_TYPE_VO) {
            $voClass    = !empty($voClass)? $voClass : $this->getVo();
            $vo = new $voClass($result);         	
            return auto_charset($vo,DB_CHARSET,TEMPLATE_CHARSET);
        }else{
        	return auto_charset($result,DB_CHARSET,TEMPLATE_CHARSET);
        }
       
    }

    /**
     +----------------------------------------------------------
     * 把查询记录集转换为VoList对象
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param Result $resultSet 记录集
     * @param string $voClass Vo对象名
     +----------------------------------------------------------
     * @return VoList
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function rsToVoList($resultSet,$voClass='',$resultType='') 
    {
        $resultType = !empty($resultType)? $resultType : $this->resultType ;
        if($resultType== DATA_TYPE_VO ) {
            $voClass    =   !empty($voClass)? $voClass : $this->getVo();
            $voList     = new VoList();
            foreach ($resultSet->toArray() as $result)
            {
                if(!empty($result)){
                    import("@.Vo.".$voClass);
                    $vo     = new $voClass($result);
                    $voList->add($vo);
                }
            }  
            return auto_charset($voList,DB_CHARSET,TEMPLATE_CHARSET);        	
        }else {
            return auto_charset($resultSet->toArray(),DB_CHARSET,TEMPLATE_CHARSET);  
        }

    }
    /**
     +----------------------------------------------------------
     * 根据表单创建Vo对象
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $type 创建类型
     * @param string $voClass 创建Vo对象的名称
     * @param string $pk Vo对象的主键名
     +----------------------------------------------------------
     * @return Vo
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function createVo($type='add',$voClass='',$pk='id')
    {
        if(empty($voClass)){
            $voClass = $this->getVo();
        }
        if ( strtolower($type) == "add" ) { //新增
            $vo = new $voClass(); //新建Vo对象
        } else { //编辑
            //根据编号获取Vo对象
            $daoClass   = substr($voClass,0,-2).'Dao';
            $dao        = new $daoClass();
            $pk         = $pk?$pk:$this->pk;
            $value      = isset($_GET[$pk])?$_GET[$pk]:$_POST[$pk];
            $vo         = $dao->find($pk."=".$value);
        }

        //给Vo对象赋值
        foreach ( $vo->__varList() as $name){
            $val = isset($_POST[$name])?$_POST[$name]:$_GET[$name];
            //保证赋值有效
            if(!is_null($val) && property_exists($vo,$name)){
                $vo->$name = $val;
            }
        }
        return $vo;
    }

    /**
     +----------------------------------------------------------
     * 得到完整的数据表名
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    function getRealTableName()
    {
        if(empty($this->realTableName)) {
            $realTableName  = !empty($this->appPrefix) ? $this->appPrefix.'_' : '';
            $realTableName .= !empty($this->modPrefix) ? $this->modPrefix.'_' : '';    
            $realTableName .= !empty($this->tableName) ? $this->tableName : strtolower(substr($this->__toString(),0,-3));
            $this->realTableName    =   '`'.$realTableName.'`';
        }
        return $this->realTableName;
    }

    /**
     +----------------------------------------------------------
     * 得到基本表名
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function getTableName()
    {
        if($this->tableName){
            return $this->tableName;
        }else 
            return substr($this->__toString(),0,-3);
    }

    /**
     +----------------------------------------------------------
     * 启动事务
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function startTrans()
    {
        $this->commit();
        $this->db->autoCommit = 0;
        return ;
    }

    /**
     +----------------------------------------------------------
     * 提交事务
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return boolean
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function commit()
    {
        return $this->db->commit();
    }

    /**
     +----------------------------------------------------------
     * 事务回滚
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return boolean
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function rollback()
    {
        return $this->db->rollback();
    }


    /**
     +----------------------------------------------------------
     * 取得当前Vo对象的名称
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function getVo()
    {
        return $this->getTableName().'Vo';
    }

    /**
     +----------------------------------------------------------
     * 取得所在对象的Dao类名
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function getDao()
    {
        return $this->__toString();
    }

    /**
     +----------------------------------------------------------
     * 获取数据库查询次数
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return integer
     +----------------------------------------------------------
     */
    function queryTimes()
    {
        return $this->db->getQueryTimes();
    }

    /**
     +----------------------------------------------------------
     * 获取数据库写入次数
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return integer
     +----------------------------------------------------------
     */
    function writeTimes()
    {
        return $this->db->getWriteTimes();
    }

};
?>