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
 * Oracle数据库驱动类
 +------------------------------------------------------------------------------
 * @package   Db
 * @author    liu21st <liu21st@gmail.com>
 * @version   $Id$
 +------------------------------------------------------------------------------
 */
Class Db_Oracle extends Db{

    /**
     +----------------------------------------------------------
     * 架构函数 读取数据库配置信息
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param array $config 数据库配置数组
     +----------------------------------------------------------
     */
    function __construct($config=''){    
        if ( !function_exists('oci_connect')) {    
            throw_exception('系统不支持oracle');
        }
		if(!empty($config)) {
			$this->config	=	$config;
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
    function connect($config='',$linkNum=0) {
        if ( !$this->linkID[$linkNum] ) {
			if(empty($config))	$config	=	$this->config;
            $conn = $this->pconnect ? 'oci_pconnect':'oci_connect';
			if(false === strpos($config['database'],$config['hostname'])) {
				$config['database']	=	"(DESCRIPTION = (ADDRESS_LIST = (ADDRESS =(COMMUNITY = ".$config['database'].")(PROTOCOL = TCP)(Host =".$config['hostname'].")(Port = ".$config['hostport'].")))(CONNECT_DATA = (SID = ".$config['sid'].")))";
			}
            $this->linkID[$linkNum] = $conn($config['username'], $config['password'],$config['database']);
            if ( !$this->linkID[$linkNum]) {
                throw_exception($this->error());
                Return False;
            }
            $this->dbVersion = OCI_Server_Version($this->linkID[$linkNum]);
			// 标记连接成功
			$this->connected	=	true;
            //注销数据库安全信息
            unset($this->config);
        }
        Return $this->linkID[$linkNum];
    }

    /**
     +----------------------------------------------------------
     * 释放查询结果
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     */
    function free() {
        @oci_free_statement($this->queryID);
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
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function _query($str='') {
		$this->initConnect(false);
        if ( !$this->_linkID ) Return False;
        if ( $str != '' ) $this->queryStr = $str;
        if ($this->autoCommit) {
            //释放前次的查询结果
            if ( $this->queryID ) {    $this->free();    }
        }
        $this->escape_string($this->queryStr);
        $this->queryTimes ++;
		$this->Q(1);
        if ( $this->debug ) Log::Write(" SQL = ".$this->queryStr,WEB_LOG_DEBUG);
        $this->queryID = OCI_Parse($this->_linkID,$this->queryStr);
        if ( !$this->queryID ) {
            throw_exception($this->error());
            Return False;
        } else {
            if(!$this->autoCommit && $this->isMainIps($this->queryStr)){
                $result = OCI_Execute($this->queryID,OCI_DEFAULT);
                $this->transTimes++;
            }else {
                $result = OCI_Execute($this->queryID,OCI_COMMIT_ON_SUCCESS);
            }
            if(!$result){
                throw_exception($this->error());
                Return False;
            }
            $this->numRows = oci_fetch_all($this->queryID,$this->resultSet);
            $this->numCols = oci_num_fields($this->queryID);
            $this->resultSet = $this->getAll();
            Return new resultSet($this->resultSet);
        }
    }

    /**
     +----------------------------------------------------------
     * 执行语句 针对 INSERT, UPDATE 以及DELETE
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
		$this->initConnect(true);
        if ( !$this->_linkID ) Return False;
        if ( $str != '' ) $this->queryStr = $str;
        if ($this->autoCommit) {
            //释放前次的查询结果
            if ( $this->queryID ) {    $this->free();    }
        }
        $this->escape_string($this->queryStr);
        $this->writeTimes ++;
		$this->W(1);
        if ( $this->debug ) Log::Write(" SQL = ".$this->queryStr,WEB_LOG_DEBUG);
        if ( !OCI_Parse($this->_linkID,$this->queryStr) ) {
            throw_exception($this->error());
            Return False;
        } else {
            if(!$this->autoCommit && $this->isMainIps($this->queryStr)){
                $result = OCI_Execute($this->queryID,OCI_DEFAULT);
                $this->transTimes++;
            }else {
                $result = OCI_Execute($this->queryID,OCI_COMMIT_ON_SUCCESS);
            }
            if(!$result){
                throw_exception($this->error());
                Return False;
            }
            $this->numRows = oci_num_rows($this->_linkID);
            Return $this->numRows;
        }
    }

    /**
     +----------------------------------------------------------
     * 用于非自动提交状态下面的查询提交
     +----------------------------------------------------------
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
            $result = oci_commit($this->_linkID);
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
     +----------------------------------------------------------
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
            $result = oci_rollback($this->_linkID);
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
        if($this->resultType== DATA_TYPE_VO){
            // 返回对象集
            $this->result = oci_fetch_object($this->queryID);
            $stat = is_object($this->result);
        }else{
            // 返回数组集
            $this->result = oci_fetch_assoc($this->queryID);
            $stat = is_array($this->result);
        }
        Return $stat;
    }

    /**
     +----------------------------------------------------------
     * 获得一条查询结果
     +----------------------------------------------------------
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
    function getRow($sql = NULL) {
        if (!empty($sql)) $this->_query($sql);
        if ( !$this->queryID ) {
            throw_exception($this->error());
            Return False;
        }
        if($this->numRows >0) {
            if($this->resultType== DATA_TYPE_VO){
                //返回对象集
                $result = oci_fetch_object($this->queryID);
            }else{
                // 返回数组集
                $result = oci_fetch_assoc($this->queryID);
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
        if($this->numRows >0) {
            if(is_null($resultType)){ $resultType   =  $this->resultType ; }
            for($i=0;$i<$this->numRows ;$i++ ){
                if($resultType== DATA_TYPE_VO){
                    //返回对象集
                    $result[$i] = oci_fetch_object($this->queryID);
                }else{
                    // 返回数组集
                    $result[$i] = oci_fetch_assoc($this->queryID);
                }
            }
        }
        Return $result;
    }

    /**
     +----------------------------------------------------------
     * 关闭数据库
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function close() { 
        if (!empty($this->queryID))
            $this->free();
        if (!OCI_close($this->_linkID)){
            throw_exception($this->error());
        }
        $this->_linkID = 0;
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
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function error() {
        $this->error = OCI_Error($this->_linkID);
        $this->error = $this->error["message"];
        if($this->queryStr!=''){
            $this->error .= "\n [ SQL语句 ] : ".$this->queryStr;
        }
        return $this->error;
    }

    /**
     +----------------------------------------------------------
     * SQL指令安全过滤
     +----------------------------------------------------------
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
    } 

}//类定义结束
?>