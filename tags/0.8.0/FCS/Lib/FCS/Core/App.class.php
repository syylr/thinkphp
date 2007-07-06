<?php 
// +---------------------------------------------------------------------------+
// | FCS -- Fast,Compatible & Simple OOP PHP Framework                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2005-2006 liu21st.com.  All rights reserved.                |
// | Website: http://www.fcs.org.cn/                                           |
// | Author : Liu21st 流年 <liu21st@gmail.com>                                 |
// +---------------------------------------------------------------------------+
// | This program is free software; you can redistribute it and/or modify it   |
// | under the terms of the GNU General Public License as published by the     |
// | Free Software Foundation; either version 2 of the License,  or (at your   |
// | option) any later version.                                                |
// |                                                                           |
// | This program is distributed in the hope that it will be useful,  but      |
// | WITHOUT ANY WARRANTY; without even the implied warranty of                |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General |
// | Public License for more details.                                          |
// +---------------------------------------------------------------------------+

/**
 +------------------------------------------------------------------------------
 * FCS
 +------------------------------------------------------------------------------
 * @package    Core
 * @copyright  Copyright (c) 2005-2006 liu21st.com.  All rights reserved. 
 * @author     liu21st <liu21st@gmail.com>
 * @version    $Id$
 +------------------------------------------------------------------------------
 */

import("FCS.Core.Dispatcher");
import("FCS.Util.Session");
import("FCS.Util.Filter");

/**
 +------------------------------------------------------------------------------
 * 应用程序类
 +------------------------------------------------------------------------------
 * @package   core
 * @author    liu21st <liu21st@gmail.com>
 * @version   0.8.0
 +------------------------------------------------------------------------------
 */
class App extends Base 
{//类定义开始

    /**
     +----------------------------------------------------------
     * 应用程序名称
     +----------------------------------------------------------
     * @var string
     * @access protected
     +----------------------------------------------------------
     */
    var $name ;

    /**
     +----------------------------------------------------------
     * 应用程序标识号
     +----------------------------------------------------------
     * @var string
     * @access protected
     +----------------------------------------------------------
     */
    var $id;

    /**
     +----------------------------------------------------------
     * 应用程序调试信息
     +----------------------------------------------------------
     * @var array
     * @access protected
     +----------------------------------------------------------
     */
    var $debug = array();


    /**
     +----------------------------------------------------------
     * 架构函数
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $name 应用名称
     * @param string $id  应用标识号
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    function __construct($name='App',$id='')
    {    
        $this->name = $name;
        $this->id   = !empty($id) ? $id : create_guid();
    }


    /**
     +----------------------------------------------------------
     * 取得应用实例对象
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return App
     +----------------------------------------------------------
     */
    function getInstance() 
    {
        return get_instance_of(__CLASS__);
    }


