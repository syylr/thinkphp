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

import("FCS.Db.ResultSet");
import("FCS.Util.SharedMemory");

/**
 +------------------------------------------------------------------------------
 * 数据库公共类
 +------------------------------------------------------------------------------
 * @package   Db
 * @author    liu21st <liu21st@gmail.com>
 * @version   0.8.0
 +------------------------------------------------------------------------------
 */
class Db extends Base
{

    /**
     +----------------------------------------------------------
     * 数据库用户名
     +----------------------------------------------------------
     * @var sting
     * @access protected
     +----------------------------------------------------------
     */
    var $username    = NULL;

    /**
     +----------------------------------------------------------
     * 数据库密码
     +----------------------------------------------------------
     * @var sting
     * @access protected
     +----------------------------------------------------------
     */
    var $password    = NULL;

    /**
     +----------------------------------------------------------
     * 数据库名
     +----------------------------------------------------------
     * @var sting
     * @access protected
     +----------------------------------------------------------
     */
    var $database    = NULL;

    /**
     +----------------------------------------------------------
     * 数据库服务器地址
     +----------------------------------------------------------
     * @var sting
     * @access protected
     +----------------------------------------------------------
     */
    var $hostname    = NULL;

    /**
     +----------------------------------------------------------
     * 主机端口
     +----------------------------------------------------------
     * @var sting
     * @access protected
     +----------------------------------------------------------
     */
    var $hostport    = NULL;

    /**
     +----------------------------------------------------------
     * 数据库类型
     +----------------------------------------------------------
     * @var sting
     * @access protected
     +----------------------------------------------------------
     */
    var $dbType        = NULL;

    /**
     +----------------------------------------------------------
     * 数据库版本
     +----------------------------------------------------------
     * @var sting
     * @access protected
     +----------------------------------------------------------
     */
    var $dbVersion    = NULL;

    /**
     +----------------------------------------------------------
     * 是否自动释放查询结果
     +----------------------------------------------------------
     * @var sting
     * @access protected
     +----------------------------------------------------------
     */
    var $autoFree    = False;

    /**
     +----------------------------------------------------------
     * 是否自动提交查询
     +----------------------------------------------------------
     * @var sting
     * @access protected
     +----------------------------------------------------------
     */
    var $autoCommit  = True;

    /**
     +----------------------------------------------------------
     * 是否显示调试信息
     +----------------------------------------------------------
     * @var sting
     * @access protected
     +----------------------------------------------------------
     */
    var $debug       = false;

    /**
     +----------------------------------------------------------
     * 是否使用永久连接
     +----------------------------------------------------------
     * @var sting
     * @access protected
     +----------------------------------------------------------
     */
    var $pconnect     = True;

    /**
     +----------------------------------------------------------
     * 是否启用查询缓存，暂时没有用
     +----------------------------------------------------------
     * @var sting
     * @access protected
     +----------------------------------------------------------
     */
    var $rsBuffer     = true;

    /**
     +----------------------------------------------------------
     * 当前SQL指令
     +----------------------------------------------------------
     * @var sting
     * @access protected
     +----------------------------------------------------------
     */
    var $queryStr = '';

    /**
     +----------------------------------------------------------
     * 当前结果
     +----------------------------------------------------------
     * @var sting
     * @access protected
     +----------------------------------------------------------
     */
    var $result = NULL;

    /**
     +----------------------------------------------------------
     * 当前查询的结果数据集
     +----------------------------------------------------------
     * @var resultSet
     * @access protected
     +----------------------------------------------------------
     */
    var $resultSet = NULL;

    /**
     +----------------------------------------------------------
     * 数据集返回类型 0 返回数组 1 返回对象
     +----------------------------------------------------------
     * @var integer
     * @access protected
     +----------------------------------------------------------
     */
    var $resultType = 0;

    /**
     +----------------------------------------------------------
     * 当前查询返回的字段集
     +----------------------------------------------------------
     * @var array
     * @access protected
     +----------------------------------------------------------
     */
    var $fields = NULL;

