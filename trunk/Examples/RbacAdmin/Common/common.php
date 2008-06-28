<?php 
// +----------------------------------------------------------------------
// | ThinkPHP                                                             
// +----------------------------------------------------------------------
// | Copyright (c) 2008 http://thinkphp.cn All rights reserved.      
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>                                  
// +----------------------------------------------------------------------
// $Id$

function toDate($time,$format='Y-m-d H:i:s') 
{
	if( empty($time)) {
		return '';
	}
    $format = str_replace('#',':',$format);
	return date(auto_charset($format),$time);
}

function getStatus($status) 
{
	return $status=="1"?"<span style='color:blue'>启用</span>":"<span style='color:red'>禁用</span>";
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
	$Group	=	D("Group");
	$list	=	$Group->getFields('id,name');
	$name	=	$list[$id];
	Session::set('groupName',$list);
    return $name;
}
?>