<?php 
// +---------------------------------------------------------------------------+
// | FCS -- Fast,Compatible & Simple OOP PHP Framework                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2005-2006 liu21st.com.  All rights reserved.                |
// | Website: http://www.fcs.org.cn/                                           |
// | Author : Liu21st 流年 <liu21st@gmail.com>                                 |
// +---------------------------------------------------------------------------+
// | This program is free software; you can redistribute it and/or modify it   |
// | under the terms of the GNU General Public License as published by the     |
// | Free Software Foundation; either version 2 of the License,  or (at your   |
// | option) any later version.                                                |
// |                                                                           |
// | This program is distributed in the hope that it will be useful,  but      |
// | WITHOUT ANY WARRANTY; without even the implied warranty of                |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General |
// | Public License for more details.                                          |
// +---------------------------------------------------------------------------+

/**
 +------------------------------------------------------------------------------
 * FCS
 +------------------------------------------------------------------------------
 * @package    Core
 * @link       http://www.fcs.org.cn
 * @copyright  Copyright (c) 2005-2006 liu21st.com.  All rights reserved. 
 * @author     liu21st <liu21st@gmail.com>
 * @version    $Id$
 +------------------------------------------------------------------------------
 */

import("FCS.Db.Db");
import("FCS.Util.SharedMemory");

