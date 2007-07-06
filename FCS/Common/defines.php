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
 * 系统定义文件
 +------------------------------------------------------------------------------
 * @package    common
 * @link       http://www.fcs.org.cn
 * @copyright  Copyright (c) 2005-2006 liu21st.com.  All rights reserved. 
 * @author     liu21st <liu21st@gmail.com>
 * @version    $Id: defines.php 96 2006-11-12 08:14:31Z fcs $
 +------------------------------------------------------------------------------
 */

if (!defined('FCS_PATH')) exit();

// 当前文件名
if(false === strpos(php_sapi_name(),'cgi')) {
    // Apache 模块方式
    define('_PHP_FILE_',	rtrim($_SERVER["SCRIPT_NAME"],'/'));
}else {
	//CGI/FASTCGI模式下
    $_temp  = explode('.php',$_SERVER["PHP_SELF"]);
    define('_PHP_FILE_',  rtrim(str_replace($_SERVER["HTTP_HOST"],'',$_temp[0].'.php'),'/'));
}

// 当前项目名称
if (!defined('APP_NAME')) define('APP_NAME', basename(_PHP_FILE_,'.php'));
// 网站URL根目录
if( strtoupper(APP_NAME) == strtoupper(basename(dirname(_PHP_FILE_))) ) {
    $_root = dirname(dirname(_PHP_FILE_));
}else {
    $_root = dirname(_PHP_FILE_);
}
define('WEB_URL',	($_root=='/' || $_root=='\\')?'':$_root);

// 目录设置
define('CACHE_DIR',  'Cache'); 
define('HTML_DIR',    'Html'); 
define('CONF_DIR',    'Conf');
define('LIB_DIR',        'Lib');
define('LOG_DIR',      'Logs');
define('LANG_DIR',    'Lang');
define('TEMP_DIR',    'Temp');
define('TAGS_DIR',    'Tags');

// 路径设置
define('LIB_PATH',         APP_PATH.'/'.LIB_DIR.'/'); //
define('TAG_PATH',       APP_PATH.'/'.TAGS_DIR.'/'); //
define('CACHE_PATH',   APP_PATH.'/'.CACHE_DIR.'/'); //
define('CONFIG_PATH',  APP_PATH.'/'.CONF_DIR.'/'); //
define('LOG_PATH',       APP_PATH.'/'.LOG_DIR.'/'); //
define('LANG_PATH',     APP_PATH.'/'.LANG_DIR.'/'); //
define('TEMP_PATH',      APP_PATH.'/'.TEMP_DIR.'/'); //
define('UPLOAD_PATH', APP_PATH.'/Uploads/'); //
define('PLUGIN_PATH', APP_PATH.'/PlugIns/'); //

//	 系统信息
@set_magic_quotes_runtime (0);
define('MAGIC_QUOTES_GPC',get_magic_quotes_gpc()?True:False);
define('OUTPUT_GZIP_ON',ini_get('output_handler') || ini_get('zlib.output_compression') );
define('MEMORY_LIMIT_ON',function_exists('memory_get_usage')?TRUE:FALSE);
define('IS_APACHE',strstr($_SERVER['SERVER_SOFTWARE'], 'Apache') || strstr($_SERVER['SERVER_SOFTWARE'], 'LiteSpeed') );
define('IS_IIS',strstr($_SERVER['SERVER_SOFTWARE'], 'Microsoft-IIS') ? 1 : 0);
define('IS_WIN',strstr(PHP_OS, 'WIN') ? 1 : 0 );
define('IS_UNIX',strstr(PHP_OS, 'WIN') ? 0 : 1 );


// 	调试和Log设置
define('WEB_LOG_ERROR',0);
define('WEB_LOG_DEBUG',1);
define('SYSTEM_LOG',0);
define('MAIL_LOG',1);
define('TCP_LOG',2);
define('FILE_LOG',3);

include_once('version.php');
?>