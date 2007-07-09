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
// $Id: Db.class.php 53 2007-03-17 15:15:42Z liu21st $

import("Think.Db.ResultSet");
import("Think.Util.Cache");

/**
 +------------------------------------------------------------------------------
 * 数据库公共类
 +------------------------------------------------------------------------------
 * @package   Db
 * @author    liu21st <liu21st@gmail.com>
 * @version   $Id: Db.class.php 53 2007-03-17 15:15:42Z liu21st $
 +------------------------------------------------------------------------------
 */
class Db extends Base
{

	// 数据库类型
    var $dbType        = NULL;

    // 数据库版本
    var $dbVersion    = NULL;

    // 是否自动释放查询结果
    var $autoFree    = False;

    // 是否自动提交查询
    var $autoCommit  = True;

    // 是否显示调试信息 如果启用会在日志文件记录sql语句
    var $debug       = false;

    // 是否使用永久连接
    var $pconnect     = false;

    // 是否启用查询缓存，暂时没有用
    var $rsBuffer     = true;

    // 当前SQL指令
    var $queryStr = '';

    // 当前结果
    var $result = NULL;

    // 当前查询的结果数据集
    var $resultSet = NULL;

    // 数据集返回类型 0 返回数组 1 返回对象
    var $resultType = DATA_TYPE_ARRAY;

    // 当前查询返回的字段集
    var $fields = NULL;

    // 最后插入ID
    var $lastInsID = NULL;

    // 返回或者影响记录数
    var $numRows = 0;

    // 返回字段数
    var $numCols = 0;

    // 查询次数
    var $queryTimes = 0;

    // 写入次数
    var $writeTimes = 0;

    // 事务指令数
    var $transTimes = 0;

    // 错误信息
    var $error = '';

    // 数据库连接ID 支持多个连接
    var $linkID  = array();

	// 当前连接ID
	var $_linkID	=	null;

    // 当前查询ID
    var $queryID = null;

	// 是否已经连接数据库
	var $connected = false;

	// 数据库连接参数配置
	var $config = '';

	// 数据库表达式
    var $comparison = array('eq'=>'=','neq'=>'!=','gt'=>'>','egt'=>'>=','lt'=>'<','elt'=>'<=','like'=>'like');

