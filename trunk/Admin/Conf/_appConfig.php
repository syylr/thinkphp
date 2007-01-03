<?php
/**
+------------------------------------------------------------------------------
* 项目配置文件 本配置文件由系统自动生成
+------------------------------------------------------------------------------
*/
if (!defined('FCS_PATH')) exit();
define('WEB_TITLE','FCS 轻量级面向对象的PHP开发框架'); 
define('WEB_DOMAIN','com.addwe'); 
define('DEBUG_MODE',true); 
define('WEB_LOG_RECORD',true); 
define('LOG_FILE_SIZE',2097152); 
define('ERROR_PAGE',''); 
define('ERROR_MESSAGE','你浏览的页面暂时发生了错误，请稍候再试！'); 
define('DISPATCH_ON',true); 
define('DISPATCH_NAME','FCSDispatcher'); 
define('URL_MODEL',1); 
define('PATH_MODEL',3); 
define('PATH_DEPR',','); 
define('VAR_MODULE','m'); 
define('VAR_ACTION','a'); 
define('VAR_FILE','f'); 
define('DEFAULT_MODULE','Index'); 
define('DEFAULT_ACTION','index'); 
define('DEFAULT_TEMPLATE','default'); 
define('ACTION_DIR','Action'); 
define('VAR_LANGUAGE','l'); 
define('VAR_TEMPLATE','t'); 
define('DEFAULT_LANGUAGE','zh-cn'); 
define('DB_CHARSET','utf8'); 
define('TEMPLATE_CHARSET','utf-8'); 
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
define('REQUIRE_AUTH_MODULE',''); 
define('TMPL_ENGINE_TYPE','fcs'); 
define('SAVE_PARENT_VO',true); 
define('UPDATE_PARENT_VO',true); 
define('DENY_REMOTE_SUBMIT',true); 
define('COOKIE_DOMAIN','.liu21st.com'); 
define('COOKIE_EXPIRE',3600); 
define('SESSION_NAME','FCSID'); 
define('SESSION_TYPE','File'); 
define('SESSION_EXPIRE',3000000); 
define('SESSION_TABLE','fcs_session'); 
define('COMPRESS_PAGE',true); 
define('COMPRESS_METHOD','gzip'); 
define('COMPRESS_LEVEL',5); 
define('BROWSER_CACHE',false); 
define('PLUGIN_CACHE_ON',true);
define('HTML_CACHE_ON',false); 
define('HTML_CACHE_TIME',100); 
define('TMPL_CACHE_ON',true); 
define('TMPL_CACHE_TIME',-1); 
define('DB_CACHE_ON',false); 
define('DB_CACHE_TIME',1000); 
define('DB_CACHE_MAX',5000); 
define('DATA_CACHE_ON',false); 
define('DATA_CACHE_TIME',1000); 
define('DATA_CACHE_MAX',5000); 
define('DATA_CACHE_COMPRESS',false); 
define('DATA_CACHE_CHECK',false);
define('DATA_CACHE_TABLE','fcs_cache'); 
define('CACHE_SERIAL_HEADER', "<?php\n//");
define('CACHE_SERIAL_FOOTER', "\n?".">");
define('CONFIG_FILE_TYPE','Define'); 
define('DATA_CACHE_TYPE','File'); 
define('SHARE_MEM_SIZE',1048576); 
define('LIST_NUMBERS',25); 
define('PAGE_NUMBERS',5); 
define('VAR_PAGE','p'); 
define('BIG_2_GB',false); 
define('THUMB_DIR',FCS_PATH.'/Thumb/'); 
define('SHOW_RUN_TIME',TRUE); 
define('TMPL_DENY_FUNC_LIST','echo,exit'); 
define('TMPL_L_DELIM','{'); 
define('TMPL_R_DELIM','}'); 
define('TAGLIB_BEGIN','<'); 
define('TAGLIB_END','>');
define('RPC_TYPE','PHPRPC'); 
define('RPC_SERVER',''); 
define('RPC_ENCRYPT',TRUE); 
//配置文件定义结束
?>