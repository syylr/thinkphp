<?php
/**
+------------------------------------------------------------------------------
* 项目配置文件 本配置文件由系统自动生成
+------------------------------------------------------------------------------
*/
if (!defined('FCS_PATH')) exit();
define('WEB_TITLE','FCS 轻量级面向对象的PHP开发框架'); 
define('WEB_DOMAIN','com.addwe'); 
define('TMPL_DIR','Tpl'); 
define('TMPL_PATH',WEB_ROOT.'/Tpl/'); 
define('APPS_PATH',FCS_PATH.'/Lib/'); 
define('DEBUG_MODE',true); 
define('WEB_LOG_RECORD',true); 
define('LOG_FILE_SIZE',2097152); 
define('ERROR_PAGE',''); 
define('ERROR_MESSAGE','你浏览的页面暂时发生了错误，请稍候再试！'); 
define('URL_MODEL',1); 
define('PATH_MODEL',3); 
define('PATH_DEPR',','); 
define('VAR_MODULE','m'); 
define('VAR_ACTION','a'); 
define('VAR_FILE','f'); 
define('DEFAULT_MODULE','index'); 
define('DEFAULT_ACTION','index'); 
define('DEFAULT_TEMPLATE','default'); 
define('ACTION_DIR','Action'); 
define('VAR_LANGUAGE','l'); 
define('VAR_TEMPLATE','t'); 
define('DEFAULT_LANGUAGE','zh-cn'); 
define('TEMPLATE_CHARSET','UTF-8'); 
define('OUTPUT_CHARSET','utf-8'); 
define('TIME_ZONE','PRC'); 
define('TEMPLATE_SUFFIX','.html'); 
define('CACHFILE_SUFFIX','.php'); 
define('HTMLFILE_SUFFIX','.shtml'); 
define('USER_AUTH_ON',true); 
define('USER_AUTH_TYPE',1); 
define('USER_AUTH_KEY','authId'); 
define('AUTH_PWD_ENCODER','md5'); 
define('USER_AUTH_PROVIDER','DaoAuthentictionProvider'); 
define('USER_AUTH_GATEWAY','/Public/login'); 
define('NOT_AUTH_MODULE','Public'); 
define('SAVE_PARENT_VO',true); 
define('UPDATE_PARENT_VO',false); 
define('DENY_REMOTE_SUBMIT',true); 
define('COOKIE_DOMAIN','.liu21st.com'); 
define('SESSION_NAME','FCSID'); 
define('SESSION_EXPIRE',3000); 
define('COMPRESS_PAGE',false); 
define('COMPRESS_METHOD','deflate'); 
define('COMPRESS_LEVEL',5); 
define('BROWSER_CACHE',false); 
define('HTML_CACHE_ON',false); 
define('HTML_CACHE_TIME',1000); 
define('TMPL_CACHE_ON',true); 
define('TMPL_CACHE_TIME',-1); 
define('DATA_CACHE_ON',false); 
define('DATA_CACHE_TIME',10000); 
define('DATA_CACHE_MAX',100); 
define('DATA_CACHE_COMPRESS',false); 
define('USE_SHARE_MEM',true); 
define('SHARE_MEM_TYPE','File'); 
define('LIST_NUMBERS',20); 
define('PAGE_NUMBERS',5); 
define('VAR_PAGE','p'); 

define('SHOW_RUN_TIME',TRUE); 
define('TMPL_DENY_FUNC_LIST','echo,exit'); 
define('TMPL_L_DELIM','{'); 
define('TMPL_R_DELIM','}'); 
define('DB_TYPE','mysql'); 
define('DB_HOST','localhost'); 
define('DB_NAME','fcs'); 
define('DB_USER','root'); 
define('DB_PWD',''); 
define('DB_PORT',''); 
define('DB_PREFIX','fcs'); 
//配置文件定义结束
?>