    /**
     +----------------------------------------------------------
     * 最后插入ID
     +----------------------------------------------------------
     * @var resultSet
     * @access protected
     +----------------------------------------------------------
     */
    var $lastInsID = NULL;

    /**
     +----------------------------------------------------------
     * 返回或者影响记录数
     +----------------------------------------------------------
     * @var integer
     * @access protected
     +----------------------------------------------------------
     */
    var $numRows = 0;

    /**
     +----------------------------------------------------------
     * 返回字段数
     +----------------------------------------------------------
     * @var integer
     * @access protected
     +----------------------------------------------------------
     */
    var $numCols = 0;

    /**
     +----------------------------------------------------------
     * 查询次数
     +----------------------------------------------------------
     * @var resultSet
     * @access protected
     +----------------------------------------------------------
     */
    var $queryTimes = 0;

    /**
     +----------------------------------------------------------
     * 写入次数
     +----------------------------------------------------------
     * @var integer
     * @access protected
     +----------------------------------------------------------
     */
    var $writeTimes = 0;

    /**
     +----------------------------------------------------------
     * 事务指令数
     +----------------------------------------------------------
     * @var integer
     * @access protected
     +----------------------------------------------------------
     */
    var $transTimes = 0;

    /**
     +----------------------------------------------------------
     * 错误信息
     +----------------------------------------------------------
     * @var string
     * @access protected
     +----------------------------------------------------------
     */
    var $error = '';

    /**
     +----------------------------------------------------------
     * 数据库连接ID
     +----------------------------------------------------------
     * @var resultSet
     * @access protected
     +----------------------------------------------------------
     */
    var $linkID  = 0;

    /**
     +----------------------------------------------------------
     * 当前查询ID
     +----------------------------------------------------------
     * @var resultSet
     * @access protected
     +----------------------------------------------------------
     */
    var $queryID = 0;

    /**
     +----------------------------------------------------------
     * 当前查询ID
     +----------------------------------------------------------
     * @var resultSet
     * @access protected
     +----------------------------------------------------------
     */
    var $comparison = array('eq'=>'=','neq'=>'!=','gt'=>'>','egt'=>'>=','lt'=>'<','elt'=>'<=','like'=>'like');

    /**
     +----------------------------------------------------------
     * 架构函数
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     */
    function __construct()
    {
    }

    /**
     +----------------------------------------------------------
     * 取得数据库类实例
     * 
     +----------------------------------------------------------
     * @static
     * @access public 
     +----------------------------------------------------------
     * @return mixed 返回数据库驱动类
     +----------------------------------------------------------
     */
    function getInstance() 
    {
        return get_instance_of(__CLASS__,'connect');
    }

