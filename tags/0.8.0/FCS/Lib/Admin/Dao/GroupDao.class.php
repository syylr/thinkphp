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
 * @package    Dao
 * @link       http://www.fcs.org.cn
 * @copyright  Copyright (c) 2005-2006 liu21st.com.  All rights reserved. 
 * @author     liu21st <liu21st@gmail.com>
 * @version    $Id$
 +------------------------------------------------------------------------------
 */
import('Admin.Dao.FCSAdminDao');
/**
 +------------------------------------------------------------------------------
 * 数据访问类
 +------------------------------------------------------------------------------
 * @package   Dao
 * @author    liu21st <liu21st@gmail.com>
 * @version   $Ver$
 +------------------------------------------------------------------------------
 */
class GroupDao extends FCSAdminDao
{//类定义开始

function getGroupAction($groupId,$actionId) 
{
    $vo        =    $this->find('groupId='.$groupId.' and actionId='.$actionId,
        $this->appPrefix.'_groupAction','status');
    $status = $vo->status;
    if(empty($status)) {
        $status = 0;
    }
    return $status;
}

function setGroupAction($groupId,$actionId) 
{
    $table = $this->appPrefix.'_groupAction';

    $result = $this->db->execute('insert into '.$table.' values('.$groupId.','.$actionId.')');
    if($result===false) {
        return false;
    }else {
        return true;
    }
}

function delGroupAction($groupId) 
{
    $table = $this->appPrefix.'_groupAction';

    $result = $this->db->execute('delete from '.$table.' where groupId='.$groupId);
    if($result===false) {
        return false;
    }else {
        return true;
    }
}

function getGroupActionList($groupId) 
{
    $table = $this->appPrefix.'_groupAction';
    $rs = $this->db->query('select b.id,b.title,b.name from '.$table.' as a ,fcs_action as b where a.actionId=b.id and  a.groupId='.$groupId.' ');
    return $this->rsToVoList($rs,'ActionVo');
}

function setGroupModule($groupId,$moduleId) 
{
    $table = $this->appPrefix.'_groupModule';

    $result = $this->db->execute('insert into '.$table.' values('.$groupId.','.$moduleId.')');
    if($result===false) {
        return false;
    }else {
        return true;
    }
}

function delGroupModule($groupId) 
{
    $table = $this->appPrefix.'_groupModule';

    $result = $this->db->execute('delete from '.$table.' where groupId='.$groupId);
    if($result===false) {
        return false;
    }else {
        return true;
    }
}

function getGroupModuleList($groupId) 
{
    $table = $this->appPrefix.'_groupModule';
    $rs = $this->db->query('select b.id,b.title,b.name from '.$table.' as a ,fcs_module as b where a.moduleId=b.id and  a.groupId='.$groupId.' ');
    return $this->rsToVoList($rs,'ModuleVo');
}

function setGroupUser($groupId,$userId) 
{
    $table = $this->appPrefix.'_groupUser';

    $result = $this->db->execute('insert into '.$table.' values('.$groupId.','.$userId.')');
    if($result===false) {
        return false;
    }else {
        return true;
    }
}

function delGroupUser($groupId) 
{
    $table = $this->appPrefix.'_groupUser';

    $result = $this->db->execute('delete from '.$table.' where groupId='.$groupId);
    if($result===false) {
        return false;
    }else {
        return true;
    }
}

function getGroupUserList($groupId) 
{
    $table = $this->appPrefix.'_groupUser';
    $rs = $this->db->query('select b.id,b.name,b.nickname from '.$table.' as a ,fcs_user as b where a.userId=b.id and  a.groupId='.$groupId.' ');
    return $this->rsToVoList($rs,'UserVo');
}

function setGroupUsers($groupId,$userIdList) 
{
    if(empty($userIdList)) {
        return true;
    }
    $id = implode(',',$userIdList);
    $where = 'a.id ='.$groupId.' AND b.id in('.$id.')';
    $rs = $this->db->execute('INSERT INTO fcs_groupUser (groupId,userId) SELECT a.id, b.id FROM fcs_group a, fcs_user b WHERE '.$where);
    if($result===false) {
        return false;
    }else {
        return true;
    }
}

function setGroupActions($groupId,$actionIdList) 
{
        if(empty($actionIdList)) {
        return true;
    }
    $id = implode(',',$actionIdList);
    $where = 'a.id ='.$groupId.' AND b.id in('.$id.')';
    $rs = $this->db->execute('INSERT INTO fcs_groupAction (groupId,actionId) SELECT a.id, b.id FROM fcs_group a, fcs_action b WHERE '.$where);
    if($result===false) {
        return false;
    }else {
        return true;
    }
}

function setGroupModules($groupId,$moduleIdList) 
{
        if(empty($moduleIdList)) {
        return true;
    }
    $id = implode(',',$moduleIdList);
    $where = 'a.id ='.$groupId.' AND b.id in('.$id.')';
    $rs = $this->db->execute('INSERT INTO fcs_groupModule (groupId,moduleId) SELECT a.id, b.id FROM fcs_group a, fcs_module b WHERE '.$where);
    if($result===false) {
        return false;
    }else {
        return true;
    }
}
}//类定义结束
?>