<?php
// +----------------------------------------------------------------------
// | ThinkPHP Lite
// +----------------------------------------------------------------------
// | Copyright (c) 2008 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// $Id$

/**
 +------------------------------------------------------------------------------
 * 系统定义文件
 +------------------------------------------------------------------------------
 * @category   Think
 * @package  Common
 * @author   liu21st <liu21st@gmail.com>
 * @version  $Id$
 +------------------------------------------------------------------------------
 */
if (!defined('THINK_PATH')) exit();
//   系统信息
if(version_compare(PHP_VERSION,'6.0.0','<') ) {
    @set_magic_quotes_runtime (0);
    define('MAGIC_QUOTES_GPC',get_magic_quotes_gpc()?True:False);
}
define('MEMORY_LIMIT_ON',function_exists('memory_get_usage')?true:false);
// 记录内存初始使用
if(MEMORY_LIMIT_ON) {
     $GLOBALS['_startUseMems'] = memory_get_usage();
}
define('IS_CGI',substr(PHP_SAPI, 0,3)=='cgi' ? 1 : 0 );
define('IS_WIN',strstr(PHP_OS, 'WIN') ? 1 : 0 );
define('IS_CLI',PHP_SAPI=='cli'? 1   :   0);

if(!IS_CLI) {
    // 当前文件名
    if(!defined('_PHP_FILE_')) {
        if(IS_CGI) {
            //CGI/FASTCGI模式下
            $_temp  = explode('.php',$_SERVER["PHP_SELF"]);
            define('_PHP_FILE_',  rtrim(str_replace($_SERVER["HTTP_HOST"],'',$_temp[0].'.php'),'/'));
        }else {
            define('_PHP_FILE_',    rtrim($_SERVER["SCRIPT_NAME"],'/'));
        }
    }
    if(!defined('WEB_URL')) {
        // 网站URL根目录
        if( strtoupper(APP_NAME) == strtoupper(basename(dirname(_PHP_FILE_))) ) {
            $_root = dirname(dirname(_PHP_FILE_));
        }else {
            $_root = dirname(_PHP_FILE_);
        }
        define('WEB_URL',   (($_root=='/' || $_root=='\\')?'':$_root));
    }

    //支持的URL模式
    define('URL_COMMON',      0);   //普通模式
    define('URL_PATHINFO',    1);   //PATHINFO模式
    define('URL_REWRITE',     2);   //REWRITE模式
    define('URL_COMPAT',        3);     // 兼容模式
}

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
define('TMPL_PATH',APP_PATH.'/'.TMPL_DIR.'/');
define('HTML_PATH',APP_PATH.'/'.HTML_DIR.'/'); //
define('COMMON_PATH',   APP_PATH.'/Common/'); // 项目公共目录
define('LIB_PATH',         APP_PATH.'/'.LIB_DIR.'/'); //
define('CACHE_PATH',   APP_PATH.'/'.CACHE_DIR.'/'); //
define('CONFIG_PATH',  APP_PATH.'/'.CONF_DIR.'/'); //
define('LOG_PATH',       APP_PATH.'/'.LOG_DIR.'/'); //
define('LANG_PATH',     APP_PATH.'/'.LANG_DIR.'/'); //
define('TEMP_PATH',      APP_PATH.'/'.TEMP_DIR.'/'); //
define('DATA_PATH', APP_PATH.'/Data/'); //

define('VENDOR_PATH',THINK_PATH.'/Vendor/');
// 为了方便导入第三方类库 设置Vendor目录到include_path
set_include_path(get_include_path() . PATH_SEPARATOR . VENDOR_PATH);

//  版本信息
define('THINK_VERSION', '1.5Lite');
?>