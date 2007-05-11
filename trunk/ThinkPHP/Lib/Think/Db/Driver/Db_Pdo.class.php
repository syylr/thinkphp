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
 * PDO数据库驱动类
 +------------------------------------------------------------------------------
 * @package   Db
 * @author    liu21st <liu21st@gmail.com>
 * @version   $Id$
 +------------------------------------------------------------------------------
 */
Class Db_Pdo extends Db{

	var $pdo		= null;
	var $dsn		= null;
	var $PDOStatement = null;

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
        if ( !class_exists('PDO') ) {    
            throw_exception('系统不支持PDO');
        }
        $this->username = $config['username'];
        $this->password = $config['password'];
        $this->database = $config['database'];
        $this->hostname = $config['hostname'];
		if(empty($config['pdodsn'])) {
			$this->dsn = defined('DB_PDO_DSN')?DB_PDO_DSN:'';
		}else {
			$this->dsn = $config['pdodsn'];
		}
		if(empty($config['pdoparms'])) {
			$this->parms = defined('DB_PDO_PARMS')?DB_PDO_PARMS:'';
		}else {
			$this->parms = $config['pdoparms'];
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
    Function connect() {
        if ( !$this->pdo ) {
            $this->pdo = new PDO( $this->dsn, $this->username, $this->password,$this->parms);

            if ( !$this->pdo) {
                throw_exception('PDO CONNECT ERROR');
                return False;
            }
			$this->pdo->exec('SET NAMES '.DB_CHARSET);  
            $this->dbVersion = $this->pdo->getAttribute(constant("PDO::ATTR_SERVER_INFO"));
            //注销数据库安全信息
            unset($this->username,$this->password,$this->database,$this->hostname,$this->hostport,$this->dsn,$this->parms);
        }
        return $this->pdo;
    }

    /**
     +----------------------------------------------------------
     * 释放查询结果
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     */
    Function free() {
        $this->PDOStatement = null;
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
    Function _query($str='') {
        if ( !$this->pdo ) return False;
        if ( $str != '' ) $this->queryStr = $str;
        if (!$this->autoCommit && $this->isMainIps($this->queryStr)) {
            //数据rollback 支持
            if ($this->transTimes == 0) {
				$this->pdo	->beginTransaction();
            }
            $this->transTimes++;
        }else {
            //释放前次的查询结果
            if ( !empty($this->PDOStatement) ) {    $this->free();    }
        }
        $this->escape_string($this->queryStr);
        $this->queryTimes++;
        if ( $this->debug ) Log::Write(" SQL = ".$this->queryStr,WEB_LOG_DEBUG);
        $this->PDOStatement = $this->pdo->prepare($this->queryStr);
		$result	=	$this->PDOStatement->execute();
        if ( !$result ) {
            if ( $this->debug ) throw_exception($this->error());
            return False;
        } else {
            $this->numRows = $this->PDOStatement->rowCount();
            $this->numCols = $this->PDOStatement->columnCount();
            $this->resultSet = $this->getAll();
            return new resultSet($this->resultSet);              	
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
    Function _execute($str='') {
        if ( !$this->pdo ) return False;
        if ( $str != '' ) $this->queryStr = $str;
        if (!$this->autoCommit && $this->isMainIps($this->queryStr)) {
            //数据rollback 支持
            if ($this->transTimes == 0) {
				$this->pdo	->beginTransaction();
            }
            $this->transTimes++;
        }else {
            //释放前次的查询结果
            if ( !empty($this->PDOStatement) ) {    $this->free();    }
        }
        $this->escape_string($this->queryStr);
        $this->writeTimes++;
        if ( $this->debug ) Log::Write(" SQL = ".$this->queryStr,WEB_LOG_DEBUG);
		$result	=	$this->pdo->exec($this->queryStr);
        if ( !$result) {
            //if ( $this->debug ) throw_exception($this->error());
            return False;
        } else {
			$this->numRows = $result;
            $this->lastInsID = $this->pdo->lastInsertId();
            return $this->numRows;            	
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
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function commit()
    {
        if ($this->transTimes > 0) {
            $result = $this->pdo->commit();
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
            $result = $this->pdo->rollback();
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
        if ( !$this->PDOStatement ) {
            throw_exception($this->error());
            return False;
        }
        if($this->resultType== DATA_TYPE_VO){
            // 返回对象集
            $this->result = $this->PDOStatement->fetch(constant('PDO::FETCH_OBJ'));
            $stat = is_object($this->result);
        }else{
            // 返回数组集
            $this->result = $this->PDOStatement->fetch(constant('PDO::FETCH_ASSOC'));
            $stat = is_array($this->result);
        }
        return $stat;
    }

    /**
     +----------------------------------------------------------
     * 获得一条查询结果
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param integer $seek 指针位置
     * @param string $str  SQL指令
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    Function getRow($sql = NULL,$seek=0) {
        if (!empty($sql)) $this->_query($sql);
        if ( empty($this->PDOStatement) ) {
            throw_exception($this->error());
            return False;
        }
        if($this->numRows >0) {
			if($this->resultType== DATA_TYPE_VO){
				//返回对象集
				$result = $this->PDOStatement->fetch(constant('PDO::FETCH_OBJ'),constant('PDO::FETCH_ORI_NEXT'),$seek);
			}else{
				// 返回数组集
				$result = $this->PDOStatement->fetch(constant('PDO::FETCH_ASSOC'),constant('PDO::FETCH_ORI_NEXT'),$seek);
			}
            return $result;
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
        if ( empty($this->PDOStatement) ) {
            throw_exception($this->error());
            return False;
        }
        //返回数据集
        $result = array();
        if($this->numRows >0) {
            if(is_null($resultType)){ $resultType   =  $this->resultType ; }
             for($i=0;$i<$this->numRows ;$i++ ){
                if($resultType== DATA_TYPE_VO){
                    //返回对象集
                    $result[$i] = $this->PDOStatement->fetch(constant('PDO::FETCH_OBJ'));
                }else{
                    // 返回数组集
                    $result[$i] = $this->PDOStatement->fetch(constant('PDO::FETCH_ASSOC'));
                }
            }
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
        $this->_query('SHOW COLUMNS FROM `'.$tableName.'`');
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
     * 取得数据库的表信息
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
     * @access public 
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function close() { 
        $this->pdo = null;
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
        $error = $this->PDOStatement->errorInfo();
		$this->error = $error[2];
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
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function escape_string(&$str) { 
        $str = str_replace("&quot;", "\"", $str);
        $str = str_replace("&lt;", "<", $str);
        $str = str_replace("&gt;", ">", $str);
        $str = str_replace("&amp;", "&", $str);
        //$str = mysql_real_escape_string($str, $this->linkID); 
    } 

}//类定义结束
?>