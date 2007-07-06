<?php 
/*
+--------------------------------------------------------
| 项目: FCS -- Fast,Compatible & Simple OOP PHP Framework 
| 版本: 0.6.0 
| PHP:	4.3.0 以上
| 文件: app.class.php
| 功能:  WEB应用程序类
| 最后修改：2006-2-9
+--------------------------------------------------------
| 版权声明: Copyright◎ 2005-2006 世纪流年 版权所有
| 主 页:	http://www.liu21st.com
| 作 者:	Liu21st <流年> liu21st@gmail.com 
+--------------------------------------------------------
*/

class App extends Base {//应用程序类定义开始


	//+----------------------------------------
	//|	私有属性
	//+----------------------------------------

		var $App	=	Array();		//属性数组
		
	//+----------------------------------------
	//|	模板显示变量
	//|	模板中只会显示Tpl数组中定义的变量
	//+----------------------------------------
		
		var $Tpl	=	Array();
	
	//+----------------------------------------
	//|	架构函数
	//+----------------------------------------

		function __construct($appName='App'){	
			$this->App['name'] = $appName;
		}

	//+----------------------------------------
	//|	应用程序初始化
	//+----------------------------------------
	function Init(){
		//设定错误讯息回报的等级
		error_reporting(E_ERROR | E_WARNING | E_PARSE);
		set_error_handler(array(&$this,"AppError"));
		if(PHP_VERSION >'5.0.0') set_exception_handler(array(&$this,"AppException"));

		//缓存页面
		ob_start();	
		//加载配置文件
		define('FCS_PATH', dirname(dirname(__FILE__)));
		includeOnce(FCS_PATH.'/Conf/config.php');
		//开启session
		ini_set('session.cookie_domain', COOKIE_DOMAIN);//跨域访问Session
		session_start();
		//set_time_limit(100);
		ini_set('max_execution_time', 186400); 
		header("Cache-control: private");  //支持页面回跳
		header("Content-Type:text/html; charset=".OUTPUT_CHARSET);//网页字符编码

		//地址转发
		$this->Dispatcher();
	
		return ;
	}

	//+----------------------------------------
	//|	URL地址转发
	//+----------------------------------------
	function Dispatcher(){
		if ($_GET) {
			$_PATHINFO = array_merge($this->getPathInfo(),$_GET);
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
			if(!empty($_POST))	$_POST = varFilter($_POST); // 过滤 _POST 数组
			//取得模块和操作名称
			define('MODULE_NAME', $this->getModule());		// Module名称
			define('ACTION_NAME', $this->getAction());		// Action操作
			unset($_GET[VAR_MODULE],$_GET[VAR_ACTION]);
			if(HTML_CACHE_ON){
				//生成唯一的静态文件名
				if(!empty($_GET))	
				define('HTML_FILE_APPEND', "_".implode("_",$_GET));
			}

		}
		return ;
		  
	}
	//+----------------------------------------
	//|	启动应用程序
	//+----------------------------------------
	function Run(){
		//运行检查
		$this->Check();

		// 载入Main类 即每个应用类都允许有自己的公共类
		$mainClassFile = APPS_PATH.APP_NAME."/common.class.php";
		if (file_exists($mainClassFile)) {
			requireOnce ($mainClassFile);
		}
		// 载入具体应用执行类
		$moduleClassFile = APPS_PATH.APP_NAME.'/'.MODULE_NAME.'.class.php';
		if (file_exists($moduleClassFile)) requireOnce($moduleClassFile);
		if (!file_exists($moduleClassFile) || !class_exists('mod_'.MODULE_NAME)) {
			ThrowException(_MODULE_NOT_EXIST_.MODULE_NAME,__METHOD__);
		}
		//把控制权交给应用类
		$moduleClass = 'mod_'.MODULE_NAME;
		$module  = & new $moduleClass();
		return ;
	}
	//+----------------------------------------
	//|	执行检查
	//+----------------------------------------
	function Check(){
		//语言和模板检查
		$this->CheckLanguage();
		$this->CheckTemplate();
	}

	//+----------------------------------------
	//|	执行操作
	//+----------------------------------------
		function Exec(){
			// 检查应用 Action
			$moduleAction = ACTION_PREFIX.ucfirst(ACTION_NAME); 
			if (!method_exists($this,$moduleAction)) {	
				ThrowException(_ERROR_ACTION_.ACTION_NAME,__METHOD__);
			}
			$this->$moduleAction();
			return ;
		}

