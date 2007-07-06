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
 * @package    Core
 * @link       http://www.fcs.org.cn
 * @copyright  Copyright (c) 2005-2006 liu21st.com.  All rights reserved. 
 * @author     liu21st <liu21st@gmail.com>
 * @version    $Id$
 +------------------------------------------------------------------------------
 */

import("FCS.Core.VoList");
import('FCS.Db.Field');

/**
 +------------------------------------------------------------------------------
 * 数据表类 继承自VoList类
 +------------------------------------------------------------------------------
 * @package   core
 * @author    liu21st <liu21st@gmail.com>
 * @version   0.8.0
 +------------------------------------------------------------------------------
 */
class Table extends VoList
{

    /**
     +----------------------------------------------------------
     * 表名
     +----------------------------------------------------------
     * @var string
     * @access protected
     +----------------------------------------------------------
     */
    var $name  ;

    /**
     +----------------------------------------------------------
     * 主键名
     +----------------------------------------------------------
     * @var string
     * @access protected
     +----------------------------------------------------------
     */
    var $pk 	=	'id';

    /**
     +----------------------------------------------------------
     * 是否自动增长
     +----------------------------------------------------------
     * @var string
     * @access protected
     +----------------------------------------------------------
     */
    var $autoInc	=	false;


    /**
     +----------------------------------------------------------
     * 字段信息
     +----------------------------------------------------------
     * @var array
     * @access protected
     +----------------------------------------------------------
     */
    var $fields = array();

    /**
     +----------------------------------------------------------
     * 架构函数 
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $name 数据表名
     * @param array $fields 数据表名
     * @param string $pk  主键名
     +----------------------------------------------------------
     */
    function __construct($name ,$fields =array(),$pk='id')
    {
        $this->name	=	$name;
        $this->fields = $fields;
        $this->primary = $primary;
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
     * @throws FcsException
     +----------------------------------------------------------
     */
    function build($dbName,$prefix= DB_PREFIX) 
    {
        $db	=	DB::getInstance();
        $info = $db->getTables($dbName);
        $this->db->autoCommit = 0;
        foreach($info as $key=>$val) {
            if($val != '') {
                $val    =   str_replace(DB_PREFIX.'_','',$val);
            }
            $this->tableToVo($val);
            $this->tableToDao($val);
			$this->tableToAction($val);
        }
        $db->commit();
		echo('创建成功！');
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
     * @throws FcsException
     +----------------------------------------------------------
     */
    function tableToVo($name='',$voClass='',$filename='') 
    {
        $db     =   DB::getInstance();
        $name   =   !empty($name)? $name : $this->name;
        $this->fields	=	$db->getFields($name);
        $voClass	=	empty($voClass)? ucwords($name).'Vo':$voClass;
        $content =   "<?php \n\r";
        $content .= "/**\n\r +------------------------------------------------------------------------------\n\r";
        $content .= " * FCS系统自动生成的".$voClass."数据对象类\n\r";
        $content .= " * 生成时间".date('Y-m-d l H:m:s')."\n\r";
        $content .= " +------------------------------------------------------------------------------\n\r";
        $content .= " */\n\r";
        $content .= "class ".$voClass." extends Vo \n\r {\n\r";
        $content .= "    //+----------------------------------------\n\r";
        $content .= "    //| 数据模型 数据表字段名 \n\r";
        $content .= "    //+----------------------------------------\n\r";
        foreach($this->fields as $key=>$val) {
            $content .= "    var $".$key.";\n\r";	
            if($val['primary']) {
                $this->primary = $val['name'];
                if($val['autoInc']) $this->autoInc = $val['autoInc'];
            }
        }
        $content .= "\n\r    //+----------------------------------------\n\r";
        $content .= "    //| 数据模型 数据表字段详细信息 \n\r";
        $content .= "    //+----------------------------------------\n\r";
        $content .= "    var $"."_info= array('primary'=>'".$this->primary."' ,'autoInc'=>".($this->autoInc? 'true':'false').",'fields'=>".var_export($this->fields,true).");\n\r";
        $content .= "}\n\r?>";
        $filename = empty($filename)?APPS_PATH.APP_NAME.'/Vo/'.$voClass.'.class.php':$filename;
        file_put_contents($filename,$content);
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
     * @throws FcsException
     +----------------------------------------------------------
     */
    function tableToDao($name='',$daoClass='',$filename='') 
    {
        $daoClass	=	empty($daoClass)? ucwords($name).'Dao':$daoClass;
        $content =   "<?php \n\r";
        $content .= "/**\n\r +------------------------------------------------------------------------------\n\r";
        $content .= " * FCS系统自动生成的".$daoClass."数据访问对象类\n\r";
        $content .= " * 生成时间".date('Y-m-d l H:m:s')."\n\r";
        $content .= " +------------------------------------------------------------------------------\n\r";
        $content .= " */\n\r";
        $content .= "class ".$daoClass." extends Dao \n\r {\n\r";
        $content .= "    //+----------------------------------------\n\r";
        $content .= "    //| 在下面添加需要的数据访问方法 \n\r";
        $content .= "    //+----------------------------------------\n\r";
        $content .= "}\n\r?>";
        $filename = empty($filename)?APPS_PATH.APP_NAME.'/Dao/'.$daoClass.'.class.php':$filename;
        file_put_contents($filename,$content);
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
     * @throws FcsException
     +----------------------------------------------------------
     */
    function tableToAction($name='',$actionClass='',$filename='') 
    {
        $actionClass	=	empty($actionClass)? ucwords($name).'Action':$actionClass;
        $content =   "<?php \n\r";
        $content .= "/**\n\r +------------------------------------------------------------------------------\n\r";
        $content .= " * FCS系统自动生成的".$actionClass."控制器对象类\n\r";
        $content .= " * 生成时间".date('Y-m-d l H:m:s')."\n\r";
        $content .= " +------------------------------------------------------------------------------\n\r";
        $content .= " */\n\r";
        $content .= "class ".$actionClass." extends Action \n\r {\n\r";
        $content .= "    //+----------------------------------------\n\r";
        $content .= "    //| 在下面添加需要的控制器方法 \n\r";
        $content .= "    //+----------------------------------------\n\r";
        $content .= "}\n\r?>";
        $filename = empty($filename)?APPS_PATH.APP_NAME.'/Action/'.$actionClass.'.class.php':$filename;
        file_put_contents($filename,$content);
    }
}//类定义结束
?>