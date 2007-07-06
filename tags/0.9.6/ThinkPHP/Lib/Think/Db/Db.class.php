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
// $Id: Db.class.php 33 2007-02-25 07:06:02Z liu21st $

import("Think.Db.ResultSet");
import("Think.Util.Cache");

/**
 +------------------------------------------------------------------------------
 * 数据库公共类
 +------------------------------------------------------------------------------
 * @package   Db
 * @author    liu21st <liu21st@gmail.com>
 * @version   $Id: Db.class.php 33 2007-02-25 07:06:02Z liu21st $
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
    var $resultType = 1;

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
        $args = func_get_args();
        return get_instance_of(__CLASS__,'connect',$args);
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
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function &connect($db_config='')
    {
        // 读取数据库配置
        if ( is_string($db_config) && !empty($db_config) ) {
            // 如果DSN字符串则进行解析
            $db_config = DB::parseDSN($db_config);
        }else if(empty($db_config)){
            // 如果配置为空，读取配置文件设置
            $db_config = array (
                'dbms'     => DB_TYPE, 
                'username' => DB_USER, 
                'password' => DB_PWD, 
                'hostname' => DB_HOST, 
                'hostport' => DB_PORT, 
                'database' => DB_NAME
            );
        }
        // 数据库类型
        $this->dbType = ucwords(strtolower($db_config['dbms']));
        if(Session::is_set(strtoupper($this->dbType))) {
            // 已经定义该类型的数据库驱动
        	$dbClass   =  Session::get(strtoupper($this->dbType));
        }else {
            // 读取系统数据库驱动目录
            $dbClass = 'Db_'. $this->dbType;
            $dbDriverPath = dirname(__FILE__).'/Driver/';      
            require_cache( $dbDriverPath . $dbClass . '.class.php');     	
        }
        // 检查驱动类
        if(class_exists($dbClass)) {
            // 存在数据库驱动类
            // 尝试进行数据库连接
            $db = & new $dbClass($db_config);
            $db->dbType = $this->dbType;
            if(!$db->connect()){
                // 连接失败
                throw_exception(_NOT_LOAD_DB_.': ' . $db_config['dbms']);
            }
        }else {
            // 类没有定义
            throw_exception(_NOT_SUPPORT_DB_.': ' . $db_config['dbms']);
        }

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
     * @throws ThinkExecption
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
     * @throws ThinkExecption
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
     * @throws ThinkExecption
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
                        	$whereStr .= '>='.$this->fieldFormat($val[0]).' AND `'.$key.'` <='.$this->fieldFormat($val[1]);
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
                throw_exception(_DATA_TYPE_INVALID_);
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
                        	$whereStr .= '>='.$this->fieldFormat($val[0]).' AND `'.$key.'` <='.$this->fieldFormat($val[1]);
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
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function parseOrder($order)
    {
        $orderStr = '';
        if(is_array($order))     
            $orderStr .= ' ORDER BY '.implode(',', $order);
        else if(is_string($order) && !empty($order)) 
            $orderStr .= ' ORDER BY '.$order;
        return $orderStr;
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
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function parseLimit($limit)
    {
        $limitStr    = '';
        if(!empty($limit))     
            $limitStr .= ' LIMIT '.$limit;
        return $limitStr;
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
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function parseGroup($group)
    {
        $groupStr = '';
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
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function parseHaving($having)
    {
        $havingStr = '';
        if(is_string($having) && !empty($having))     
            $havingStr .= ' HAVING '.$having;
        return $havingStr;
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
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function parseFields($fields)
    {
        if(is_array($fields)) {
            array_walk($fields, array($this, 'addSpecialChar'));
            $fieldsStr = implode(',', $fields);
        }else if(is_string($fields) && !empty($fields)) {
            if( false === strpos($fields,'`') ) {
                $fields = explode(',',$fields);
            	array_walk($fields, array($this, 'addSpecialChar'));
                $fieldsStr = implode(',', $fields);
            }else {
            	$fieldsStr = $fields;
            }
        }else 
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
     * @throws ThinkExecption
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
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function parseSets($sets)
    {
        $setsStr  = '';
        if(is_object($sets) && !empty($sets)){
            if(is_instance_of($sets,'Vo')){
                //如果是Vo对象则转换为Map对象
                $sets = $sets->toMap();
            }
            if(is_instance_of($sets,'HashMap')){
                $sets = $sets->toArray();
            }
        }
        $sets    = auto_charset($sets,OUTPUT_CHARSET,DB_CHARSET);
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
     * @throws ThinkExecption
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
     * 字段和表名添加` 符合
     * 保证指令中使用关键字不出错
     +----------------------------------------------------------
     * @static
     * @access public 
     +----------------------------------------------------------
     * @param mixed $value  
     +----------------------------------------------------------
     * @return mixed
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function addSpecialChar(&$value) 
    {
        if( '*' == $value ||  false !== strpos($value,'(') || false !== strpos($value,'.')) {
            //如果包含* 或者 使用了sql方法 则不作处理
        }
        elseif(false === strpos($value,'`') ) {
            $value = '`'.$value.'`';
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
     * @throws ThinkExecption
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
     * @throws ThinkExecption
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
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function query($sql='',$cache=true)
    {
        if(!empty($sql)) {
            $this->queryStr = $sql;
        }
        if(DB_CACHE_ON && $cache) {// 启用数据库缓存
            $guid   =   md5($this->queryStr);
            //取得缓存实例
            $cache = Cache::getInstance();
            //获取缓存数据
            if(!$cache->get($guid.'_count')) {
                //单文件数据缓存
                $data = $cache->get($guid);
            }else {
                //多文件数据缓存
                $length = $cache->get($guid.'_count');
                if(!empty($length)) {
                    $data =   new ResultSet();
                    for($i=0; $i<$length; $i++) {
                        $array   =   $cache->get($guid.'_'.$i);
                        foreach($array as $key=>$val) {
                            $data->add($val);
                        }
                    }                
                    
                }
            } 
            if(!empty($data)){
                return $data;
            }
        }
        // 进行查询
        $data = $this->_query();
        if(DB_CACHE_ON){
            //如果启用数据库缓存则重新缓存
            $rowNums    =   $this->numRows;  //总的记录数
            if($rowNums > DB_CACHE_MAX) {
                //如果记录数超过设置范围，多文件缓存，
                //避免serialize超时
                $length =   ceil($rowNums / DB_CACHE_MAX);   //缓存文件数
                for($i=0; $i<$length; $i++) {
                    //依次缓存
                    $cache->set($guid.'_'.$i,$data->range(DB_CACHE_MAX * $i,DB_CACHE_MAX));
                }
                //记录缓存文件数目
                $cache->set($guid.'_count',$length);                        
            }else {
                //全部数据缓存
                $cache->set($guid,$data);                    	
            }
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
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function execute($sql='')
    {
        if(empty($sql)) {
        	$sql  = $this->queryStr;
        }
        return $this->_execute($sql);
    }

    function autoExec($sql='') 
    {
        if(empty($sql)) {
        	$sql  = $this->queryStr;
        }
        if($this->isMainIps($this->queryStr)) {
        	$this->execute($sql);
        }else {
        	$this->query($sql);
        }
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
     * @throws ThinkExecption
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
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function count($where,$tables,$fields='count(id) as count')
    {

        $this->queryStr = 'SELECT '.$fields 
                        .' FROM '.$tables
                        .$this->parseWhere($where);

        return $this->getOne('count',$this->queryStr);
    }

    /**
     +----------------------------------------------------------
     * 优化数据表 
     * 默认优化数据库中的全部表
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $tableName 数据表名
     +----------------------------------------------------------
     * @return resultSet
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function optimize($tableName)
    {
        if(empty($tableName)) {
        	$tables = $this->getTables();
            $tableName   = implode(',',$tables);
        }
        $this->execute("Optimize Table " . $tableName);
        return ;
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
     * @return false | integer
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function add($map,$table)
    {
        if(!is_array($map)) {
            if(!is_instance_of($map,'HashMap')){
                throw_exception(_DATA_TYPE_INVALID_);
            }
            $map    = $map->toArray();        	
        }
        //转换数据库编码
        $map    = auto_charset($map,OUTPUT_CHARSET,DB_CHARSET);
        //如果某个字段的值为非字符串的NULL，则过滤该字段和值
        /*
        foreach ($map as $key=>$val){
            if(is_null($val)){
                unset($map[$key]);
            }
        }*/
        $fields = array_keys($map);
        array_walk($fields, array($this, 'addSpecialChar'));
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
     * @param string $limit  
     * @param string $order
     +----------------------------------------------------------
     * @return false | integer
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function remove($where,$table,$limit='',$order='')
    {
        $this->queryStr = 'DELETE FROM '.$table.$this->parseWhere($where).$this->parseOrder($order).$this->parseLimit($limit);;
        return $this->execute();
    }


    /**
     +----------------------------------------------------------
     * 清空表
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $table  数据表名
     +----------------------------------------------------------
     * @return false | integer
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function clear($table)
    {
        if(is_string($table)) {
            return $this->execute( 'TRUNCATE TABLE '.$table);        	
        }else {
        	return false;
        }
    }

    /**
     +----------------------------------------------------------
     * 更新记录 只支持Map对象保存
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param mixed $sets 数据
     * @param string $table  数据表名
     * @param string $where  更新条件
     * @param string $limit  
     * @param string $order
     +----------------------------------------------------------
     * @return false | integer
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function save($sets,$table,$where,$limit=0,$order='')
    {
        if(!is_instance_of($sets,'HashMap')){
            throw_exception(_DATA_TYPE_INVALID_);
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