	//+----------------------------------------
	//|	加载模板和页面输出
	//+----------------------------------------
	function Display(){
		//如果开启HTML功能，定向到静态页面，无需再次执行
		if(HTML_CACHE_ON){
			if ($this->checkHTML()) {
				if(!headers_sent()){
					header(sprintf("Location: %s",
						str_replace (HTML_PATH,WEB_ROOT.'/'.HTML_DIR,HTML_FILE_NAME))); 
					ob_end_clean();					//清空缓存
					exit;
				}
			}
		}

		//加载模板
		$this->loadTemplate();
		if(HTML_CACHE_ON){//开启HTML功能
			//检查并重写HTML文件
			if (!$this->checkHTML()) {
				$this->writeHTML(HTML_FILE_NAME,ob_get_contents());
			}
		}
		//header('Content-Length: ' . ob_get_length());
		while (ob_get_level() > 0)  ob_end_flush();
		//ob_end_clean();					//清空缓存

		return ;
	}


	//+----------------------------------------
	//|	语言检查
	//+----------------------------------------
	function CheckLanguage(){
		if ( isset($_GET['lang']) ) {
			$langSet = $_GET['lang'];
			$_COOKIE['langSet'] = $langSet;
		} else {
			if ( !isset($_COOKIE['langSet']) ) {
				$langSet = explode(",",$_SERVER["HTTP_ACCEPT_LANGUAGE"]);
				$langSet = $langSet[0];
				$_COOKIE['langSet'] = $langSet;
			}
			else {
				$langSet = $_COOKIE['langSet'];
			}
		}
		if (file_exists(FCS_PATH."/Lang/".$langSet.".php")) {
			includeOnce(FCS_PATH."/Lang/".$langSet.".php");
		}else {
			ThrowException(_LANGUAGE_NOT_LOAD_);
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
			ThrowException(_TEMPLATE_NOT_EXIST_,__METHOD__);
		}
		define('TEMPLATE_NAME',$templateSet); //
		define('TEMPLATE_PATH',TMPL_PATH.TEMPLATE_NAME); // 
		//模板Module路径
		define('TEMPLATE_MODULE_PATH','/'.APP_NAME.'/'.MODULE_NAME.'/');
		//CACHE文件名 绝对路径
		define('CACHE_FILE_NAME',CACHE_PATH.TEMPLATE_MODULE_PATH.ACTION_NAME.CACHFILE_SUFFIX);
		//模板文件名 绝对路径
		define('TMPL_FILE_NAME',TMPL_PATH.TEMPLATE_NAME.TEMPLATE_MODULE_PATH.ACTION_NAME.TEMPLATE_SUFFIX);
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
			//$res = preg_replace('@(\w+),(\w+)@e', '$pathInfo[\\1]="\\2";', $_SERVER['PATH_INFO']);
			//2006-1-17 完善了支持中文值的正则，之前的变量和值都只能是英文
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
	//|	检查缓存文件是否需要更新
	//+----------------------------------------
	function checkCache($tmplCacheFile=CACHE_FILE_NAME)
	{
		if($tmplCacheFile == CACHE_FILE_NAME){
			$tmplTemplateFile = TMPL_FILE_NAME;
		}else{
			$tmplTemplateFile = str_replace(array(CACHE_PATH,CACHFILE_SUFFIX),array(TMPL_PATH.TEMPLATE_NAME,TEMPLATE_SUFFIX),$tmplCacheFile);
		}
		if(!file_exists($tmplCacheFile)){
			$tmplCahceFileDir     = substr($tmplCacheFile,0,strrpos($tmplCacheFile,"/"));
			$tmplCacheFileDirDown = $tmplCahceFileDir;
			$tmplCacheFileDirUp   = substr($tmplCacheFileDirDown,0,strrpos($tmplCacheFileDirDown,"/"));
			if (!file_exists($tmplCacheFileDirUp)) { // 缓存文件目录是否存在
				mkdir($tmplCacheFileDirUp);
			}
			if (!file_exists($tmplCacheFileDirDown)) {
				mkdir($tmplCacheFileDirDown);
			}
			return False;
		} elseif (filemtime($tmplTemplateFile) > filemtime($tmplCacheFile)) { // 源模板文件是否更新
			return False; 
		} elseif (TMPL_CACHE_TIME != -1 && time() > filemtime($tmplCacheFile)+TMPL_CACHE_TIME*60) { // 源模板文件是否更新
			return False; 
		}
		return True;
	}

	//+----------------------------------------
	//|	检查是否存在静态HTML文件
	//+----------------------------------------
	function checkHTML($tmplHTMLFile = HTML_FILE_NAME)
	{
		if(!file_exists($tmplHTMLFile)){
			$tmplHTMLFileDir     = substr($tmplHTMLFile,0,strrpos($tmplHTMLFile,"/"));
			$tmplHTMLFileDirDown = $tmplHTMLFileDir;
			$tmplHTMLFileDirUp   = substr($tmplHTMLFileDirDown,0,strrpos($tmplHTMLFileDirDown,"/"));
			if (!file_exists($tmplHTMLFileDirUp)) { // 缓存文件目录是否存在
				mkdir($tmplHTMLFileDirUp);
			}
			if (!file_exists($tmplHTMLFileDirDown)) {
				mkdir($tmplHTMLFileDirDown);
			}
			return False;
		} elseif (HTML_CACHE_TIME != -1 && time() > filemtime($tmplHTMLFile)+HTML_CACHE_TIME*60) { // 源模板文件是否更新
			return False; 
		}
		return True;
	}

	//+----------------------------------------
	//|	读取模板文件
	//+----------------------------------------
	function readTemplate($tmplTemplateFile) 
	{ 
		$fp = @fopen($tmplTemplateFile, 'rb');
		@flock($fp, LOCK_SH);
		$tmplContent = @fread($fp, filesize($tmplTemplateFile));
		@fclose($fp);
		//return file_get_contents($tmplTemplateFile);
		return $tmplContent;
	} 

	//+----------------------------------------
	//|	生成缓存文件
	//+----------------------------------------
	function writeCache($tmplCacheFile,& $tmplContent) 
	{ 
		//编译模板内容
		$tmplContent = $this->compiler($tmplContent);
		$len = strlen($tmplContent);
		if ( $len > 0) {
			$fp = fopen($tmplCacheFile, 'w'); 
			flock($fp, LOCK_EX);
			fwrite($fp, $tmplContent,$len); 
			flock($fp, LOCK_UN);
			fclose($fp); 
		}
		return;
	} 

	//+----------------------------------------
	//|	生成HTML文件
	//+----------------------------------------
	function writeHTML($tmplHtmlFile,& $tmplContent) 
	{ 
		$len = strlen($tmplContent);
		if ( $len > 0) {
			$fp = fopen($tmplHtmlFile, 'w'); 
			flock($fp, LOCK_EX);
			fwrite($fp, $tmplContent,$len); 
			flock($fp, LOCK_UN);
			fclose($fp); 
		}
		return;
	} 


	//+----------------------------------------
	//|	编译模板文件
	//+----------------------------------------
	function compiler (& $tmplContent)
	{
		//加载公共页面
		$tmplContent = preg_replace('/(<!--TMPL:)(.+?)(-->)/eis',"loadPublicTemplate('\\2')",$tmplContent);
		//替换合法变量. 例：$abc -> <?php echo $abc ? >
		$tmplContent = preg_replace('/(\{\$)([^->,\[\]]+?)(\})/is', '<?php echo $\\2 ?>', $tmplContent); 
		//过滤其它形式变量 只允许使用 {$var} 形式模板变量
		$tmplContent = preg_replace('/(\{\$)(.+?)(\})/is', '', $tmplContent); 
		//替换编码
		$tmplContent = str_replace('charset='.TEMPLATE_CHARSET, 'charset='.OUTPUT_CHARSET, $tmplContent); 
		
		//替换特殊字符
		//TODO
		$tmplContent = $this->syncPath($tmplContent);//同步模板路径
		
		return $tmplContent;
	}

	//+----------------------------------------
	//|	替换模板文件变量
	//+----------------------------------------
	function tmplVarReplace(& $tmplContent)
	{
		// 替换模板变量{$var} 为 $var 格式，方便替换变量值
		$tmplContent = preg_replace('/(\{\$)(.+?)(\})/is', '$\\2', $tmplContent); 
		extract($this->Tpl, EXTR_OVERWRITE); // 模板阵列变量分解成为独立变量
		$temp  = AddSlashes($tmplContent);
		eval( "\$temp = \"$temp\";" );
		$temp  = StripSlashes($temp);
		return $temp;
	}

	//+----------------------------------------
	//|	加载主模板并缓存
	//+----------------------------------------
	function loadTemplate ($tmplTemplateFile=TMPL_FILE_NAME)
	{
		$tmplContent = '';
		if(!file_exists($tmplTemplateFile)){
			ThrowException(_TEMPLATE_FILE_NOT_EXIST_);
		}
		if($tmplTemplateFile==TMPL_FILE_NAME){
			$tmplCacheFile = CACHE_FILE_NAME;
		}else{
			$tmplCacheFile = str_replace(array(TMPL_PATH.TEMPLATE_NAME,TEMPLATE_SUFFIX),array(CACHE_PATH,CACHFILE_SUFFIX),$tmplTemplateFile);
		}
		// 检查Cache文件是否需要更新
		if (!$this->checkCache($tmplCacheFile)) {	
			$tmplContent .= $this->readTemplate($tmplTemplateFile);		//读出原模板内容
			$tmplContent = autoCharset($tmplContent);
			$this->writeCache($tmplCacheFile,$tmplContent);			//重写Cache文件
	
		}
		$this->Tpl = autoCharSet($this->Tpl);
		extract($this->Tpl, EXTR_OVERWRITE); // 模板阵列变量分解成为独立变量
		include_once($tmplCacheFile);	//载入Cache文件
		return;
	}

	//+----------------------------------------
	//|	读取循环模板文件
	//+----------------------------------------
	function getSubTmplFileContent($subTmplName)
	{
		$subTmplName	= TEMPLATE_PATH.TEMPLATE_MODULE_PATH.ACTION_NAME.".".$subTmplName.TEMPLATE_SUFFIX;
		$subTmplContent = explode("<!--TMPL:Repeat-->",$this->readTemplate($subTmplName));
		$subTmplContent = $this->syncPath($subTmplContent);
		return $subTmplContent;
	}

	//+----------------------------------------
	//|	同步模板中的路径
	//+----------------------------------------
	function syncPath($tmplContent)
	{
		$ModulePath  = WEB_ROOT.'/'.TMPL_DIR.'/'.TEMPLATE_NAME.TEMPLATE_MODULE_PATH;
		$tmplContent = str_replace('../public',$ModulePath.'../public',$tmplContent);
		$tmplContent = str_replace('charset='.TEMPLATE_CHARSET, 'charset='.OUTPUT_CHARSET, $tmplContent);
		return $tmplContent;
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
		  $this->AppLog($errstr);
		  halt("错误：[$errno] $errstr ".basename($errfile)." 第 $errline 行");
		  break;
	  case E_USER_WARNING:
	  case E_USER_NOTICE:
	  default: //ECHO("注意：[$errno] $errstr ".basename($errfile)." 第 $errline 行.<BR>");
		  break;
	  }
	}

