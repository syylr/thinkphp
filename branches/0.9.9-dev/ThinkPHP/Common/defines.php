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
// $Id$

/**
 +------------------------------------------------------------------------------
 * 系统定义文件
 +------------------------------------------------------------------------------
 * @copyright  Copyright (c) 2005-2006 liu21st.com.  All rights reserved. 
 * @author     liu21st <liu21st@gmail.com>
 * @version    $Id$
 +------------------------------------------------------------------------------
 */

if (!defined('THINK_PATH')) exit();

// PATH_INFO 修正
if(!isset($_SERVER["PATH_INFO"]))
{
	$_SERVER['PATH_INFO'] = "";
}
elseif (empty($_SERVER["PATH_INFO"])) 
{
	// 在FastCGI模式下面 $_SERVER["PATH_INFO"] 为空
	$_SERVER['PATH_INFO'] = str_replace($_SERVER['SCRIPT_NAME'], "", $_SERVER['REQUEST_URI']);
}
if($_SERVER["SERVER_PORT"] == 443)
{
	define('WEB_HOST','https://'.$_SERVER['HTTP_HOST']);
}
else
{
	define('WEB_HOST','http://'.$_SERVER['HTTP_HOST']);
}

// 当前文件名
if(function_exists("apache_lookup_uri")) {
    // Apache 模块方式
    define('_PHP_FILE_',	rtrim($_SERVER["SCRIPT_NAME"],'/'));
}elseif(false !== strpos(php_sapi_name(),'cgi')) {
	//CGI/FASTCGI模式下
    $_temp  = explode('.php',$_SERVER["PHP_SELF"]);
    define('_PHP_FILE_',  rtrim(str_replace($_SERVER["HTTP_HOST"],'',$_temp[0].'.php'),'/'));
}else {
	define('_PHP_FILE_',	rtrim($_SERVER["SCRIPT_NAME"],'/'));
}
// 当前项目名称
if (!defined('APP_NAME')) define('APP_NAME', basename(_PHP_FILE_,'.php'));
// 网站URL根目录
if( strtoupper(APP_NAME) == strtoupper(basename(dirname(_PHP_FILE_))) ) {
    $_root = dirname(dirname(_PHP_FILE_));
}else {
    $_root = dirname(_PHP_FILE_);
}
define('WEB_URL',	(($_root=='/' || $_root=='\\')?'':$_root));

// 目录设置
define('CACHE_DIR',  'Cache'); 
define('HTML_DIR',    'Html'); 
define('CONF_DIR',    'Conf');
define('LIB_DIR',        'Lib');
define('LOG_DIR',      'Logs');
define('LANG_DIR',    'Lang');
define('TEMP_DIR',    'Temp');
define('TMPL_DIR',     'Tpl'); 
// 路径设置
if (!defined('ADMIN_PATH')) define('ADMIN_PATH', APP_PATH.'/../Admin/');
define('TMPL_PATH',APP_PATH.'/'.TMPL_DIR.'/'); 
define('HTML_PATH',APP_PATH.'/'.HTML_DIR.'/'); //
define('LIB_PATH',         APP_PATH.'/'.LIB_DIR.'/'); //
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
define('IS_LINUX',strstr(PHP_OS, 'Linux') ? 1 : 0 );
define('IS_FREEBSD',strstr(PHP_OS, 'FreeBSD') ? 1 : 0 );
if(version_compare(PHP_VERSION, '5.0.0', '>')){
	define('IS_PHP5',true);
	define('IS_PHP4',false);
}elseif(version_compare(PHP_VERSION, '4.3.0', '>')){
	define('IS_PHP4',true);
	define('IS_PHP5',false);
}else{
	// 不支持PHP4.3.0以下版本
}
define('NOW',time() );

// 	调试和Log设置
define('WEB_LOG_ERROR',0);
define('WEB_LOG_DEBUG',1);
define('SYSTEM_LOG',0);
define('MAIL_LOG',1);
define('TCP_LOG',2);
define('FILE_LOG',3);

define('DATA_TYPE_VO',1);
define('DATA_TYPE_ARRAY',0);

// 是否使用惯例配置
define('CONVENTION_CONFIG',true);

//	版本信息
define('THINK_VERSION', '0.9.8');
?>