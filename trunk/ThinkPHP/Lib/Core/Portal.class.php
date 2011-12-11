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
 * ThinkPHP Portal类
 +------------------------------------------------------------------------------
 * @category   Think
 * @package  Think
 * @subpackage  Core
 * @author    liu21st <liu21st@gmail.com>
 * @version   $Id$
 +------------------------------------------------------------------------------
 */
class Portal {

    private static $_instance = array();

    /**
     +----------------------------------------------------------
     * 应用程序初始化
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    static public function Start() {
        // 设定错误和异常处理
        set_error_handler(array('Portal','appError'));
        set_exception_handler(array('Portal','appException'));
        // 注册AUTOLOAD方法
        spl_autoload_register(array('Portal', 'autoload'));
        //[RUNTIME]
        Portal::buildApp();         // 预编译项目
        //[/RUNTIME]
        // 运行应用
        App::run();
        return ;
    }

    //[RUNTIME]
    /**
     +----------------------------------------------------------
     * 读取配置信息 编译项目
     +----------------------------------------------------------
     * @access private
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    static private function buildApp() {
        // 加载底层惯例配置文件
        C(include THINK_PATH.'Conf/convention.php');

        // 读取运行模式
        if(defined('MODE_NAME')) { // 模式的设置并入核心模式
            $mode   = include MODE_PATH.strtolower(MODE_NAME).'.php';
        }else{
            $mode   =  array();
        }

        // 加载模式配置文件
        if(isset($mode['config'])) {
            C( is_array($mode['config'])?$mode['config']:include $mode['config'] );
        }

        // 加载项目配置文件
        if(is_file(CONFIG_PATH.'config.php'))
            C(include CONFIG_PATH.'config.php');

        // 加载框架底层语言包
        L(include THINK_PATH.'Lang/'.strtolower(C('DEFAULT_LANG')).'.php');

        // 加载模式系统行为定义
        if(C('APP_TAGS_ON')) {
            if(isset($mode['extends'])) {
                C('extends',is_array($mode['extends'])?$mode['extends']:include $mode['extends']);
            }else{ // 默认加载系统行为扩展定义
                C('extends', include THINK_PATH.'Conf/tags.php');
            }
        }

        // 加载应用行为定义
        if(isset($mode['tags'])) {
            C('tags', is_array($mode['tags'])?$mode['tags']:include $mode['tags']);
        }elseif(is_file(CONFIG_PATH.'tags.php')){
            // 默认加载项目配置目录的tags文件定义
            C('tags', include CONFIG_PATH.'tags.php');
        }

        $compile   = '';
        // 读取核心编译文件列表
        if(isset($mode['core'])) {
            $list   =  $mode['core'];
        }else{
            $list  =  array(
                THINK_PATH.'Common/functions.php', // 标准模式函数库
                CORE_PATH.'Core/Log.class.php',    // 日志处理类
                CORE_PATH.'Core/Dispatcher.class.php', // URL调度类
                CORE_PATH.'Core/App.class.php',   // 应用程序类
                CORE_PATH.'Core/Action.class.php', // 控制器类
                CORE_PATH.'Core/View.class.php',  // 视图类
            );
        }
        foreach ($list as $file){
            if(is_file($file))  {
                require_cache($file);
                if(!APP_DEBUG)   $compile .= compile($file);
            }
        }

        // 加载公共文件
        if(!isset($mode['common'])) { // 默认加载项目公共文件
            $mode['common'] =  COMMON_PATH.'common.php';
        }
        if(is_file($mode['common'])) {
            include $mode['common'];
            // 编译文件
            if(!APP_DEBUG)  $compile   .= compile($mode['common']);
        }

        // 加载应用别名定义
        if(isset($mode['alias'])) {
            $alias = is_array($mode['alias'])?$mode['alias']:include $mode['alias'];
        }elseif(is_file(CONFIG_PATH.'alias.php')){ 
            // 没有定义 则获取项目配置目录的alias别名定义文件
            $alias = include CONFIG_PATH.'alias.php';
        }
        if(is_array($alias)) {
            alias_import($alias);
            if(!APP_DEBUG) $compile .= 'alias_import('.var_export($alias,true).');';
        }

        if(APP_DEBUG) {
            // 调试模式加载系统默认的开发模式配置文件
            C(include THINK_PATH.'Conf/debug.php');
            if(is_file(CONFIG_PATH.'debug.php'))
                // 允许项目增加开发模式配置定义
                C(include CONFIG_PATH.'debug.php');
        }else{
            // 部署模式下面生成编译文件
            build_runtime_cache($compile);
        }
        return ;
    }
    //[/RUNTIME]

    /**
     +----------------------------------------------------------
     * 系统自动加载ThinkPHP类库
     * 并且支持配置自动加载路径
     +----------------------------------------------------------
     * @param string $classname 对象类名
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    public static function autoload($classname) {
        // 检查是否存在别名定义
        if(alias_import($classname)) return ;
        // 加载系统行为
        if(substr($classname,-8)=="Behavior" && require_cache(CORE_PATH.'Behavior/'.$classname.'.class.php')){
            return ;
        }
        // 根据自动加载路径设置进行尝试搜索
        $paths  =   explode(',',C('APP_AUTOLOAD_PATH'));
        foreach ($paths as $path){
            if(import($path.'.'.$classname))
                // 如果加载类成功则返回
                return ;
        }
    }

    /**
     +----------------------------------------------------------
     * 取得对象实例 支持调用类的静态方法
     +----------------------------------------------------------
     * @param string $class 对象类名
     * @param string $method 类的静态方法名
     +----------------------------------------------------------
     * @return object
     +----------------------------------------------------------
     */
    static public function instance($class,$method='') {
        $identify   =   $class.$method;
        if(!isset(self::$_instance[$identify])) {
            if(class_exists($class)){
                $o = new $class();
                if(!empty($method) && method_exists($o,$method))
                    self::$_instance[$identify] = call_user_func_array(array(&$o, $method));
                else
                    self::$_instance[$identify] = $o;
            }
            else
                halt(L('_CLASS_NOT_EXIST_').':'.$class);
        }
        return self::$_instance[$identify];
    }

    /**
     +----------------------------------------------------------
     * 自定义异常处理
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param mixed $e 异常对象
     +----------------------------------------------------------
     */
    static public function appException($e) {
        halt($e->__toString());
    }

    /**
     +----------------------------------------------------------
     * 自定义错误处理
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param int $errno 错误类型
     * @param string $errstr 错误信息
     * @param string $errfile 错误文件
     * @param int $errline 错误行数
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    static public function appError($errno, $errstr, $errfile, $errline) {
      switch ($errno) {
          case E_ERROR:
          case E_USER_ERROR:
            $errorStr = "[$errno] $errstr ".basename($errfile)." 第 $errline 行.";
            if(C('LOG_RECORD')) Log::write($errorStr,Log::ERR);
            halt($errorStr);
            break;
          case E_STRICT:
          case E_USER_WARNING:
          case E_USER_NOTICE:
          default:
            $errorStr = "[$errno] $errstr ".basename($errfile)." 第 $errline 行.";
            Log::record($errorStr,Log::NOTICE);
            break;
      }
    }
}