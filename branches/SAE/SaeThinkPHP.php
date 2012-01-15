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
// Sae版ThinkPHP 入口文件
//[sae]判断是否运行在SAE上。
if (!isset($_SERVER["HTTP_APPNAME"])) {
    define("IS_SAE", FALSE);
    if (!defined('THINK_PATH'))
        define('THINK_PATH', dirname(__FILE__) . '/');
    if (!defined('APP_PATH'))
        define('APP_PATH', dirname($_SERVER['SCRIPT_FILENAME']) . '/');
    //加载模拟器
    if (!defined('SAE_APPNAME'))
        require THINK_PATH . 'Sae/SaeImit.php';
    require THINK_PATH . 'ThinkPHP.php';
    exit();
}
define("IS_SAE", TRUE);
register_shutdown_function(array('SaeMC', 'error'));

//[sae]对Memcache操作的封装，编译缓存，模版缓存存入Memcache中（非wrappers的形式）
class SaeMC {

    static public $handler;
    static private $current_include_file = null;
    static private $contents = array();
    static private $filemtimes = array();

    //设置文件内容
    static public function set($filename, $content) {
        self::$handler->set($_SERVER['HTTP_APPVERSION'] . '/' . $filename, time() . $content, MEMCACHE_COMPRESSED, 0);
    }

    //载入文件
    static public function include_file($filename) {
        self::$current_include_file = 'saemc://' . $_SERVER['HTTP_APPVERSION'] . '/' . $filename;
        $content = isset(self::$contents[$filename]) ? self::$contents[$filename] : self::getValue($filename, 'content');
        if (!$content)
            exit('<br /><b>SAE_Parse_error</b>: failed to open stream: No such file ' . self::$current_include_file);
        if (@(eval(' ?>' . $content)) === false)
            self::error();
        self::$current_include_file = null;
        unset(self::$contents[$filename]); //释放内存
    }

    static private function getValue($filename, $type='mtime') {
        $content = self::$handler->get($_SERVER['HTTP_APPVERSION'] . '/' . $filename);
        if (!$content)
            return false;
        $ret = array(
            'mtime' => substr($content, 0, 10),
            'content' => substr($content, 10)
        );
        self::$contents[$filename] = $ret['content'];
        self::$filemtimes[$filename] = $ret['mtime'];
        return $ret[$type];
    }

    //获得文件修改时间
    static public function filemtime($filename) {
        if (!isset(self::$filemtimes[$filename]))
            return self::getValue($filename, 'mtime');
        return self::$filemtimes[$filename];
    }

    //删除文件
    static public function unlink($filename) {
        if (isset(self::$contents[$filename]))
            unset(self::$contents[$filename]);
        if (isset(self::$filemtimes[$filename]))
            unset(self::$filemtimes[$filename]);
        return self::$handler->delete($_SERVER['HTTP_APPVERSION'] . '/' . $filename);
    }

    static public function file_exists($filename) {
        return self::filemtime($filename) === false ? false : true;
    }

    static function error() {
        $error = error_get_last();
        if (!is_null($error)) {
            $file = strpos($error['file'], 'eval()') !== false ? self::$current_include_file : $error['file'];
            exit("<br /><b>SAE_error</b>:  {$error['message']} in <b>" . $file . "</b> on line <b>{$error['line']}</b><br />");
        }
    }

}

//[sae] 初始化memcache
if (!(SaeMC::$handler = @(memcache_init()))) {
    header("Content-Type:text/html; charset=utf-8");
    exit('<div style=\'font-weight:bold;float:left;width:430px;text-align:center;border:1px solid silver;background:#E8EFFF;padding:8px;color:red;font-size:14px;font-family:Tahoma\'>您的Memcache还没有初始化，请登录SAE平台进行初始化~</div>');
}
//记录开始运行时间
$GLOBALS['_beginTime'] = microtime(TRUE);
// 记录内存初始使用
define('MEMORY_LIMIT_ON', function_exists('memory_get_usage'));
if (MEMORY_LIMIT_ON)
    $GLOBALS['_startUseMems'] = memory_get_usage();
if (!defined('APP_PATH'))
    define('APP_PATH', dirname($_SERVER['SCRIPT_FILENAME']) . '/');
//[sae] 判断是否手动建立项目目录
if (!is_dir(APP_PATH . "/Lib/")) {
    header("Content-Type:text/html; charset=utf-8");
    exit('<div style=\'font-weight:bold;float:left;width:430px;text-align:center;border:1px solid silver;background:#E8EFFF;padding:8px;color:red;font-size:14px;font-family:Tahoma\'>sae环境下请手动生成项目目录~</div>');
}
if (!defined('RUNTIME_PATH'))
    define('RUNTIME_PATH', APP_PATH . 'Runtime/');
if (!defined('APP_DEBUG'))
    define('APP_DEBUG', false); // 是否调试模式
$runtime = defined('MODE_NAME') ? '~' . strtolower(MODE_NAME) . '_runtime.php' : '~runtime.php';
if (!defined('RUNTIME_FILE'))
    define('RUNTIME_FILE', RUNTIME_PATH . $runtime);
//[sae] 载入核心编译缓存
if (!APP_DEBUG && SaeMC::file_exists(RUNTIME_FILE)) {
    // 部署模式直接载入allinone缓存
    SaeMC::include_file(RUNTIME_FILE);
} else {
    if (version_compare(PHP_VERSION, '5.2.0', '<'))
        die('require PHP > 5.2.0 !');
    // ThinkPHP系统目录定义
    if (!defined('THINK_PATH'))
        define('THINK_PATH', dirname(__FILE__) . '/');
    if (!defined('APP_NAME'))
        define('APP_NAME', basename(dirname($_SERVER['SCRIPT_FILENAME'])));
    //[sae] 加载运行时文件
    require THINK_PATH . "Sae/runtime.php";
    // 记录加载文件时间
    G('loadTime');
    // 执行入口
    Think::Start();
}