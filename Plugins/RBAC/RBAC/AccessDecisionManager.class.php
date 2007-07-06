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
 * @version    $Id: AccessDecisionManager.class.php 92 2006-11-11 08:47:04Z fcs $
 +------------------------------------------------------------------------------
 */

/**
 +------------------------------------------------------------------------------
 * 访问决策管理器
 +------------------------------------------------------------------------------
 * @package   core
 * @author    liu21st <liu21st@gmail.com>
 * @version   $Ver$
 +------------------------------------------------------------------------------
 */
class AccessDecisionManager extends Base
{//类定义开始

    var $roleTable    ;
    var $roleUserTable  ;
    var $roleAccessTable;
    var $roleNodeTable;


    /**
     +----------------------------------------------------------
     * 架构函数
     * 
     +----------------------------------------------------------
     * @static
     * @access public 
     +----------------------------------------------------------
     */
    function __construct()
    {
        $this->roleTable = DB_PREFIX.'_group';
        $this->roleUserTable  =  DB_PREFIX.'_groupuser';
        $this->roleAccessTable=   DB_PREFIX.'_access';
        $this->roleNodeTable    =   DB_PREFIX.'_node';
    }

    /**
     +----------------------------------------------------------
     * 决策认证
     * 检查是否具有当前的操作权限
     +----------------------------------------------------------
     * @param integer $authId 认证id
     * @param string $app 项目名
     * @param string $module 模块名
     * @param string $action 操作名
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     */
    function decide($authId,$app=APP_NAME,$module=MODULE_NAME,$action=ACTION_NAME)
    {
        //决策认证号是否具有当前模块权限
        $db     =   DB::getInstance();
        $sql    =   "select a.id from ".
                    $this->roleTable." as a,".
                    $this->roleUserTable." as b,".
                    $this->roleAccessTable." as c ,".
                    $this->roleNodeTable." as d ".
                    "where b.userId={$authId} and b.groupId=a.id and ( c.groupId=a.id  or (c.groupId=a.parentId and a.parentId!=0 ) )  and a.status=1 and c.groupId=a.id and c.nodeId=d.id and ( (d.name='".$module."' and d.level=2) or ( d.name='".$action."' and d.level=3 ) or ( d.name='".$app."' and d.level=1) )";
        $rs =   $db->query($sql);
        if($rs->isEmpty()) {
            return false;
        }else {
            return true;
        }
    }


    /**
     +----------------------------------------------------------
     * 取得当前认证号的项目权限列表
     * 
     +----------------------------------------------------------
     * @param string $appPrefix 数据库前缀
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     */
    function getAppAccessList($authId)
    {
        $db     =   DB::getInstance();
        $sql    =   "select d.id,d.name from ".
                    $this->roleTable." as a,".
                    $this->roleUserTable." as b,".
                    $this->roleAccessTable." as c ,".
                    $this->roleNodeTable." as d ".
                    "where b.userId={$authId} and b.groupId=a.id and ( c.groupId=a.id  or (c.groupId=a.parentId and a.parentId!=0 ) ) and a.status=1 and c.nodeId=d.id and d.level=1 and d.status=1";
        $rs =   $db->query($sql);
        return $rs->getCol('id,name');
    }

    /**
     +----------------------------------------------------------
     * 取得当前认证号的模块权限列表
     * 
     +----------------------------------------------------------
     * @param string $appPrefix 数据库前缀
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     */
    function getModuleAccessList($authId,$app=APP_NAME)
    {
        $db     =   DB::getInstance();
        $sql    =   "select d.id,d.name from ".
                    $this->roleTable." as a,".
                    $this->roleUserTable." as b,".
                    $this->roleAccessTable." as c ,".
                    $this->roleNodeTable." as d ".
                    "where b.userId={$authId} and b.groupId=a.id and ( c.groupId=a.id  or (c.groupId=a.parentId and a.parentId!=0 ) ) and a.status=1 and c.nodeId=d.id and d.level=2 and d.status=1";
        $rs =   $db->query($sql);
        return $rs->getCol('id,name');
    }

