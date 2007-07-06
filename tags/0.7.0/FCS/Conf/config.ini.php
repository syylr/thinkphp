<?php 
/*
+---------------------------------------------------------+
| 项目: FCS -- Fast,Compatible & Simple OOP PHP Framework |
| 文件: config.ini.php									  |
| 功能: FCS系统配置文件									  |
+---------------------------------------------------------+
| 本框架代码基于GPL协议，可免费使用，但必须保留版权信息	  |
| 版权声明: Copyright◎ 2005-2006 世纪流年 版权所有		  |
| 主 页:	http://www.liu21st.com						  |
| 作 者:	Liu21st <流年> liu21st@gmail.com			  |
+---------------------------------------------------------+
*/
if (!defined('FCS_PATH')) {
	exit();
}

//+----------------------------------------
//|	路径参数设置
//+----------------------------------------
//网站访问目录 
define('WEB_URL',	'http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER["SCRIPT_NAME"]));
//当前文件名 即应用程序名称
//注意：在CGI/FASTCGI模式下工作不正常
if (!defined('APP_NAME')) define('APP_NAME', basename($_SERVER["SCRIPT_NAME"],'.php'));
//目录设置
define('CACHE_DIR','Cache'); 
define('CONF_DIR','Conf');
define('LIB_DIR','Lib');
define('LOG_DIR','Logs');


//路径设置
define('LIB_PATH',FCS_PATH.'/'.LIB_DIR.'/'); //
define('CACHE_PATH',FCS_PATH.'/'.CACHE_DIR.'/'); //
define('CONFIG_PATH',FCS_PATH.'/'.CONF_DIR.'/'); //
define('LOG_PATH',FCS_PATH.'/'.LOG_DIR.'/'.APP_NAME.'/'); //

//+----------------------------------------
//|	版本信息
//+----------------------------------------
define('FCS_VERSION', '0.7.0');
define('MAGIC_QUOTES_GPC',get_magic_quotes_gpc()?True:False);
//+----------------------------------------
//|	调试和Log设置
//+----------------------------------------
define('WEB_LOG_RECORD',true);
define('WEB_LOG_ERROR',0);
define('WEB_LOG_DEBUG',1);
define('SYSTEM_LOG',0);
define('MAIL_LOG',1);
define('TCP_LOG',2);
define('FILE_LOG',3);

?>