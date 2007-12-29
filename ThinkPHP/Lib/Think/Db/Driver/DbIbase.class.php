<?php 
// +----------------------------------------------------------------------+
// | ThinkPHP                                                             |
// +----------------------------------------------------------------------+
// | Copyright (c) 2006~2007 http://thinkphp.cn All rights reserved.      |
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
 * Firebird数据库驱动类 剑雷 2007.12.28
 +------------------------------------------------------------------------------
 * @category   Think
 * @package  Think
 * @subpackage  Db
 * @author    liu21st <liu21st@gmail.com>
 * @version   $Id$
 +------------------------------------------------------------------------------
 */
Class DbIbase extends Db{

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
        if ( !extension_loaded('interbase') ) {    
            throw_exception(L('_NOT_SUPPERT_').':Interbase or Firebird');
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
    public function connect($config='',$linkNum=0) {
        if ( !isset($this->linkID[$linkNum]) ) {
			if(empty($config))	$config	=	$this->config;
            $conn = $this->pconnect ? 'ibase_pconnect':'ibase_connect';
            $this->linkID[$linkNum] = $conn( $config['hostname'].'/'.$config['hostport'].':'.$config['database'], $config['username'], $config['password']);
            if ( !$this->linkID[$linkNum]) {
                throw_exception(ibase_errmsg());
                return False;
            }
		//剑雷 2007.12.28	
       if ( ($svc = ibase_service_attach($config['hostname'], $config['username'], $config['password'])) != FALSE)
		{
			$ibase_info = ibase_server_info ($svc, IBASE_SVC_SERVER_VERSION) . '/' . ibase_server_info($svc, IBASE_SVC_IMPLEMENTATION);
			ibase_service_detach ($svc);
		}
		else
		{
			$ibase_info = 'Unable to Determine';
		}
            $this->dbVersion = $ibase_info;

			// 标记连接成功
			$this->connected	=	true;
            // 注销数据库连接配置信息
            if(1 != C('DB_DEPLOY_TYPE')) unset($this->config);
        }
        return $this->linkID[$linkNum];
    }

    /**
     +----------------------------------------------------------
     * 释放查询结果
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     */
    public function free() {
        @ibase_free_result($this->queryID);
        $this->queryID = 0;
    }

    /**
     +----------------------------------------------------------
     * 执行查询 主要针对 SELECT, SHOW 等指令
     * 返回数据集
     +----------------------------------------------------------
     * @access protected 
     +----------------------------------------------------------
     * @param string $str  sql指令
     +----------------------------------------------------------
     * @return resultSet
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    protected function _query($str='') {
		$this->initConnect(false);
        if ( !$this->_linkID ) return false;
        if ( $str != '' ) $this->queryStr = $str;
        if (!$this->autoCommit && $this->isMainIps($this->queryStr)) {
			$this->startTrans();
        }else {
            //释放前次的查询结果
            if ( $this->queryID ) {    $this->free();    }
        }
        $this->queryTimes++;
		$this->Q(1);
        $this->queryID = ibase_query($this->_linkID, $this->queryStr);
		$this->debug();
        if ( !$this->queryID ) {
            return false;
        } else {
            $this->numCols = ibase_num_fields($this->queryID);
			$this->resultSet = $this->getAll();
            return new ArrayObject($this->resultSet);              	
        }
    }

    /**
     +----------------------------------------------------------
     * 执行语句 针对 INSERT, UPDATE 以及DELETE
     +----------------------------------------------------------
     * @access protected 
     +----------------------------------------------------------
     * @param string $str  sql指令
     +----------------------------------------------------------
     * @return integer
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    protected function _execute($str='') {
		$this->initConnect(true);
        if ( !$this->_linkID ) return false;
        if ( $str != '' ) $this->queryStr = $str;
        if (!$this->autoCommit && $this->isMainIps($this->queryStr)) {
			$this->startTrans();
        }else {
            //释放前次的查询结果
            if ( $this->queryID ) {    $this->free();    }
        }
        $this->writeTimes++;
		$this->W(1);
		$result	=	ibase_query($this->_linkID, $this->queryStr) ;
		$this->debug();
        if ( false === $result) {
            return false;
        } else {
            $this->numRows = ibase_affected_rows($this->_linkID);
			//剑雷 2007.12.28
            //$this->lastInsID = mysql_insert_id($this->_linkID);
			$this->lastInsID =0;
			
            return $this->numRows;            	
        }
    }

	public function startTrans() {
		//数据rollback 支持
		if ($this->transTimes == 0) {
			ibase_trans( IBASE_DEFAULT, $this->_linkID);
		}
		$this->transTimes++;
		return ;
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
    public function commit()
    {
        if ($this->transTimes > 0) {
            $result =  ibase_commit($this->_linkID);
            $this->transTimes = 0;
            if(!$result){
                throw_exception($this->error());
                return false;
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
    public function rollback()
    {
        if ($this->transTimes > 0) {
            $result =ibase_rollback($this->_linkID);
            $this->transTimes = 0;
            if(!$result){
                throw_exception($this->error());
                return false;
            }
        }
        return true;
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
    public function next() {
        if ( !$this->queryID ) {
            throw_exception($this->error());
            return false;
        }
        if($this->resultType== DATA_TYPE_OBJ){
            // 返回对象集
            $this->result = @ibase_fetch_object($this->queryID);
            $stat = is_object($this->result);
        }else{
            // 返回数组集
            $this->result = @ibase_fetch_assoc($this->queryID);
            $stat = is_array($this->result);
        }
        return $stat;
    }

    /**
     +----------------------------------------------------------
     * 获得一条查询结果
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
    public function getRow($sql = null,$seek=0) {
        if (!empty($sql)) $this->_query($sql);
        if ( !$this->queryID ) {
            throw_exception($this->error());
            return false;
        }
        if($this->numRows >0) {
            if($this->resultType== DATA_TYPE_OBJ){
              //返回对象集
			  while ($seek>=0)
			  {
                $result = ibase_fetch_object($this->queryID);
				if (!$result){ $seek=-1;} else { $seek--;}
			  }
             }else{
              // 返回数组集
			  while ($seek>=0)
			  {
               $result = ibase_fetch_assoc($this->queryID);
				if (!$result){ $seek=-1;} else { $seek--;}
			  }
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
    public function getAll($sql = null,$resultType=null) {
        if (!empty($sql)) $this->_query($sql);
        if ( !$this->queryID ) {
            throw_exception($this->error());
            return false;
        }
        //返回数据集
        $result = array();
		$rowCount	 =	 0;
		if(is_null($resultType)){ $resultType   =  $this->resultType ; }
		$fun = ($resultType== DATA_TYPE_OBJ) ?	'ibase_fetch_object' : 'ibase_fetch_assoc';
		while ( $row = $fun($this->queryID))
		{ 
			$result[]	=	$row;
			$rowCount = $rowCount + 1;
		}
		$this->numRows	=	$rowCount;
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
    public function getFields($tableName) { 
        $this->_query('SELECT RDB$FIELD_NAME AS FIELD, RDB$DEFAULT_VALUE AS DEFAULT1, RDB$NULL_FLAG AS NULL1 FROM RDB$RELATION_FIELDS WHERE RDB$RELATION_NAME=UPPER(\''.$tableName.'\') ORDER By RDB$FIELD_POSITION');
        $result =   $this->getAll();
        $info   =   array();
        foreach ($result as $key => $val) {
			if(is_object($val)) {
				$val	=	get_object_vars($val);
			}
            $info[trim($val['FIELD'])] = array(
                'name'    => trim($val['FIELD']),
                'type'    => '',
                'notnull' => (bool) ($val['NULL1'] ==1), // 1表示不为Null
                'default' => $val['DEFAULT1'],
                'primary' => false,
                'autoInc' => false,
            );
       }
	  //剑雷 取表字段类型 
     $sql='select first 1 * from '. $tableName;
     $rs_temp = ibase_query ($this->_linkID, $sql);
     $fieldCount = ibase_num_fields($rs_temp);
	 
	 for ($i = 0; $i < $fieldCount; $i++)
	 {
	   $col_info = ibase_field_info($rs_temp, $i);
	   $info[trim($col_info['name'])]['type']=$col_info['type'];
     }
     ibase_free_result ($rs_temp);
	 
	 //剑雷 取表的主键
	 $sql='select b.rdb$field_name as FIELD_NAME from rdb$relation_constraints a join rdb$index_segments b
on a.rdb$index_name=b.rdb$index_name
where a.rdb$constraint_type=\'PRIMARY KEY\' and a.rdb$relation_name=UPPER(\''.$tableName.'\')';
     $rs_temp = ibase_query ($this->_linkID, $sql);
     while ($row=ibase_fetch_object($rs_temp)) {
	  $info[trim($row->FIELD_NAME)]['primary']=True;
	 }    
     ibase_free_result ($rs_temp);
	 	
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
    public function getTables($dbName='') { 
        $sql='SELECT DISTINCT RDB$RELATION_NAME FROM RDB$RELATION_FIELDS WHERE RDB$SYSTEM_FLAG=0';
		$this->_query($sql);
        $result =   $this->getAll();
        $info   =   array();
        foreach ($result as $key => $val) {
            $info[$key] = trim(current($val));
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
    public function close() { 
        if (!empty($this->queryID))
            ibase_free_result($this->queryID);
        if (!ibase_close($this->_linkID)){
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
    public function error() {
        $this->error = ibase_errmsg();
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
     * @param string $str  SQL字符串
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    public function escape_string($str) { 
        return addslashes($str); 
    } 

}//类定义结束
?>