	//+----------------------------------------
	//|	日志处理
	//+----------------------------------------
	function AppLog($message,$type=WEB_LOG_ERROR,$file='')
	{
		if(WEB_LOG_RECORD){
			$now = date('y-m-d H:i:s');
			switch($type){
				case WEB_LOG_DEBUG:$logType ='[调试]';break;
				default :$logType ='[错误]';
			}
			$destination = $file == ''? LOG_PATH."log.txt" : $file;
			if(!is_writable($destination)){
				ThrowException('目录(文件)'.$destination.'不可写');
			}
			error_log($now.$logType.str_replace(array("\n", "\r", "\t"), ' ', $message)."\n\r", FILE_LOG,$destination );
		}
	}

	//+----------------------------------------
	//|	析构函数
	//+----------------------------------------
	function __destruct(){

	}
	

	//+----------------------------------------
	//|	自动变量设置
	//+----------------------------------------
	function __set($name ,$value)
	{
		$this->App[$name]= $value;
	}

	//+----------------------------------------
	//|	自动变量获取
	//+----------------------------------------
	function __get($name)
	{
		if( array_key_exists( $name, $this->App ) )  
			return $this->App[$name];
	}

	//+----------------------------------------
	//|	自动检测变量
	//+----------------------------------------
	function __isset( $name )
	{       
		return array_key_exists( $name, $this->App );   
	} 
	
	//+----------------------------------------
	//|	自动卸载变量
	//+----------------------------------------
	function __unset( $name )    
	{        
		if( array_key_exists( $name, $this->App ) )       
			unset ($this->App[$key]);        
	}

	//+----------------------------------------
	//|	获取访问Action
	//+----------------------------------------
	function __call( $func, $args )    
	{        
		ThrowException("方法[ $func ]不存在或参数有误！");        
	}

	//+----------------------------------------
	//|	应用类输出
	//+----------------------------------------
	function __toString()
	{
		return $this->name;
	}


};//类定义结束
?>