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
 * @version    $Id$
 +------------------------------------------------------------------------------
 */

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
            $showImg    = '<IMG SRC="'.WEB_URL.'/'.TMPL_DIR.'/'.TEMPLATE_NAME.'/'.APP_NAME.'/Public/images/locked.gif" WIDTH="20" HEIGHT="20" BORDER="0" ALT="禁用">';
            break;
    	case 2:
            $showText   = '保护';
            $showImg    = '<IMG SRC="'.WEB_URL.'/'.TMPL_DIR.'/'.TEMPLATE_NAME.'/'.APP_NAME.'/Public/images/prected.gif" WIDTH="20" HEIGHT="20" BORDER="0" ALT="保护">';
            break;
        case 1:
        default:
            $showText   =   '正常';
            $showImg    =   '<IMG SRC="'.WEB_URL.'/'.TMPL_DIR.'/'.TEMPLATE_NAME.'/'.APP_NAME.'/Public/images/ok.gif" WIDTH="20" HEIGHT="20" BORDER="0" ALT="正常">';
            
    }
    return ($imageShow===true)? auto_charset($showImg) : $showText;

}




function getUserName($userId) 
{
	if(Session::is_set('userName')) {
		$name	=	Session::get('userName');
		return $name[$userId];
	}
	import('Admin.Dao.UserDao');
	$dao	=	new UserDao();
	$list	=	$dao->findAll('','','id,nickname');
	$nameList	=	$list->getCol('id,nickname');
	$name	=	$nameList[$userId];
	Session::set('userName',$nameList);
    return $name;
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
		case 'doc': $icon = '<IMG SRC="'.WEB_URL.'/'.TMPL_DIR.'/'.TEMPLATE_NAME.'/'.APP_NAME.'/Public/images/ok.gif" BORDER="0" align="absmiddle" ALT="">';break;
	default:
		$icon = '<IMG SRC="'.WEB_URL.'/'.TMPL_DIR.'/'.TEMPLATE_NAME.'/'.APP_NAME.'/Public/images/ok.gif" align="absmiddle" BORDER="0" ALT="">';
	}
	return $icon;
}

function getMessageStatus($status) 
{
	switch($status) {
		case 2: $icon = '<IMG SRC="'.WEB_URL.'/'.TMPL_DIR.'/'.TEMPLATE_NAME.'/'.APP_NAME.'/Public/images/hasread.gif" BORDER="0"  ALT="已读">';break;
	default:
		$icon = '<IMG SRC="'.WEB_URL.'/'.TMPL_DIR.'/'.TEMPLATE_NAME.'/'.APP_NAME.'/Public/images/newmessage.gif" BORDER="0" ALT="未读">';
	}
	return $icon;
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
function getModule($name) 
{
	if(Session::is_set('module')) {
		$module	=	Session::get('module');
		return $module[$name];
	}
	import('Admin.Dao.ModuleDao');
	$dao	=	new ModuleDao();
	$moduleList	=	$dao->findAll('','','name,title');
	$module	=	$moduleList->getCol('name,title');
	$title	=	$module[$name];
	Session::set('module',$module);
    return $title;

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
function getAction($name) 
{
	if(Session::is_set('action')) {
		$action	=	Session::get('action');
		return $action[$name];
	}
	import('Admin.Dao.ActionDao');
	$dao	=	new ActionDao();
	$actionList	=	$dao->findAll('','','name,title');
	$action	=	$actionList->getCol('name,title');
	$title	=	$action[$name];
	Session::set('action',$action);
    return $title;

}

function getUser($id) 
{
	if(Session::is_set('user')) {
		$user	=	Session::get('user');
		return $user[$id];
	}
	import('Admin.Dao.UserDao');
	$dao	=	new UserDao();
	$userList	=	$dao->findAll('','','id,name,nickname');
	$user	=	$userList->getCol('id,name');
	$name	=	$user[$id];
	Session::set('user',$user);
    return $name;	
}
?>