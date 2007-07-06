<?php 
/*
+---------------------------------------------------------+
| 项目: FCS -- Fast,Compatible & Simple OOP PHP Framework |
| 文件: Template.class.php								  |
| 功能: FCS内置模板引擎类								  |
+---------------------------------------------------------+
| 本框架代码基于GPL协议，可免费使用，但必须保留版权信息	  |
| 版权声明: Copyright◎ 2005-2006 世纪流年 版权所有		  |
| 主 页:	http://www.liu21st.com						  |
| 作 者:	Liu21st <流年> liu21st@gmail.com			  |
+---------------------------------------------------------+
*/

class Template extends Base{

	// +----------------------------------------
	// |	模板显示变量
	// |	未经定义的变量不会显示在页面中
	// +----------------------------------------
	var $Tpl	=	Array();	//在页面中显示的变量
	var $Repeat =	Array();	//循环数据集的数组
	var $Vo		=   Array();	//在页面中显示的数据对象
	var $VoList =   Array();	//在页面中显示的对象列表

	//+----------------------------------------
	//|	架构函数
	//+----------------------------------------
	function __construct()
	{

	}

	// +----------------------------------------
	// | 取得模板类实例
	// +----------------------------------------
	function &getInstance() {
		static $Instance = array();
		$className = get_class($this);
		if (!isset($Instance[$className])) {
			$Instance[$className] = new Template();
		}
		return $Instance[$className];
	}

	// +----------------------------------------
	// | 模板变量赋值
	// +----------------------------------------
	function assign($name,$value){
		//限制为字符串变量才能输出
		if(is_string($value)){
			$this->Tpl[$name] = $value;
		}
	}

	// +----------------------------------------
	// | 把Vo对象输出到页面
	// | 只有放入该数组的Vo才可以在模板中用 {Vo:name|property}来显示
	// +----------------------------------------
	function assignVo($name,$Vo,$index=0){
		if( !is_a($Vo,'Vo')){
			ThrowException('模板赋值使用非法的数据类型！');
		}
		$this->Vo[$name] = $Vo ;
		$VoArray = $Vo->__toArray();
		foreach ( $VoArray as $key => $val){
			//为了避免变量名称重复，所有Vo对象的变量输出添加前缀
			$this->assign(get_class($Vo).'_'.$index.'_'.$key,$val);
		}

	}

	// +----------------------------------------
	// | 把Vo对象集输出到页面
	// | 只有放入该数组的VoList才可以在模板中显示
	// +----------------------------------------
	function assignVoList($name,$VoList){
		if( !is_a($VoList,'VoList')){
			ThrowException('模板赋值使用非法的数据类型！');
		}
		$this->VoList[$name] = $VoList ;
		$VoArray = $VoList->toArray();
		foreach ( $VoArray as $key => $Vo){
			$this->assignVo(get_class($Vo),$Vo,$key);
		}

	}

	// +----------------------------------------
	// | 取得模板变量的值
	// +----------------------------------------
	function get($name){
		return $this->Tpl[$name];
	}

	// +----------------------------------------
	// | 取得模板输出Vo对象
	// +----------------------------------------
	function getVo($name){
		return $this->Vo[$name];
	}

	// +----------------------------------------
	// | 取得输出的Vo对象列表
	// +----------------------------------------
	function getVoList($name){
		return $this->VoList[$name];
	}

