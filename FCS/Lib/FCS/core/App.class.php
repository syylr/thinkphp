<?php 
/*
+---------------------------------------------------------+
| 项目: FCS -- Fast,Compatible & Simple OOP PHP Framework |
| 文件: app.class.php									  |
| 功能: WEB应用程序类									  |
+---------------------------------------------------------+
| 本框架代码基于GPL协议，可免费使用，但必须保留版权信息	  |
| 版权声明: Copyright◎ 2005-2006 世纪流年 版权所有		  |
| 主 页:	http://www.liu21st.com						  |
| 作 者:	Liu21st <流年> liu21st@gmail.com			  |
+---------------------------------------------------------+
*/

class App extends Base {//应用程序类定义开始

	// +----------------------------------------
	// |	私有属性
	// +----------------------------------------

		var $App		= Array();	//应用程序属性数组
		
	// +----------------------------------------
	// |	架构函数
	// +----------------------------------------

		function __construct($appName='App'){	
			$this->App['name'] = $appName;
			$this->App['id'] = time();
		}

	// +----------------------------------------
	// |	取得应用类实例
	// +----------------------------------------
	function &getInstance() {
		static $Instance = array();
		$className = get_class($this);
		if (!isset($Instance[$className])) {
			$Instance[$className] = new App();
		}
		return $Instance[$className];
	}

	//+----------------------------------------
	//|	应用程序初始化
	//+----------------------------------------
	function Init(){
		//设定错误讯息回报的等级
		//error_reporting(E_ERROR | E_WARNING | E_PARSE);
		set_error_handler(array(&$this,"AppError"));
		if(PHP_VERSION >'5.0.0') set_exception_handler(array(&$this,"AppException"));


		//缓存页面
		ob_start();	
		//加载配置文件
		include_once(FCS_PATH."/Conf/".APP_NAME.".ini.php");
		//includeOnce(FCS_PATH.'/Conf/config.php');
		//开启session
		ini_set('session.cookie_domain', COOKIE_DOMAIN);//跨域访问Session
		session_start();
		//set_time_limit(100);
		ini_set('max_execution_time', 186400); 
		header("Cache-control: private");  //支持页面回跳
		header("Content-Type:text/html; charset=".OUTPUT_CHARSET);//网页字符编码

		//地址转发
		if(PATHINFO_URL){
			$this->Dispatcher();
		}
		return ;
	}

	//+----------------------------------------
	//|	URL地址转发
	//+----------------------------------------
	function Dispatcher(){
		if ($_GET) {
			$_PATHINFO = array_merge($this->getPathInfo(),$_GET);
			if(isset($_GET[VAR_MODULE])){
				unset($_PATHINFO[VAR_ACTION]);
			}
			$_URL = '/';
			foreach ($_PATHINFO as $_VAR => $_VAL) { 
				if(!empty($_VAL)){
					if(URL_MODEL==1){
						$_URL .= strtolower($_VAR).PATH_DEPR.$_VAL."/";
					}else if(URL_MODEL==2){
						$_URL .= strtolower($_VAR).PATH_DEPR.$_VAL.",";
					}
				}
			}
			if(URL_MODEL==2) $_URL = substr($_URL, 0, -1).'/';
			$jumpUrl = $_SERVER["SCRIPT_NAME"].$_URL;
			if(!headers_sent()){
				//重定向成规范的URL格式
				header("Location: ".$jumpUrl); 
				ob_end_clean();					//清空缓存
				exit;
			}
		}else {
			//给_GET赋值 以保证可以按照正常方式取_GET值
			$_GET = $this->getPathInfo();
		}
		return ;
		  
	}
	//+----------------------------------------
	//|	启动应用程序
	//+----------------------------------------
	function Exec(){
		//运行检查
		$this->Check();

		// 载入具体应用模块类
		$moduleClassFile = APPS_PATH.APP_NAME.'/'.ACTION_DIR.'/'.MODULE_NAME.'Action.class.php';
		if (file_exists($moduleClassFile)) requireOnce($moduleClassFile);
		if (!file_exists($moduleClassFile) || !class_exists(''.MODULE_NAME.'Action')) {
			ThrowException(_MODULE_NOT_EXIST_.MODULE_NAME);
		}

		//创建应用模块对象实例
		$moduleClass = MODULE_NAME.'Action';
		$module  = & new $moduleClass();

		//检查操作
		$moduleAction = ACTION_PREFIX.ACTION_NAME; 
		if (!method_exists($module,$moduleAction)) {	
			ThrowException(_ERROR_ACTION_.ACTION_NAME);
		}

		//执行操作
		$module->$moduleAction();
		return ;

	}
	//+----------------------------------------
	//|	执行检查
	//+----------------------------------------
	function Check(){

		//取得模块和操作名称
		define('MODULE_NAME', $this->getModule());		// Module名称
		define('ACTION_NAME', $this->getAction());		// Action操作
		unset($_GET[VAR_MODULE],$_GET[VAR_ACTION]);
		$_POST = varFilter($_POST); // 过滤 _POST 数组
		$_GET = varFilter($_GET);
		if(HTML_CACHE_ON){
			//生成唯一的静态文件名
			if(!empty($_GET))	
				define('HTML_FILE_APPEND', "_".implode("_",$_GET));
			else 
				define('HTML_FILE_APPEND', "");
		}
		//语言和模板检查
		$this->CheckLanguage();
		$this->CheckTemplate();
	}

