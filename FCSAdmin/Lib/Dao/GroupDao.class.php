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
 * @version    $Id: GroupDao.class.php 73 2006-11-08 10:08:01Z fcs $
 +------------------------------------------------------------------------------
 */

/**
 +------------------------------------------------------------------------------
 * 数据访问类
 +------------------------------------------------------------------------------
 * @package   Dao
 * @author    liu21st <liu21st@gmail.com>
 * @version   $Ver$
 +------------------------------------------------------------------------------
 */
class GroupDao extends Dao
{//类定义开始


function setGroupApp($groupId,$appId) 
{
    $table = $this->appPrefix.'_access';

    $result = $this->db->execute('insert into '.$table.' values('.$groupId.','.$actionId.')');
    if($result===false) {
        return false;
    }else {
        return true;
    }
}
function setGroupApps($groupId,$appIdList) 
{
        if(empty($appIdList)) {
        return true;
    }
    $id = implode(',',$appIdList);
    $where = 'a.id ='.$groupId.' AND b.id in('.$id.')';
    $rs = $this->db->execute('INSERT INTO '.$this->appPrefix.'_access (groupId,nodeId,parentNodeId,level) SELECT a.id, b.id,b.parentId,b.level FROM '.$this->appPrefix.'_group a, '.$this->appPrefix.'_node b WHERE '.$where);
    if($result===false) {
        return false;
    }else {
        return true;
    }
}


function delGroupApp($groupId) 
{
    $table = $this->appPrefix.'_access';

    $result = $this->db->execute('delete from '.$table.' where level=1 and groupId='.$groupId);
    if($result===false) {
        return false;
    }else {
        return true;
    }
}

function delGroupAction($groupId,$moduleId) 
{
    $table = $this->appPrefix.'_access';

    $result = $this->db->execute('delete from '.$table.' where level=3 and parentNodeId='.$moduleId.' and groupId='.$groupId);
    if($result===false) {
        return false;
    }else {
        return true;
    }
}

function getGroupActionList($groupId,$moduleId) 
{
    $table = $this->appPrefix.'_access';
    $rs = $this->db->query('select b.id,b.title,b.name from '.$table.' as a ,'.$this->appPrefix.'_node as b where a.nodeId=b.id and  b.parentId='.$moduleId.' and  a.groupId='.$groupId.' ');
    return $this->rsToVoList($rs,'NodeVo');
}


function delGroupModule($groupId,$appId) 
{
    $table = $this->appPrefix.'_access';

    $result = $this->db->execute('delete from '.$table.' where level=2 and parentNodeId='.$appId.' and groupId='.$groupId);
    if($result===false) {
        return false;
    }else {
        return true;
    }
}

function getGroupAppList($groupId) 
{
    $table = $this->appPrefix.'_access';
    $rs = $this->db->query('select b.id,b.title,b.name from '.$table.' as a ,'.$this->appPrefix.'_node as b where a.nodeId=b.id and  b.parentId=0 and a.groupId='.$groupId.' ');
    return $this->rsToVoList($rs,'NodeVo');
}

function getGroupModuleList($groupId,$appId) 
{
    $table = $this->appPrefix.'_access';
    $rs = $this->db->query('select b.id,b.title,b.name from '.$table.' as a ,'.$this->appPrefix.'_node as b where a.nodeId=b.id and  b.parentId='.$appId.' and a.groupId='.$groupId.' ');
    return $this->rsToVoList($rs,'NodeVo');
}

function setGroupUser($groupId,$userId) 
{
    $table = $this->appPrefix.'_groupuser';

    $result = $this->db->execute('insert into '.$table.' values('.$groupId.','.$userId.')');
    if($result===false) {
        return false;
    }else {
        return true;
    }
}

function delGroupUser($groupId) 
{
    $table = $this->appPrefix.'_groupuser';

    $result = $this->db->execute('delete from '.$table.' where groupId='.$groupId);
    if($result===false) {
        return false;
    }else {
        return true;
    }
}

function getGroupUserList($groupId) 
{
    $table = $this->appPrefix.'_groupuser';
    $rs = $this->db->query('select b.id,b.name,b.nickname from '.$table.' as a ,'.$this->appPrefix.'_user as b where a.userId=b.id and  a.groupId='.$groupId.' ');
    return $this->rsToVoList($rs,'UserVo');
}

function setGroupUsers($groupId,$userIdList) 
{
    if(empty($userIdList)) {
        return true;
    }
    $id = implode(',',$userIdList);
    $where = 'a.id ='.$groupId.' AND b.id in('.$id.')';
    $rs = $this->db->execute('INSERT INTO '.$this->appPrefix.'_groupuser (groupId,userId) SELECT a.id, b.id FROM '.$this->appPrefix.'_group a, '.$this->appPrefix.'_user b WHERE '.$where);
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
    $rs = $this->db->execute('INSERT INTO '.$this->appPrefix.'_access (groupId,nodeId,parentNodeId,level) SELECT a.id, b.id,b.parentId,b.level FROM '.$this->appPrefix.'_group a, '.$this->appPrefix.'_node b WHERE '.$where);
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
    $rs = $this->db->execute('INSERT INTO '.$this->appPrefix.'_access (groupId,nodeId,parentNodeId,level) SELECT a.id, b.id,b.parentId,b.level FROM '.$this->appPrefix.'_group a, '.$this->appPrefix.'_node b WHERE '.$where);
    if($result===false) {
        return false;
    }else {
        return true;
    }
}
}//类定义结束
?>