    /**
     +----------------------------------------------------------
     * 取得当前认证号的操作权限列表
     * 
     +----------------------------------------------------------
     * @param string $appPrefix 数据库前缀
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     */
    function getActionAccessList($authId)
    {
        $db     =   DB::getInstance();
        $sql    =   "select d.id,d.name from ".
                    $this->roleTable." as a,".
                    $this->roleUserTable." as b,".
                    $this->roleAccessTable." as c ,".
                    $this->roleNodeTable." as d ".
                    "where b.userId={$authId} and b.groupId=a.id and ( c.groupId=a.id  or (c.groupId=a.parentId and a.parentId!=0 ) )  and a.status=1 and  c.nodeId=d.id and d.level=3 and d.status=1";
        $rs =   $db->query($sql);
        return $rs->getCol('id,name');
    }

    /**
     +----------------------------------------------------------
     * 取得当前认证号的所有权限列表
     * 
     +----------------------------------------------------------
     * @param string $appPrefix 数据库前缀
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     */
    function getAccessList($authId)
    {
        $db     =   DB::getInstance();
        $sql    =   "select d.id,d.name from ".
                    $this->roleTable." as a,".
                    $this->roleUserTable." as b,".
                    $this->roleAccessTable." as c ,".
                    $this->roleNodeTable." as d ".
                    "where b.userId={$authId} and b.groupId=a.id and ( c.groupId=a.id  or (c.groupId=a.parentId and a.parentId!=0 ) ) and a.status=1 and c.nodeId=d.id and d.level=1 and d.status=1";
        $rs =   $db->query($sql);
        $app = $rs->getCol('id,name');
        $access =  array();
        $publicAction = array();
        foreach($app as $appId=>$appName) {
            $access[strtoupper($appName)]   =  array();
            $sql    =   "select d.id,d.name from ".
                        $this->roleTable." as a,".
                        $this->roleUserTable." as b,".
                        $this->roleAccessTable." as c ,".
                        $this->roleNodeTable." as d ".
                        "where b.userId={$authId} and b.groupId=a.id and ( c.groupId=a.id  or (c.groupId=a.parentId and a.parentId!=0 ) ) and a.status=1 and c.nodeId=d.id and d.level=2 and d.parentId={$appId} and d.status=1";
            $rs =   $db->query($sql);      
            $module = $rs->getCol('id,name');
            if(false !== $index = array_search('Public',$module)) {
            	 $sql    =   "select d.id,d.name from ".
                            $this->roleTable." as a,".
                            $this->roleUserTable." as b,".
                            $this->roleAccessTable." as c ,".
                            $this->roleNodeTable." as d ".
                            "where b.userId={$authId} and b.groupId=a.id and ( c.groupId=a.id  or (c.groupId=a.parentId and a.parentId!=0 ) )  and a.status=1 and  c.nodeId=d.id and d.parentId={$index} and d.level=3 and d.status=1";
                $rs =   $db->query($sql);    
                $publicAction =  $rs->getCol('name,id');
                unset($module[$index]);
            }

            foreach($module as $moduleId=>$moduleName) {
                $sql    =   "select d.id,d.name from ".
                            $this->roleTable." as a,".
                            $this->roleUserTable." as b,".
                            $this->roleAccessTable." as c ,".
                            $this->roleNodeTable." as d ".
                            "where b.userId={$authId} and b.groupId=a.id and ( c.groupId=a.id  or (c.groupId=a.parentId and a.parentId!=0 ) )  and a.status=1 and  c.nodeId=d.id and d.parentId={$moduleId} and d.level=3 and d.status=1";
                $rs =   $db->query($sql);    
                $action =  $rs->getCol('name,id');
                $action += $publicAction;
                $access[strtoupper($appName)][strtoupper($moduleName)]   =  array_change_key_case($action,CASE_UPPER);
            }

        }
        return $access;
    }
}//类定义结束
?>