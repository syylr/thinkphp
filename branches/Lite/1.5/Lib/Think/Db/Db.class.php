<?php
// +----------------------------------------------------------------------
// | ThinkPHP Lite
// +----------------------------------------------------------------------
// | Copyright (c) 2008 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// $Id$

/**
 +------------------------------------------------------------------------------
 * ThinkPHP 数据库中间层实现类
 * 支持Mysql 可以使用PDO
 +------------------------------------------------------------------------------
 * @category   Think
 * @package  Think
 * @subpackage  Db
 * @author    liu21st <liu21st@gmail.com>
 * @version   $Id$
 +------------------------------------------------------------------------------
 */
class Db extends Base
{

    // 数据库类型
    protected $dbType           = null;

    // 是否自动释放查询结果
    protected $autoFree         = false;

    // 是否显示调试信息 如果启用会在日志文件记录sql语句
    protected $debug             = false;

    // 是否使用永久连接
    protected $pconnect         = false;

    // 当前SQL指令
    protected $queryStr          = '';

    // 当前查询的结果数据集
    protected $resultSet         = null;

    // 最后插入ID
    protected $lastInsID         = null;

    // 返回或者影响记录数
    protected $numRows        = 0;

    // 返回字段数
    protected $numCols          = 0;

    // 事务指令数
    protected $transTimes      = 0;

    // 错误信息
    protected $error              = '';

    // 数据库连接ID 支持多个连接
    protected $linkID              = array();

    // 当前连接ID
    protected $_linkID            =   null;

    // 当前查询ID
    protected $queryID          = null;

    // 是否已经连接数据库
    protected $connected       = false;

    // 数据库连接参数配置
    protected $config             = '';

    // SQL 执行时间记录
    protected $beginTime;

    /**
     +----------------------------------------------------------
     * 架构函数
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param array $config 数据库配置数组
     +----------------------------------------------------------
     */
    function __construct($config=''){
        return $this->factory($config);
    }

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
    public static function getInstance()
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
    public function &factory($db_config='')
    {
        // 读取数据库配置
        $db_config = $this->parseConfig($db_config);
        if(empty($db_config['dbms'])) {
            throw_exception(L('_NO_DB_CONFIG_'));
        }
        // 数据库类型
        $this->dbType = ucwords(strtolower($db_config['dbms']));
        // 读取系统数据库驱动目录
        $dbClass = 'Db'. $this->dbType;
        $dbDriverPath = dirname(__FILE__).'/Driver/';
        require_cache( $dbDriverPath . $dbClass . '.class.php');

        // 检查驱动类
        if(class_exists($dbClass)) {
            $db = new $dbClass($db_config);
            // 获取当前的数据库类型
            if( 'pdo' != strtolower($db_config['dbms']) ) {
                $db->dbType = strtoupper($this->dbType);
            }else{
                $db->dbType = $this->_getDsnType($db_config['dsn']);
            }
            if(C('DEBUG_MODE') || C('SQL_DEBUG_LOG')) {
                $db->debug    = true;
            }
        }else {
            // 类没有定义
            throw_exception(L('_NOT_SUPPORT_DB_').': ' . $db_config['dbms']);
        }
        return $db;
    }

