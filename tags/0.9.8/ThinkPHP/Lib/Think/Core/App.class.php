<?php 
// +----------------------------------------------------------------------+
// | ThinkPHP                                                             |
// +----------------------------------------------------------------------+
// | Copyright (c) 2006 liu21st.com All rights reserved.                  |
// +----------------------------------------------------------------------+
// | Licensed under the Apache License, Version 2.0 (the 'License');      |
// | you may not use this file except in compliance with the License.     |
// | You may obtain a copy of the License at                              |
// | http://www.apache.org/licenses/LICENSE-2.0                           |
// | Unless required by applicable law or agreed to in writing, software  |
// | distributed under the License is distributed on an 'AS IS' BASIS,    |
// | WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or      |
// | implied. See the License for the specific language governing         |
// | permissions and limitations under the License.                       |
// +----------------------------------------------------------------------+
// | Author: liu21st <liu21st@gmail.com>                                  |
// +----------------------------------------------------------------------+
// $Id$

import("Think.Util.Session");

/**
 +------------------------------------------------------------------------------
 * 应用程序类
 +------------------------------------------------------------------------------
 * @package   core
 * @author    liu21st <liu21st@gmail.com>
 * @version   $Id$
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
        $this->id   =  $id ;//| create_guid();
    }

    /**
     +----------------------------------------------------------
     * 取得应用实例对象 
     * 静态方法
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return App
     +----------------------------------------------------------
     */
    function  getInstance() 
    {
        return get_instance_of(__CLASS__);
    }

    /**
     +----------------------------------------------------------
     * 应用程序初始化
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    function init()
    {
        // 设定错误和异常处理
        set_error_handler(array(&$this,"appError"));
        if(version_compare(PHP_VERSION, '5.0.0', '>')) 
            set_exception_handler(array(&$this,"appException"));

		// 加载惯例配置文件
		include_once (THINK_PATH.'/Common/convention.php');
		C(array_change_key_case($_default_config));

        // 加载项目配置文件
        // 支持App.* 作为配置文件
        // 自动生成_appConfig.php 配置缓存
        $this->loadConfig('App',CONFIG_PATH,'_appConfig.php');

        // 设置系统时区 PHP5支持
        if(function_exists('date_default_timezone_set')) 
            date_default_timezone_set(C('TIME_ZONE'));

        if('DB' === strtoupper(C('SESSION_TYPE'))) {
			// 数据库Session支持
			import("Think.Util.Filter");
			Filter::load('DbSession');
        }
        // Session初始化
        Session::start(C('SESSION_NAME'),'',C('SESSION_PATH'),'unserialize_callback');   

        // 加载插件，因为使用了Session
        // 必须在Session开启之后加载插件
		if(C('THINK_PLUGIN_ON')) {
	        $this->loadPlugIn();  
		}

        // 应用调度过滤器
        // 如果没有加载任何URL调度器
        // 默认只支持 QUERY_STRING 方式
        // 例如 ?m=user&a=add
		if(C('DISPATCH_ON')) {
			if( 'ThinkDispatcher'== C('DISPATCH_NAME') ) {
				// 使用ThinkDispatcher调度器
				import('Think.Core.ThinkDispatcher');
				ThinkDispatcher::dispatch();
			}else{
				// 加载第三方调度器
		        apply_filter('app_dispatch');
			}
		}

        if(!defined('PHP_FILE')) {
            // PHP_FILE 由内置的Dispacher定义
            // 如果不使用该插件，需要重新定义
        	define('PHP_FILE',_PHP_FILE_);
        }

        // 取得模块和操作名称
		// 可以在Dispatcher中定义获取规则
		if(!defined('MODULE_NAME')) define('MODULE_NAME',   $this->getModule());       // Module名称
        if(!defined('ACTION_NAME')) define('ACTION_NAME',   $this->getAction());        // Action操作

		// 加载模块配置文件 并自动生成配置缓存文件
		$this->loadConfig('m_'.MODULE_NAME,CONFIG_PATH,'m_'.MODULE_NAME.'Config.php',false);

        // 加载项目公共文件
       	include_cache(APP_PATH.'/Common/common.php');

        // 系统检查
        $this->checkLanguage();     //语言检查
        $this->checkTemplate();     //模板检查

		//	启用页面防刷新机制
		if(C('LIMIT_RESFLESH_ON')) {
			$guid	=	md5($_SERVER['PHP_SELF']);
			// 检查页面刷新间隔
			if(Session::is_set('_last_visit_time_'.$guid) && Session::get('_last_visit_time_'.$guid)>time()-C('LIMIT_REFLESH_TIMES')) {
				// 页面刷新读取浏览器缓存
				header('HTTP/1.1 304 Not Modified');
				exit;
			}else{
				// 缓存当前地址访问时间
				Session::set('_last_visit_time_'.$guid,time());
				header('Last-Modified:'.(date('D,d M Y H:i:s',time()-C('LIMIT_REFLESH_TIMES'))).' GMT');
			}
		}

        // 应用初始化过滤插件
        apply_filter('app_init');

		// 记录应用初始化时间
		$GLOBALS['_initTime'] = array_sum(explode(' ', microtime()));

        return ;
    }

    /**
     +----------------------------------------------------------
     * 获得模块名称
     +----------------------------------------------------------
     * @static
     * @access public 
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    function getModule()
    {
        $module = isset($_POST[C('VAR_MODULE')]) ? 
            $_POST[C('VAR_MODULE')] :
            (isset($_GET[C('VAR_MODULE')])? $_GET[C('VAR_MODULE')]:'');
        // 如果 $module 为空，则赋予默认值
        if (empty($module)) $module = C('DEFAULT_MODULE'); 
        return $module; 
    }

    /**
     +----------------------------------------------------------
     * 获得操作名称
     +----------------------------------------------------------
     * @static
     * @access public 
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    function getAction()
    {
        $action   = isset($_POST[C('VAR_ACTION')]) ? 
            $_POST[C('VAR_ACTION')] : 
            (isset($_GET[C('VAR_ACTION')])?$_GET[C('VAR_ACTION')]:'');
        // 如果 $action 为空，则赋予默认值
        if (empty($action)) $action = C('DEFAULT_ACTION');
        return $action; 
    }

    /**
     +----------------------------------------------------------
     * 加载配置文件
     * 支持XML、INI和PHP数组、对象和常量定义文件
     +----------------------------------------------------------
     * @access private 
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    function loadConfig($name,$path,$configFile) 
    {
        //加载项目配置文件
        $cacheFile =   $path.$configFile;
        //如果存在系统生成的配置缓存文件，则直接引入，无需再进行配置文件解析
        if(!file_exists($cacheFile)) {
            //寻找匹配的项目配置文件
            //支持XML、INI和PHP数组、对象和常量定义文件
            $list = glob($path.$name.'.*');
            if(!empty($list)) {
				import('Think.Util.Config');
                $config  = Config::getInstance();
                //分析第一个配置文件
                $result  = $config->parse($list[0]);
                // 生成配置缓存文件供下次加载
				// 默认采用PHP数组方式缓存
				$result->toArray($cacheFile);
            }
        }
		if(file_exists($cacheFile)) {
			$_config	=	include $cacheFile;
			if(is_array($_config)) {
				C(array_change_key_case($_config));
			}
		}
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
    function checkLanguage()
    {
        //检测浏览器支持语言
        $langSet = detect_browser_language();
        // setlocale操作比较费时，暂时屏蔽 
        //setlocale(LC_ALL, $langSet);       
        
        // 定义当前语言
        define('LANG_SET',$langSet);
		// 加载语言类
		import("Think.Util.Language");
		// 加载框架语言包
		$language = Language::getInstance();
        // 读取系统语言包
        if (!file_exists(THINK_PATH.'/Lang/'.LANG_SET.'.php')){
			$language->load(THINK_PATH.'/Lang/'.LANG_SET.'.php');
		}else{
			$language->load(THINK_PATH.'/Lang/'.C('DEFAULT_LANGUAGE').'.php');       
		}
        	     
        // 读取项目语言包
        $language->load(LANG_PATH.'/'.LANG_SET.'.php');  
		// 缓存语言变量
		L($language->_lang);
        return ;
    }

    /**
     +----------------------------------------------------------
     * 模板检查，如果不存在使用默认
     +----------------------------------------------------------
     * @access private 
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function checkTemplate()
    {
        if ( isset($_GET[C('VAR_TEMPLATE')]) ) {
            $templateSet = $_GET[C('VAR_TEMPLATE')];
            setcookie('Think_'.C('VAR_TEMPLATE'),$templateSet,time()+C('COOKIE_EXPIRE'),'/');
        } else {
            if ( isset($_COOKIE['Think_'.C('VAR_TEMPLATE')]) ) {
                $templateSet = $_COOKIE['Think_'.C('VAR_TEMPLATE')];
            }
            else {
                $templateSet =    C('DEFAULT_TEMPLATE');
                setcookie('Think_'.C('VAR_TEMPLATE'),$templateSet,time()+C('COOKIE_EXPIRE'),'/');
            }
        }
        if (!is_dir(TMPL_PATH.$templateSet)) {
            //模版不存在的话，使用默认模版
            $templateSet =    C('DEFAULT_TEMPLATE');
            //throw_exception(_TEMPLATE_NOT_EXIST_);
        }
        //模版名称
        define('TEMPLATE_NAME',$templateSet); 
        // 当前模版路径
        define('TEMPLATE_PATH',TMPL_PATH.TEMPLATE_NAME); 
        //当前网站地址

        define('__ROOT__',WEB_URL);
        //当前项目地址
        define('__APP__',PHP_FILE);
        //模块地址
        define('__URL__',PHP_FILE.'/'.MODULE_NAME);
        //当前操作地址
        define('__ACTION__',PHP_FILE.'/'.MODULE_NAME.'/'.ACTION_NAME);  
        //当前页面地址
        define('__SELF__',$_SERVER['PHP_SELF']);

        //模板文件名 绝对路径
        define('TMPL_FILE_NAME',TEMPLATE_PATH.'/'.MODULE_NAME.'/'.ACTION_NAME.C('TEMPLATE_SUFFIX'));
        //网站公共文件地址
        define('WEB_PUBLIC_URL', WEB_URL.'/Public');
        //项目公共文件地址
        define('APP_PUBLIC_URL', WEB_URL.'/'.APP_NAME.'/'.TMPL_DIR.'/'.TEMPLATE_NAME.'/Public'); 
        //当前操作地址(绝对地址）
        define('__CURRENT__', WEB_URL.'/'.APP_NAME.'/'.TMPL_DIR.'/'.TEMPLATE_NAME.'/'.MODULE_NAME);

        return ;
    }

    /**
     +----------------------------------------------------------
     * 加载插件
     +----------------------------------------------------------
     * @access private 
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    function loadPlugIn()
    {
        // 如果存在缓存插件数据，直接包含
        if(file_exists(CONFIG_PATH.'_appPlugins.php')) {
            $plugins    = include_once CONFIG_PATH.'_appPlugins.php';
        }else {
            // 检查插件数据
            $common_plugins = get_plugins(THINK_PATH.'/PlugIns','Think');// 公共插件
            $app_plugins = get_plugins();// 项目插件
            // 合并插件数据
            $plugins    = array_merge($common_plugins,$app_plugins);   
            // 缓存插件数据
            $content  = "<?php\nreturn ".var_export($plugins,true).";\n?>";
            file_put_contents(CONFIG_PATH.'_appPlugins.php',$content);              
        }

        //加载有效插件文件
        foreach($plugins as $key=>$val) {
            include_cache($val['file']);
        }
        return ;
    }

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
    function exec()
    {
		//检查应用模块
        $moduleClass = MODULE_NAME.'Action';
        import(APP_NAME.'.Action.'.$moduleClass);
        if(version_compare(PHP_VERSION, '5.0.0', '>')) {
            if(!class_exists($moduleClass,false)) {
                // 如果应用类库不存在
                // 检查是否有插件模块
                $_module = Session::get('_modules');
                if(isset($_module[APP_NAME.'_'.MODULE_NAME])) {
                    $moduleClass = $_module[APP_NAME.'_'.MODULE_NAME];
                }
                if(!class_exists($moduleClass,false)) {
                    //仍然没有发现模块类，抛出异常
                    throw_exception(L('_MODULE_NOT_EXIST_').MODULE_NAME);
                }
            }
        }else {
            if(!class_exists($moduleClass)) {
                // 如果应用类库不存在
                // 检查是否有插件模块
                $_module = Session::get('_modules');
                if(isset($_module[APP_NAME.'_'.MODULE_NAME])) {
                    $moduleClass = $_module[APP_NAME.'_'.MODULE_NAME];
                }
                if(!class_exists($moduleClass)) {
                    //仍然没有发现模块类，抛出异常
                    throw_exception(L('_MODULE_NOT_EXIST_').MODULE_NAME);
                }
            }        	
        }
        //创建Action控制器实例
        $module  = & new $moduleClass();

        //获取当前操作名
        $moduleAction = ACTION_NAME; 
        if (!method_exists($module,$moduleAction)) {    
            // 如果当前模块类的操作方法不存在
            // 检查是否有插件操作
            $_action = Session::get('_actions');
            if(isset($_action[APP_NAME.'_'.$moduleClass][$moduleAction])) {
                // 检查是否提供当前模块的插件操作
            	$moduleAction = $_action[APP_NAME.'_'.$moduleClass][$moduleAction];
            }elseif(isset($_action[APP_NAME.'_'.'public'][$moduleAction])) {
                // 检查是否提供公共模块的插件操作
            	$moduleAction = $_action[APP_NAME.'_'.'public'][$moduleAction];
            }
            if(!is_callable($moduleAction)) {
                // 检查是否存在模版 如果有直接输出模版
                if(file_exists(TMPL_FILE_NAME)) {
                	$module->display();
                }else { 
					// 如果定义了_empty操作 则调用
					if(method_exists($module,'_empty')) {
						$module->_empty();
					}else {
						// 如果定义操作无法调用 抛出异常
						throw_exception(L('_ERROR_ACTION_').ACTION_NAME);       
					}
                }
            }else{
				//执行操作方法
				call_user_func(array($module,$moduleAction));
			}
        }else {
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
        }
        // 执行应用结束过滤器
        apply_filter('app_end');
        // 写入错误日志
        if(C('WEB_LOG_RECORD'))
            system_out(trim(implode('',$this->debug)));

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
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
	function run() {
		$this->init();
		$this->exec();
		return ;
	}

    /**
     +----------------------------------------------------------
     * 自定义异常处理
     +----------------------------------------------------------
     * @access protected 
     +----------------------------------------------------------
     * @param mixed $e 异常对象
     +----------------------------------------------------------
     */
    function appException($e)
    {
        halt($e->__toString());
    }

    /**
     +----------------------------------------------------------
     * 自定义错误处理
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
    function appError($errno, $errstr, $errfile, $errline)
    {
      switch ($errno) {
          case E_ERROR: 
          case E_USER_ERROR: 
              $errorStr = "错误：[$errno] $errstr ".basename($errfile)." 第 $errline 行.\n";
              if(C('WEB_LOG_RECORD')){
                 system_out($errorStr);
              }
              halt($errorStr);
              break;
          case E_STRICT:break;//屏蔽PHP5 E_STRICT 错误
          case E_USER_WARNING:
          case E_USER_NOTICE:
          default: 
              if(C('WEB_LOG_RECORD')){
                $errorStr = "注意：[$errno] $errstr ".basename($errfile)." 第 $errline 行.\n";
                $this->debug[] = $errorStr;
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
        return 'Think_'.THINK_VERSION.' '.$this->name.' '.$this->id;
    }

};//类定义结束
?>