    /**
     +----------------------------------------------------------
     * 应用程序初始化
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    function Init()
    {
        //设定错误和异常处理
        set_error_handler(array(&$this,"AppError"));
        if(version_compare(PHP_VERSION, '5.0.0', '>')) 
            set_exception_handler(array(&$this,"AppException"));

        //如果存在项目公共函数和定义文件则自动加载
        $list = glob(FCS_PATH.'/Common/'.APP_NAME.'/*.php');
        if(!empty($list)) {
            import(APP_NAME.'.*',FCS_PATH.'/Common/','.php');
        }

        // 加载服务器配置文件和自定义函数
        // 位于同一服务器上面的项目可以公用某些配置项目
        $this->loadConfig('Server',CONFIG_PATH,false);

        // 加载项目配置文件和自定义函数
        $this->loadConfig('App',CONFIG_PATH.APP_NAME.'/');

        // 设置系统时区
        if(function_exists('date_default_timezone_set')) 
            date_default_timezone_set(TIME_ZONE);

        // Session初始化
        Session::start(SESSION_NAME,'','','unserialize_callback');
        // 应用调度器
        // 并且默认加载了MagicQuote过滤器
        Dispatcher::dispatch(URL_MODEL);
        //系统检查
        $this->check();

        return ;
    }

    /**
     +----------------------------------------------------------
     * 加载项目配置文件
     * 支持XML、INI等多种方式
     +----------------------------------------------------------
     * @access private 
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    function loadConfig($name,$path,$flag=true,$configFile='_define.php') 
    {
        //加载项目配置文件
        $defineFile =   $path.$configFile;
        //如果存在系统生成的定义文件，则直接引入，无需再进行配置文件解析
        if(file_exists($defineFile)) {
            include $defineFile;
        }else {
            //寻找匹配的项目配置文件
            //支持XML、INI和PHP数组、对象和常量定义文件
            $list = glob($path.$name.'.*');
            if(empty($list)) {
                if($flag) {
                    throw_exception('项目配置文件不存在！');
                }
                
            }else {
                import('FCS.Util.Config');
                $config  = & new Config();
                //分析第一个配置文件
                $result  = $config->parse($list[0]);
                //转换成常量并生成定义文件供下次加载
                $result->toConst($defineFile);        	            	
            }
        }
    }

    /**
     +----------------------------------------------------------
     * 过滤器检查
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    function loadFilters($filters) 
    {
        Filter::load($filters);
        return ;
    }


    /**
     +----------------------------------------------------------
     * 系统环境检查
     * 
     +----------------------------------------------------------
     * @access private 
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    function check() 
    {
        $this->CheckLanguage();     //语言检查
        $this->CheckTemplate();     //模板检查
        $this->CheckModule();       //模块检查

        return ;
    }

    /**
     +----------------------------------------------------------
     * 语言检查
     * 检查浏览器支持语言，并自动加载语言包
     +----------------------------------------------------------
     * @access private 
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    function CheckLanguage()
    {
        //检测浏览器支持语言
        $langSet = detect_browser_language();
        setlocale(LC_ALL, $langSet);
        //读取系统语言包
        if (!file_exists(LANG_PATH.$langSet.".php")) {
            $langSet = DEFAULT_LANGUAGE;
        }
        include_cache(LANG_PATH.$langSet.".php");    
        //读取项目语言包
        if (file_exists(LANG_PATH.APP_NAME.'/'.$langSet.".php")) {
            include_cache(LANG_PATH.APP_NAME.'/'.$langSet.".php");    
        }
        define('CHAR_SET',$langSet); // 
        return ;
    }


    /**
     +----------------------------------------------------------
     * 模板检查，如果不存在则抛出异常
     * 
     +----------------------------------------------------------
     * @access private 
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function CheckTemplate()
    {

        if ( isset($_GET[VAR_TEMPLATE]) ) {
            $templateSet = $_GET[VAR_TEMPLATE];
            $_COOKIE['FCS_'.VAR_TEMPLATE] = $templateSet;
        } else {
            if ( isset($_COOKIE['FCS_'.VAR_TEMPLATE]) ) {
                $templateSet = $_COOKIE['FCS_'.VAR_TEMPLATE];
            }
            else {
                $templateSet =    DEFAULT_TEMPLATE;
            }
        }
        if (!is_dir(TMPL_PATH.$templateSet)) {
            throw_exception(_TEMPLATE_NOT_EXIST_);
        }
        define('TEMPLATE_NAME',$templateSet); //
        define('TEMPLATE_PATH',TMPL_PATH.TEMPLATE_NAME); // 
        //网站地址
        define('__ROOT__',WEB_URL);
        //项目地址
        define('__APP__',$_SERVER["SCRIPT_NAME"]);
        //模块地址
        define('__URL__',$_SERVER["SCRIPT_NAME"].'/'.MODULE_NAME);
        //当前操作地址
        define('__ACT__',$_SERVER["SCRIPT_NAME"].'/'.MODULE_NAME.'/'.ACTION_NAME);

        //模块路径
        define('TEMPLATE_MODULE_PATH',APP_NAME.'/'.MODULE_NAME.'/');
        //系统公共文件地址
        define('SYS_PUBLIC_URL', WEB_URL.'/'.TMPL_DIR.'/'.TEMPLATE_NAME.'/Public');
        //网站公共文件地址
        define('WEB_PUBLIC_URL', WEB_URL.'/Public');
        //项目公共文件地址
        define('APP_PUBLIC_URL', WEB_URL.'/'.TMPL_DIR.'/'.TEMPLATE_NAME.'/'.APP_NAME.'/Public');
        define('__CURRENT__', WEB_URL.'/'.TMPL_DIR.'/'.TEMPLATE_NAME.'/'.APP_NAME.'/'.MODULE_NAME);
        //模板文件名 绝对路径
        define('TMPL_FILE_NAME',TEMPLATE_PATH.'/'.TEMPLATE_MODULE_PATH.ACTION_NAME.TEMPLATE_SUFFIX);
        //CACHE文件名 绝对路径
        define('CACHE_FILE_NAME',CACHE_PATH.APP_NAME.'/'.md5(TMPL_FILE_NAME).CACHFILE_SUFFIX);
        if(HTML_CACHE_ON != false){
            //生成唯一的静态文件名
            define('HTML_FILE_NAME',HTML_PATH.APP_NAME.'/'.md5($_SERVER['REQUEST_URI']).HTMLFILE_SUFFIX);

        }
        return ;
    }

    /**
     +----------------------------------------------------------
     * 检查模块状态
     * 
     +----------------------------------------------------------
     * @access private 
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function checkModule() 
    {
        $moduleClassFile = APPS_PATH.APP_NAME;
        $moduleClass = ucwords(strtolower(MODULE_NAME)).'Action';
        $moduleClassFile .= '/Action/'.$moduleClass.'.class.php';

        // 载入具体应用模块类
        if (file_exists($moduleClassFile)) require_cache($moduleClassFile);
        if(version_compare(PHP_VERSION, '5.0.0', '>')) {
            if (!class_exists($moduleClass,false)) {
                throw_exception(_MODULE_NOT_EXIST_.MODULE_NAME);
            }
        }else {
            if (!class_exists($moduleClass)) {
                throw_exception(_MODULE_NOT_EXIST_.MODULE_NAME);
            }        	
        }
        return ;
    }

    /**
     +----------------------------------------------------------
     * 执行应用程序
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function exec()
    {

        //如果开启HTML功能，载入静态页面，无需再次执行
        if( HTML_CACHE_ON ){
            if ($this->checkHTML()) {//静态页面有效
                readfile(HTML_FILE_NAME);
                return;
            }
        }
        //创建Action控制器实例
        $moduleClass = ucwords(strtolower(MODULE_NAME)).'Action';
        $module  = & new $moduleClass();

        //检查操作
        $moduleAction = ACTION_NAME; 
        if (!method_exists($module,$moduleAction)) {    
            throw_exception(_ERROR_ACTION_.$moduleAction);
        }

        //如果存在前置操作，首先执行
        if (method_exists($module,'_before_'.$moduleAction)) {    
            $module->{'_before_'.$moduleAction}();
        }

        //执行操作
        $module->{$moduleAction}();

        //如果存在后置操作，继续执行
        if (method_exists($module,'_after_'.$moduleAction)) {    
            $module->{'_after_'.$moduleAction}();
        }
        return ;

    }

    /**
     +----------------------------------------------------------
     * 检查是否存在静态HTML文件
     * 
     +----------------------------------------------------------
     * @access private 
     +----------------------------------------------------------
     * @return boolen
     +----------------------------------------------------------
     */
    function checkHTML()
    {
        if(!file_exists(HTML_FILE_NAME)){
            return False;
        }
        elseif (filemtime(TMPL_FILE_NAME) > filemtime(HTML_FILE_NAME)) { // 源模板文件是否更新
            return False; 
        } 
        elseif (HTML_CACHE_TIME != -1 && time() > filemtime(HTML_FILE_NAME)+HTML_CACHE_TIME) { 
            // 缓存是否在有效期
            return False; 
        }
        return True;
    }