    /**
     +----------------------------------------------------------
     * 加载数据库 支持配置文件或者 DSN
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param mixed $db_config 数据库配置信息
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function &connect($db_config='')
    {
        if ( is_string($db_config) && !empty($db_config) ) {
            $db_config = DB::parseDSN($db_config);
        }else if(empty($db_config)){
            $db_config = array (
            'dbms'     => DB_TYPE, 
            'username' => DB_USER, 
            'password' => DB_PWD, 
            'hostname' => DB_HOST, 
            'hostport' => DB_PORT, 
            'database' => DB_NAME
            );
        }
        $this->dbType = ucwords(strtolower($db_config['dbms']));
        $dbClass = 'Db_'. $this->dbType;
        $dbDriverPath = dirname(__FILE__).'/Driver/';
        if(file_exists($dbDriverPath.$dbClass . '.class.php')){
            if (include_once $dbDriverPath . $dbClass . '.class.php')    
                $db = & new $dbClass($db_config);
            if(!$db->connect()){
                throw_exception('无法加载数据库: ' . $db_config['dbms']);
            }
        }else throw_exception('系统暂时不支持数据库: ' . $db_config['dbms']);

        return $db;
    }

    /**
     +----------------------------------------------------------
     * DSN解析
     * 格式： mysql://username:passwd@localhost:3306/DbName
     +----------------------------------------------------------
     * @static
     * @access public 
     +----------------------------------------------------------
     * @param string $dsnStr  
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function parseDSN($dsnStr) 
    {
        if( empty($dsnStr) ){return false;}
        $info = parse_url($dsnStr);
        if($info['scheme']){
            $dsn = array(
            'dbms'     => $info['scheme'], 
            'username' => $info['user'] ? $info['user'] : '', 
            'password' => $info['pass'] ? $info['pass'] : '', 
            'hostname' => $info['host'] ? $info['host'] : '', 
            'hostport' => $info['port'] ? $info['port'] : '', 
            'database' => $info['path'] ? substr($info['path'],1) : ''
            );
        }else {
            preg_match('/^(.*?)\:\/\/(.*?)\:(.*?)\@(.*?)\:([0-9]{1, 6})\/(.*?)$/',trim($dsnStr),$matches);
            $dsn = array (
            'dbms'     => $matches[1], 
            'username' => $matches[2], 
            'password' => $matches[3], 
            'hostname' => $matches[4], 
            'hostport' => $matches[5], 
            'database' => $matches[6]
            );
        }
        return $dsn;
     }


    /**
     +----------------------------------------------------------
     * table分析
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param mixed $tables  数据表名
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function parseTables($tables)
    {
        if(is_array($tables)) $tablesStr = implode(',', $tables);
        else if(is_string($tables)) $tablesStr = $tables;
        return empty($tablesStr)?'':$tablesStr;
    }


    /**
     +----------------------------------------------------------
     * where分析
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param mixed $where 查询条件
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function parseWhere($where)
    {
        $whereStr = '';
        if(is_object($where)){
            if(is_instance_of($where,'Vo')){
                //如果是Vo对象则转换为Map对象
                $where = $where->toMap();
            }
            if(is_instance_of($where,'HashMap')){
                $it = $where->getIterator();
                foreach ($it as $key=>$val){
                    $whereStr .= "`$key` ";
                    if(is_array($val)) {
                        if(preg_match('/(EQ|NEQ|GT|EGT|LT|ELT|LIKE)/i',$val[0])) {
                            $whereStr .= $this->comparison[strtolower($val[0])].' '.$this->fieldFormat($val[1]);
                        }else {
                        	$whereStr .= '>='.$this->fieldFormat($val[0]).' AND '.$key.' <='.$this->fieldFormat($val[1]);
                        }
                        
                    }else {
                        //对字符串类型字段采用模糊匹配
                        if(preg_match('/(\w*)(title|name|content|value|remark|company|address)(\w*)/i',$key)) {
                            $val = '%'.$val.'%';
                            $whereStr .= "like ".$this->fieldFormat($val);
                        }
                        else {
                            $whereStr .= "= ".$this->fieldFormat($val);
                        }                    	
                    }
                    $whereStr .= " AND ";
                }
                $whereStr = substr($whereStr,0,-4);
            }else{
                throw_exception('非法数据对象！');
            }
        }
        if(is_array($where)){
            //支持数组作为条件
            foreach ($where as $key=>$val){
                    $whereStr .= "`$key` ";
                    if(is_array($val)) {
                        if(preg_match('/(EQ|NEQ|GT|EGT|LT|ELT|LIKE)/i',$val[0])) {
                            $whereStr .= $this->comparison[strtolower($val[0])].' '.$this->fieldFormat($val[1]);
                        }else {
                        	$whereStr .= '>='.$this->fieldFormat($val[0]).' AND '.$key.' <='.$this->fieldFormat($val[1]);
                        }                        
                    }else {
                        if(preg_match('/(\w*)(title|name|content|value|remark|company|address)(\w*)/i',$key)) {
                            $val = '%'.$val.'%';
                            $whereStr .= "like ".$this->fieldFormat($val);
                        }
                        else {
                            $whereStr .= "= ".$this->fieldFormat($val);
                        }                    	
                    }

                    $whereStr .= " AND ";
            }
            $whereStr = substr($whereStr,0,-4);
        }else if(is_string($where)) { 
            //支持String作为条件 如使用 > like 等
            $whereStr = $where; 
        }

        return empty($whereStr)?'':' WHERE '.$whereStr;
    }


    /**
     +----------------------------------------------------------
     * order分析
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param mixed $order 排序
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function parseOrder($order)
    {
        $orderStr = '';
        if(is_array($order))     
            $orderStr .= ' ORDER BY '.implode(',', $order);
        else if(is_string($order) && !empty($order)) 
            $orderStr .= ' ORDER BY '.$order;
        return empty($orderStr)?'':$orderStr;
    }

    /**
     +----------------------------------------------------------
     * limit分析
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $limit  
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function parseLimit($limit)
    {
        if(!empty($limit))     
            $limitStr .= ' LIMIT '.$limit;
        return empty($limitStr)?'':$limitStr;
    }

    /**
     +----------------------------------------------------------
     * group分析
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param mixed $group  
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function parseGroup($group)
    {
        if(is_array($group))     
            $groupStr .= ' GROUP BY '.implode(',', $group);
        else if(is_string($group) && !empty($group)) 
            $groupStr .= ' GROUP BY '.$group;
        return empty($groupStr)?'':$groupStr;
    }

    /**
     +----------------------------------------------------------
     * having分析
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $having  
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function parseHaving($having)
    {
        if(is_string($having) && !empty($having))     
            $havingStr .= ' HAVING '.$having;
        return empty($having)?'':$havingStr;
    }

    /**
     +----------------------------------------------------------
     * fields分析
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param mixed $fields
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function parseFields($fields)
    {
        if(is_array($fields)) 
            $fieldsStr = implode(',', $fields);
        else if(is_string($fields) && !empty($fields)) 
            $fieldsStr = $fields;
        else 
            $fieldsStr = '*';
        return $fieldsStr;
    }

    /**
     +----------------------------------------------------------
     * value分析
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param mixed $values
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function parseValues($values)
    {
        if(is_array($values)) {
            array_walk($values, array($this, 'fieldFormat'));
            $valuesStr = implode(',', $values);
        }
        else if(is_string($values)) 
            $valuesStr = $values;
        return $valuesStr;
    }

    /**
     +----------------------------------------------------------
     * set分析
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param mixed $sets
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function parseSets($sets)
    {
        if(is_object($sets) && !empty($sets)){
            if(is_instance_of($sets,'Vo')){
                //如果是Vo对象则转换为Map对象
                $sets = $sets->toMap();
            }
            if(is_instance_of($sets,'HashMap')){
                $sets = $sets->toArray();
            }
        }
        if(is_array($sets)){
            foreach ($sets as $key=>$val){
                if(!is_null($val)){//过滤空值元素
                    $setsStr .= "`$key` = ".$this->fieldFormat($val).",";
                }
            }
            $setsStr = substr($setsStr,0,-1);
        }else if(is_string($sets)) { 
            $setsStr = $sets; 
        }
        return $setsStr;
    }

    /**
     +----------------------------------------------------------
     * 字段格式化
     * 
     +----------------------------------------------------------
     * @static
     * @access public 
     +----------------------------------------------------------
     * @param mixed $value  
     +----------------------------------------------------------
     * @return mixed
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function fieldFormat(&$value) 
    {
        if(is_int($value)) {
            $value = intval($value);
        } else if(is_float($value)) {
            $value = floatval($value);
        } else if(defined($value) && $value === null) {
            $value = strval(constant($value));
        } else if(is_string($value)) {
            $value = '\''.addslashes($value).'\'';
        }
        return $value;
    }

    /**
     +----------------------------------------------------------
     * 是否为数据库查询操作
     * 
     +----------------------------------------------------------
     * @static
     * @access public 
     +----------------------------------------------------------
     * @param string $query  SQL指令
     +----------------------------------------------------------
     * @return boolen 如果是操作操作返回true
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function isMainIps($query)
    {
        $queryIps = 'INSERT|UPDATE|DELETE|REPLACE|'
                . 'CREATE|DROP|'
                . 'LOAD DATA|SELECT .* INTO|COPY|'
                . 'ALTER|GRANT|REVOKE|'
                . 'LOCK|UNLOCK';
        if (preg_match('/^\s*"?(' . $queryIps . ')\s+/i', $query)) {
            return true;
        }
        return false;
    }

    /**
     +----------------------------------------------------------
     * 获得一条查询记录的某个字段数据
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $field 字段名
     * @param string $str  SQL指令
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function getOne($field,$sql)
    {
        $this->_query($sql);
        if($this->next()) {
            $result =   $this->result;
            if($this->resultType==1) {
                return   $result->$field;
            }else {
                return   $result[$field];
            }
        }else {
            return null;
        }
    }


    /**
     +----------------------------------------------------------
     * 查询数据方法，支持动态缓存
     * 动态缓存方式为可配置，默认为文件方式
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $sql  查询语句
     * @param string $cache  是否缓存查询
     +----------------------------------------------------------
     * @return mixed
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function query($sql='',$cache=true)
    {
        if(!empty($sql)) {
            $this->queryStr = $sql;
        }
        if($cache) {
            $guid   =   md5($this->queryStr);
            //实例化共享内存对象
            if(DATA_CACHE_ON){//启用数据动态缓存
                //取得共享内存实例
                $sm = SharedMemory::getInstance();
                //获取共享内存数据
                if(!$sm->get($guid.'_count')) {
                    //单文件数据缓存
                    $data = $sm->get($guid);
                }else {
                    //多文件数据缓存
                    $length = $sm->get($guid.'_count');
                    if(!empty($length)) {
                        $data =   new ResultSet();
                        for($i=0; $i<$length; $i++) {
                            $array   =   $sm->get($guid.'_'.$i);
                            foreach($array as $key=>$val) {
                                $data->add($val);
                            }
                        }                
                        
                    }
                }
            }
            if(empty($data)){
                //如果共享内存无效或者没有启用则重新查询
                $data = $this->_query();
                //如果启用数据缓存则重新缓存
                if(DATA_CACHE_ON){
                    $rowNums    =   $this->numRows;  //总的记录数
                    if($rowNums > DATA_CACHE_MAX) {
                        //如果记录数超过设置范围，多文件缓存，
                        //避免serialize超时
                        $length =   ceil($rowNums / DATA_CACHE_MAX);   //缓存文件数
                        for($i=0; $i<$length; $i++) {
                            //依次缓存
                            $sm->set($guid.'_'.$i,$data->range(DATA_CACHE_MAX * $i,DATA_CACHE_MAX));
                        }
                        //记录缓存文件数目
                        $sm->set($guid.'_count',$length);                        
                    }else {
                        //全部数据缓存
                        $sm->set($guid,$data);                    	
                    }
                }
            }
        }else {
            $data = $this->_query();
        }

        return $data;
    }


    /**
     +----------------------------------------------------------
     * 数据库操作方法
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $sql  执行语句
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function execute($sql)
    {
        return $this->_execute($sql);
    }


    /**
     +----------------------------------------------------------
     * 查找记录 
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param mixed $where 数据
     * @param string $tables  数据表名
     * @param string $fields  字段名
     * @param string $order  排序
     * @param string $limit  
     * @param string $group
     * @param string $having
     * @param boolean $cache 是否缓存
     +----------------------------------------------------------
     * @return resultSet
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function find($where,$tables,$fields='*',$order=NULL,$limit=NULL,$group=NULL,$having=NULL,$cache=true)
    {

        $this->queryStr = 'SELECT '.$this->parseFields($fields)
                        .' FROM '.$tables
                        .$this->parseWhere($where)
                        .$this->parseGroup($group)
                        .$this->parseHaving($having)
                        .$this->parseOrder($order)
                        .$this->parseLimit($limit);
        return $this->query('',$cache);
    }

    /**
     +----------------------------------------------------------
     * 查找记录 
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param mixed $where 数据
     * @param string $tables  数据表名
     * @param string $fields  字段名
     * @param boolean $cache 是否缓存
     +----------------------------------------------------------
     * @return resultSet
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function count($where,$tables,$fields='count(id) as count')
    {

        $this->queryStr = 'SELECT '.$fields 
                        .' FROM '.$tables
                        .$this->parseWhere($where);

        return $this->getOne($this->queryStr);
    }

    /**
     +----------------------------------------------------------
     * 优化数据表 
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param mixed $where 数据
     * @param string $tables  数据表名
     * @param string $fields  字段名
     * @param boolean $cache 是否缓存
     +----------------------------------------------------------
     * @return resultSet
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function optimize()
    {
        $tables = $this->getTables();
        $tablesNum = count($tables);
        for($i=0; $i < $tablesNum; $i++)
        {
            $this->update("Optimize Table " . $tables[$i]);
            echo "Optimeze Table " . $tables[$i] . "\n";
        }
    }

    /**
     +----------------------------------------------------------
     * 插入记录
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param mixed $map 数据
     * @param string $table  数据表名
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function add($map,$table)
    {
        if(!is_instance_of($map,'HashMap')){
            throw_exception('新增记录格式非法');
        }
        $map = $map->toArray();

        //如果某个字段的值为非字符串的NULL，则过滤该字段和值
        /*
        foreach ($map as $key=>$val){
            if(is_null($val)){
                unset($map[$key]);
            }
        }*/
        $fields = array_keys($map);
        $values = array_Values($map);
        $fieldsStr = implode(',', array_keys($map));
        array_walk($values, array($this, 'fieldFormat'));

