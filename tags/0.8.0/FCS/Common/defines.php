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
 * 系统配置文件
 +------------------------------------------------------------------------------
 * @package    Conf
 * @link       http://www.fcs.org.cn
 * @copyright  Copyright (c) 2005-2006 liu21st.com.  All rights reserved. 
 * @author     liu21st <liu21st@gmail.com>
 * @version    $Id$
 +------------------------------------------------------------------------------
 */

if (!defined('FCS_PATH')) {
	exit();
}

//+----------------------------------------
//|	路径参数设置
//+----------------------------------------
//网站访问目录 
define('WEB_URL',	rtrim('http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER["SCRIPT_NAME"]),'/'));

//当前文件名 即应用程序名称
//注意：在CGI/FASTCGI模式下工作不正常
if (!defined('APP_NAME')) define('APP_NAME', basename($_SERVER["SCRIPT_NAME"],'.php'));

//目录设置
define('CACHE_DIR','Cache'); 
define('HTML_DIR','Html'); 
define('CONF_DIR','Conf');
define('LIB_DIR','Lib');
define('LOG_DIR','Logs');
define('LANG_DIR','Lang');
define('TEMP_DIR','Temp');
define('TAGS_DIR','Tags');

//路径设置
define('LIB_PATH',FCS_PATH.'/'.LIB_DIR.'/'); //
define('TAG_PATH',FCS_PATH.'/'.TAGS_DIR.'/'); //
define('CACHE_PATH',FCS_PATH.'/'.CACHE_DIR.'/'); //
define('HTML_PATH',FCS_PATH.'/'.HTML_DIR.'/'); //
define('CONFIG_PATH',FCS_PATH.'/'.CONF_DIR.'/'); //
define('LOG_PATH',FCS_PATH.'/'.LOG_DIR.'/'.APP_NAME.'/'); //
define('LANG_PATH',FCS_PATH.'/'.LANG_DIR.'/'); //
define('TEMP_PATH',FCS_PATH.'/'.TEMP_DIR.'/'.APP_NAME.'/'); //
define('CLIENT_PATH',FCS_PATH.'/Client/'); //
define('UPLOAD_PATH',FCS_PATH.'/Uploads/'); //
//+----------------------------------------
//|	版本信息
//+----------------------------------------
define('FCS_VERSION', '0.8.0');
define('MAGIC_QUOTES_GPC',get_magic_quotes_gpc()?True:False);
//+----------------------------------------
//|	调试和Log设置
//+----------------------------------------
define('WEB_LOG_ERROR',0);
define('WEB_LOG_DEBUG',1);
define('SYSTEM_LOG',0);
define('MAIL_LOG',1);
define('TCP_LOG',2);
define('FILE_LOG',3);

?>