/**
 +------------------------------------------------------------------------------
 * 数据访问基础类
 +------------------------------------------------------------------------------
 * @package   core
 * @author    liu21st <liu21st@gmail.com>
 * @version   0.8.0
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
    function __construct($appPrefix='',$tableName='',$pk='',$autoIncrement=true)
    {
        $this->db = DB::getInstance();
        if(!empty($appPrefix))  $this->appPrefix    =   $appPrefix;
        if(!empty($tableName))  $this->tableName    =   $tableName;
        if(!empty($pk))         $this->pk = $pk;
        if(is_bool($autoIncrement))   $this->autoIncrement = $autoIncrement;

        //自动加载所需的Vo类文件
        import(APP_NAME.'.Vo.'.$this->getVo(),APPS_PATH);
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
     * @throws FcsException
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
            $this->error = '非法数据对象！';
            return false;
        }
        if($this->autoIncrement) {
            //如果主键为自动增长类型
            //删除主键属性 由数据库自动生成
            $map->remove($pk?$pk:$this->pk); 
        }
        $table = empty($table)?$this->getRealTableName():$table;
        if(FALSE === $this->db->add($map,$table)){
            $this->error = "新增数据出错！";
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
     * @throws FcsException
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
     * @throws FcsException
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
            $this->error = '非法数据对象！';
            return false;
        }

        $pk     = $pk?$pk:$this->pk;
        if($map->containsKey($pk)) {
            $where  = $pk."=".$map->get($pk);
            $map->remove($pk);         	
        }

        $table = empty($table)?$this->getRealTableName():$table;
        if(FALSE === $this->db->save($map,$table,$where,$limit,$order)){
            $this->error = '更新数据出错！';
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
        if(FALSE === $this->db->remove($pk."=$Id",$table)){
            $this->error =  '删除数据出错！';
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
     * @throws FcsException
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
            $this->error =  '删除数据出错！';
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
     * @throws FcsException
     +----------------------------------------------------------
     */
    function deleteAll($condition,$table='')
    {
        $table = empty($table)?$this->getRealTableName():$table;
        if(FALSE === $this->db->remove($condition,$table)){
            $this->error =  '删除数据出错！';
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
     * @throws FcsException
     +----------------------------------------------------------
     */
    function getById($id,$table='',$fields='*',$pk='')
    {
        $table  = empty($table)?$this->getRealTableName():$table;
        $pk     = $pk?$pk:$this->pk;
        $rs     = $this->db->find($pk."=$id",$table,$fields);
        return $this->rsToVo($rs->get(0));
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
     +----------------------------------------------------------
     * @return Vo
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function find($condition,$table=NULL,$fields='*')
    {
        $table = empty($table)?$this->getRealTableName():$table;
        $rs = $this->db->find($condition,$table,$fields);
        if($rs->size()>0) {
            return $this->rsToVo($rs->get(0));
        }else {
        	return null;
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
     * @throws FcsException
     +----------------------------------------------------------
     */
    function findAll($condition='',$table='',$fields='*',$order='',$limit='',$group='',$having='')
    {
        $table = empty($table)?$this->getRealTableName():$table;
        //数据集唯一标识符
        $identify   =   to_guid_string(func_get_args());
        $rs = $this->db->find($condition,$table,$fields,$order,$limit,$group,$having,$this->cacheQuery);
        return $this->rsToVoList($rs,'',$identify);
    }

    /**
     +----------------------------------------------------------
     * 查询SQL语句
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $sql  SQL指令
     * @param string $table  数据表名
     +----------------------------------------------------------
     * @return integer
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function query($sql)
    {
        if(empty($sql)) {
            $result =   $this->db->query($sql);
            return $result;
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
     * @return integer
     +----------------------------------------------------------
     * @throws FcsException
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
     * @throws FcsException
     +----------------------------------------------------------
     */
    function getCount($condition='',$table='')
    {
        $table = empty($table)?$this->getRealTableName():$table;
        $fields = 'count('.$this->pk.') as count';
        $rs = $this->db->find($condition,$table,$fields);
        $result =   $rs->get(0);
        $count  =   is_array($result)? $result['count']:$result->count;
        return empty($count)?0:$count;
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
     * @throws FcsException
     +----------------------------------------------------------
     */
    function getMax($field,$condition='',$table='')
    {
        $table = empty($table)?$this->getRealTableName():$table;
        $fields = 'MAX('.$field.') as max';
        $rs = $this->db->find($condition,$table,$fields);
        $result =   $rs->get(0);
        $count  =   is_array($result)? $result['max']:$result->max;
        return empty($count)?0:$count;
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
     * @throws FcsException
     +----------------------------------------------------------
     */
    function getMin($field,$condition='',$table='')
    {
        $table = empty($table)?$this->getRealTableName():$table;
        $fields = 'MIN('.$field.') as min';
        $rs = $this->db->find($condition,$table,$fields);
        $result =   $rs->get(0);
        $count  =   is_array($result)? $result['min']:$result->min;
        return empty($count)?0:$count;
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
     * @throws FcsException
     +----------------------------------------------------------
     */
    function getSum($field,$condition='',$table='')
    {
        $table = empty($table)?$this->getRealTableName():$table;
        $fields = 'SUM('.$field.') as sum';
        $rs = $this->db->find($condition,$table,$fields);
        $result =   $rs->get(0);
        $sum  =   is_array($result)? $result['sum']:$result->sum;
        return empty($sum)?0:$sum;
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
     * @throws FcsException
     +----------------------------------------------------------
     */
    function forbid($condition,$table='')
    {
        $table = empty($table)?$this->getRealTableName():$table;
        if(FALSE === $this->db->execute('update '.$table.' set status=0 where status=1 and ('.$condition.')')){
            $this->error =  '操作数据出错！';
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
     * @throws FcsException
     +----------------------------------------------------------
     */
    function resume($condition,$table='')
    {
        $table = empty($table)?$this->getRealTableName():$table;
        if(FALSE === $this->db->execute('update '.$table.' set status=1 where status=0 and ('.$condition.')')){
            $this->error =  '操作数据出错！';
            return false;
        }else {
            return True;
        }
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
     * @throws FcsException
     +----------------------------------------------------------
     */
    function rsToVo($result,$voClass=NULL)
    {
        $voClass    = !empty($voClass)? $voClass : $this->getVo();
        //给数据记录分配一个唯一的标识号
        if(is_object($result)) {
            $id =   $result->{$this->pk};
        }elseif(is_array($result)) {
            $id =   $result[$this->pk];
        }
        $guid = strtoupper($voClass).'_'.$id;
        //判断是否存在永久缓存
        if(file_exists(TEMP_PATH.$guid)) {
            $vo =   unserialize(file_get_contents(TEMP_PATH.$guid));
        }
        elseif(DATA_CACHE_ON){//启用数据动态缓存
            //取得共享内存实例
            $sm = SharedMemory::getInstance();
            //获取共享内存数据
            $vo = $sm->get($guid);
        }
        if(empty($vo)){
            //如果共享内存无效或者没有启用
            //则重新取得Vo对象
            $vo = new $voClass($result);
            //永久缓存
            if($vo->_info['cache'] == FCS_CACHE_STATIC ){
                file_put_contents(TEMP_PATH.$guid,serialize($vo));
            }elseif($vo->_info['cache'] != FCS_CACHE_NO && DATA_CACHE_ON){
                //如果启用动态缓存
                //则重新写入缓存
                $sm->set($guid,$vo);
            }
        }
        return $vo;
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
     * @throws FcsException
     +----------------------------------------------------------
     */
    function rsToVoList($resultSet,$voClass='',$identify='')
    {

        $voClass    =   !empty($voClass)? $voClass : $this->getVo();
        if(empty($identify)) {
            // 给数据集分配一个唯一的标识号
            // 便于缓存定位
            $identify   =   to_guid_string($resultSet);
        }
        $guid   =   strtoupper($voClass.'List_').$identify;
        //判断是否存在永久缓存
        if(file_exists(TEMP_PATH.$guid)) {
            $voList =   unserialize(file_get_contents(TEMP_PATH.$guid));
        }
        elseif(DATA_CACHE_ON){//启用数据动态缓存
            //取得共享内存实例
            $sm = SharedMemory::getInstance();
            //获取共享内存数据
            $voList = $sm->get($guid);
        }
        if(empty($voList)){
            //如果共享内存无效或者没有启用
            //则重新取得VoList对象
            $voList     = new VoList();
            foreach ($resultSet->toArray() as $result)
            {
                if(!empty($result)){
                    $vo     = new $voClass($result);
                    $voList->add($vo);
                }
            }
            //永久缓存
            if($vo->_info['cache'] == FCS_CACHE_STATIC ){
                file_put_contents(TEMP_PATH.$guid,serialize($voList));
            }elseif($vo->_info['cache'] != FCS_CACHE_NO && DATA_CACHE_ON){
                //如果启用动态缓存
                //则重新写入缓存
                $sm->set($guid,$voList);                	
            }
        }

        return $voList;
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
     * @throws FcsException
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
            if(isset($val) && property_exists($vo,$name)){
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
     * @throws FcsException
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
     * @throws FcsException
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
     * @throws FcsException
     +----------------------------------------------------------
     */
    function commit()
    {
        $result =   $this->db->commit();
        return $result;
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
     * @throws FcsException
     +----------------------------------------------------------
     */
    function rollback()
    {
        $result =   $this->db->rollback();
        return $result;
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
     * @throws FcsException
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
     * @throws FcsException
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