        $valuesStr = implode(',', $values);
        $this->queryStr =    'INSERT INTO '.$table.' ('.$fieldsStr.') VALUES ('.$valuesStr.')';
        return $this->execute();
    }


    /**
     +----------------------------------------------------------
     * 删除记录
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param mixed $where 为条件Map、Array或者String
     * @param string $table  数据表名
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function remove($where,$table,$limit='',$order='')
    {
        $this->queryStr = 'DELETE FROM '.$table.$this->parseWhere($where).$this->parseOrder($order).$this->parseLimit($limit);;
        return $this->execute();
    }


    /**
     +----------------------------------------------------------
     * 更新记录 只支持Map对象保存
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param mixed $name 数据
     * @param string $value  数据表名
     * @param string $where  更新条件
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function save($sets,$table,$where,$limit=0,$order='')
    {
        if(!is_instance_of($sets,'HashMap')){
            throw_exception('新增记录格式非法');
        }
        $this->queryStr = 'UPDATE '.$table.' SET '.$this->parseSets($sets).$this->parseWhere($where).$this->parseOrder($order).$this->parseLimit($limit);

        return $this->execute();
    }

    /**
     +----------------------------------------------------------
     * 查询数据集返回 Array Iterator
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    function getArrayIterator() 
    {
        return new resultSet($this->getAll(0));
    }


    /**
     +----------------------------------------------------------
     * 查询数据集返回 Object Iterator
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    function getObjectIterator() 
    {
        return new resultSet($this->getAll(1));
    }

    // +----------------------------------------
    // |    get set 方法
    // +----------------------------------------
    function getAutoFree() {return $this->autoFree;}
    function getAutoCommit() {return $this->autoCommit;}
    function getPconnect() {return $this->pconnect;}
    function getDebug() {return $this->debug;}

    //只读属性获取
    function getDbType() {return $this->dbType;}
    function getDbVersion() {return $this->dbVersion;}
    function getResult()  {return $this->result;}
    function getFields()  {return $this->fields;}
    function getLastInsID()  {return $this->lastInsID;}
    function getNumCols() {return $this->numCols;}
    function getNumRows() {return $this->numRows;}
    function getQueryTimes() { return $this->queryTimes;  }
    function getWriteTimes() { return $this->writeTimes;  }

    function setAutoFree($autofree) {$this->autoFree = $autofree;}
    function setAutoCommit($autocommit) {$this->autoCommit = $autocommit;}
    function setPconnect($pconnect) {$this->pconnect = $pconnect;}
    function setDebug($debug) {$this->debug = $debug;}


}//类定义结束
?>