    /**
     +----------------------------------------------------------
     * 根据DSN获取数据库类型 返回大写
     +----------------------------------------------------------
     * @access protected
     +----------------------------------------------------------
     * @param string $dsn  dsn字符串
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    protected function _getDsnType($dsn) {
        $match  =  explode(':',$dsn);
        $dbType = strtoupper(trim($match[0]));
        return $dbType;
    }

    /**
     +----------------------------------------------------------
     * 分析数据库配置信息，支持数组和DSN
     +----------------------------------------------------------
     * @access private
     +----------------------------------------------------------
     * @param mixed $db_config 数据库配置信息
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    private function parseConfig($db_config='') {
        if ( !empty($db_config) && is_string($db_config)) {
            // 如果DSN字符串则进行解析
            $db_config = $this->parseDSN($db_config);
        }else if(empty($db_config)){
            // 如果配置为空，读取配置文件设置
            $db_config = array (
                'dbms'        =>   C('DB_TYPE'),
                'username'  =>   C('DB_USER'),
                'password'   =>   C('DB_PWD'),
                'hostname'  =>   C('DB_HOST'),
                'hostport'    =>   C('DB_PORT'),
                'database'   =>   C('DB_NAME'),
                'dsn'          =>   C('DB_DSN'),
                'params'     =>   C('DB_PARAMS'),
            );
        }
        return $db_config;
    }

    /**
     +----------------------------------------------------------
     * 增加数据库连接(相同类型的)
     +----------------------------------------------------------
     * @access protected
     +----------------------------------------------------------
     * @param mixed $config 数据库连接信息
     * @param mixed $linkNum  创建的连接序号
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    public function addConnect($config,$linkNum=null) {
        $db_config  =   $this->parseConfig($config);
        if(empty($linkNum)) {
            $linkNum     =   count($this->linkID);
        }
        if(isset($this->linkID[$linkNum])) {
            // 已经存在连接
            return false;
        }
        // 创建新的数据库连接
        return $this->connect($db_config,$linkNum);
    }

    /**
     +----------------------------------------------------------
     * 切换数据库连接
     +----------------------------------------------------------
     * @access protected
     +----------------------------------------------------------
     * @param integer $linkNum  创建的连接序号
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    public function switchConnect($linkNum) {
        if(isset($this->linkID[$linkNum])) {
            // 存在指定的数据库连接序号
            $this->_linkID  =   $this->linkID[$linkNum];
            return true;
        }else{
            return false;
        }
    }

    /**
     +----------------------------------------------------------
     * 初始化数据库连接
     +----------------------------------------------------------
     * @access protected
     +----------------------------------------------------------
     * @param boolean $master 主服务器
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    protected function initConnect($master=true) {
        if(1 == C('DB_DEPLOY_TYPE')) {
            // 采用分布式数据库
            $this->_linkID = $this->multiConnect($master);
        }else{
            // 默认单数据库
            if ( !$this->connected ) $this->_linkID = $this->connect();
        }
    }

    /**
     +----------------------------------------------------------
     * 连接分布式服务器
     +----------------------------------------------------------
     * @access protected
     +----------------------------------------------------------
     * @param boolean $master 主服务器
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    protected function multiConnect($master=false) {
        static $_config = array();
        if(empty($_config)) {
            // 缓存分布式数据库配置解析
            foreach ($this->config as $key=>$val){
                $_config[$key]      =   explode(',',$val);
            }
        }
        // 数据库读写是否分离
        if(C('DB_RW_SEPARATE')){
            // 主从式采用读写分离
            if($master) {
                // 默认主服务器是连接第一个数据库配置
                $r  =   0;
            }else{
                // 读操作连接从服务器
                $r = floor(mt_rand(1,count($_config['hostname'])-1));   // 每次随机连接的数据库
            }
        }else{
            // 读写操作不区分服务器
            $r = floor(mt_rand(0,count($_config['hostname'])-1));   // 每次随机连接的数据库
        }
        $db_config = array(
            'username'  =>   isset($_config['username'][$r])?$_config['username'][$r]:$_config['username'][0],
            'password'   =>   isset($_config['password'][$r])?$_config['password'][$r]:$_config['password'][0],
            'hostname'  =>   isset($_config['hostname'][$r])?$_config['hostname'][$r]:$_config['hostname'][0],
            'hostport'    =>   isset($_config['hostport'][$r])?$_config['hostport'][$r]:$_config['hostport'][0],
            'database'   =>   isset($_config['database'][$r])?$_config['database'][$r]:$_config['database'][0],
            'dsn'          =>   isset($_config['dsn'][$r])?$_config['dsn'][$r]:$_config['dsn'][0],
            'params'     =>   isset($_config['params'][$r])?$_config['params'][$r]:$_config['params'][0],
        );
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
     */
    public function parseDSN($dsnStr)
    {
        if( empty($dsnStr) ){return false;}
        $info = parse_url($dsnStr);
        if($info['scheme']){
            $dsn = array(
            'dbms'        => $info['scheme'],
            'username'  => isset($info['user']) ? $info['user'] : '',
            'password'   => isset($info['pass']) ? $info['pass'] : '',
            'hostname'  => isset($info['host']) ? $info['host'] : '',
            'hostport'    => isset($info['port']) ? $info['port'] : '',
            'database'   => isset($info['path']) ? substr($info['path'],1) : ''
            );
        }else {
            preg_match('/^(.*?)\:\/\/(.*?)\:(.*?)\@(.*?)\:([0-9]{1, 6})\/(.*?)$/',trim($dsnStr),$matches);
            $dsn = array (
            'dbms'        => $matches[1],
            'username'  => $matches[2],
            'password'   => $matches[3],
            'hostname'  => $matches[4],
            'hostport'    => $matches[5],
            'database'   => $matches[6]
            );
        }
        return $dsn;
     }

