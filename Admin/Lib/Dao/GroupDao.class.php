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
// $Id: GroupDao.class.php 142 2007-06-15 03:28:16Z liu21st $

import('@.Dao.AdminDao');

/**
 +------------------------------------------------------------------------------
 * 权限数据访问
 +------------------------------------------------------------------------------
 * @author liu21st <liu21st@gmail.com>
 * @version  $Id: GroupDao.class.php 142 2007-06-15 03:28:16Z liu21st $
 +------------------------------------------------------------------------------
 */
class GroupDao extends Dao
{//类定义开始


function setGroupApp($groupId,$appId) 
{
    $table = DB_PREFIX.'_access';

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
    $rs = $this->db->execute('INSERT INTO '.DB_PREFIX.'_access (groupId,nodeId,parentNodeId,level) SELECT a.id, b.id,b.pid,b.level FROM '.DB_PREFIX.'_group a, '.DB_PREFIX.'_node b WHERE '.$where);
    if($result===false) {
        return false;
    }else {
        return true;
    }
}


function delGroupApp($groupId) 
{
    $table = DB_PREFIX.'_access';

    $result = $this->db->execute('delete from '.$table.' where level=1 and groupId='.$groupId);
    if($result===false) {
        return false;
    }else {
        return true;
    }
}

function delGroupAction($groupId,$moduleId) 
{
    $table = DB_PREFIX.'_access';

    $result = $this->db->execute('delete from '.$table.' where level=3 and parentNodeId='.$moduleId.' and groupId='.$groupId);
    if($result===false) {
        return false;
    }else {
        return true;
    }
}

function getGroupActionList($groupId,$moduleId) 
{
    $table = DB_PREFIX.'_access';
    $rs = $this->db->query('select b.id,b.title,b.name from '.$table.' as a ,'.DB_PREFIX.'_node as b where a.nodeId=b.id and  b.pid='.$moduleId.' and  a.groupId='.$groupId.' ');
    return $this->rsToVoList($rs,'NodeVo');
}


function delGroupModule($groupId,$appId) 
{
    $table = DB_PREFIX.'_access';

    $result = $this->db->execute('delete from '.$table.' where level=2 and parentNodeId='.$appId.' and groupId='.$groupId);
    if($result===false) {
        return false;
    }else {
        return true;
    }
}

function getGroupAppList($groupId) 
{
    $table = DB_PREFIX.'_access';
    $rs = $this->db->query('select b.id,b.title,b.name from '.$table.' as a ,'.DB_PREFIX.'_node as b where a.nodeId=b.id and  b.pid=0 and a.groupId='.$groupId.' ');
    return $this->rsToVoList($rs,'NodeVo');
}

function getGroupModuleList($groupId,$appId) 
{
    $table = DB_PREFIX.'_access';
    $rs = $this->db->query('select b.id,b.title,b.name from '.$table.' as a ,'.DB_PREFIX.'_node as b where a.nodeId=b.id and  b.pid='.$appId.' and a.groupId='.$groupId.' ');
    return $this->rsToVoList($rs,'NodeVo');
}

function setGroupUser($groupId,$userId) 
{
    $table = DB_PREFIX.'_groupuser';

    $result = $this->db->execute('insert into '.$table.' values('.$groupId.','.$userId.')');
    if($result===false) {
        return false;
    }else {
        return true;
    }
}

function delGroupUser($groupId) 
{
    $table = DB_PREFIX.'_groupUser';

    $result = $this->db->execute('delete from '.$table.' where groupId='.$groupId);
    if($result===false) {
        return false;
    }else {
        return true;
    }
}

function getGroupUserList($groupId) 
{
    $table = DB_PREFIX.'_groupUser';
    $rs = $this->db->query('select b.id,b.name,b.nickname from '.$table.' as a ,'.DB_PREFIX.'_user as b where a.userId=b.id and  a.groupId='.$groupId.' ');
    return $this->rsToVoList($rs,'UserVo');
}

function setGroupUsers($groupId,$userIdList) 
{
    if(empty($userIdList)) {
        return true;
    }
    $id = implode(',',$userIdList);
    $where = 'a.id ='.$groupId.' AND b.id in('.$id.')';
    $rs = $this->db->execute('INSERT INTO '.DB_PREFIX.'_groupuser (groupId,userId) SELECT a.id, b.id FROM '.DB_PREFIX.'_group a, '.DB_PREFIX.'_user b WHERE '.$where);
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
    $rs = $this->db->execute('INSERT INTO '.DB_PREFIX.'_access (groupId,nodeId,parentNodeId,level) SELECT a.id, b.id,b.pid,b.level FROM '.DB_PREFIX.'_group a, '.DB_PREFIX.'_node b WHERE '.$where);
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
    $rs = $this->db->execute('INSERT INTO '.DB_PREFIX.'_access (groupId,nodeId,parentNodeId,level) SELECT a.id, b.id,b.pid,b.level FROM '.DB_PREFIX.'_group a, '.DB_PREFIX.'_node b WHERE '.$where);
    if($result===false) {
        return false;
    }else {
        return true;
    }
}
}//类定义结束
?>