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
 * ThinkPHP 应用程序类 执行应用过程管理
 +------------------------------------------------------------------------------
 * @category   Think
 * @package  Think
 * @subpackage  Core
 * @author    liu21st <liu21st@gmail.com>
 * @version   $Id$
 +------------------------------------------------------------------------------
 */
class App {

    /**
     +----------------------------------------------------------
     * 应用程序初始化
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    static public function init() {
        // 设定错误和异常处理
        set_error_handler(array('App','appError'));
        set_exception_handler(array('App','appException'));
        //[RUNTIME]
        App::build();         // 预编译项目
        //[/RUNTIME]
        
        // 注册AUTOLOAD方法
        spl_autoload_register(array('Think', 'autoload'));
        // 设置系统时区
        date_default_timezone_set(C('DEFAULT_TIMEZONE'));
        // 加载动态项目公共文件和配置
        load_ext_file();

        // 项目初始化标签
        tag('app_init');

        // URL调度
        Dispatcher::dispatch();
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
    static private function build() {
        // 加载惯例配置文件
        C(include THINK_PATH.'Common/convention.php');
        // 加载项目配置文件
        if(is_file(CONFIG_PATH.'config.php'))
            C(include CONFIG_PATH.'config.php');

        // 如果是模式扩展 首先载入模式配置文件 可覆盖项目配置文件中的参数
        if(defined('THINK_MODE') && is_file(MODE_PATH.ucwords(strtolower(THINK_MODE)).'/config.php')) {
            C(include MODE_PATH.ucwords(strtolower(THINK_MODE)).'/config.php');
        }

        //----------------------------------------START
        // 读取行为集合Collection 模式扩展也可以定义行为集合
        $collection   = C('COLLECTION_NAME')?
            include EXTEND_PATH.'Collection/'.strtolower(C('COLLECTION_NAME')).'.php':
            include THINK_PATH.'Common/collection.php');

        // 解析行为集合
        $compile   = '';
        // 加载行为集合配置文件
        if(isset($collection['config'])) {
            C( is_array($collection['config'])?$collection['config']:include $collection['config'] );
        }
        // 加载系统行为定义
        if(isset($collection['extends']) && C('APP_TAGS_ON')) {
            C('extends',is_array($collection['extends'])?$collection['extends']:include $collection['extends']);
        }
        // 加载应用行为定义
        if(isset($collection['tags'])) {
            C('tags',is_array($collection['tags'])?$collection['tags']:include $collection['tags']);
        }
        // 加载公共文件
        if(isset($collection['common']) && is_file($collection['common'])) {
            include $collection['common'];
            // 编译文件
            if(!APP_DEBUG)  $compile   .= compile($collection['common']);
        }
        // 加载应用别名定义
        if(isset($collection['alias'])) {
            $alias = is_array($collection['alias'])?$collection['alias']:include $collection['alias'];
            alias_import($alias);
            // 编译文件
            if(!APP_DEBUG) $compile .= 'alias_import('.var_export($alias,true).');';
        }
        // 加载项目编译文件列表
        if(isset($collection['app'])) {
            $list   =   is_array($collection['app'])?$collection['app']:include $collection['app'];
            foreach ($list as $file){
                // 加载并编译文件
                require_cache($file);
                if(!APP_DEBUG) $compile   .= compile($file);
            }
        }
        // ----------------------------------------------END
        /*
        if(C('APP_TAGS_ON')) {
            if(defined('THINK_MODE') && is_file(MODE_PATH.ucwords(strtolower(THINK_MODE)).'/tags.php')) {
                // 模式可以单独定义系统的行为扩展
                C('extends',include MODE_PATH.ucwords(strtolower(THINK_MODE)).'/tags.php');
            }else{ // 加载系统默认行为扩展定义文件
                C('extends',include THINK_PATH.'Common/tags.php');
            }
        }
        $common   = '';
        // 加载项目公共文件
        if(is_file(COMMON_PATH.'common.php')) {
            include COMMON_PATH.'common.php';
            // 编译文件
            if(!APP_DEBUG)  $common   .= compile(COMMON_PATH.'common.php');
        }
        // 加载应用别名定义
        if(is_file(CONFIG_PATH.'alias.php')) {
            $alias = include CONFIG_PATH.'alias.php';
            alias_import($alias);
            if(!APP_DEBUG) $common .= 'alias_import('.var_export($alias,true).');';
        }
        // 加载项目编译文件列表
        if(is_file(CONFIG_PATH.'app.php')) {
            $list   =  include CONFIG_PATH.'app.php';
            foreach ($list as $file){
                // 加载并编译文件
                require_cache($file);
                if(!APP_DEBUG) $common   .= compile($file);
            }
        }
        // 加载动态配置文件
        $configs =  C('APP_CONFIG_LIST');
        if(is_string($configs)) $configs =  explode(',',$configs);
        foreach ($configs as $config) {
            $file   = CONFIG_PATH.$config.'.php';
            if(is_file($file))
                C($config,array_change_key_case(include $file));
        }
        C('APP_CONFIG_LIST',''); // 清除配置参数
        */
        if(APP_DEBUG) {
            // 调试模式加载系统默认的开发模式配置文件
            C(include THINK_PATH.'Common/debug.php');
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
     * 执行应用程序
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    static public function exec() {
        // 安全检测
        if(!preg_match('/^[A-Za-z_0-9]+$/',MODULE_NAME)){
            throw_exception(L('_MODULE_NOT_EXIST_'));
        }
        //创建Action控制器实例
        $group =  defined('GROUP_NAME') ? GROUP_NAME.C('APP_GROUP_DEPR') : '';
        $module  =  A($group.MODULE_NAME);
        if(!$module) {
            if(function_exists('__hack_module')) {
                // hack 方式定义扩展模块 返回Action对象
                $module = __hack_module();
                if(!is_object($module)) {
                    // 不再继续执行 直接返回
                    return ;
                }
            }else{
                // 是否定义Empty模块
                $module = A("Empty");
                if(!$module)
                    // 模块不存在 抛出异常
                    throw_exception(L('_MODULE_NOT_EXIST_').MODULE_NAME);
            }
        }
        //获取当前操作名
        $action = ACTION_NAME;
        if (method_exists($module,'_before_'.$action)) {
            // 执行前置操作
            call_user_func(array(&$module,'_before_'.$action));
        }
        //执行当前操作
        call_user_func(array(&$module,$action));
        if (method_exists($module,'_after_'.$action)) {
            //  执行后缀操作
            call_user_func(array(&$module,'_after_'.$action));
        }
        return ;
    }

    /**
     +----------------------------------------------------------
     * 运行应用实例 入口文件使用的快捷方法
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    static public function run() {
        App::init();
        // 项目开始标签
        tag('app_begin');
         // Session初始化 支持其他客户端
        if(isset($_REQUEST[C("VAR_SESSION_ID")]))
            session_id($_REQUEST[C("VAR_SESSION_ID")]);
        if(C('SESSION_AUTO_START'))  session_start();
        // 记录应用初始化时间
        if(C('SHOW_RUN_TIME')) G('initTime');
        App::exec();
        // 项目结束标签
        tag('app_end');
        // 保存日志记录
        if(C('LOG_RECORD')) Log::save();
        return ;
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