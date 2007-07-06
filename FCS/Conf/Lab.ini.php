<?php 
/*
+---------------------------------------------------------+
| 项目: FCS -- Fast,Compatible & Simple OOP PHP Framework |
| 文件: index.ini.php									  |
| 功能: FCS项目配置文件									  |
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
//|	网站参数设置
//+----------------------------------------
define('WEB_TITLE',	'FCS 轻量级面向对象的PHP开发框架');
define('WEB_DOMAIN','com.liu21st');		//注意要反过来写 用作定位应用模块类

//+----------------------------------------
//|	路径参数设置
//+----------------------------------------

//网站根目录 绝对路径
if (!defined('WEB_ROOT')) define('WEB_ROOT',	'd:/www/webapps');


//目录设置
define('TMPL_DIR','Tpl'); 
define('HTML_DIR','Html'); 

//路径设置
define('TMPL_PATH',WEB_ROOT.'/'.TMPL_DIR.'/'); //
define('HTML_PATH',WEB_ROOT.'/'.HTML_DIR.'/'); //
define('APPS_PATH',LIB_PATH.str_replace('.','/',WEB_DOMAIN).'/'); 


//是否调试模式
define('DEBUG_MODE', true);
//错误导向页面
define('ERROR_PAGE_URL', '');
//默认的错误信息 未定义错误导向页面的错误文字显示
define('DEFAULT_ERROR_MESSAGE', '你浏览的页面暂时发生了错误，请稍候再试！');

//+----------------------------------------
//|	SEO参数设置
//+----------------------------------------

//使用搜索引擎友好URL 
define('PATHINFO_URL', true);
//使用path_info的参数方式
//1 /var1,val1/var2,val2/var3,val3/ 参数对并联
//2 /var1=val1,var2=val2,var3,val3/ 参数对串联
define('URL_MODEL',1);	
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
//应用模块下属目录设置
define('ACTION_DIR','Action');			//操作目录

//+----------------------------------------
//|	字符集设置
//+----------------------------------------
define('DEFAULT_LANGUAGE', 'zh-cn');
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
//|	Cookie和cache设置
//+----------------------------------------
//跨域有效的域名
define('COOKIE_DOMAIN','.liu21st.com');
//是否启用HTML缓存
define('HTML_CACHE_ON', false);
//HTML缓存有效期 -1 为永久
define('TMPL_CACHE_TIME',1);	
//是否启用模板缓存
define('TMPL_CACHE_ON', false);
//模板缓存有效期 -1 为永久
define('HTML_CACHE_TIME',1);	

//+----------------------------------------
//|	模板设置
//+----------------------------------------
define('TMPL_DENY_FUNC_LIST','echo,exit');
define('TMPL_L_DELIM','{');
define('TMPL_R_DELIM','}');

//数据库设置 MYSQL
define('DB_TYPE', 'mysql');
define('DB_HOST', "localhost");
define('DB_NAME',"web");
define('DB_USER',"root");
define('DB_PWD',"");
define('DB_PORT',"");
define('DB_PREFIX',"");
?>