	//+----------------------------------------
	//|	语言检查
	//+----------------------------------------
	function CheckLanguage(){
		//检测浏览器支持语言
		$langSet = detectLanguage();
		setlocale(LC_ALL, $langSet);
		//读取系统语言包
		if (!file_exists(FCS_PATH."/Lang/".$langSet.".php")) {
			$langSet = DEFAULT_LANGUAGE;
		}
		includeOnce(FCS_PATH."/Lang/".$langSet.".php");	
		//读取项目语言包
		if (file_exists(FCS_PATH."/Lang/".APP_NAME.'/'.$langSet.".php")) {
			includeOnce(FCS_PATH."/Lang/".APP_NAME.'/'.$langSet.".php");	
		}
		define('CHAR_SET',$langSet); // 
		return ;
	}

	//+----------------------------------------
	//|	模板检查
	//+----------------------------------------
	function CheckTemplate(){
		if ( isset($_GET['template']) ) {
			$templateSet = $_GET['template'];
			$_COOKIE['templateSet'] = $templateSet;
		} else {
			if ( isset($_COOKIE['templateSet']) ) {
				$templateSet = $_COOKIE['templateSet'];
			}
			else {
				$templateSet =	DEFAULT_TEMPLATE_NAME;
			}
		}
		if (!is_dir(TMPL_PATH.$templateSet)) {
			ThrowException(_TEMPLATE_NOT_EXIST_);
		}
		define('TEMPLATE_NAME',$templateSet); //
		define('TEMPLATE_PATH',TMPL_PATH.TEMPLATE_NAME); // 
		//模板Module路径
		define('TEMPLATE_MODULE_PATH',APP_NAME.'/'.MODULE_NAME.'/');
		//CACHE文件名 绝对路径
		define('CACHE_FILE_NAME',CACHE_PATH.TEMPLATE_MODULE_PATH.ACTION_NAME.CACHFILE_SUFFIX);
		//模板文件名 绝对路径
		define('TMPL_FILE_NAME',TMPL_PATH.TEMPLATE_NAME.'/'.TEMPLATE_MODULE_PATH.ACTION_NAME.TEMPLATE_SUFFIX);
		if(HTML_CACHE_ON){
		//HTML文件名 绝对路径
		define('HTML_FILE_NAME',HTML_PATH.TEMPLATE_MODULE_PATH.ACTION_NAME.HTML_FILE_APPEND.HTMLFILE_SUFFIX);
		}
		return ;
	}

	//+----------------------------------------
	//|	获得PATH_INFO信息
	//+----------------------------------------
	function getPathInfo($name='')
	{
		$pathInfo = Array();
		if(isset($_SERVER['PATH_INFO'])) {
			$res = preg_replace('@(\w+)'.PATH_DEPR.'([^,\/]+)@e', '$pathInfo[\'\\1\']="\\2";', $_SERVER['PATH_INFO']);
			
		}
		$pathInfo = varFilter($pathInfo);
		if(!empty($name)){
			return $pathInfo[$name];
		}else{
			return $pathInfo;
		}
		
	}

	//+----------------------------------------
	//|	获得访问Module
	//+----------------------------------------
	function getModule()
	{
		$module = isset($_POST[VAR_MODULE]) ? $_POST[VAR_MODULE] : $_GET[VAR_MODULE];
		if (empty($module)) $module = DEFAULT_MODULE; // 如果 $module 为空，则赋予默认值
		return strtolower($module); 
	}

	//+----------------------------------------
	//|	获取访问Action
	//+----------------------------------------
	function getAction()
	{
		$action	 = isset($_POST[VAR_ACTION]) ? $_POST[VAR_ACTION] : $_GET[VAR_ACTION];
		if (empty($action)) $action = DEFAULT_ACTION;
		if(ALLOW_ACTION_PREFIX){
			$actionPrefix = isset($_POST[VAR_ACTION]) ? POST_ACTION_PREFIX : GET_ACTION_PREFIX;
			define('ACTION_PREFIX', $actionPrefix);
		}else{
			define('ACTION_PREFIX', '');
		}
		return strtolower($action); 
	}

	//+----------------------------------------
	//|	自定义异常处理
	//+----------------------------------------
	function AppException($e)
	{
		halt($e->__toString());
	}

	//+----------------------------------------
	//|	自定义错误处理
	//+----------------------------------------
	function AppError($errno, $errstr, $errfile, $errline)
	{
	  switch ($errno) {
	  case E_USER_ERROR: 
		  $errorStr = "[$errno] $errstr ".basename($errfile)." 第 $errline 行";
		  if(DEBUG_MODE){
			 Log::Write($errorStr);
		  }
		  halt($errorStr);
		  break;
	  case E_USER_WARNING:
	  case E_USER_NOTICE:
	  default: 
		  if(DEBUG_MODE){
			$errorStr = "注意：[$errno] $errstr ".basename($errfile)." 第 $errline 行.\n";
			Log::Write($errorStr,WEB_LOG_DEBUG);
		  }
		  break;
	  }
	}


	//+----------------------------------------
	//|	析构函数
	//+----------------------------------------
	function __destruct(){

	}

};//类定义结束
?>