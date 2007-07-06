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
// $Id: Table.class.php 92 2007-04-04 13:33:33Z liu21st $

/**
 +------------------------------------------------------------------------------
 * 数据表类 用于自动创建数据对象和数据访问对象
 +------------------------------------------------------------------------------
 * @package   core
 * @author    liu21st <liu21st@gmail.com>
 * @version   $Id: Table.class.php 92 2007-04-04 13:33:33Z liu21st $
 +------------------------------------------------------------------------------
 */

class Table extends Base
{

    // 项目路径
    var $appPath;
    // 项目名
    var $appName;
    // 模块名
    var $moduleName;
    // 表前缀
    var $prefix;
    // 表名
    var $name  ;
    // 主键名
    var $pk 	=	'id';
    // 自动增长
    var $autoInc	=	false;
    // 字段信息
    var $fields = array();

    /**
     +----------------------------------------------------------
     * 架构函数 
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     */
    function __construct($appName=APP_NAME,$moduleName=MODULE_NAME)
    {
        $this->appName = $appName;
        $this->moduleName  =  $moduleName;
        $this->appPath  = realpath(str_replace('Admin',$appName,ADMIN_PATH));
    }

    /**
     +----------------------------------------------------------
     * 自动生成指定数据库的Vo类
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $dbName 数据库名
     * @param string $prefix 数据表名前缀
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws ThinkException
     +----------------------------------------------------------
     */
    function build($tableName='',$dbName=DB_NAME,$prefix= DB_PREFIX) 
    {
        $db	=	DB::getInstance();
        if(empty($tableName)) {
        	$tables = $db->getTables($dbName);
        }else {
        	$tables  = array($tableName);
        }
        $this->prefix =  $prefix;
        $this->db->autoCommit = 0;
        foreach($tables as $key=>$table) {
            if($table != '') {
                $table    =   str_replace($prefix.'_','',$table);
            }
            $this->tableToVo($table);
            $this->tableToDao($table);
			$this->tableToAction($table);
        }
        $db->commit();
    }

    /**
     +----------------------------------------------------------
     * 根据数据库表或者视图自动生成Vo类
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $name 创建表名
     * @param string $voClass 创建Vo对象的名称
     * @param string $filename vo对象文件名
     +----------------------------------------------------------
     * @return Void
     +----------------------------------------------------------
     * @throws ThinkException
     +----------------------------------------------------------
     */
    function tableToVo($name,$voClass='',$filename='') 
    {
        $voClass	=	empty($voClass)? ucwords($name).'Vo':$voClass;
        $filename = empty($filename)?$this->appPath.'/Lib/Vo/'.$voClass.'.class.php':$filename;
        if(!file_exists($filename)) {
            $db     =   DB::getInstance();
            $this->fields	=	$db->getFields(strtolower($this->prefix.'_'.$name));
            $content =   "<?php \n";
            $content .= "/**\n +------------------------------------------------------------------------------\n";
            $content .= " * ThinkPHP系统自动生成的".$voClass."数据对象类\n";
            $content .= " * 生成时间".date('Y-m-d l H:m:s')."\n";
            $content .= " +------------------------------------------------------------------------------\n";
            $content .= " */\n";
            $content .= "class ".$voClass." extends Vo \n{\n";
            $content .= "    //+----------------------------------------\n";
            $content .= "    //| 数据模型 数据表字段名 \n";
            $content .= "    //+----------------------------------------\n";
            foreach($this->fields as $key=>$val) {
                $content .= "    var $".$key.";\n";	
                if($val['primary']) {
                    $this->primary = $val['name'];
                    if($val['autoInc']) $this->autoInc = $val['autoInc'];
                }
            }
            /*
            $content .= "\n    //+----------------------------------------\n";
            $content .= "    //| 数据模型 数据表字段详细信息 \n";
            $content .= "    //+----------------------------------------\n";
            $content .= "    var $"."_info= array('primary'=>'".$this->primary."' ,'autoInc'=>".($this->autoInc? 'true':'false').",'fields'=>".var_export($this->fields,true).");\n";*/
            $content .= "}\n?>";
            $result = file_put_contents($filename,$content);  
        }
    }

    /**
     +----------------------------------------------------------
     * 根据数据库表或者视图自动生成Vo类
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $name 创建类型
     * @param string $daoClass 创建Dao对象的名称
     * @param string $filename Dao类文件名
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws ThinkException
     +----------------------------------------------------------
     */
    function tableToDao($name,$daoClass='',$filename='') 
    {
        $daoClass	=	empty($daoClass)? ucwords($name).'Dao':$daoClass;
        $filename = empty($filename)?$this->appPath.'/Lib/Dao/'.$daoClass.'.class.php':$filename;
        if(!file_exists($filename)) {
            $content =   "<?php \n";
            $content .= "/**\n +------------------------------------------------------------------------------\n";
            $content .= " * ThinkPHP系统自动生成的".$daoClass."数据访问对象类\n";
            $content .= " * 生成时间".date('Y-m-d l H:m:s')."\n";
            $content .= " +------------------------------------------------------------------------------\n";
            $content .= " */\n";
            $content .= "class ".$daoClass." extends Dao \n{\n";
            $content .= "    //+----------------------------------------\n";
            $content .= "    //| 在下面添加需要的数据访问方法 \n";
            $content .= "    //+----------------------------------------\n";
            $content .= "}\n?>";
            file_put_contents($filename,$content);        	
        }
    }

    /**
     +----------------------------------------------------------
     * 根据数据库表或者视图自动生成Action类
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $name 创建类型
     * @param string $actionClass 创建Action对象的名称
     * @param string $filename Action对象文件名
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws ThinkException
     +----------------------------------------------------------
     */
    function tableToAction($name,$actionClass='',$filename='') 
    {
        $actionClass	=	empty($actionClass)? ucwords($name).'Action':$actionClass;
        $filename = empty($filename)?$this->appPath.'/Lib/Action/'.$actionClass.'.class.php':$filename;
        if(!file_exists($filename)) {
            $content =   "<?php \n";
            $content .= "/**\n +------------------------------------------------------------------------------\n";
            $content .= " * ThinkPHP系统自动生成的".$actionClass."控制器对象类\n";
            $content .= " * 生成时间".date('Y-m-d l H:m:s')."\n";
            $content .= " +------------------------------------------------------------------------------\n";
            $content .= " */\n";
            $content .= "class ".$actionClass." extends Action \n{\n";
            $content .= "    //+----------------------------------------\n";
            $content .= "    //| 在下面添加需要的控制器方法 \n";
            $content .= "    //+----------------------------------------\n";
            $content .= "}\n?>";
            file_put_contents($filename,$content);
        }
    }

}//类定义结束
?>