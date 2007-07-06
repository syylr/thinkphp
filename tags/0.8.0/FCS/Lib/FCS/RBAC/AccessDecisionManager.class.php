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
import("FCS.Core.AuthenticationManager");

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

    var $roleTable      =   "fcs_group";
    var $roleUserTable  =   'fcs_groupUser';
    var $roleModuleTable=   'fcs_groupModule';
    var $roleActionTable=   'fcs_groupAction';
    var $moduleTable    =   'fcs_module';
    var $actionTable    =   'fcs_action';

    /**
     +----------------------------------------------------------
     * 决策认证
     * 检查是否具有当前模块和操作的权限
     +----------------------------------------------------------
     * @param string $appPrefix 数据库前缀
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     */
    function decide($authId,$module=MODULE_NAME,$action=ACTION_NAME)
    {
        //决策认证号是否具有当前模块权限
        $db     =   DB::getInstance();
        $sql    =   "select a.id from ".
                    $this->roleTable." as a,".
                    $this->roleUserTable." as b,".
                    $this->roleModuleTable." as c ,".
                    $this->moduleTable." as d ,".
                    $this->roleActionTable." as e ,".
                    $this->actionTable." as f ".
                    "where b.userId={$authId} and b.groupId=a.id and c.groupId=a.id and a.status=1 and c.moduleId=d.id and d.name='".$module."' and e.groupId=a.id and e.actionId=f.id and f.name='".$action."'";
        $rs =   $db->query($sql);
        if($rs->isEmpty()) {
            return false;
        }else {
            return true;
        }
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
    function getModuleAccessList($authId)
    {
        $db     =   DB::getInstance();
        $sql    =   "select d.id,d.name from ".
                    $this->roleTable." as a,".
                    $this->roleUserTable." as b,".
                    $this->roleModuleTable." as c ,".
                    $this->moduleTable." as d ".
                    "where b.userId={$authId} and b.groupId=a.id and c.groupId=a.id and a.status=1 and c.moduleId=d.id and d.status=1";
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
                    $this->roleActionTable." as c ,".
                    $this->actionTable." as d ".
                    "where b.userId={$authId} and b.groupId=a.id and c.groupId=a.id and a.status=1 and  c.actionId=d.id and d.status=1";
        $rs =   $db->query($sql);
        return $rs->getCol('id,name');
    }
}//类定义结束
?>