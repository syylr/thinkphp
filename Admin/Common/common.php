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

function toDate($time,$format='Y年m月d日 H:i:s') 
{
	if( empty($time)) {
		return '';
	}
    $format = str_replace('#',':',$format);
	return date(auto_charset($format),$time);
}


    function s2m($fSecond,$min=false){
        if(!$min) { // 显示小时
            $secs = sprintf("%02d",$fSecond%60);
            $mins   =  sprintf("%02d",floor($fSecond/60)%60);
            $hours = sprintf("%02d",floor($fSecond/60)/60);
            return $hours.':'.$mins.':'.$secs;        	
        }else { // 显示分钟
            $minute=floor($fSecond/60)."分钟".(sprintf("%02d",$fSecond%60))."秒";
            return $minute;        	
        }


    }
/**
 +----------------------------------------------------------
 * UBB 解析
 +----------------------------------------------------------
 * @return string
 +----------------------------------------------------------
 */
    function ubb($Text) { 
      $Text=trim($Text);
      //$Text=htmlspecialchars($Text);  
      //$Text=ereg_replace("\n","<br>",$Text); 
      $Text=preg_replace("/\\t/is","  ",$Text); 
      $Text=preg_replace("/\[h1\](.+?)\[\/h1\]/is","<h1>\\1</h1>",$Text); 
      $Text=preg_replace("/\[h2\](.+?)\[\/h2\]/is","<h2>\\1</h2>",$Text); 
      $Text=preg_replace("/\[h3\](.+?)\[\/h3\]/is","<h3>\\1</h3>",$Text); 
      $Text=preg_replace("/\[h4\](.+?)\[\/h4\]/is","<h4>\\1</h4>",$Text); 
      $Text=preg_replace("/\[h5\](.+?)\[\/h5\]/is","<h5>\\1</h5>",$Text); 
      $Text=preg_replace("/\[h6\](.+?)\[\/h6\]/is","<h6>\\1</h6>",$Text); 
      $Text=preg_replace("/\[center\](.+?)\[\/center\]/is","<center>\\1</center>",$Text); 
      $Text=preg_replace("/\[url\](http:\/\/.+?)\[\/url\]/is","<a href=\\1>\\1</a>",$Text); 
      $Text=preg_replace("/\[url\](.+?)\[\/url\]/is","<a href=\"http://\\1\">http://\\1</a>",$Text); 
      $Text=preg_replace("/\[url=(http:\/\/.+?)\](.*)\[\/url\]/is","<a href=\\1>\\2</a>",$Text); 
      $Text=preg_replace("/\[url=(.+?)\](.*)\[\/url\]/is","<a href=http://\\1>\\2</a>",$Text); 
      $Text=preg_replace("/\[img\](.+?)\[\/img\]/is","<img src=\\1>",$Text); 
      $Text=preg_replace("/\[color=(.+?)\](.+?)\[\/color\]/is","<font color=\\1>\\2</font>",$Text); 
      $Text=preg_replace("/\[size=(.+?)\](.+?)\[\/size\]/is","<font size=\\1>\\2</font>",$Text); 
      $Text=preg_replace("/\[sup\](.+?)\[\/sup\]/is","<sup>\\1</sup>",$Text); 
      $Text=preg_replace("/\[sub\](.+?)\[\/sub\]/is","<sub>\\1</sub>",$Text); 
      $Text=preg_replace("/\[pre\](.+?)\[\/pre\]/is","<pre>\\1</pre>",$Text); 
      $Text=preg_replace("/\[email\](.+?)\[\/email\]/is","<a href='mailto:\\1'>\\1</a>",$Text); 
      $Text=preg_replace("/\[i\](.+?)\[\/i\]/is","<i>\\1</i>",$Text); 
      $Text=preg_replace("/\[u\](.+?)\[\/u\]/is","<u>\\1</u>",$Text); 
      $Text=preg_replace("/\[b\](.+?)\[\/b\]/is","<b>\\1</b>",$Text); 
      $Text=preg_replace("/\[quote\](.+?)\[\/quote\]/is","<blockquote><font size='1' face='Courier New'>quote:</font><hr>\\1<hr></blockquote>", $Text); 
      $Text=preg_replace("/\[code\](.+?)\[\/code\]/eis","highlight_code('\\1')", $Text); 
      $Text=preg_replace("/\[php\](.+?)\[\/php\]/eis","highlight_code('\\1')", $Text); 
      $Text=preg_replace("/\[sig\](.+?)\[\/sig\]/is","<div style='text-align: left; color: darkgreen; margin-left: 5%'><br><br>--------------------------<br>\\1<br>--------------------------</div>", $Text); 
      return $Text; 
    }

function readFileList($filename) 
{
    $file = base64_encode($filename);
    $name  =  auto_charset(basename($filename),'gb2312','utf-8');
    if(is_dir($filename)) {
    	$click = "readDir(\"$file\")";
         return "<a href='".__URL__.'/index/f/'.$file."'>".$name."</a>";
    }else if(is_file($filename)) {
        $writeable =  is_writable($filename);
    	$click = "readFile(\"$file\",\"$writeable\")";
        return "<a href='javascript:void(0)' onclick='$click'>".$name."</a>";
    }
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
 +------------------------------------------------------------------------------
 * Admin模块自定义函数
 +------------------------------------------------------------------------------
 * @package    Common
 * @link       http://www.fcs.org.cn
 * @copyright  Copyright (c) 2005-2006 liu21st.com.  All rights reserved. 
 * @author     liu21st <liu21st@gmail.com>
 * @version    $Id: common.php 83 2007-04-01 11:03:37Z liu21st $
 +------------------------------------------------------------------------------
 */
    function getAppId($appName=APP_NAME) 
    {
        if(Session::is_set($appName.'_id')){
        	return Session::get($appName.'_id');
        }
        $dao = D("Node"); 
        $app = $dao->getBy("name",$appName,'','id');
        Session::set($appName.'_id',$app->id);
        return $app->id;

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

function getPluginNameUri($pluginId) 
{
    $dao = D("PlugIn");
    $vo  = $dao->find('id="'.$pluginId.'"','','id,uri,name');
    $uri  =  '<a href="'.$vo->uri.'" target="_blank" >'.$vo->name.'</a>';
    return $uri;
}

function getUserType($typeId) 
{
	if(Session::is_set('userType')) {
		$type	=	Session::get('userType');
		return $type[$typeId];
	}
	$dao	=	D("UserType");
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
	$dao	=	D("Group");
	$list	=	$dao->findAll('','','id,name');
	$nameList	=	$list->getCol('id,name');
	$name	=	$nameList[$id];
	Session::set('groupName',$nameList);
    return $name;

}

function getUserName($userId) 
{
    if($userId==0) {
    	return '';
    }
	if(Session::is_set('userName')) {
		$name	=	Session::get('userName');
		return $name[$userId];
	}
	$dao	=	D("User");
	$list	=	$dao->findAll('','','id,nickname');
	$nameList	=	$list->getCol('id,nickname');
	$name	=	$nameList[$userId];
	Session::set('userName',$nameList);
    return $name;
}

function dateDiff($date1,$date2) 
{
	import('ORG.Date.Date');
	$date	=	new Date(intval($date1));
	$return	=	$date->dateDiff($date2);
	if($return<=1) {
		$return =	'今天';
	}elseif($return <=2) {
		$return = '昨天';
	}elseif($return <=7) {
		$return = $date->cWeekday;
	}
	elseif($return>7 && $return <14) {
		$return = '上周';
	}
	else {
		$return = '两周前';
	}
	return $return;
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

?>