    /**
     +----------------------------------------------------------
     * 取得数据库类实例
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
        return get_instance_of(__CLASS__,'factory',$args);
    }

    /**
     +----------------------------------------------------------
     * 加载数据库 支持配置文件或者 DSN
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
    function &factory($db_config='')
    {
        // 读取数据库配置
        if ( is_string($db_config) && !empty($db_config) ) {
            // 如果DSN字符串则进行解析
            $db_config = DB::parseDSN($db_config);
        }else if(empty($db_config)){
            // 如果配置为空，读取配置文件设置
            $db_config = array (
                'dbms'     => C('DB_TYPE'), 
                'username' => C('DB_USER'), 
                'password' => C('DB_PWD'), 
                'hostname' => C('DB_HOST'), 
                'hostport' => C('DB_PORT'), 
                'database' => C('DB_NAME'),
            );
        }
        // 数据库类型

			$this->dbType = ucwords(strtolower($db_config['dbms']));
			if(Session::is_set(strtoupper($this->dbType))) {
				// 已经定义该类型的数据库驱动
				$dbClass   =  Session::get(strtoupper($this->dbType));
			}else {
				// 读取系统数据库驱动目录
				$dbClass = 'Db'. $this->dbType;
				$dbDriverPath = dirname(__FILE__).'/Driver/';      
				require_cache( $dbDriverPath . $dbClass . '.class.php');     	
			}
			// 检查驱动类
			if(class_exists($dbClass)) {
				$db = & new $dbClass($db_config);
				$db->dbType = $this->dbType;
			}else {
				// 类没有定义
				throw_exception(L('_NOT_SUPPORT_DB_').': ' . $db_config['dbms']);
			}
			return $db;
    }

	// 判断是否连接
	function connected() {
		return $this->connected;
	}

	// 初始化连接
	function initConnect($master=true) {
		if(1 == C('DB_DEPLOY_TYPE')) {
			// 采用分布式数据库
			$this->_linkID = $this->multi_connect($master);
		}else{
			// 默认单数据库
			if ( !$this->connected ) $this->_linkID = $this->connect();
		}
	}

	// 连接分布式数据库服务器
	function multi_connect($master=false) {
		static $_config = array();
		if(empty($_config)) {
			// 缓存分布式数据库配置解析
			foreach ($this->config as $key=>$val){
				$_config[$key]	 	=	explode(',',$val);
			}
		}
		// 默认是连接第一个数据库配置 主服务器
		$r	=	0;
		if(!$master) {
			// 连接从服务器
			$r = floor(mt_rand(1,count($_config['hostname'])-1));	// 每次随机连接的数据库 不包括主服务器
		}
		$db_config = array(
			'username'=>	 isset($_config['username'][$r])?$_config['username'][$r]:$_config['username'][0],
			'password' => isset($_config['password'][$r])?$_config['password'][$r]:$_config['password'][0], 
			'hostname' => isset($_config['hostname'][$r])?$_config['hostname'][$r]:$_config['hostname'][0], 
			'hostport' =>	 isset($_config['hostport'][$r])?$_config['hostport'][$r]:$_config['hostport'][0], 
			'database' =>	 isset($_config['database'][$r])?$_config['database'][$r]:$_config['database'][0],
		);	
		if(strtoupper(C('DB_TYPE'))=='PDO') {
			$db_config['dsn']	=	isset($_config['pdodsn'][$r])?$_config['pdodsn'][$r]:$_config['pdodsn'][0];
			$db_config['parms']	=	isset($_config['pdoparms'][$r])?$_config['pdoparms'][$r]:$_config['pdoparms'][0];
		}
		return $this->connect($db_config,$r);
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
                    if(false !== strpos(strtoupper(DB_TYPE),'MYSQL')) {
                        $key = "`$key`";
                    }
                    $whereStr .= "$key ";
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
                throw_exception(_DATA_TYPE_INVALID_);
            }
        }
        if(is_array($where)){
            //支持数组作为条件
            foreach ($where as $key=>$val){
                    if(false !== strpos(strtoupper(C('DB_TYPE')),'MYSQL')) {
                        $key = "`$key`";
                    }
                    $whereStr .= "$key ";
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
            if(false !== strpos(strtoupper(C('DB_TYPE')),'MYSQL')) {
                array_walk($fields, array($this, 'addSpecialChar'));
            }
            $fieldsStr = implode(',', $fields);
        }else if(is_string($fields) && !empty($fields)) {
            if( false === strpos($fields,'`') ) {
                $fields = explode(',',$fields);
                if(false !== strpos(strtoupper(C('DB_TYPE')),'MYSQL')) {
            	    array_walk($fields, array($this, 'addSpecialChar'));
                }
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
        $sets    = auto_charset($sets,C('OUTPUT_CHARSET'),C('DB_CHARSET'));
        if(is_array($sets)){
            foreach ($sets as $key=>$val){
                if(!is_null($val)){//过滤空值元素
					if(false !== strpos(strtoupper(C('DB_TYPE')),'MYSQL')) {
	                    $setsStr .= "`$key` = ".$this->fieldFormat($val).",";
					}else {
	                    $setsStr .= "$key = ".$this->fieldFormat($val).",";
					}
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
        if(C('DB_CACHE_ON') && $cache) {// 启用数据库缓存
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
        if(C('DB_CACHE_ON')){
            //如果启用数据库缓存则重新缓存
            $rowNums    =   $this->numRows;  //总的记录数
            if($rowNums > C('DB_CACHE_MAX')) {
                //如果记录数超过设置范围，多文件缓存，
                //避免serialize超时
                $length =   ceil($rowNums / C('DB_CACHE_MAX'));   //缓存文件数
                for($i=0; $i<$length; $i++) {
                    //依次缓存
                    $cache->set($guid.'_'.$i,$data->range(C('DB_CACHE_MAX') * $i,C('DB_CACHE_MAX')));
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

	// 自动判断进行查询或者执行操作
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
     * 查找符合条件的记录数
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param mixed $where 数据
     * @param string $tables  数据表名
     * @param string $fields  字段名
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
                throw_exception(L('_DATA_TYPE_INVALID_'));
            }
            $map    = $map->toArray();        	
        }
        //转换数据库编码
        $map    = auto_charset($map,C('OUTPUT_CHARSET'),C('DB_CHARSET'));
        $fields = array_keys($map);
        if(false !== strpos(strtoupper(C('DB_TYPE')),'MYSQL')) {
        	array_walk($fields, array($this, 'addSpecialChar'));
        }
        $fieldsStr = implode(',', $fields);
        $values = array_Values($map);
        array_walk($values, array($this, 'fieldFormat'));

        $valuesStr = implode(',', $values);
        $this->queryStr =    'INSERT INTO '.$table.' ('.$fieldsStr.') VALUES ('.$valuesStr.')';
        return $this->execute();
    }


    /**
     +----------------------------------------------------------
     * 删除记录
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
            throw_exception(L('_DATA_TYPE_INVALID_'));
        }
        $this->queryStr = 'UPDATE '.$table.' SET '.$this->parseSets($sets).$this->parseWhere($where).$this->parseOrder($order).$this->parseLimit($limit);
        return $this->execute();
    }

    /**
     +----------------------------------------------------------
     * 查询数据集返回 Array Iterator
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

	// 查询次数更新或者查询
	function Q($times='') {
		static $_times = 0;
		if(empty($times)) {
			return $_times;
		}else{
			$_times++;
		}
	}

	// 写入次数更新或者查询
	function W($times='') {
		static $_times = 0;
		if(empty($times)) {
			return $_times;
		}else{
			$_times++;
		}
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