    /**
     +----------------------------------------------------------
     * 数据库调试 记录当前SQL
     +----------------------------------------------------------
     * @access protected
     +----------------------------------------------------------
     */
    protected function debug() {
        // 记录操作结束时间
        if ( $this->debug )    {
            $runtime    =   number_format(microtime(TRUE) - $this->beginTime, 6);
            Log::record(" RunTime:".$runtime."s SQL = ".$this->queryStr,Log::SQL);
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
     * @param boolean $cache  是否缓存查询
     * @param boolean $lazy  是否惰性加载
     * @param boolean $lock 是否lock
     * @param boolean $fetchSql 是否返回SQL
     +----------------------------------------------------------
     * @return mixed
     +----------------------------------------------------------
     */
    public function query($sql='',$fetchSql=false)
    {
        if(empty($sql)) {
            $sql   = $this->queryStr;
        }
        if($fetchSql) {
            return $sql;
        }
        // 进行查询
        $data = $this->_query($sql);
        return $data;
    }

    /**
     +----------------------------------------------------------
     * 数据库操作方法
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $sql  执行语句
     * @param boolean $lock 是否lock
     * @param boolean $fetchSql 是否返回SQL
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    public function execute($sql='',$lock=false,$fetchSql=false)
    {
        if(empty($sql)) {
            $sql  = $this->queryStr;
        }
        if($lock) {
            $sql .= $this->setLockMode();
        }
        if($fetchSql) {
            return $sql;
        }
        return $this->_execute($sql);
    }

    /**
     +----------------------------------------------------------
     * 插入记录
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param mixed $data 数据
     * @param string $table  数据表名
     +----------------------------------------------------------
     * @return false | integer
     +----------------------------------------------------------
     */
    public function insert($data,$table) {
        foreach ($data as $key=>$val){
            $fields[] =  '`'.$key.'`';
            if(is_int($val)) {
                $values[]   =  intval($val);
            }elseif(is_float($val)){
                $values[]   =  floatval($val);
            }elseif(is_string($val)){
                $values[]  = '\''.$val.'\'';
            }elseif(is_null($val)){
                $values[]   =  'null';
            }
        }
        $fieldsStr    = implode(',', $fields);
        $valuesStr  = implode(',', $values);
        $sql   =  'INSERT INTO `'.$table.'` ('.$fieldsStr.') VALUES ('.$valuesStr.')';
        return $this->execute($sql);
    }

    /**
     +----------------------------------------------------------
     * 更新记录
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param mixed $data 数据
     * @param array $options 表达式
     +----------------------------------------------------------
     * @return false | integer
     +----------------------------------------------------------
     */
    public function update($data,$options) {
        if(is_array($data)) {
            foreach ($data as $key=>$val){
                if(is_int($val)) {
                    $set[]   =  '`'.$key.'`='.intval($val);
                }elseif(is_float($val)){
                    $set[]   =  '`'.$key.'`='.floatval($val);
                }elseif(is_string($val)){
                    $set[]  = '`'.$key.'`=\''.$val.'\'';
                }elseif(is_null($val)){
                    $set[]   =  '`'.$key.'`=null';
                }elseif(is_array($val) && strtolower($val[0]) == 'exp') {
                    // 使用表达式
                    $set[]    =   '`'.$key.'`='.$val[1];
                }
            }
            $setStr  =  implode(',',$set);
        }elseif(is_string($data)){
            $setStr  =  $data;
        }
        $table = isset($options['table'])?$options['table']:'';
        $where  =  isset($options['where'])?$options['where']:'';
        $limit =  isset($options['limit'])?$options['limit']:'';
        $order   =  isset($options['order'])?$options['order']:'';
        $sql   =  'UPDATE `'.$table.'` SET '.$setStr;
        if(!empty($where)) {
            $sql   .= ' WHERE '.$where;
        }
        if(!empty($order)) {
            $sql   .= ' ORDER '.$order;
        }
        if(!empty($limit)) {
            $sql   .= ' LIMIT '.$limit;
        }
        return $this->execute($sql);
    }

    /**
     +----------------------------------------------------------
     * 删除记录
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param array $options 表达式
     +----------------------------------------------------------
     * @return false | integer
     +----------------------------------------------------------
     */
    public function delete($options=array())
    {
        $table = isset($options['table'])?$options['table']:'';
        $where  =  isset($options['where'])?$options['where']:'';
        $limit =  isset($options['limit'])?$options['limit']:'';
        $order   =  isset($options['order'])?$options['order']:'';
        $sql   = 'DELETE FROM `'.$table.'`';
        if(!empty($where)) {
            $sql   .= ' WHERE '.$where;
        }
        if(!empty($order)) {
            $sql   .= ' ORDER '.$order;
        }
        if(!empty($limit)) {
            $sql   .= ' LIMIT '.$limit;
        }
        return $this->execute($sql);
    }

    /**
     +----------------------------------------------------------
     * 查找记录
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param array $options 表达式
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
     */
    public function select($options=array()) {
        $table = isset($options['table'])?$options['table']:'';
        $field   =  isset($options['field'])?$options['field']:'*';
        $distinct   =  isset($options['distinct'])?$options['distinct']:false;
        $where  =  isset($options['where'])?$options['where']:'';
        $limit =  isset($options['limit'])?$options['limit']:'';
        $order   =  isset($options['order'])?$options['order']:'';
        $join   =  isset($options['join'])?$options['join']:'';
        $group   =  isset($options['group'])?$options['group']:'';
        $having   =  isset($options['having'])?$options['having']:'';
        $sql   = 'SELECT ';
        if($distinct) {
            $sql   .=  ' DISTINCT ';
        }
        $sql   .= $field.' FROM `'.$table.'`';
        if(!empty($join)) {
            if(is_array($join)) {
                foreach ($join as $key=>$_join){
                    if(false !== stripos($_join,'JOIN')) {
                        $sql .= ' '.$_join;
                    }else{
                        $sql .= ' LEFT JOIN ' .$_join;
                    }
                }
            }else{
                if(false !== stripos($join,'JOIN')) {
                    $sql .= ' '.$join;
                }else{
                    $sql .= ' LEFT JOIN ' .$join;
                }
            }
        }
        if(!empty($where)) {
            $sql   .= ' WHERE '.$where;
        }
        if(!empty($group)) {
            $sql   .= ' GROUP '.$group;
        }
        if(!empty($order)) {
            $sql   .= ' ORDER '.$order;
        }
        if(!empty($limit)) {
            $sql   .= ' LIMIT '.$limit;
        }
        return $this->query($sql);
    }

    /**
     +----------------------------------------------------------
     * 查询次数更新或者查询
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param mixed $times
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    public function Q($times='') {
        static $_times = 0;
        if(empty($times)) {
            return $_times;
        }else{
            $_times++;
            // 记录开始执行时间
            $this->beginTime = microtime(TRUE);
        }
    }

    /**
     +----------------------------------------------------------
     * 写入次数更新或者查询
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param mixed $times
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    public function W($times='') {
        static $_times = 0;
        if(empty($times)) {
            return $_times;
        }else{
            $_times++;
            // 记录开始执行时间
            $this->beginTime = microtime(TRUE);
        }
    }

    /**
     +----------------------------------------------------------
     * 获取最近一次查询的sql语句
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    public function getLastSql() {
        return $this->queryStr;
    }

}//类定义结束
?>