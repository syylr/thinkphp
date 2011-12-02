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

// 加载模式列表文件
load_think_mode();

// 检查缓存目录(Runtime) 如果不存在则自动创建
function check_runtime() {
    if(!is_writeable(RUNTIME_PATH)) {
        header("Content-Type:text/html; charset=utf-8");
        exit('<div style=\'font-weight:bold;float:left;width:345px;text-align:center;border:1px solid silver;background:#E8EFFF;padding:8px;color:red;font-size:14px;font-family:Tahoma\'>目录 [ '.RUNTIME_PATH.' ] 不可写！</div>');
    }
    if(!is_dir(CACHE_PATH)) {
        mkdir(CACHE_PATH);  // 模板缓存目录
    }elseif(APP_DEBUG){
        // 调试模式切换删除编译缓存
        $runtime = defined('THINK_MODE')?'~'.strtolower(THINK_MODE).'_runtime.php':'~runtime.php';
        if(is_file(RUNTIME_PATH.$runtime)) unlink(RUNTIME_PATH.$runtime);
    }
    if(!is_dir(LOG_PATH))	mkdir(LOG_PATH);    // 日志目录
    if(!is_dir(TEMP_PATH))  mkdir(TEMP_PATH);	// 数据缓存目录
    if(!is_dir(DATA_PATH))	mkdir(DATA_PATH);	// 数据文件目录
    return true;
}

// 加载模式列表文件
function load_think_mode() {
    // 加载常量定义文件
    require THINK_PATH.'Common/defines.php';
    // 加载路径定义文件
    require defined('PATH_DEFINE_FILE')?PATH_DEFINE_FILE:THINK_PATH.'Common/paths.php';
    // 读取核心编译文件列表
    if(is_file(CONFIG_PATH.'core.php')) {
        // 加载项目自定义的核心编译文件列表
        $list   =  include CONFIG_PATH.'core.php';
    }elseif(defined('THINK_MODE')) {
        // 根据设置的运行模式加载不同的核心编译文件
        $list   =  include EXTEND_PATH.'Mode/'.strtolower(THINK_MODE).'.php';
    }else{
        // 默认核心
        $list = include THINK_PATH.'Common/core.php';
    }
    // 加载模式文件列表
    foreach ($list as $key=>$file){
        if(is_file($file))  require $file;
    }
    // 加载系统别名定义
    alias_import(include THINK_PATH.'Common/alias.php');
    // 检查项目目录结构 如果不存在则自动创建
    if(!is_dir(RUNTIME_PATH)) {
        // 创建项目目录结构
        build_app_dir();
    }else{
        // 检查缓存目录
        check_runtime();
    }
}

// 创建编译缓存
function build_runtime_cache($append='') {
    // 读取核心编译文件列表
    if(is_file(CONFIG_PATH.'core.php')) {
        // 加载项目自定义的核心编译文件列表
        $list   =  include CONFIG_PATH.'core.php';
    }elseif(defined('THINK_MODE')) {
        // 根据设置的运行模式加载不同的核心编译文件
        $list   =  include EXTEND_PATH.'Mode/'.strtolower(THINK_MODE).'.php';
    }else{
        // 默认核心
        $list = include THINK_PATH.'Common/core.php';
    }
    // 生成编译文件
    $defs = get_defined_constants(TRUE);
    $content  = array_define($defs['user']);
    foreach ($list as $file){
        $content .= compile($file);
    }
    // 加载核心别名定义
    $content .= 'alias_import('.var_export(include THINK_PATH.'Common/alias.php',true).');';
    // 系统行为扩展文件统一编译
    if(C('APP_TAGS_ON')) {
        $files =  scandir(EXTEND_PATH.'Behavior/');
        foreach ($files as $file){
            if($file == '.' || $file == '..') continue;
            $content .= compile(EXTEND_PATH.'Behavior/'.$file);
        }
    }
    $content .= $append."\nC(".var_export(C(),true).');';
    $runtime = defined('THINK_MODE')?'~'.strtolower(THINK_MODE).'_runtime.php':'~runtime.php';
    file_put_contents(RUNTIME_PATH.$runtime,strip_whitespace('<?php '.$content));
}

// 批量创建目录
function mkdirs($dirs,$mode=0777) {
    foreach ($dirs as $dir){
        if(!is_dir($dir))  mk_dir($dir,$mode);
    }
}

// 创建项目目录结构
function build_app_dir() {
    // 没有创建项目目录的话自动创建
    if(!is_dir(APP_PATH)) mk_dir(APP_PATH,0777);
    if(is_writeable(APP_PATH)) {
        $dirs  = array(
            LIB_PATH,
            RUNTIME_PATH,
            CONFIG_PATH,
            COMMON_PATH,
            LANG_PATH,
            CACHE_PATH,
            TMPL_PATH,
            TMPL_PATH.'default/',
            LOG_PATH,
            TEMP_PATH,
            DATA_PATH,
            LIB_PATH.'Model/',
            LIB_PATH.'Action/',
            LIB_PATH.'Behavior/',
            LIB_PATH.'Widget/',
            LIB_PATH.'Filter/',
            );
        mkdirs($dirs);
        // 目录安全写入
        if(!defined('BUILD_DIR_SECURE')) define('BUILD_DIR_SECURE',false);
        if(BUILD_DIR_SECURE) {
            if(!defined('DIR_SECURE_FILENAME')) define('DIR_SECURE_FILENAME','index.html');
            if(!defined('DIR_SECURE_CONTENT')) define('DIR_SECURE_CONTENT',' ');
            // 自动写入目录安全文件
            $content = DIR_SECURE_CONTENT;
            $a = explode(',', DIR_SECURE_FILENAME);
            foreach ($a as $filename){
                foreach ($dirs as $dir)
                    file_put_contents($dir.$filename,$content);
            }
        }
        // 写入配置文件
        if(!is_file(CONFIG_PATH.'config.php'))
            file_put_contents(CONFIG_PATH.'config.php',"<?php\nreturn array(\n\t//'配置项'=>'配置值'\n);\n?>");
        // 写入测试Action
        if(!is_file(LIB_PATH.'Action/IndexAction.class.php'))
            build_first_action();
    }else{
        header("Content-Type:text/html; charset=utf-8");
        exit('<div style=\'font-weight:bold;float:left;width:345px;text-align:center;border:1px solid silver;background:#E8EFFF;padding:8px;color:red;font-size:14px;font-family:Tahoma\'>项目目录不可写，目录无法自动生成！<BR>请使用项目生成器或者手动生成项目目录~</div>');
    }
}

// 创建测试Action
function build_first_action() {
    $content = file_get_contents(THINK_PATH.'Tpl/'.(defined('BUILD_MODE')?BUILD_MODE:'AutoIndex').'.tpl.php');
    file_put_contents(LIB_PATH.'Action/IndexAction.class.php',$content);
}
?>