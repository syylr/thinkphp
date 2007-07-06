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
 * @package    Db
 * @link       http://www.fcs.org.cn
 * @copyright  Copyright (c) 2005-2006 liu21st.com.  All rights reserved. 
 * @author     liu21st <liu21st@gmail.com>
 * @version    $Id$
 +------------------------------------------------------------------------------
 */

import("FCS.Db.Db");
import("FCS.Db.ReusltSet");
import("FCS.Util.Log");

/**
 +------------------------------------------------------------------------------
 * Pgsql数据库驱动类
 +------------------------------------------------------------------------------
 * @package   Db
 * @author    liu21st <liu21st@gmail.com>
 * @version   0.8.0
 +------------------------------------------------------------------------------
 */
Class Db_Pgsql extends Db{

    /**
     +----------------------------------------------------------
     * 架构函数 读取数据库配置信息
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param array $config 数据库配置数组
     +----------------------------------------------------------
     */
    function __construct($config){    
        if ( !extension_loaded('pgsql') ) {    
            throw_exception('系统不支持pgsql');
        }
        $this->username = $config['username'];
        $this->password = $config['password'];
        $this->database = $config['database'];
        $this->hostname = $config['hostname'];
        $this->hostport = $config['hostport'];
    }

    /**
     +----------------------------------------------------------
     * 连接数据库方法
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    Function connect() {
        if ( !$this->linkID ) {
            $conn = $this->pconnect ? 'pg_pconnect':'pg_connect';
            $this->linkID =  $conn(
                'host='      . $this->hostname .
                ' port='     . $this->hostport .
                ' dbname='   . $this->database .
                ' user='     . $this->username .
                ' password=' . $this->password
            );

             if (pg_connection_status($this->linkID) !== 0)
                throw_exception($this->error(False));
                Return False;
            }
            $pgInfo = pg_version($this->linkID);
            $this->dbVersion = $pgInfo['server'];
            @pg_query( $this->linkID,"SET NAMES 'utf8'");
            unset($this->username,$this->password,$this->database,$this->hostname,$this->hostport);
        }
        Return $this->linkID;
    }

    /**
     +----------------------------------------------------------
     * 释放查询结果
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     */
    Function free() {
        @pg_free_result($this->queryID);
        $this->queryID = 0;
    }

    /**
     +----------------------------------------------------------
     * 执行查询 主要针对 SELECT, SHOW 等指令
     * 返回数据集
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $str  sql指令
     +----------------------------------------------------------
     * @return resultSet
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    Function _query($str='') {
        if ( !$this->linkID ) Return False;
        if ( $str != '' ) $this->queryStr = $str;
        if (!$this->autoCommit && $this->isMainIps($this->queryStr)) {
            //数据rollback 支持
            if ($this->transTimes == 0) {
                @pg_exec($this->linkID,'begin;');
            }
            $this->transTimes++;
        }else {
            //释放前次的查询结果
            if ( $this->queryID ) {    $this->free();    }
        }
        $this->queryStr = $this->escape_string($this->queryStr);
        $this->queryTimes ++;
        if ( $this->debug ) Log::Write(" SQL = ".$this->queryStr,WEB_LOG_DEBUG);
        $this->queryID = @pg_query($this->linkID,$this->queryStr );
        if ( !$this->queryID ) {
            throw_exception($this->error());
            Return False;
        } else {
            $this->numRows = pg_num_rows($this->queryID);
            $this->numCols = pg_num_fields($this->queryID);
            $this->resultSet = $this->getAll();
            Return new resultSet($this->resultSet);
        }
    }

    /**
     +----------------------------------------------------------
     * 执行语句 针对 INSERT, UPDATE 以及DELETE
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $str  sql指令
     +----------------------------------------------------------
     * @return integer
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    Function _execute($str='') {
        if ( !$this->linkID ) Return False;
        if ( $str != '' ) $this->queryStr = $str;

        if (!$this->autoCommit && $this->isMainIps($this->queryStr)) {
            //数据rollback 支持
            if ($this->transTimes == 0) {
                @pg_exec($this->linkID,'begin;');
            }
            $this->transTimes++;
        }else {
            //释放前次的查询结果
            if ( $this->queryID ) {    $this->free();    }
        }

        $this->queryStr = $this->escape_string($this->queryStr);
        $this->writeTimes ++;
        if ( $this->debug ) Log::Write(" SQL = ".$this->queryStr,WEB_LOG_DEBUG);
        if ( !@pg_query($this->linkID,$this->queryStr) ) {
            throw_exception($this->error());
            Return False;
        } else {
            $this->numRows = pg_affected_rows($this->queryID);
            $this->lastInsID = pg_last_oid($this->queryID);
            Return $this->numRows;
        }
    }

    /**
     +----------------------------------------------------------
     * 用于非自动提交状态下面的查询提交
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return boolen
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function commit()
    {
        if ($this->transTimes > 0) {
            $result = @pg_exec($this->linkID,'end;');
            if(!$result){
                throw_exception($this->error());
                return False;
            }
            $this->transTimes = 0;
        }
        return true;
    }

    /**
     +----------------------------------------------------------
     * 事务回滚
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return boolen
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function rollback()
    {
        if ($this->transTimes > 0) {
            $result = @pg_exec($this->linkID,'abort;');
            if(!$result){
                throw_exception($this->error());
                return False;
            }
            $this->transTimes = 0;
        }
        return True;
    }

    /**
     +----------------------------------------------------------
     * 获得下一条查询结果 简易数据集获取方法
     * 查询结果放到 result 数组中
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return boolen
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    Function next() {

        if ( !$this->queryID ) {
            throw_exception($this->error());
            Return False;
        }
        // 查询结果
        if($this->resultType==1){
            $this->result = @pg_fetch_object($this->queryID);
            $stat = is_object($this->result);
        }else{
            $this->result = @pg_fetch_assoc($this->queryID);
            $stat = is_array($this->result);
        }
        Return $stat;
    }

    /**
     +----------------------------------------------------------
     * 获得一条查询结果
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param index $seek 指针位置
     * @param string $str  SQL指令
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
     function getRow($sql = NULL,$seek=0) 
        {
            if (!empty($sql)) $this->_query($sql);
            if ( !$this->queryID ) {
                throw_exception($this->error());
                Return False;
            }
            if(pg_result_seek($this->queryID,$seek)){
                if($this->resultType==1){
                    //返回对象集
                    $result = @pg_fetch_object($this->queryID);
                }else{
                    // 返回数组集
                    $result = @pg_fetch_assoc($this->queryID);
                }
            }
            return $result;
            
        }

    /**
     +----------------------------------------------------------
     * 获得所有的查询数据
     * 查询结果放到 resultSet 数组中
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $resultType  数据集类型
     +----------------------------------------------------------
     * @return resultSet
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    Function getAll($resultType=NULL) {
        if ( !$this->queryID ) {
            throw_exception($this->error());
            Return False;
        }
        //返回数据集
        $result = array();
        if($this->numRows>0) {
            if(!is_null($resultType)){ $this->resultType = $resultType; }
            //返回数据集
            for($i=0;$i<$this->numRows ;$i++ ){
                $row=pg_fetch_assoc($this->queryID);
                for($j=0;$j<$this->numCols;$j++){
                    $name = pg_field_name($this->queryID, $j);
                    if($this->resultType==1){//返回对象集
                        $result[$i]->$name = $row[$name];
                    }else{//返回数组集
                        $result[$i][$name] = $row[$name];
                    }
                }
            }
            pg_result_seek($this->queryID,0);
        }
        Return $result;
    }

    /**
     +----------------------------------------------------------
     * 关闭数据库
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function close() { 
        if (!empty($this->queryID))
            @pg_free_result($this->queryID);
        if(!@pg_close($this->linkID)){
            throw_exception($this->error(False));
        }
        $this->linkID = 0;
    } 

    /**
     +----------------------------------------------------------
     * 数据库错误信息
     * 并显示当前的SQL语句
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function error($result = True) {
        if($result){
            $this->error = pg_result_error($this->queryID);
        }else{
            $this->error = pg_last_error($this->linkID);
        }
        if($this->queryStr!=''){
            $this->error .= "\n [ SQL语句 ] : ".$this->queryStr;
        }
        return $this->error;
    }

    /**
     +----------------------------------------------------------
     * SQL指令安全过滤
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $str  SQL指令
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function escape_string(&$str) { 
        $str = str_replace("&quot;", "\"", $str);
        $str = str_replace("&lt;", "<", $str);
        $str = str_replace("&gt;", ">", $str);
        $str = str_replace("&amp;", "&", $str);
        //$str = pg_escape_string($str); 
    } 

}//类定义结束
?>