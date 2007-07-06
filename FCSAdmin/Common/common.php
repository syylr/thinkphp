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
 * Admin模块自定义函数
 +------------------------------------------------------------------------------
 * @package    Common
 * @link       http://www.fcs.org.cn
 * @copyright  Copyright (c) 2005-2006 liu21st.com.  All rights reserved. 
 * @author     liu21st <liu21st@gmail.com>
 * @version    $Id: common.php 73 2006-11-08 10:08:01Z fcs $
 +------------------------------------------------------------------------------
 */
    function getAppId($appName=APP_NAME) 
    {
        if(Session::is_set('app')) {
            $app	=	Session::get('app');
            return $app[$appName];
        }
    	import('@.Dao.NodeDao');
        $dao = new NodeDao();
        $appList = $dao->findAll('level=1');
        $app = $appList->getCol('name,id');
        $appName = $app[$appName];
        Session::set('app',$app);
        return $appName;

    }
/**
 +----------------------------------------------------------
 * 取得状态显示 
 * 
 +----------------------------------------------------------
 * @param integer $status 数据库中存储的状态值
 * @param integer $imageShow 是否显示图形 默认显示
 +----------------------------------------------------------
 * @return string
 +----------------------------------------------------------
 */
function getStatus($status,$imageShow=true) 
{
    switch($status) {
    	case 0:
            $showText   = '禁用';
            $showImg    = '<IMG SRC="'.APP_PUBLIC_URL.'/images/locked.gif" WIDTH="20" HEIGHT="20" BORDER="0" ALT="禁用">';
            break;
    	case 2:
            $showText   = '保护';
            $showImg    = '<IMG SRC="'.APP_PUBLIC_URL.'/images/prected.gif" WIDTH="20" HEIGHT="20" BORDER="0" ALT="保护">';
            break;
        case 1:
        default:
            $showText   =   '正常';
            $showImg    =   '<IMG SRC="'.APP_PUBLIC_URL.'/images/ok.gif" WIDTH="20" HEIGHT="20" BORDER="0" ALT="正常">';
            
    }
    return ($imageShow===true)? auto_charset($showImg) : $showText;

}



/**
 +----------------------------------------------------------
 * 取得充值卡类型显示 
 * 
 +----------------------------------------------------------
 * @param integer $typeId 充值卡类型id
 +----------------------------------------------------------
 * @return string
 +----------------------------------------------------------
 */
function getUserType($typeId) 
{
	if(Session::is_set('userType')) {
		$type	=	Session::get('userType');
		return $type[$typeId];
	}
	import('@.Dao.UserTypeDao');
	$dao	=	new UserTypeDao();
	$typeList	=	$dao->findAll('','','id,name,status');
	$type	=	$typeList->getCol('id,name');
	$name	=	$type[$typeId];
	Session::set('userType',$type);
    return $name;

}

function getGroupName($id) 
{
    if($id==0) {
    	return '无上级组';
    }
	if(Session::is_set('groupName')) {
		$name	=	Session::get('groupName');
		return $name[$id];
	}
	import('@.Dao.GroupDao');
	$dao	=	new GroupDao();
	$list	=	$dao->findAll('','','id,name');
	$nameList	=	$list->getCol('id,name');
	$name	=	$nameList[$id];
	Session::set('groupName',$nameList);
    return $name;

}


function getUserName($userId) 
{
	if(Session::is_set('userName')) {
		$name	=	Session::get('userName');
		return $name[$userId];
	}
	import('@.Dao.UserDao');
	$dao	=	new UserDao();
	$list	=	$dao->findAll('','','id,nickname');
	$nameList	=	$list->getCol('id,nickname');
	$name	=	$nameList[$userId];
	Session::set('userName',$nameList);
    return $name;

}

/**
 +----------------------------------------------------------
 * 取得标题的截断显示 默认截断长度为12 个字 
 * 
 +----------------------------------------------------------
 * @param string $title 标题
 * @param integer $length 截断长度 默认12
 +----------------------------------------------------------
 * @return string
 +----------------------------------------------------------
 */
function getShortTitle($title,$length=12) 
{
    return msubstr ($title,0,$length,OUTPUT_CHARSET);
}

function getUser($id) 
{
	if(Session::is_set('user')) {
		$user	=	Session::get('user');
		return $user[$id];
	}
	import('@.Dao.UserDao');
	$dao	=	new UserDao();
	$userList	=	$dao->findAll('','','id,name,nickname');
	$user	=	$userList->getCol('id,name');
	$name	=	$user[$id];
	Session::set('user',$user);
    return $name;	
}

/**
 +----------------------------------------------------------
 * 字节格式化 把字节数格式为 B K M G T 描述的大小
 +----------------------------------------------------------
 * @return string
 +----------------------------------------------------------
 */
function byte_format($input, $dec=0) 
{ 
  $prefix_arr = array("B", "K", "M", "G", "T"); 
  $value = round($input, $dec); 
  $i=0; 
  while ($value>1024) 
  { 
     $value /= 1024; 
     $i++; 
  } 
  $return_str = round($value, $dec).$prefix_arr[$i]; 
  return $return_str; 
}
/**
 +----------------------------------------------------------
 * 取得下载图标显示 
 * 
 +----------------------------------------------------------
 * @param integer $type 下载文件类型
 +----------------------------------------------------------
 * @return string
 +----------------------------------------------------------
 */
function getFileIcon($type) 
{
	switch($type) {
		case 'doc': $icon = '<IMG SRC="'.APP_PUBLIC_URL.'/images/ok.gif" BORDER="0" align="absmiddle" ALT="">';break;
	default:
		$icon = '<IMG SRC="'.APP_PUBLIC_URL.'/images/ok.gif" align="absmiddle" BORDER="0" ALT="">';
	}
	return $icon;
}
?>