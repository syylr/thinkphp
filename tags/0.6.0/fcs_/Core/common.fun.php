<?php 
/*
+--------------------------------------------------------
| 项目: FCS -- Fast,Compatible & Simple OOP PHP Framework
| 版本: 0.6.0
| PHP:	4.3.0 以上
| 文件: common.fun.php
| 功能:  公用函数库
| 最后修改：2006-2-9
+--------------------------------------------------------
| 版权声明: Copyright◎ 2005-2006 世纪流年 版权所有
| 主 页:	http://www.liu21st.com
| 作 者:	Liu21st <流年> liu21st@gmail.com 
+--------------------------------------------------------
*/

	//+----------------------------------------
	//|	获取系统时间 微秒
	//+----------------------------------------
	function getmicrotime() {
		return round(array_sum(split(' ', microtime())),6);
	}

	//+----------------------------------------
	//|	加载公共模板
	//+----------------------------------------
	function loadPublicTemplate($tmplPublicName){
		$tmplTemplateFile =  TEMPLATE_PATH.'/'.APP_NAME.'/'.MODULE_NAME.'/'.$tmplPublicName.TEMPLATE_SUFFIX;
		$tmplContent = '';
		if(file_exists($tmplTemplateFile)){
			$tmplContent = file_get_contents($tmplTemplateFile);
		}
		return $tmplContent;
		
	}

	//+----------------------------------------
	//|	错误输出
	//+----------------------------------------
	Function halt($fStr) {
		if(DEBUG_MODE){//调试模式下输出错误信息
			$output = "<table width='100%' height='85%' align='center'>"
				."<tr><td valign='middle' align=center>"
				."<div style='text-align:center;width:45%;border:1pt dotted gray;background:#FFFFEC'><div style='margin:8;font-size:9pt;line-height:150%;color:orangered;'>"
				.nl2br(htmlspecialchars($fStr))
				."</div></div></td></tr></table>";
			exit($output);
		}else {//否则定向到错误页面
			if(ERROR_PAGE_URL!=''){
				header("Location: ".ERROR_PAGE_URL); 
			}else {
				$output = "<table width='100%' height='85%' align='center'>"
				."<tr><td valign='middle' align=center>"
				."<div style='text-align:center;width:45%;border:1pt dotted gray;background:#FFFFEC'><div style='margin:8;font-size:9pt;line-height:150%;color:orangered;'>"
				.'发生错误！'
				."</div></div></td></tr></table>";
				exit($output);
			}
			
		}
	} 

	//+----------------------------------------
	//|	自定义异常处理 支持 PHP4和PHP5
	//+----------------------------------------
	function ThrowException($msg,$file='')
	{
		if(PHP_VERSION < '5.0.0'){
			halt($msg);
		}else {
			//throw new MyException($msg,$file);
			$my = & new MyException($msg,$file);
			halt($my->__toString());
		}
	}



	//+----------------------------------------
	//|	自动转换字符集
	//+----------------------------------------
	Function autoCharSet($fContents){
		if(OUTPUT_CHARSET === TEMPLATE_CHARSET){
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
	//|	优化的include_once
	//+----------------------------------------
	function includeOnce($filename){
		static $ImportFiles = array();
		if(file_exists($filename)){
			$basename = basename($filename);
			if (!isset($ImportFiles[$basename])) {
				include($filename);
				$ImportFiles[$basename] = true;
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
			$basename = basename($filename);
			if (!isset($ImportFiles[$basename])) {
				require($filename);
				$ImportFiles[$basename] = true;
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
		  if($class_strut[sizeof($class_strut)-1] == "*"){
			  //包含 * 符号导入该目录下面所有的类库 包含子目录
			   $tmp_class_strut = $class_strut;
			   unset($tmp_class_strut[sizeof($tmp_class_strut)-1]);
			   $tmp_base_class = $baseUrl.implode("/",$tmp_class_strut);
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
			$classfile = $baseUrl.implode("/",$class_strut).".class.php";
			if(file_exists($classfile))
				return includeOnce($classfile);
		  }
	   
	} 

	//+----------------------------------------
	//|	变量安全过滤
	//+----------------------------------------
	Function varFilter (& $fStr) {
		if (is_array($fStr)) {
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
		}
		Return $fStr;
	}

	//+----------------------------------------
	//|	Magic函数，在new 自定义对象时候自动包含需要的库文件
	//| 系统自动加载下列类库
	//| com.liu21st.core
	//| com.liu21st.util
	//| com.liu21st.db
	//| com.liu21st.exception
	//+----------------------------------------
	function __autoLoad($var){
		$auto = array('com.liu21st.core','com.liu21st.util','com.liu21st.db','com.liu21st.exception');
		foreach($auto as $val){
			if(import($val.'.'.$var))	return;
		}
		halt("错误：不能载入".$var." 类库。");

	}

?>