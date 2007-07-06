<?php 
/*
+---------------------------------------------------------+
| 项目: FCS -- Fast,Compatible & Simple OOP PHP Framework |
| 文件: functions.php									  |
| 功能: FCS公共函数库									  |
+---------------------------------------------------------+
| 本框架代码基于GPL协议，可免费使用，但必须保留版权信息	  |
| 版权声明: Copyright◎ 2005-2006 世纪流年 版权所有		  |
| 主 页:	http://www.liu21st.com						  |
| 作 者:	Liu21st <流年> liu21st@gmail.com			  |
+---------------------------------------------------------+
*/

	//+----------------------------------------
	//|	检测浏览器语言
	//+----------------------------------------
	function detectLanguage()
	{
		if ( isset($_GET['lang']) ) {
			$langSet = $_GET['lang'];
			$_COOKIE['langSet'] = $langSet;
		} else {
			if ( !isset($_COOKIE['langSet']) ) {
				preg_match('/^([a-z\-]+)/i', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $matches);
				$langSet = $matches[1];
				//$langSet = explode(",",$_SERVER["HTTP_ACCEPT_LANGUAGE"]);
				//$langSet = $langSet[0];
				$_COOKIE['langSet'] = $langSet;
			}
			else {
				$langSet = $_COOKIE['langSet'];
			}
		}
		return $langSet;
	}

	//+----------------------------------------
	//|	错误输出
	//+----------------------------------------
	Function halt($error) {
		$publicDir = WEB_URL.'/'.TMPL_DIR.'/'.TEMPLATE_NAME.'/public/';
		$output = "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'><meta http-equiv='Content-Type' content='text/html; charset=utf-8' /><title>出现错误</title><link rel='stylesheet' type='text/css' href='".$publicDir."css/style.css' />";
		if(DEBUG_MODE){//调试模式下输出错误信息
			if(is_array($error)){
				$output .= "<div class='errorFrame'><h3 style='margin:3pt;color:orange'> 我们很抱歉的告诉您，您浏览的页面出现了错误！</h3><h2 class='errorMessage' ><IMG SRC='".$publicDir."images/update.gif' class='img' align='absmiddle' BORDER='0'>错误信息：&lt".nl2br(htmlspecialchars($error['message']))."&gt</h2>"
				."<div style='margin:3pt'>"
				."<div class='errorContent'><IMG SRC='".$publicDir."images/arrow.gif' class='img' align='absmiddle' BORDER='0'>错误页面：".WEB_URL.$_SERVER["PHP_SELF"]."</div>"
				."<div class='errorContent'><IMG SRC='".$publicDir."images/arrow.gif' class='img' align='absmiddle' BORDER='0'>错误类型：".$error['type']."</div>"
				."<div class='errorContent'><IMG SRC='".$publicDir."images/arrow.gif' class='img' align='absmiddle' BORDER='0'>详细描述：".$error['detail']."</div>"
				."<div class='errorContent'><IMG SRC='".$publicDir."images/arrow.gif' class='img' align='absmiddle' BORDER='0'>错误文件：".$error['file']."(".$error['line'].")</div>"
				."<div class='errorContent'><IMG SRC='".$publicDir."images/arrow.gif' class='img' align='absmiddle' BORDER='0'>错误跟踪：<br/>".nl2br($error['trace'])."</div></div></div><P/><div class='logo'> FCS <sup style='color:gray;font-size:9pt'>".FCS_VERSION."</sup><span style='color:silver'> { Fast,Compatible & Simple OOP PHP Framework }</span></div>";
			}else{
				$output .= "<div class='errorFrame'><div style='margin:3pt;font:bold 12pt Tahoma;color:orange'>".WEB_TITLE."</div><h2 class='errorMessage' ><IMG SRC='".$publicDir."images/update.gif' class='img' align='absmiddle' BORDER='0'>错误信息：&lt".nl2br(htmlspecialchars($error))."&gt</h2>"
				."<div style='margin:3pt'><div class='errorContent'><IMG SRC='".$publicDir."images/arrow.gif' class='img' align='absmiddle' BORDER='0'>错误页面：".WEB_URL.$_SERVER["PHP_SELF"]."</div></div></div><P/><div class='logo'> FCS <sup style='color:gray;font-size:9pt'>".FCS_VERSION."</sup><span style='color:silver'> { Fast,Compatible & Simple OOP PHP Framework }</span></div>";
			}
			exit($output);
		}else {//否则定向到错误页面
			if(ERROR_PAGE_URL!=''){
				header("Location: ".ERROR_PAGE_URL); 
			}else {
				$output .= "<div class='errorFrame'><div style='margin:3pt;font:bold 12pt Tahoma;color:orange'>".WEB_TITLE."</div><h2 class='errorMessage'><IMG SRC='".$publicDir."images/update.gif' class='img' align='absmiddle' BORDER='0'>".DEFAULT_ERROR_MESSAGE."</h2>"
				."<div style='margin:3pt'><div class='errorContent'><IMG SRC='".$publicDir."images/arrow.gif' class='img' align='absmiddle' BORDER='0'>错误页面：".WEB_URL.$_SERVER["PHP_SELF"]."</div></div></div><P/><div class='logo'> FCS <sup style='color:gray;font-size:9pt'>".FCS_VERSION."</sup><span style='color:silver'> { Fast,Compatible & Simple OOP PHP Framework }</span></div>";
				exit($output);
			}
			
		}
	} 

	//+----------------------------------------
	//|	自定义异常处理 支持 PHP4和PHP5
	//+----------------------------------------
	function ThrowException($msg,$type='')
	{
		if(!empty($type)){
			$e = & new $type($msg);
		}else {
			$e = & new FCSException($msg);
		}
		halt($e->__toString());
	}

	//+----------------------------------------
	//|	系统调试输出
	//+----------------------------------------
	function systemOut($msg){
		Log::Write($msg,WEB_LOG_DEBUG);
	}

	//+----------------------------------------
	//|	自动转换字符集
	//+----------------------------------------
	Function autoCharSet($fContents){
		if( strtoupper(OUTPUT_CHARSET) === strtoupper(TEMPLATE_CHARSET)){
			return $fContents;
		}
		if(is_array($fContents)){
			foreach ( $fContents AS $key => $val ) {
				$fContents[$key] = autoCharSet($val);
			}
			return $fContents;
		}else{
			if(function_exists('iconv')){
				Return iconv(TEMPLATE_CHARSET,OUTPUT_CHARSET,$fContents);
			}
			elseif(function_exists('mb_convert_encoding')){
				return mb_convert_encoding ($fContents, OUTPUT_CHARSET, TEMPLATE_CHARSET);
			}else{
				halt('您的系统不支持自动编码转换！');
				return $fContents;
			}
		
		}
	}

	//+----------------------------------------
	//|	变量安全过滤
	//+----------------------------------------
	Function varFilter (& $fStr) {
		if (is_array($fStr)) {
			if (MAGIC_QUOTES_GPC) {
			   $_GET    = array_map('stripslashes', $_GET);
			   $_POST  = array_map('stripslashes', $_POST);
			   $_COOKIE = array_map('stripslashes', $_COOKIE);
			}
			foreach ( $fStr AS $_arrykey => $_arryval ) {
				if ( is_string($_arryval) ) {
					$fStr["$_arrykey"] = trim($fStr["$_arrykey"]);	// 去除左右两端空格
					$fStr["$_arrykey"] = htmlspecialchars($fStr["$_arrykey"]);	// 将特殊字元转成 HTML 格式
					$fStr["$_arrykey"] = str_replace(",", "\,", $fStr["$_arrykey"]);
					$fStr["$_arrykey"] = str_replace("javascript", "javascript ", $fStr["$_arrykey"]);	// 禁止 javascript
				}else if (is_array($_arryval)){
					$fStr["$_arrykey"] = varFilter($_arryval);
				}
			}
		} else {
			$fStr = trim($fStr);									// 去除左右两端空格
			$fStr = htmlspecialchars($fStr);						// 将特殊字元转成 HTML 格式
			$fStr = str_replace("javascript", "j avascript", $fStr);// 禁止 javascript
			if (MAGIC_QUOTES_GPC) $fStr = stripslashes($fStr);
		}
		Return $fStr;
	}

	//+----------------------------------------
	//|	Magic函数，在new 自定义对象时候自动包含需要的库文件
	//| 系统自动加载FCS基类库
	//| com.liu21st.core
	//| com.liu21st.util
	//| com.liu21st.db
	//| com.liu21st.exception
	//+----------------------------------------
	function __autoLoad($var){
		$auto = array('FCS.core','FCS.util','FCS.db','FCS.exception');
		foreach($auto as $val){
			if(import($val.'.'.$var))	return;
		}
		halt("不能载入".$var." 类库。");

	}

	//+----------------------------------------
	//|	系统函数优化和扩展
	//+----------------------------------------

	//+----------------------------------------
	//|	优化的include_once
	//+----------------------------------------
	function includeOnce($filename){
		static $ImportFiles = array();
		if(file_exists($filename)){
			if (!isset($ImportFiles[$filename])) {
				include($filename);
				$ImportFiles[$filename] = true;
				return true;
			}
			return false;

		}
		return false;
	}


	//+----------------------------------------
	//|	优化的require_once
	//+----------------------------------------
	function requireOnce($filename){
		static $ImportFiles = array();
		if(file_exists($filename)){
			if (!isset($ImportFiles[$filename])) {
				require($filename);
				$ImportFiles[$filename] = true;
				return true;
			}
			return false;
		}
		return false;
	}

	//+----------------------------------------
	//|	导入所需的类库 支持目录和* 同java的Import
	//+----------------------------------------
	function import($class,$baseUrl = LIB_PATH){
		  if(substr($baseUrl, -1) != "/")	$baseUrl .= "/";
		  $class_strut = explode(".",$class);
		  if(array_pop($class_strut) == "*"){
			  //包含 * 符号导入该目录下面所有的类库 包含子目录
			   $tmp_base_class = $baseUrl.implode("/",$class_strut);
			   $dir = dir($tmp_base_class);
			   while (false !== ($entry = $dir->read())) {
					//如果是特殊目录继续
					if($entry == "." || $entry == "..")   continue;
					//如果是目录 ，递归调用import
					if(is_dir($tmp_base_class.'/'.$entry)){	
						import('*',$tmp_base_class.'/'.$entry.'/');
					}else{	
						//导入类库文件 后缀必须为 class.php
						if(strpos($entry, ".class.php")){
							includeOnce($tmp_base_class.'/'.$entry);
						}
					}
			   }
			   $dir->close(); 
			   return true;

		  }else{
			//导入目录下的指定类库文件
			$classfile = $baseUrl.str_replace('.', '/', $class).'.class.php';
			if(file_exists($classfile))
				return includeOnce($classfile);
		  }
	   
	} 

	//+----------------------------------------
	//|	import方法的别名
	//+----------------------------------------
	function using($class,$baseUrl = LIB_PATH){
		return import($class,$baseUrl);
	}

	//+----------------------------------------
	//|	获取include的内容
	//+----------------------------------------
	function get_include_contents($filename) { 
	 if (is_file($filename)) { 
		 ob_start(); 
		 include $filename; 
		 $contents = ob_get_contents(); 
		 ob_end_clean(); 
		 return $contents; 
	 } 
	 return false; 
	} 

	//+----------------------------------------
	//|	PHP4 兼容处理函数库
	//+----------------------------------------

	//+----------------------------------------
	//|	判断对象的属性是否存在 PHP5.1.0以上已经定义
	//+----------------------------------------
	if (!function_exists('property_exists')) {
		function property_exists($class, $property) {
			if (is_object($class))
			 $class = get_class($class);
			return array_key_exists($property, get_class_vars($class));
		}
	}

	//+----------------------------------------
	//|	用一个数组的值作为其键名，另一个数组的值作为其值
	//+----------------------------------------
	if(!function_exists('array_combine')){
		function array_combine($keys,$vals){
			$combine = array();
			foreach($keys as $index => $key)
			   $combine[$key] = $vals[$index];
			return $combine ;
		}
	}

	
?>