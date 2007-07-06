<?php 
/*
+--------------------------------------------------------
| 项目: FCS -- Fast,Compatible & Simple OOP PHP Framework 
| 版本: 0.6.1
| PHP:	4.3.0 以上
| 文件: config.php
| 功能:  配置文件
| 最后修改：2006-2-10
+--------------------------------------------------------
| 版权声明: Copyright◎ 2004-2005 世纪流年 版权所有
| 主 页:	http://www.liu21st.com
| 作 者:	Liu21st <流年> liu21st@gmail.com 
+--------------------------------------------------------
*/
if (!defined('FCS_PATH')) {
	exit();
}
//+----------------------------------------
//|	网站参数设置
//+----------------------------------------
define('WEB_TITLE',	'FCS');
define('WEB_URL',	'www.liu21st.com');

//+----------------------------------------
//|	路径参数设置
//+----------------------------------------

//网站根目录 
define('WEB_ROOT',	'http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER["SCRIPT_NAME"]));
//当前文件名 即应用程序名称
define('APP_NAME', basename($_SERVER["SCRIPT_NAME"],'.php'));
//目录设置
define('TMPL_DIR','Tpl'); 
define('CACHE_DIR','Cache'); 
define('HTML_DIR','Html'); 
define('APPS_DIR','Apps'); 
define('CONF_DIR','Conf');
define('LIB_DIR','Lib');
define('LOG_DIR','Logs');
define('DATA_DIR','Data');

define('TMPL_PATH',FCS_PATH.'/'.TMPL_DIR.'/'); //
define('HTML_PATH',FCS_PATH.'/'.HTML_DIR.'/'); //
define('APPS_PATH',FCS_PATH.'/'.APPS_DIR.'/'); //
define('CACHE_PATH',FCS_PATH.'/'.CACHE_DIR.'/'); //
define('CONFIG_PATH',FCS_PATH.'/'.CONF_DIR.'/'); //
define('LOG_PATH',FCS_PATH.'/'.LOG_DIR.'/'); //
define('LIB_PATH',FCS_PATH.'/'.LIB_DIR.'/'); //
define('DATA_PATH',FCS_PATH.'/'.DATA_DIR.'/'); //
//+----------------------------------------
//|	路径参数设置
//+----------------------------------------
//是否启用模板缓存
define('TEMPLATE_CACHE_ON', false);
//是否启用HTML
define('HTML_CACHE_ON', false);
//是否调试模式
define('DEBUG_MODE', true);

define('ERROR_PAGE_URL', WEB_ROOT.'/error.html');
//+----------------------------------------
//|	SEO参数设置
//+----------------------------------------

//使用搜索引擎友好URL 
define('URL_FRIEND', True);
//使用path_info的参数方式
//1 /var1,val1/var2,val2/var3,val3/ 参数对并联
//2 /var1=val1,var2=val2,var3,val3/ 参数对串联
define('URL_MODEL',2);	
//定义参数和值之间的分割符
define('PATH_DEPR', ',');

//+----------------------------------------
//|	模块和操作参数设置
//+----------------------------------------
//模块调用变量名
define('VAR_MODULE', 'm');		
//操作方法变量名
define('VAR_ACTION', 'a');
//缺省调用模块
define('DEFAULT_MODULE','index');	
//缺省执行操作
define('DEFAULT_ACTION','index'); 
//操作方法是否需要前缀来区分不同的方式
define('ALLOW_ACTION_PREFIX',False);
//当ALLOW_ACTION_PREFIX为True时候有效
//区分POST和GET方式的操作
define('POST_ACTION_PREFIX','do');
define('GET_ACTION_PREFIX','show');
//默认模板名称
define('DEFAULT_TEMPLATE_NAME','default'); 

//+----------------------------------------
//|	字符集设置
//+----------------------------------------
//模板字符集 
define('TEMPLATE_CHARSET', 'UTF-8');
//输出字符集 Cache和HTML文件
define('OUTPUT_CHARSET','UTF-8');

//+----------------------------------------
//|	后缀设置
//+----------------------------------------
//模板文件后缀
define('TEMPLATE_SUFFIX', '.html');
//Cache文件后缀
define('CACHFILE_SUFFIX', '.php');
//静态文件后缀
define('HTMLFILE_SUFFIX', '.shtml');

//+----------------------------------------
//|	分页设置
//+----------------------------------------
//分页显示行数
define('LIST_NUMBERS', 12);
//分页显示页数
define('PAGE_NUMBERS', 5);

define('FCS_VERSION', '0.6.0');
//+----------------------------------------
//|	Cookie和cache设置
//+----------------------------------------
//跨域有效的域名
define('COOKIE_DOMAIN','.liu21st.com');
define('TMPL_CACHE_TIME',10);	//模板缓存有效时间（分钟） -1 为永久
define('HTML_CACHE_TIME',10);	//静态页面有效时间（分钟） -1 为永久

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