	//+----------------------------------------
	//|	加载模板和页面输出
	//+----------------------------------------
	function Display($templateFile='',$varPrefix=''){
		
		//如果开启HTML功能，定向到静态页面，无需再次执行
		if(HTML_CACHE_ON){
			if ($this->checkHTML()) {
				if(!headers_sent()){
					header(sprintf("Location: %s",
						str_replace (HTML_PATH,WEB_URL.'/'.HTML_DIR,HTML_FILE_NAME))); 
					ob_end_clean();					//清空缓存
					exit;
				}
			}
		}

		//加载模板
		$this->loadTemplate($templateFile,$varPrefix);

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
	//|	编译模板文件
	//+----------------------------------------
	function compiler (& $tmplContent)
	{
		//模板解析
		$tmplContent = $this->TemplateParse($tmplContent);
		//同步路径
		$tmplContent = $this->syncPath($tmplContent);
		//编码转换
		$tmplContent = autoCharset($tmplContent);

		return $tmplContent;
	}

	//+----------------------------------------
	//| 模板解析
	//+----------------------------------------
	function TemplateParse(& $Content){

		//解析模板标签{}
		$Content = preg_replace('/('.TMPL_L_DELIM.')(.+?)('.TMPL_R_DELIM.')/eis',"\$this->TagParse('\\2')",$Content);

		return $Content;

	}

	//+----------------------------------------
	//| 模板标签解析
	//| 格式： {TagName:args [|content] }
	//+----------------------------------------
	function TagParse($TagStr){
		//还原非模板标签 
		//标签过滤
		if(preg_match('/^\s/is',$TagStr)){
			return '{'.$TagStr.'}';
		}
		//解析模板变量 格式 {$varName}
		if(substr($TagStr,0,1)=='$'){
			return $this->VarParse(substr($TagStr,1));
		}
		$TagStr = trim($TagStr);
		//注释标签
		if(substr($TagStr,0,2)=='//' || (substr($TagStr,0,2)=='/*' && substr($TagStr,0,2)=='*/')){
			return '';
		}
		//解析其它标签
		//统一标签格式 {TagName:args [|content]}
		$varArray = explode(':',$TagStr);
		//取得标签名称
		$tag = trim(array_shift($varArray));

		//解析标签内容
		$args = explode('|',$varArray[0],2);
		switch(strtoupper($tag)){
			case 'INCLUDE':
				$parseStr = $this->ParseInclude($args[0]);
				break;
			case 'REPEAT':
				$parseStr = $this->ParseRepeat($args[0],$args[1]);
				break;
			case 'VO':
				$parseStr = $this->ParseVo($args[0],$args[1]);
				break;
			case 'VOLIST':
				$parseStr = $this->ParseVoList($args[0],$args[1]);
				break;
			//这里扩展其它标签
			//…………
			default:$parseStr = '';break;
		}
		return $parseStr;
	}

	//+----------------------------------------
	//| 模板变量解析
	//| 支持使用函数
	//|	格式： {$varname|function1|function2=arg1,arg2}
	//+----------------------------------------
	function VarParse($varStr){
		$varStr = trim($varStr);
		static $varParseList = array();
		//如果已经解析过该变量字串，则直接返回变量值
		if(isset($varParseList[$varStr])) return $varParseList[$varStr];
		$parseStr =''; 
		$varExists = true;
		if(!empty($varStr)){
			$varArray = explode('|',$varStr);
			//取得变量名称
			$var = array_shift($varArray);
			//非法变量过滤 只允许使用 {$var} 形式模板变量
			//TODO：还需要继续完善
			if(preg_match('/->/is',$var)){
				return '';
			}
			//检测变量是否有定义，防止输出Notice错误
			if(substr($var,0,4)!='FCS.' && !isset($this->Tpl[$var])) 
				$varExists = false;
			//特殊变量
			if(substr($var,0,4)=='FCS.'){
				$name = $this->FCSvarParse($var);
			}else {
				$name = "$$var";
			}
			//对变量使用函数
			$name = $this->VarFunction($name,$varArray);

			if( $name=="''" || empty($name) ) $varExists = false;
			//变量存在而且有值就echo
			if( $varExists ){
				$parseStr = '<?php echo '.$name.' ?>';
			}
		}
		$varParseList[$varStr] = $parseStr;
		return $parseStr;
	}

	//+----------------------------------------
	//| 对模板变量使用函数
	//| 格式 {$varname|function1|function2=arg1,arg2}
	//+----------------------------------------
	function VarFunction($name,$varArray){
		//对变量使用函数
		$length = count($varArray);
		//取得模板禁止使用函数列表
		$template_deny_funs = explode(',',TMPL_DENY_FUNC_LIST);
		for($i=0;$i<$length ;$i++ ){
			$args = explode('=',$varArray[$i]);
			//模板函数过滤
			$args[0] = trim($args[0]);
			if(!in_array($args[0],$template_deny_funs)){
				if(isset($args[1])){
					if(strstr($args[1],'###')){
						$args[1] = str_replace('###',$name,$args[1]);
						$name = "$args[0]($args[1])";
					}else{
						$name = "$args[0]($name,$args[1])";
					}
				}else if(!empty($args[0])){
					$name = "$args[0]($name)";
				}
			}
		}
		return $name;
	}

	//+----------------------------------------
	//| 特殊模板变量解析
	//| 格式 以 $FCS. 打头的变量属于特殊模板变量
	//+----------------------------------------
	function FCSVarParse($varStr){
		$vars = explode('.',$varStr);
		$vars[1] = strtoupper(trim($vars[1]));
		$parseStr = '';

		if(count($vars)==3){
			$vars[2] = trim($vars[2]);
			switch($vars[1]){
				case 'SERVER':$parseStr = $_SERVER[$vars[2]];break;
				case 'GET':$parseStr = $_GET[$vars[2]];break;
				case 'POST':$parseStr = $_POST[$vars[2]];break;
				case 'COOKIE':$parseStr = $_COOKIE[$vars[2]];break;
				case 'SESSION':$parseStr = $_SESSION[$vars[2]];break;
				case 'ENV':$parseStr = $_ENV[$vars[2]];break;
				case 'REQUEST':$parseStr = $_REQUEST[$vars[2]];break;
				case 'CONST':$parseStr = $vars[2];break;
				default:break;
			}
		}else if(count($vars)==2){
			switch($vars[1]){
				case 'NOW':$parseStr = date('Y-m-d g:i a',time());break;
				case 'VERSION':$parseStr = FCS_VERSION;break;	
				case 'TEMPLATE':$parseStr = TMPL_FILE_NAME;break;
				case 'LDELIM':$parseStr = TMPL_L_DELIM;break;
				case 'RDELIM':$parseStr = TMPL_R_DELIM;break;
			}
			if(defined($vars[1])){ $parseStr = strval(constant($vars[1]));}
		}
		if($vars[1]!='CONST'){
			$parseStr = '\''.$parseStr.'\'';
		}
		return $parseStr;
	}

	//+----------------------------------------
	//| 处理循环部分
	//| $offset和$length用于处理循环数据集中的一部分
	//+----------------------------------------
	function ParseRepeat($id, $tmplRepeat,$offset=0,$length=0){ 
	   $tmplContent = '';
	   $repeat = $length==0? array_slice($this->Repeat[trim($id)],$offset) :array_slice($this->Repeat[trim($id)],$offset,$length);
	   foreach ((array)$repeat as $row){
			// 用vsprintf()代换每行中的变量'%n$s'，源代码用\n换行
		   $tmplContent .= vsprintf( str_replace('%0$s',$key,$tmplRepeat),$row )."\n";
		}
	   return stripslashes($tmplContent);
	} 

	//+----------------------------------------
	//| 显示Vo对象的属性
	//+----------------------------------------
	function ParseVo($name,$val){
		 $name = trim($name);
		 $varArray = explode('|',$val);
		 //取得Vo对象的属性名称
		 $property = trim(array_shift($varArray));
		 if(substr($property,0,1)=='$'){
			 $property = substr($property,1);
		 }
		 $tmplContent = '';
		 if(isset($this->Vo[$name])){
			 $Vo = $this->Vo[$name];
			 if(property_exists($Vo,$property)){
				 $tmplContent = $Vo->$property;
				 $tmplContent = "$".get_class($Vo).'_0_'."$property";
			 }
		 }
		 if(count($varArray)>0){
			 $tmplContent = $this->VarFunction($tmplContent,$varArray);
		 }
		 $tmplContent = '<?php echo '.$tmplContent.' ?>';
		 return  $tmplContent;
	}

	//+----------------------------------------
	//| 循环显示VoList对象的属性
	//+----------------------------------------
	function ParseVoList($name,$val){
		 $name = trim($name);
		 $val  = trim($val);
		 $tmplContent = Array();
		 if(isset($this->VoList[$name])){
			 $VoList = $this->VoList[$name];
			 $VoArray = $VoList->toArray();
			 foreach ($VoArray as $key => $Vo){
				$propertys = $Vo->__varList();
				$tmplContent[$key] = $val;
				foreach ($propertys as $property){
				$tmplContent[$key] = str_replace('$'.$property,'<?php echo $'.get_class($Vo).'_'.$key.'_'.$property.' ?>',$tmplContent[$key]);
				}
			 }
		 }
		$tmplContent = implode('',$tmplContent);
		$tmplContent = stripslashes($tmplContent);
		return  $tmplContent;
	}

	//+----------------------------------------
	//|	加载主模板并缓存
	//+----------------------------------------
	function loadTemplate ($tmplTemplateFile='',$varPrefix='')
	{
		$tmplContent = '';
		if(empty($tmplTemplateFile))	$tmplTemplateFile = TMPL_FILE_NAME;
		if(!file_exists($tmplTemplateFile)){
			ThrowException(_TEMPLATE_NOT_EXIST_);
		}
		if($tmplTemplateFile==TMPL_FILE_NAME){
			$tmplCacheFile = CACHE_FILE_NAME;
		}else{
			$tmplCacheFile = str_replace(array(TMPL_PATH.TEMPLATE_NAME,TEMPLATE_SUFFIX),array(CACHE_PATH,CACHFILE_SUFFIX),$tmplTemplateFile);
		}
		// 检查Cache文件是否需要更新
		if (!$this->checkCache($tmplCacheFile)) {	
			$tmplContent .= $this->readTemplate($tmplTemplateFile);		//读出原模板内容
			$this->writeCache($tmplCacheFile,$tmplContent);			//重写Cache文件
		}

		$this->Tpl = autoCharSet($this->Tpl);
		// 模板阵列变量分解成为独立变量
		extract($this->Tpl, empty($varPrefix)? EXTR_OVERWRITE : EXTR_PREFIX_ALL,$varPrefix); 

		include_once($tmplCacheFile);	//载入Cache文件

		return;
	}

	//+----------------------------------------
	//|	加载公共模板并缓存
	//+----------------------------------------
	function ParseInclude($tmplPublicName){
		$tmplTemplateFile =  TEMPLATE_PATH.'/'.APP_NAME.'/'.MODULE_NAME.'/'.strtolower(trim($tmplPublicName)).TEMPLATE_SUFFIX;
		$tmplContent = $this->readTemplate($tmplTemplateFile);
		return $this->TemplateParse($tmplContent);
		
	}
	//+----------------------------------------
	//|	读取循环模板文件 （暂时保留）
	//+----------------------------------------
	function getSubTmplFileContent($subTmplName)
	{
		$subTmplName	= TEMPLATE_PATH.TEMPLATE_MODULE_PATH.ACTION_NAME.".".$subTmplName.TEMPLATE_SUFFIX;
		$subTmplContent = explode("<!--TMPL:Repeat-->",$this->readTemplate($subTmplName));
		$subTmplContent = $this->syncPath($subTmplContent);
		return $subTmplContent;
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
	//|	同步模板中的路径
	//+----------------------------------------
	function syncPath($tmplContent)
	{
		$ModulePath  = WEB_URL.'/'.TMPL_DIR.'/'.TEMPLATE_NAME.'/'.APP_NAME.'/public';
		$tmplContent = str_replace('../public',$ModulePath,$tmplContent);
		$tmplContent = str_replace('charset='.TEMPLATE_CHARSET, 'charset='.OUTPUT_CHARSET, $tmplContent);

		return $tmplContent;
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
		}
		elseif (!TMPL_CACHE_ON){
			return false;
		}elseif (filemtime($tmplTemplateFile) > filemtime($tmplCacheFile)) { // 源模板文件是否更新
			return False; 
		} elseif (TMPL_CACHE_TIME != -1 && time() > filemtime($tmplCacheFile)+TMPL_CACHE_TIME*60) { 
			// 缓存是否在有效期
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
		} elseif (HTML_CACHE_TIME != -1 && time() > filemtime($tmplHTMLFile)+HTML_CACHE_TIME*60) { 
			// 缓存是否在有效期
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
	//|	清除缓存或者静态文件
	//+----------------------------------------
	function cleanCache($filename) 
	{ 
		if(!file_exists($filename) && !@unlink($filename)){
			ThrowException('清除缓存失败！');
		}
		return;
	} 

	//+----------------------------------------
	//|	清除缓存目录
	//+----------------------------------------
	function cleanAll($dirname) 
	{ 
		return delDir($dirname);
	} 

};
?>