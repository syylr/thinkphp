<?php
// +----------------------------------------------------------------------
// | ThinkPHP
// +----------------------------------------------------------------------
// | Copyright (c) 2009 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// $Id$

/**
 +------------------------------------------------------------------------------
 * ThinkPHP Model模型类 用于CLI模式
 +------------------------------------------------------------------------------
 * @category   Think
 * @package  Think
 * @subpackage  Core
 * @author    liu21st <liu21st@gmail.com>
 * @version   $Id$
 +------------------------------------------------------------------------------
 */
class Model extends Base
{
    // 当前数据库操作对象
    protected $db = null;

    // 主键名称
    protected $pk  = 'id';

    // 数据表前缀
    protected $tablePrefix  =   '';

    // 模型名称
    protected $name = '';

    // 数据库名称
    protected $dbName  = '';

    // 数据表名（不包含表前缀）
    protected $tableName = '';

    // 实际数据表名（包含表前缀）
    protected $trueTableName ='';

    // 最近错误信息
    protected $error = '';

    /**
     +----------------------------------------------------------
     * 架构函数
     * 取得DB类的实例对象 数据表字段检查
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param mixed $data 要创建的数据对象内容
     +----------------------------------------------------------
     */
    public function __construct()
    {
        // 模型初始化
        $this->_initialize();
        // 模型名称获取
        $this->name =   $this->getModelName();
        // 数据库初始化操作
        alias_import("Db");
        // 获取数据库操作对象
        if(!empty($this->connection)) {
            // 当前模型有独立的数据库连接信息
            $this->db = Db::getInstance($this->connection);
        }else{
            $this->db = Db::getInstance();
        }
        // 设置表前缀
        $this->tablePrefix = $this->tablePrefix?$this->tablePrefix:C('DB_PREFIX');
    }

    /**
     +----------------------------------------------------------
     * 取得模型实例对象
     +----------------------------------------------------------
     * @static
     * @access public
     +----------------------------------------------------------
     * @return Model 返回数据模型实例
     +----------------------------------------------------------
     */
    public static function getInstance()
    {
        return get_instance_of(__CLASS__);
    }

    // 回调方法 初始化模型
    protected function _initialize() {}

    /**
     +----------------------------------------------------------
     * SQL查询
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param mixed $sql  SQL指令
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
     */
    public function query($sql)
    {
        if(is_array($sql)) {
            return $this->patchQuery($sql);
        }
        if(!empty($sql)) {
            if(strpos($sql,'__TABLE__')) {
                $sql    =   str_replace('__TABLE__',$this->getTableName(),$sql);
            }
            return $this->db->query($sql);
        }else{
            return false;
        }
    }

    /**
     +----------------------------------------------------------
     * 执行SQL语句
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $sql  SQL指令
     +----------------------------------------------------------
     * @return false | integer
     +----------------------------------------------------------
     */
    public function execute($sql='')
    {
        if(!empty($sql)) {
            if(strpos($sql,'__TABLE__')) {
                $sql    =   str_replace('__TABLE__',$this->getTableName(),$sql);
            }
            $result =   $this->db->execute($sql);
            return $result;
        }else {
            return false;
        }
    }

    /**
     +----------------------------------------------------------
     * 得到当前的数据对象名称
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    public function getModelName()
    {
        if(empty($this->name)) {
            $this->name =   substr(get_class($this),0,-5);
        }
        return $this->name;
    }

    /**
     +----------------------------------------------------------
     * 得到完整的数据表名
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    public function getTableName()
    {
        if(empty($this->trueTableName)) {
            $tableName  = !empty($this->tablePrefix) ? $this->tablePrefix : '';
            if(!empty($this->tableName)) {
                $tableName .= $this->tableName;
            }elseif(C('AUTO_NAME_IDENTIFY')){
                // 智能识别表名
                $tableName .= $this->parseName($this->name);
            }else{
                $tableName .= $this->name;
            }
            if(!empty($this->dbName)) {
                $tableName    =  $this->dbName.'.'.$tableName;
            }
            $this->trueTableName    =   strtolower($tableName);
        }
        return $this->trueTableName;
    }

    /**
     +----------------------------------------------------------
     * 启动事务
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    public function startTrans()
    {
        $this->commit();
        $this->db->startTrans();
        return ;
    }

    /**
     +----------------------------------------------------------
     * 提交事务
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return boolean
     +----------------------------------------------------------
     */
    public function commit()
    {
        return $this->db->commit();
    }

    /**
     +----------------------------------------------------------
     * 事务回滚
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return boolean
     +----------------------------------------------------------
     */
    public function rollback()
    {
        return $this->db->rollback();
    }

};
?>