    /**
     +----------------------------------------------------------
     * 自定义异常处理
     * 
     +----------------------------------------------------------
     * @access protected 
     +----------------------------------------------------------
     * @param mixed $e 异常对象
     +----------------------------------------------------------
     */
    function AppException($e)
    {
        halt($e->__toString());
    }


    /**
     +----------------------------------------------------------
     * 自定义错误处理
     * 
     +----------------------------------------------------------
     * @access protected 
     +----------------------------------------------------------
     * @param int $errno 错误类型
     * @param string $errstr 错误信息
     * @param string $errfile 错误文件
     * @param int $errline 错误行数
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    function AppError($errno, $errstr, $errfile, $errline)
    {
      switch ($errno) {
      case E_USER_ERROR: 
          $errorStr = "[$errno] $errstr ".basename($errfile)." 第 $errline 行";
          if(WEB_LOG_RECORD){
             $this->debug[count($this->debug)] = $errorStr;
          }
          halt($errorStr);
          break;
      case E_USER_WARNING:
      case E_USER_NOTICE:
      default: 
          if(WEB_LOG_RECORD){
            $errorStr = "注意：[$errno] $errstr ".basename($errfile)." 第 $errline 行.\n";
            $this->debug[count($this->debug)] = $errorStr;
          }
          break;
      }
    }


    /**
     +----------------------------------------------------------
     * 重载基类的__toString方法，用于输出应用程序对象
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    function __toString()
    {
        return 'FCS_'.FCS_VERSION.' '.$this->name.' '.$this->id;
    }


    /**
     +----------------------------------------------------------
     * 析构函数 在应用程序类结束的时候进行日志记录，提高效率
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     */
    function __destruct()
    {
        $str = trim(implode('',$this->debug));
        if(WEB_LOG_RECORD && !empty($str)){
            Log::Write($str,WEB_LOG_DEBUG);
        }
    }

};//类定义结束
?>