<?php
// +----------------------------------------------------------------------
// | ThinkPHP
// +----------------------------------------------------------------------
// | Copyright (c) 2009 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// $Id$

/**
 +------------------------------------------------------------------------------
 * ThinkPHP 应用程序类 用于简洁模式
 +------------------------------------------------------------------------------
 * @category   Think
 * @package  Think
 * @subpackage  Core
 * @author    liu21st <liu21st@gmail.com>
 * @version   $Id$
 +------------------------------------------------------------------------------
 */
class App extends Think
{//类定义开始

    /**
     +----------------------------------------------------------
     * 应用程序初始化
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    public function run()
    {
        // 设定错误和异常处理
        set_error_handler(array(&$this,"appError"));
        set_exception_handler(array(&$this,"appException"));
        // 检查项目是否编译过
        // 在部署模式下会自动在第一次执行的时候编译项目
        if(is_file(RUNTIME_PATH.'~app.php')) {
            // 直接读取编译后的项目文件
            C(include RUNTIME_PATH.'~app.php');
        }else{
            // 预编译项目
            $this->build();
        }
        // 取得模块和操作名称
        define('MODULE_NAME',   $this->getModule());       // Module名称
        define('ACTION_NAME',   $this->getAction());        // Action操作
        // 不使用语言包功能，仅仅加载框架语言文件
        L(include THINK_PATH.'/Lang/'.C('DEFAULT_LANGUAGE').'.php');

        // 记录应用初始化时间
        if(C('SHOW_RUN_TIME')){
            $GLOBALS['_initTime'] = microtime(TRUE);
        }
        R(MODULE_NAME,ACTION_NAME);
        return ;
    }

    /**
     +----------------------------------------------------------
     * 读取配置信息 编译项目
     +----------------------------------------------------------
     * @access private
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    private function build()
    {
        // 加载惯例配置文件
        C(include THINK_PATH.'/Common/convention.php');
        // 加载项目配置文件
        if(is_file(CONFIG_PATH.'config.php')) {
            C(include CONFIG_PATH.'config.php');
        }
        $common   = '';
        $debug  =  C('DEBUG_MODE');  //  是否调试模式
        // 加载项目公共文件
        if(is_file(COMMON_PATH.'common.php')) {
            include COMMON_PATH.'common.php';
            if(!$debug) { // 编译文件
                $common   .= compile(COMMON_PATH.'common.php');
            }
        }
        // 加载项目编译文件列表
        if(is_file(CONFIG_PATH.'app.php')) {
            $list   =  include CONFIG_PATH.'app.php';
            foreach ($list as $key=>$file){
                // 加载并编译文件
                require $file;
                if(!$debug) {
                    $common   .= compile($file);
                }
            }
        }
        // 如果是调试模式加载调试模式配置文件
        if($debug) {
            // 加载系统默认的开发模式配置文件
            C(include THINK_PATH.'/Common/debug.php');
            if(is_file(CONFIG_PATH.'debug.php')) {
                // 允许项目增加开发模式配置定义
                C(include CONFIG_PATH.'debug.php');
            }
        }else{
            // 部署模式下面生成编译文件
            // 下次直接加载项目编译文件
            $content  = "<?php ".$common."\nreturn ".var_export(C(),true).";\n?>";
            file_put_contents(RUNTIME_PATH.'~app.php',$content);
        }
        return ;
    }


    /**
     +----------------------------------------------------------
     * 获得实际的模块名称
     +----------------------------------------------------------
     * @access private
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    private function getModule()
    {
        $module = !empty($_POST[C('VAR_MODULE')]) ?
            $_POST[C('VAR_MODULE')] :
            (!empty($_GET[C('VAR_MODULE')])? $_GET[C('VAR_MODULE')]:C('DEFAULT_MODULE'));
        if(C('URL_CASE_INSENSITIVE')) {
            // URL地址不区分大小写
            define('P_MODULE_NAME',strtolower($module));
            // 智能识别方式 index.php/user_type/index/ 识别到 UserTypeAction 模块
            $module = ucfirst($this->parseName(strtolower($module),1));
        }
        unset($_POST[C('VAR_MODULE')],$_GET[C('VAR_MODULE')]);
        return $module;
    }

    /**
     +----------------------------------------------------------
     * 获得实际的操作名称
     +----------------------------------------------------------
     * @access private
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    private function getAction()
    {
        $action   = !empty($_POST[C('VAR_ACTION')]) ?
            $_POST[C('VAR_ACTION')] :
            (!empty($_GET[C('VAR_ACTION')])?$_GET[C('VAR_ACTION')]:C('DEFAULT_ACTION'));
        unset($_POST[C('VAR_ACTION')],$_GET[C('VAR_ACTION')]);
        return $action;
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
    public function appException($e)
    {
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
    public function appError($errno, $errstr, $errfile, $errline)
    {
      switch ($errno) {
          case E_ERROR:
          case E_USER_ERROR:
              $errorStr = "[$errno] $errstr ".basename($errfile)." 第 $errline 行.";
              if(C('WEB_LOG_RECORD')){
                 Log::write($errorStr,Log::ERR);
              }
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

   /**
     +----------------------------------------------------------
     * 析构方法
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     */
    public function __destruct()
    {
        // 保存日志记录
        if(C('WEB_LOG_RECORD')) Log::save();
    }
};//类定义结束
?>