<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2010 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// $Id$

/**
 +------------------------------------------------------------------------------
 * ThinkPHP 运行时文件 编译后不再加载
 +------------------------------------------------------------------------------
 */

//  版本信息
define('THINK_VERSION', '3.0beta');
//   系统信息
if(version_compare(PHP_VERSION,'5.4.0','<') ) {
  //[sae]下不支持这个函数  
  //  @set_magic_quotes_runtime (0);
    define('MAGIC_QUOTES_GPC',get_magic_quotes_gpc()?True:False);
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
    if(!defined('__ROOT__')) {
        // 网站URL根目录
        if( strtoupper(APP_NAME) == strtoupper(basename(dirname(_PHP_FILE_))) ) {
            $_root = dirname(dirname(_PHP_FILE_));
        }else {
            $_root = dirname(_PHP_FILE_);
        }
        define('__ROOT__',   (($_root=='/' || $_root=='\\')?'':$_root));
    }

    //支持的URL模式
    define('URL_COMMON',      0);   //普通模式
    define('URL_PATHINFO',    1);   //PATHINFO模式
    define('URL_REWRITE',     2);   //REWRITE模式
    define('URL_COMPAT',      3);   // 兼容模式
}

// 路径设置
define('COMMON_PATH',    APP_PATH.'Common/'); // 项目公共目录
define('LIB_PATH',    APP_PATH.'Lib/'); //
define('CACHE_PATH',   RUNTIME_PATH.'Cache/'); //
define('CONFIG_PATH',  APP_PATH.'Conf/'); //
define('LOG_PATH',  RUNTIME_PATH.'Logs/'); //
define('LANG_PATH', APP_PATH.'Lang/'); //
define('TEMP_PATH', RUNTIME_PATH.'Temp/'); //
define('DATA_PATH', RUNTIME_PATH.'Data/'); //

// 可在入口文件中重新定义的常量
if(!defined('CORE_PATH')) define('CORE_PATH',THINK_PATH.'Lib/');
if(!defined('TMPL_PATH')) define('TMPL_PATH',APP_PATH.'Tpl/');
if(!defined('HTML_PATH')) define('HTML_PATH',APP_PATH.'Html/');
if(!defined('EXTEND_PATH')) define('EXTEND_PATH',THINK_PATH.'Extend/');
if(!defined('MODE_PATH')) define('MODE_PATH',EXTEND_PATH.'Mode/');
if(!defined('VENDOR_PATH')) define('VENDOR_PATH',EXTEND_PATH.'Vendor/');
if(!defined('LIBRARY_PATH')) define('LIBRARY_PATH',EXTEND_PATH.'Library/');


// 加载运行时所需要的文件 并负责自动目录生成
function load_runtime_file() {
    //[sae] 加载系统基础函数库
    require THINK_PATH.'Sae/common.php';
    //[sae] 读取核心编译文件列表
    $list = array(
        THINK_PATH.'Sae/Think.class.php',
        CORE_PATH.'Core/ThinkException.class.php',  // 异常处理类
        CORE_PATH.'Core/Behavior.class.php',
    );
    // 加载模式文件列表
    foreach ($list as $key=>$file){
        if(is_file($file))  require_cache($file);
    }
    //[sae] 加载系统类库别名定义
    alias_import(include THINK_PATH.'Sae/alias.php');
    //[sae]在sae下不对目录结构进行检查
    if(APP_DEBUG){
        //[sae] 调试模式切换删除编译缓存
        if(SaeMC::file_exists(RUNTIME_FILE)) SaeMC::unlink(RUNTIME_FILE) ;
    }
}
// 创建编译缓存
function build_runtime_cache($append='') {
    // 生成编译文件
    $defs = get_defined_constants(TRUE);
    $content    =  '$GLOBALS[\'_beginTime\'] = microtime(TRUE);';
    if(defined('RUNTIME_DEF_FILE')) { //[sae] 编译后的常量文件外部引入
        SaeMC::set(RUNTIME_DEF_FILE, '<?php '.array_define($defs['user']));
        $content  .=  'SaeMC::include_file(\''.RUNTIME_DEF_FILE.'\');';
    }else{
        $content  .= array_define($defs['user']);
    }
    //[sae] 读取核心编译文件列表
    $list = array(
        THINK_PATH.'Sae/common.php',
        THINK_PATH.'Sae/Think.class.php',
        CORE_PATH.'Core/ThinkException.class.php',
        CORE_PATH.'Core/Behavior.class.php',
    );
    foreach ($list as $file){
        $content .= compile($file);
    }
    // 系统行为扩展文件统一编译
    if(C('APP_TAGS_ON')) {
        $content .= build_tags_cache();
    }
    $alias = include THINK_PATH.'Conf/alias.php';
    $content .= 'alias_import('.var_export($alias,true).');';
    // 编译框架默认语言包和配置参数
    $content .= $append."\nL(".var_export(L(),true).");C(".var_export(C(),true).');G(\'loadTime\');Think::Start();';
    //[sae] 生成编译缓存文件
    SaeMC::set(RUNTIME_FILE, strip_whitespace('<?php '.$content));
}

// 编译系统行为扩展类库
function build_tags_cache() {
    $tags = C('extends');
    $content = '';
    foreach ($tags as $tag=>$item){
        foreach ($item as $key=>$name) {
            $content .= is_int($key)?compile(CORE_PATH.'Behavior/'.$name.'Behavior.class.php'):compile($name);
        }
    }
    return $content;
}
// 加载运行时所需文件
load_runtime_file();