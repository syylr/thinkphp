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

/**
 +------------------------------------------------------------------------------
 * Mysqli数据库驱动类
 +------------------------------------------------------------------------------
 * @package   Db
 * @author    liu21st <liu21st@gmail.com>
 * @version   $Id$
 +------------------------------------------------------------------------------
 */
Class Db_Mysqli extends Db{

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
    function __construct($config=''){    
        if ( !extension_loaded('mysqli') ) {    
            throw_exception('系统不支持mysqli');
        }
		if(!empty($config)) {
			$this->config	=	$config;
		}
    }

    /**
     +----------------------------------------------------------
     * 连接数据库方法
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function connect($config='') {
        if ( !$this->linkID ) {
			if(empty($config))	$config	=	$this->config;
            $this->linkID = mysqli_connect(
                                $config['hostname'], 
                                $config['username'], 
                                $config['password'],
                                $config['database'], 
                                $config['hostport']);
            if ( !$this->linkID) {
                throw_exception(mysqli_connect_error());
                Return False;
            }
            if($this->autoCommit){
                mysqli_autocommit($this->linkID, True);
            }else {
                mysqli_autocommit($this->linkID, False);
            }
            $this->dbVersion = mysqli_get_server_info($this->linkID);
            if ($this->dbVersion >= "4.1") { 
                //使用UTF8存取数据库 需要mysql 4.1.0以上支持
                mysqli_query( $this->linkID,"SET NAMES '".C('DB_CHARSET')."'");
            }
			// 标记连接成功
			$this->connected	=	true;
            //注销数据库安全信息
            unset($this->config);
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
    function free() {
        mysqli_free_result($this->queryID);
        $this->queryID = 0;
    }

    /**
     +----------------------------------------------------------
     * 执行查询 主要针对 SELECT, SHOW 等指令
     * 返回数据集
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $sqlStr  sql指令
     +----------------------------------------------------------
     * @return resultSet
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function _query($str='') {
		if ( !$this->connected ) $this->connect();
        if ( !$this->linkID ) Return False;
        if ( $str != '' ) $this->queryStr = $str;
        if (!$this->autoCommit && $this->isMainIps($this->queryStr)) {
            //数据rollback 支持
            if ($this->transTimes == 0) {
                mysqli_autocommit($this->linkID, false);
            }
            $this->transTimes++;
        }else {
            //释放前次的查询结果
            if ( $this->queryID ) {    $this->free();    }
        }

        $this->escape_string($this->queryStr);
        $this->queryTimes ++;
        if ( $this->debug ) Log::Write(" SQL = ".$this->queryStr,WEB_LOG_DEBUG);
        $this->queryID = mysqli_query($this->linkID,$this->queryStr );
        if ( !$this->queryID ) {
            throw_exception($this->error());
            Return False;
        } else {
            $this->numRows = mysqli_num_rows($this->queryID);
            $this->numCols = mysqli_num_fields($this->queryID);
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
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function _execute($str='') {
		if ( !$this->connected ) $this->connect();
        if ( !$this->linkID ) Return False;
        if ( $str != '' ) $this->queryStr = $str;
        if (!$this->autoCommit && $this->isMainIps($this->queryStr)) {
            //数据rollback 支持
            if ($this->transTimes == 0) {
                mysqli_autocommit($this->linkID, false);
            }
            $this->transTimes++;
        }else {
            //释放前次的查询结果
            if ( $this->queryID ) {    $this->free();    }
        }
        $this->escape_string($this->queryStr);
        $this->writeTimes ++;
        if ( $this->debug ) Log::Write(" SQL = ".$this->queryStr,WEB_LOG_DEBUG);
        if ( !mysqli_query($this->linkID,$this->queryStr) ) {
            throw_exception($this->error());
            Return False;
        } else {
            $this->numRows = mysqli_affected_rows($this->linkID);
            $this->lastInsID = mysqli_insert_id($this->linkID);
            Return $this->numRows;
        }
    }

    /**
     +----------------------------------------------------------
     * 用于非自动提交状态下面的查询提交
     * 
     +----------------------------------------------------------
     * @static
     * @access public 
     +----------------------------------------------------------
     * @return boolen
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function commit()
    {
        if ($this->transTimes > 0) {
            $result = mysqli_commit($this->linkID);
            mysqli_autocommit($this->linkID, TRUE);
            $this->transTimes = 0;
            if(!$result){
                throw_exception($this->error());
                return False;
            }
        }
        return true;
    }

    /**
     +----------------------------------------------------------
     * 事务回滚
     * 
     +----------------------------------------------------------
     * @static
     * @access public 
     +----------------------------------------------------------
     * @return boolen
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function rollback()
    {
        if ($this->transTimes > 0) {
            $result = mysqli_rollback($this->linkID);
            $this->transTimes = 0;
            if(!$result){
                throw_exception($this->error());
                return False;
            }
        }
        return True;
    }

    /**
     +----------------------------------------------------------
     * 获得下一条查询结果 简易数据集获取方法
     * 查询结果放到 result 数组中
     +----------------------------------------------------------
     * @static
     * @access public 
     +----------------------------------------------------------
     * @return boolen
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function next() {
        if ( !$this->queryID ) {
            throw_exception($this->error());
            Return False;
        }
        if($this->resultType==DATA_TYPE_VO){
            // 返回对象集
            $this->result = mysqli_fetch_object($this->queryID);
            $stat = is_object($this->result);
        }else{
            // 返回数组集
            $this->result = mysqli_fetch_assoc($this->queryID);
            $stat = is_array($this->result);
        }
        Return $stat;
    }

    /**
     +----------------------------------------------------------
     * 获得一条查询结果
     * 
     +----------------------------------------------------------
     * @static
     * @access public 
     +----------------------------------------------------------
     * @param index $seek 指针位置
     * @param string $str  SQL指令
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function getRow($sql = NULL,$seek=0) {
        if (!empty($sql)) $this->_query($sql);
        if ( !$this->queryID ) {
            throw_exception($this->error());
            Return False;
        }
        if($this->numRows >0) {
            if(mysqli_data_seek($this->queryID,$seek)){
                if($this->resultType== DATA_TYPE_VO){
                    //返回对象集
                    $result = mysqli_fetch_object($this->queryID);
                }else{
                    // 返回数组集
                    $result = mysqli_fetch_assoc($this->queryID);
                }
            }
            Return $result;
        }else {
        	return false;
        }
    }

    /**
     +----------------------------------------------------------
     * 获得所有的查询数据
     * 查询结果放到 resultSet 数组中
     +----------------------------------------------------------
     * @static
     * @access public 
     +----------------------------------------------------------
     * @param string $resultType  数据集类型
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function getAll($sql = NULL,$resultType=NULL) {
        if (!empty($sql)) $this->_query($sql);
        if ( !$this->queryID ) {
            throw_exception($this->error());
            Return False;
        }
        //返回数据集
        $result = array();
        $info   = mysqli_fetch_fields($this->queryID);
        if($this->numRows>0) {
            if(is_null($resultType)){ $resultType   =  $this->resultType ; }
            //返回数据集
            for($i=0;$i<$this->numRows ;$i++ ){
                if($resultType==DATA_TYPE_VO){
                    //返回对象集
                    $result[$i] = mysqli_fetch_object($this->queryID);
                }else{
                    // 返回数组集
                    $result[$i] = mysqli_fetch_assoc($this->queryID);
                }
            }
            mysqli_data_seek($this->queryID,0);
        }
        return $result;
    }

    /**
     +----------------------------------------------------------
     * 取得数据表的字段信息
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function getFields($tableName) { 
        $this->_query('SHOW COLUMNS FROM '.$tableName);
        $result =   $this->getAll();
        $info   =   array();
        foreach ($result as $key => $val) {
            $info[$val['Field']] = array(
                'name'    => $val['Field'],
                'type'    => $val['Type'],
                'notnull' => (bool) ($val['Null'] === ''), // not null is empty, null is yes
                'default' => $val['Default'],
                'primary' => (strtolower($val['Key']) == 'pri'),
                'autoInc' => (strtolower($val['Extra']) == 'auto_increment'),
            );
        }
        return $info;
    } 

    /**
     +----------------------------------------------------------
     * 取得数据表的字段信息
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function getTables($dbName='') { 
        $this->_query('SHOW TABLES');
        $result =   $this->getAll();
        $info   =   array();
        foreach ($result as $key => $val) {
            $info[$key] = current($val);
        }
        return $info;
    } 


    /**
     +----------------------------------------------------------
     * 关闭数据库
     +----------------------------------------------------------
     * @static
     * @access public 
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function close() { 
        if (!empty($this->queryID))
            mysqli_free_result($this->queryID);
        if (!mysqli_close($this->linkID)){
            throw_exception($this->error());
        }
        $this->linkID = 0;
    } 

    /**
     +----------------------------------------------------------
     * 数据库错误信息
     * 并显示当前的SQL语句
     +----------------------------------------------------------
     * @static
     * @access public 
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function error() {
        $this->error = mysqli_error($this->linkID);
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
     * @static
     * @access public 
     +----------------------------------------------------------
     * @param string $str  SQL指令
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function escape_string(&$str) { 
        $str = str_replace("&quot;", "\"", $str);
        $str = str_replace("&lt;", "<", $str);
        $str = str_replace("&gt;", ">", $str);
        $str = str_replace("&amp;", "&", $str);
        //$str = mysqli_real_escape_string($this->linkID,$str); 
    } 

}//类定义结束
?>