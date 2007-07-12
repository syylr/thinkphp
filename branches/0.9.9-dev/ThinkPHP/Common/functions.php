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

/**
 +------------------------------------------------------------------------------
 * Think公共函数库
 +------------------------------------------------------------------------------
 * @copyright  Copyright (c) 2005-2006 liu21st.com.  All rights reserved. 
 * @author     liu21st <liu21st@gmail.com>
 * @version    $Id$
 +------------------------------------------------------------------------------
 */

function mk_dir($dir, $mode = 0755)
{
  if (is_dir($dir) || @mkdir($dir,$mode)) return TRUE;
  if (!make_dir(dirname($dir),$mode)) return FALSE;
  return @mkdir($dir,$mode);
}

/**
 +----------------------------------------------------------
 * 检测浏览器语言
 +----------------------------------------------------------
 * @return string
 +----------------------------------------------------------
 */
function detect_browser_language()
{
    if ( isset($_GET[C('VAR_LANGUAGE')]) ) {
        $langSet = $_GET[C('VAR_LANGUAGE')];
        setcookie('Think_'.C('VAR_LANGUAGE'),$langSet,time()+3600,'/');
    } else {
        if ( !isset($_COOKIE['Think_'.C('VAR_LANGUAGE')]) ) {
            preg_match('/^([a-z\-]+)/i', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $matches);
            $langSet = $matches[1];
            setcookie('Think_'.C('VAR_LANGUAGE'),$langSet,time()+3600,'/');
        }
        else {
            $langSet = $_COOKIE['Think_'.C('VAR_LANGUAGE')];
        }
    }
    return $langSet;
}

/**
 +----------------------------------------------------------
 * 错误输出 
 * 在调试模式下面会输出详细的错误信息
 * 否则就定向到指定的错误页面
 +----------------------------------------------------------
 * @param mixed $error 错误信息 可以是数组或者字符串
 * 数组格式为异常类专用格式 不接受自定义数组格式
 +----------------------------------------------------------
 * @return void
 +----------------------------------------------------------
 */
function halt($error) {
    $e = array();
    if(C('DEBUG_MODE')){
        //调试模式下输出错误信息
        if(!is_array($error)) {
            $trace = debug_backtrace();
            $e['message'] = $error;
            $e['file'] = $trace[0]['file'];
            $e['class'] = $trace[0]['class'];
            $e['function'] = $trace[0]['function'];
            $e['line'] = $trace[0]['line'];
            $traceInfo='';
            $time = date("y-m-d H:i:m");
            foreach($trace as $t)
            {
                $traceInfo .= '['.$time.'] '.$t['file'].' ('.$t['line'].') ';
                $traceInfo .= $t['class'].$t['type'].$t['function'].'(';
                $traceInfo .= implode(', ', $t['args']);
                $traceInfo .=")<br/>";
            }
            $e['trace']  = $traceInfo;
        }else {
        	$e = $error;
        }
        include(THINK_PATH.'/Lib/Think/Exception/ThinkException.tpl.php');
    }
    else 
    {
        //否则定向到错误页面
		$error_page	=	C('ERROR_PAGE');
        if(!empty($error_page)){
            redirect($error_page); 
        }else {
            $e['message'] = C('ERROR_MESSAGE');
            include(THINK_PATH.'/Lib/Think/Exception/ThinkException.tpl.php');
        }
    }
    exit;    	
}

/**
 +----------------------------------------------------------
 * URL重定向
 +----------------------------------------------------------
 * @static
 * @access public 
 +----------------------------------------------------------
 * @param string $url  要定向的URL地址
 * @param integer $time  定向的延迟时间，单位为秒
 * @param string $msg  提示信息
 +----------------------------------------------------------
 */
function redirect($url,$time=0,$msg='')
{
    //多行URL地址支持
    $url = str_replace(array("\n", "\r"), '', $url);
    if(empty($msg)) {
        $msg    =   "系统将在{$time}秒之后自动跳转到{$url}！";
    }
    if (!headers_sent()) {
        // redirect
        header("Content-Type:text/html; charset=".C('OUTPUT_CHARSET'));
        if(0===$time) {
        	header("Location: ".$url); 
        }else {
            header("refresh:{$time};url={$url}");
            echo($msg);
        }
        exit();
    }else {
        $str    = "<meta http-equiv='Refresh' content='{$time};URL={$url}'>";
        if($time!=0) {
            $str   .=   $msg;
        }
        exit($str);
    }
}

/**
 +----------------------------------------------------------
 * 自定义异常处理 支持 PHP4和PHP5
 +----------------------------------------------------------
 * @param string $msg 错误信息
 * @param string $type 异常类型 默认为ThinkException
 * 如果指定的异常类不存在，则直接输出错误信息
 +----------------------------------------------------------
 * @return void
 +----------------------------------------------------------
 */
function throw_exception($msg,$type='ThinkException',$code=0)
{
    if(isset($_REQUEST['ajax']) || (isset($_REQUEST['_AJAX_SUBMIT_']) && $_REQUEST['_AJAX_SUBMIT_']) ) {
        header("Content-Type:text/html; charset=utf-8");
        exit($msg);
    }
    if(IS_PHP4){ 
        if(class_exists($type)){
            $e = & new $type($msg,$code);
            halt($e->__toString());
        }else {// 异常类型不存在则输出错误信息字串
            halt($msg);
        }
    }else {
        if(class_exists($type,false)){
            // PHP5使用 throw关键字抛出异常
            // 出于兼容考虑包含下面语句实现
            // throw new $type($msg,$code);
            include('_throw_exception.php');
        }else {// 异常类型不存在则输出错误信息字串
            halt($msg);
        }
    	
    }
}

/**
 +----------------------------------------------------------
 *  区间调试开始
 +----------------------------------------------------------
 * @param string $label  标记名称
 +----------------------------------------------------------
 * @return void
 +----------------------------------------------------------
 */
function debug_start($label='')
{
    $GLOBALS[$label]['_beginTime'] = array_sum(split(' ', microtime()));
    if ( MEMORY_LIMIT_ON )	$GLOBALS[$label]['memoryUseStartTime'] = number_format((memory_get_usage() / 1024));
}

/**
 +----------------------------------------------------------
 *  区间调试结束，显示指定标记到当前位置的调试
 +----------------------------------------------------------
 * @param string $label  标记名称
 +----------------------------------------------------------
 * @return void
 +----------------------------------------------------------
 */
function debug_end($label='')
{
    $GLOBALS[$label]['_endTime'] = array_sum(split(' ', microtime()));
    echo '<div style="text-align:center;width:100%">Process: '.$label.' Times '.number_format($GLOBALS[$label]['_endTime']-$GLOBALS[$label]['_beginTime'],6).'s </div>';
    if ( MEMORY_LIMIT_ON )	{
        $GLOBALS[$label]['memoryUseEndTime'] = number_format((memory_get_usage() / 1024));
        echo '<div style="text-align:center;width:100%">Process: '.$label.' Memorys '.number_format($GLOBALS[$label]['memoryUseEndTime']-$GLOBALS[$label]['memoryUseStartTime']).' k</div>';
    }
}

/**
 +----------------------------------------------------------
 * 系统调试输出 Log::Write 的一个调用方法
 +----------------------------------------------------------
 * @param string $msg 调试信息
 +----------------------------------------------------------
 * @return void
 +----------------------------------------------------------
 */
function system_out($msg)
{
    if(C('WEB_LOG_RECORD') && !empty($msg))
        Log::Write($msg,WEB_LOG_DEBUG);
}

/**
 +----------------------------------------------------------
 * 变量输出
 +----------------------------------------------------------
 * @param string $var 变量名
 * @param string $label 显示标签
 * @param string $echo 是否显示
 +----------------------------------------------------------
 * @return string
 +----------------------------------------------------------
 */
function dump($var, $label=null, $strict=true,$echo=true)
{
    $label = ($label===null) ? '' : rtrim($label) . ' ';
    if(!$strict) {
        if (ini_get('html_errors')) {
            $output = print_r($var, true);
            $output = "<pre>".$label.htmlspecialchars($output,ENT_QUOTES,C('OUTPUT_CHARSET'))."</pre>";
        } else {
            $output = $label . " : " . print_r($var, true);
        }    	
    }else {
        ob_start();
        var_dump($var);
        $output = ob_get_clean();
        if(!extension_loaded('xdebug')) {
            $output = preg_replace("/\]\=\>\n(\s+)/m", "] => ", $output);
            $output = '<pre>'
                    . $label
                    . htmlspecialchars($output, ENT_QUOTES,C('OUTPUT_CHARSET'))
                    . '</pre>';    	
        }    	
    }
    if ($echo) {
        echo($output);
        return null;
    }else {
    	return $output;
    }
}

/**
 +----------------------------------------------------------
 * 自动转换字符集 支持数组转换
 * 需要 iconv 或者 mb_string 模块支持
 * 如果 输出字符集和模板字符集相同则不进行转换
 +----------------------------------------------------------
 * @param string $fContents 需要转换的字符串
 +----------------------------------------------------------
 * @return string
 +----------------------------------------------------------
 */
function auto_charset($fContents,$from='',$to=''){
	if(empty($from)) $from = C('TEMPLATE_CHARSET');
	if(empty($to))  $to	=	C('OUTPUT_CHARSET');
    if( strtoupper($from) === strtoupper($to) || empty($fContents) || (is_scalar($fContents) && !is_string($fContents)) ){
        //如果编码相同或者非字符串标量则不转换
        return $fContents;
    }
    $from   =  strtoupper($from)=='UTF8'? 'utf-8':$from;
    $to       =  strtoupper($to)=='UTF8'? 'utf-8':$to;
    if(is_string($fContents) ) {
		if(function_exists('mb_convert_encoding')){
            return mb_convert_encoding ($fContents, $to, $from);
        }elseif(function_exists('iconv')){
            Return iconv($from,$to,$fContents);
        }else{
            halt(L('_NO_AUTO_CHARSET_'));
            return $fContents;
        }
    }
    elseif(is_array($fContents)){
        foreach ( $fContents AS $key => $val ) {
            $fContents[$key] = auto_charset($val,$from,$to);
        }
        return $fContents;
    }
    elseif(is_object($fContents)) {
		$vars = get_object_vars($fContents);
        foreach($vars as $key=>$val) {
            $fContents->$key = auto_charset($val,$from,$to);
        }
        return $fContents;
    }
    else{
        //halt('系统不支持对'.gettype($fContents).'类型的编码转换！');
        return $fContents;
    }
}

/**
 +----------------------------------------------------------
 * 取得对象实例 支持调用类的静态方法
 +----------------------------------------------------------
 * @param string $className 对象类名
 * @param string $method 类的静态方法名
 +----------------------------------------------------------
 * @return object
 +----------------------------------------------------------
 */
function get_instance_of($className,$method='',$args=array()) 
{
    static $_instance = array();
    if (!isset($_instance[$className])) {
        if(class_exists($className)){
            $o = new $className();
            if(method_exists($o,$method)){
                if(!empty($args)) {
                	$_instance[$className] = call_user_func_array(array(&$o, $method), $args);;
                }else {
                	$_instance[$className] = &$o->$method();
                }
            }
            else 
                $_instance[$className] = &$o;
        }
        else 
            halt(L('_CLASS_NOT_EXIST_'));
    }
    return $_instance[$className];
}

/**
 +----------------------------------------------------------
 * 系统自动加载ThinkPHP基类库和当前项目的Dao
 * 需要PHP5支持
 +----------------------------------------------------------
 * @param string $classname 对象类名
 +----------------------------------------------------------
 * @return void
 +----------------------------------------------------------
 */
function __autoload($classname)
{
	// 自动加载当前项目的Dao类
	if(substr($classname,-3)=="Dao") {
		import('@.Dao.'.$classname);
	}else {
		// 根据自动加载路径设置进行尝试搜索
		if(C('AUTO_LOAD_PATH')) {
			$paths	=	explode(',',C('AUTO_LOAD_PATH'));
			foreach ($paths as $path){
				if(import($path.$classname)) {
					// 如果加载类成功则返回
					return ;
				}
			}
		}
	}
	return ;
}

/**
 +----------------------------------------------------------
 * 反序列化对象时自动回调方法 
 +----------------------------------------------------------
 * @param string $classname 对象类名
 +----------------------------------------------------------
 * @return void
 +----------------------------------------------------------
 */
function unserialize_callback($classname) 
{
    $autoLoad = array('Vo','Util');
    foreach($autoLoad as $key=>$val) {
        if(import('@.'.$val.'.'.$classname) ) 	return ;
    }
    foreach($autoLoad as $key=>$val) {
        if(import('Admin.'.$val.'.'.$classname) ) 	return ;
    }
    halt(_UNSERIALIZE_CLASS_NOT_EXIST_.$classname);
}

/**
 +----------------------------------------------------------
 * 优化的include_once
 +----------------------------------------------------------
 * @param string $filename 文件名
 +----------------------------------------------------------
 * @return boolen
 +----------------------------------------------------------
 */
function include_cache($filename)
{
    static $_importFiles = array();
    if(file_exists($filename)){
        if (!isset($_importFiles[$filename])) {
            include($filename);
            $_importFiles[$filename] = true;
            return true;
        }
        return false;
    }
    return false;
}

/**
 +----------------------------------------------------------
 * 优化的require_once
 +----------------------------------------------------------
 * @param string $filename 文件名
 +----------------------------------------------------------
 * @return boolen
 +----------------------------------------------------------
 */
function require_cache($filename)
{
    static $_importFiles = array();
    if(file_exists($filename)){
        if (!isset($_importFiles[$filename])) {
			//echo $filename.'<br/>';
            require($filename);
            $_importFiles[$filename] = true;
            //$GLOBALS['LoadFile']++;//统计加载文件数
            return true;
        }
        return false;
    }
    return false;
}

/**
 +----------------------------------------------------------
 * 获取include的内容 
 +----------------------------------------------------------
 * @param string $filename 文件名
 +----------------------------------------------------------
 * @return false|string
 +----------------------------------------------------------
 */
function get_include_contents($filename) 
{ 
    if (is_file($filename)) { 
        ob_start(); 
        include $filename; 
        $contents = ob_get_clean(); 
        return $contents; 
    } 
    return false; 
} 

/**
 +----------------------------------------------------------
 * 导入所需的类库 支持目录和* 同java的Import
 * 本函数有缓存功能 
 +----------------------------------------------------------
 * @param string $class 类库命名空间字符串
 * @param string $baseUrl 起始路径
 * @param string $appName 项目名
 * @param string $ext 导入的文件扩展名
 * @param string $subdir 是否导入子目录 默认false
 +----------------------------------------------------------
 * @return boolen
 +----------------------------------------------------------
 */
function import($class,$baseUrl = '',$ext='.class.php',$subdir=false)
{
      //echo($class.$baseUrl.'<br>');
      static $_import_class = array();
      if(isset($_import_class[$class.$baseUrl]))
            return true;
      else 
            $_import_class[$class.$baseUrl] = true;
      //if (preg_match('/[^a-z0-9\-_.*]/i', $class)) throw_exception('Import非法的类名或者目录！');
      if( 0 === strpos($class,'@')) 	$class =  str_replace('@',APP_NAME,$class);
      if(empty($baseUrl)) {
            // 默认方式调用应用类库
      	    $baseUrl   =  dirname(LIB_PATH);
      }else {
            //相对路径调用
      	    $isPath =  true;
      }
      $class_strut = explode(".",$class);
      if('*' == $class_strut[0] || isset($isPath) ) {
      	//多级目录加载支持
        //用于子目录递归调用
      }
      elseif($class_strut[0]=='Admin') {
          //加载管理项目类库
          $class =  str_replace('Admin.','Lib.',$class);    
          $baseUrl =  ADMIN_PATH;
      }
      elseif(APP_NAME == $class_strut[0]) {
          //加载当前项目应用类库
          $class =  str_replace(APP_NAME.'.','Lib.',$class);
      }
      elseif(in_array(strtolower($class_strut[0]),array('think','org','com'))) {
          //加载ThinkPHP基类库或者公共类库
		  // think 官方基类库 org 第三方公共类库 com 企业公共类库
          $baseUrl =  THINK_PATH.'/Lib/';
      }else {
          // 加载其他项目应用类库
          $class =  str_replace($class_strut[0],'',$class);
          $baseUrl =  APP_PATH.'/../'.$class_strut[0].'/Lib/';
      }
      if(substr($baseUrl, -1) != "/")    $baseUrl .= "/";
      $classfile = $baseUrl.str_replace('.', '/', $class).$ext;
      if(array_pop($class_strut) == "*"){
          //包含 * 符号导入该目录下面所有的类库 
          //如果 subdir为true 则包含子目录
           $tmp_base_class = dirname($classfile);
           // 使用glob方式遍历目录 需要PHP 4.3.0以上版本
           $dir = glob ( $tmp_base_class . '/*'.$ext  );
           if($dir) {
               foreach($dir as $key=>$val) {
                    if( is_dir($val)){    
                        if($subdir) import('*',$val.'/',APP_NAME,$ext,$subdir);
                    }else{    
                        //导入类库文件 后缀默认为 class.php
                        require_cache($val);
                    }
               }           	
           }
           return true;
      }else{
            //导入目录下的指定类库文件
            return require_cache($classfile);          	
      }
  
} 
/**
 +----------------------------------------------------------
 * 文件追加写入 
 +----------------------------------------------------------
 * @param string $filename 文件名
 * @param string $data 数据
 +----------------------------------------------------------
 * @return boolean
 +----------------------------------------------------------
 */
function file_add_contents($filename,$data) 
{
        $len = strlen($data);
        if ( $len > 0 ) {
            $fp = fopen($filename, 'ab'); 
            flock($fp, LOCK_EX);
            $filesize =   fwrite($fp, $data,$len); 
            flock($fp, LOCK_UN);
            fclose($fp); 
            return true;
        }else {
        	return false;
        }
}
/**
 +----------------------------------------------------------
 * import方法的别名 
 +----------------------------------------------------------
 * @param string $package 包名
 * @param string $baseUrl 起始路径
 * @param string $ext 导入的文件扩展名
 * @param string $subdir 是否导入子目录 默认false
 +----------------------------------------------------------
 * @return boolean
 +----------------------------------------------------------
 */
function using($class,$baseUrl = LIB_PATH,$ext='.class.php',$subdir=false)
{
    return import($class,$baseUrl,$ext,$subdir);
}

/**
 +----------------------------------------------------------
 * 根据PHP各种类型变量生成唯一标识号 
 +----------------------------------------------------------
 * @param mixed $mix 变量
 +----------------------------------------------------------
 * @return string
 +----------------------------------------------------------
 */
function to_guid_string($mix)
{
    if(is_resource($mix)){
        $mix = get_resource_type($mix).strval($mix);
    }else{
        $mix = serialize($mix);
    }
    return md5($mix);
}

/**
 +----------------------------------------------------------
 * 获得迭代因子  使用foreach遍历对象
 +----------------------------------------------------------
 * @param mixed $values 对象元素
 +----------------------------------------------------------
 * @return string
 +----------------------------------------------------------
 */
function get_iterator($values) 
{
    if(IS_PHP4){
        //PHP4下面的ArrayObject模拟了Iterator接口
		import("Think.Util.ArrayObject");
        return new ArrayObject($values);
    }else {
        //ListIterator在PHP5中实现了Iterator接口
		import("Think.Util.ListIterator");
        return new ListIterator($values);
    }
}

/**
 +----------------------------------------------------------
 * 判断是否为对象实例
 +----------------------------------------------------------
 * @param mixed $object 实例对象
 * @param mixed $className 对象名
 +----------------------------------------------------------
 * @return boolean
 +----------------------------------------------------------
 */
function is_instance_of($object, $className)
{
   if(IS_PHP4){
        return is_a($object, $className);
   }
   else{
       $is = false;
       include ('_instanceof.php');
       return $is;
   }
}

/**
 +----------------------------------------------------------
 * 判断目录是否为空
 +----------------------------------------------------------
 * @return void
 +----------------------------------------------------------
 */
function empty_dir($directory)
{
    $handle = opendir($directory);
    while (($file = readdir($handle)) !== false)
    {
        if ($file != "." && $file != "..")
        {
            closedir($handle);
            return false;
        }
    }
    closedir($handle);
    return true;
}

function get_themes($app=APP_NAME,$ext='.php') 
{
	$path  = dirname(dirname(TMPL_PATH)).'/'.$app.'/Tpl';
    $dir = dir($path);
    if($dir) {
        $theme_files = array();
        while (false !== ($file = $dir->read())) {
            if(is_dir($path.'/'.$file)){    
                if (file_exists($path.'/'.$file.'/theme'.$ext)) 
                            $theme_files[] = $path.'/'.$file.'/theme'.$ext;
            }
        }    
        $dir->close(); 
    }
    $themes[$app] = array();
    foreach ($theme_files as $theme_file) {
        if ( !is_readable("$theme_file"))		continue;
        //取得插件文件的信息
        $theme_name = basename(dirname($theme_file));
        $theme_data = get_theme_info($theme_file,$theme_name);
        if (empty ($theme_data['name'])) {
            continue;
        }
        $themes[$app][] = $theme_data;
    }
    return $themes[$app]; 
}
/**
 +----------------------------------------------------------
 * 获取插件信息
 +----------------------------------------------------------
 * @param string $plugin_file 插件文件名
 +----------------------------------------------------------
 * @return Array
 +----------------------------------------------------------
 */
function get_theme_info($theme_file,$theme_name) {

	$theme_data = file_get_contents($theme_file);
	preg_match("/Theme URI:(.*)/i", $theme_data, $theme_uri);
	preg_match("/Description:(.*)/i", $theme_data, $description);
	preg_match("/Author:(.*)/i", $theme_data, $author_name);
	preg_match("/Author URI:(.*)/i", $theme_data, $author_uri);
	if (preg_match("/Version:(.*)/i", $theme_data, $version))
		$version = trim($version[1]);
	else
		$version = '';
    if(!empty($author_name)) {
        if(!empty($author_uri)) {
            $author_name = '<a href="'.trim($author_uri[1]).'" target="_blank">'.$author_name[1].'</a>';
        }else {
            $author_name = $author_name[1];
        }    	
    }else {
    	$author_name = '';
    }
	return array ('name' => trim($theme_name), 'uri' => trim($theme_uri[1]), 'description' => trim($description[1]), 'author' => trim($author_name), 'version' => $version);
}
/**
 +----------------------------------------------------------
 * 读取插件
 +----------------------------------------------------------
 * @param string $path 插件目录
 * @param string $app 所属项目名
 +----------------------------------------------------------
 * @return Array
 +----------------------------------------------------------
 */
function get_plugins($path=PLUGIN_PATH,$app=APP_NAME,$ext='.php') 
{
	static $plugins = array ();
    if(isset($plugins[$app])) {
    	return $plugins[$app];
    }
    // 如果插件目录为空 返回空数组
    if(empty_dir($path)) {
        return array();
    }
    $path = realpath($path);

    // 缓存无效 重新读取插件文件
    /*
    $dir = glob ( $path . '/*' );
    if($dir) {
       foreach($dir as $val) {
            if(is_dir($val)){    
                $subdir = glob($val.'/*'.$ext);
                if($subdir) {
                    foreach($subdir as $file) 
                        $plugin_files[] = $file;
                }
            }else{    
                if (strrchr($val, '.') == $ext) 
                    $plugin_files[] = $val;
            }
       } 
       */

    $dir = dir($path);
    if($dir) {
        $plugin_files = array();
        while (false !== ($file = $dir->read())) {
            if($file == "." || $file == "..")   continue;
            if(is_dir($path.'/'.$file)){    
                    $subdir =  dir($path.'/'.$file);
                    if ($subdir) {
                        while (($subfile = $subdir->read()) !== false) {
                            if($subfile == "." || $subfile == "..")   continue;
                            if (preg_match('/\.php$/', $subfile))
                                $plugin_files[] = "$file/$subfile";
                        }
                        $subdir->close();
                    }            
            }else{    
                $plugin_files[] = $file;
            }
        }    
        $dir->close(); 

        //对插件文件排序
        if(count($plugin_files)>1) {
            sort($plugin_files);
        }
        $plugins[$app] = array();
        foreach ($plugin_files as $plugin_file) {
            if ( !is_readable("$path/$plugin_file"))		continue;
            //取得插件文件的信息
            $plugin_data = get_plugin_info("$path/$plugin_file");
            if (empty ($plugin_data['name'])) {
                continue;
            }
            $plugins[$app][] = $plugin_data;
        }
       return $plugins[$app];    	
    }else {
    	return array();
    }

}

/**
 +----------------------------------------------------------
 * 获取插件信息
 +----------------------------------------------------------
 * @param string $plugin_file 插件文件名
 +----------------------------------------------------------
 * @return Array
 +----------------------------------------------------------
 */
function get_plugin_info($plugin_file) {

	$plugin_data = file_get_contents($plugin_file);
	preg_match("/Plugin Name:(.*)/i", $plugin_data, $plugin_name);
    if(empty($plugin_name)) {
    	return false;
    }
	preg_match("/Plugin URI:(.*)/i", $plugin_data, $plugin_uri);
	preg_match("/Description:(.*)/i", $plugin_data, $description);
	preg_match("/Author:(.*)/i", $plugin_data, $author_name);
	preg_match("/Author URI:(.*)/i", $plugin_data, $author_uri);
	if (preg_match("/Version:(.*)/i", $plugin_data, $version))
		$version = trim($version[1]);
	else
		$version = '';
    if(!empty($author_name)) {
        if(!empty($author_uri)) {
            $author_name = '<a href="'.trim($author_uri[1]).'" target="_blank">'.$author_name[1].'</a>';
        }else {
            $author_name = $author_name[1];
        }    	
    }else {
    	$author_name = '';
    }
	return array ('file'=>$plugin_file,'name' => trim($plugin_name[1]), 'uri' => trim($plugin_uri[1]), 'description' => trim($description[1]), 'author' => trim($author_name), 'version' => $version);
}

/**
 +----------------------------------------------------------
 * 动态添加模块
 +----------------------------------------------------------
 * @param string $module 模块名
 * @param string $class 类名
 +----------------------------------------------------------
 * @return Boolean
 +----------------------------------------------------------
 */
function add_module($module,$class) 
{
	static $_module = array();
    $_module[APP_NAME.'_'.$module] = $class;
    Session::set('_modules',$_module);
    return true;
}

/**
 +----------------------------------------------------------
 * 移除模块 仅限动态加载的模块
 +----------------------------------------------------------
 * @param string $module 模块名
 +----------------------------------------------------------
 * @return boolean
 +----------------------------------------------------------
 */
function remove_module($module) 
{
    if(Session::is_set('_modules')) {
    	$_module = Session::get('_modules');
        if(isset($_module[APP_NAME.'_'.$module])) {
            unset($_module[APP_NAME.'_'.$module]);
            Session::set('_modules',$_module);        	
        }
        return true;
    }
}

/**
 +----------------------------------------------------------
 * 动态添加操作
 +----------------------------------------------------------
 * @param mixed $action 操作名称
 * @param string $function 操作方法名
 +----------------------------------------------------------
 * @return boolean
 +----------------------------------------------------------
 */
function add_action($action,$function) 
{
	static $_action = array();
    if(is_array($action)) {
    	$module   = $action[0];
        $action     =  $action[1];
    }else {
    	$module   = 'public';
    }
    $_action[APP_NAME.'_'.$module][$action] = $function;
    Session::set('_actions',$_action);
    return true;
}

/**
 +----------------------------------------------------------
 * 移除动态操作
 +----------------------------------------------------------
 * @param mixed $action 操作名称
 +----------------------------------------------------------
 * @return boolean
 +----------------------------------------------------------
 */
function remove_action($action) 
{
    if(Session::is_set('_actions')) {
        if(is_array($action)) {
            $module   = $action[0];
            $action     =  $action[1];
        }else {
            $module   = 'public';
        }
    	$_action = Session::get('_actions');
        if(isset($_action[APP_NAME.'_'.$module][$action])) {
            unset($_action[APP_NAME.'_'.$module][$action]);
            Session::set('_actions',$_action);        	
        }
        return true;
    }
}

/**
 +----------------------------------------------------------
 * 动态添加模版编译引擎
 +----------------------------------------------------------
 * @param string $tag 模版引擎定义名称
 * @param string $compiler 编译器名称
 +----------------------------------------------------------
 * @return boolean
 +----------------------------------------------------------
 */
function add_compiler($tag,$compiler) 
{
	$GLOBALS['template_compiler'][strtoupper($tag)] = $compiler ;
    return ;
}

/**
 +----------------------------------------------------------
 * 使用模版编译引擎
 +----------------------------------------------------------
 * @param string $tag 模版引擎定义名称
 +----------------------------------------------------------
 * @return boolean
 +----------------------------------------------------------
 */
function use_compiler($tag) 
{
	$args = array_slice(func_get_args(), 1);
	if(is_callable($GLOBALS['template_compiler'][strtoupper($tag)])) {
		call_user_func_array($GLOBALS['template_compiler'][strtoupper($tag)],$args);    
	}else{
		throw_exception('模板引擎错误：'.C('TMPL_ENGINE_TYPE'));
	}
    return ;
}
/**
 +----------------------------------------------------------
 * 动态添加过滤器
 +----------------------------------------------------------
 * @param string $tag 过滤器标签
 * @param string $function 过滤方法名
 * @param integer $priority 执行优先级
 * @param integer $args 参数
 +----------------------------------------------------------
 * @return boolean
 +----------------------------------------------------------
 */
function add_filter($tag,$function,$priority = 10,$args = 1) 
{
    static $_filter = array();
	if ( isset($_filter[APP_NAME.'_'.$tag]["$priority"]) ) {
		foreach($_filter[APP_NAME.'_'.$tag]["$priority"] as $filter) {
			if ( $filter['function'] == $function ) {
				return true;
			}
		}
	}
    $_filter[APP_NAME.'_'.$tag]["$priority"][] = array('function'=> $function,'args'=> $args);
    Session::set('_filters',$_filter);
    return true;
}

/**
 +----------------------------------------------------------
 * 删除动态添加的过滤器
 +----------------------------------------------------------
 * @param string $tag 过滤器标签
 * @param string $function 过滤方法名
 * @param integer $priority 执行优先级
 +----------------------------------------------------------
 * @return boolean
 +----------------------------------------------------------
 */
function remove_filter($tag, $function_to_remove, $priority = 10) {
	$_filter  = Session::get('_filters');
	if ( isset($_filter[APP_NAME.'_'.$tag]["$priority"]) ) {
		$new_function_list = array();
		foreach($_filter[APP_NAME.'_'.$tag]["$priority"] as $filter) {
			if ( $filter['function'] != $function_to_remove ) {
				$new_function_list[] = $filter;
			}
		}
		$_filter[APP_NAME.'_'.$tag]["$priority"] = $new_function_list;
	}
    Session::set('_filters',$_filter);
	return true;
}

/**
 +----------------------------------------------------------
 * 执行过滤器
 +----------------------------------------------------------
 * @param string $tag 过滤器标签
 * @param string $string 参数
 +----------------------------------------------------------
 * @return boolean
 +----------------------------------------------------------
 */
function apply_filter($tag,$string='') 
{
    $_filter  = Session::get('_filters');
	if ( !isset($_filter[APP_NAME.'_'.$tag]) ) {
		return $string;
	}
    ksort($_filter[APP_NAME.'_'.$tag]);
    $args = array_slice(func_get_args(), 2);
	foreach ($_filter[APP_NAME.'_'.$tag] as $priority => $functions) {
		if ( !is_null($functions) ) {
			foreach($functions as $function) {
                if(is_callable($function['function'])) {
                    $args = array_merge(array($string), $args);
                    $string = call_user_func_array($function['function'],$args);                 	
                }
			}
		}
	}
	return $string;	
}

/**
 +----------------------------------------------------------
 * 字符串截取，支持中文和其他编码
 +----------------------------------------------------------
 * @param string $fStr 需要转换的字符串
 * @param string $fStart 开始位置
 * @param string $fLen 截取长度
 * @param string $fCode 编码格式
 * @param string $show 截断显示字符
 +----------------------------------------------------------
 * @return string
 +----------------------------------------------------------
 */
function msubstr (&$fStr, $fStart, $fLen, $fCode = "utf-8",$show='...') {
    if(function_exists('mb_substr')) {
        if(mb_strlen($fStr,$fCode)>$fLen) {
            return mb_substr ($fStr,$fStart,$fLen,$fCode).$show;
        }
        return mb_substr ($fStr,$fStart,$fLen,$fCode);
    }else if(function_exists('iconv_substr')) {
        if(iconv_strlen($fStr,$fCode)>$fLen) {
            return iconv_substr ($fStr,$fStart,$fLen,$fCode).$show;
        }
        return iconv_substr ($fStr,$fStart,$fLen,$fCode);
    }

    $fCode = strtolower($fCode);
    switch ($fCode) {
        case "utf-8" : 
            preg_match_all("/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/", $fStr, $ar);  
            if(func_num_args() >= 3) {  
                if (count($ar[0])>$fLen) {
                    return join("",array_slice($ar[0],$fStart,$fLen)).$show; 
                }
                return join("",array_slice($ar[0],$fStart,$fLen)); 
            } else {  
                return join("",array_slice($ar[0],$fStart)); 
            } 
            break;
        default:
            $fStart = $fStart*2;
            $fLen   = $fLen*2;
            $strlen = strlen($fStr);
            for ( $i = 0; $i < $strlen; $i++ ) {
                if ( $i >= $fStart && $i < ( $fStart+$fLen ) ) {
                    if ( ord(substr($fStr, $i, 1)) > 129 ) $tmpstr .= substr($fStr, $i, 2);
                    else $tmpstr .= substr($fStr, $i, 1);
                }
                if ( ord(substr($fStr, $i, 1)) > 129 ) $i++;
            }
            if ( strlen($tmpstr) < $strlen ) $tmpstr .= $show;
            Return $tmpstr;
    }
}

/**
 +----------------------------------------------------------
 * 产生随机字串，可用来自动生成密码 默认长度6位 字母和数字混合
 +----------------------------------------------------------
 * @param string $len 长度 
 * @param string $type 字串类型 
 * 0 字母 1 数字 其它 混合
 * @param string $addChars 额外字符 
 +----------------------------------------------------------
 * @return string
 +----------------------------------------------------------
 */
function rand_string($len=6,$type='',$addChars='') { 
    $str ='';
    switch($type) { 
        case 0:
            $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'.$addChars; 
            break;
        case 1:
            $chars='0123456789'; 
            break;
        case 2:
            $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZ'.$addChars; 
            break;
        case 3:
            $chars='abcdefghijklmnopqrstuvwxyz'.$addChars; 
            break;
        default :
            // 默认去掉了容易混淆的字符oOLl和数字01，要添加请使用addChars参数
            $chars='ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789'.$addChars; 
            break;
    }
    if($len>10 ) {//位数过长重复字符串一定次数
        $chars= $type==1? str_repeat($chars,$len) : str_repeat($chars,5); 
    }
    $chars   =   str_shuffle($chars);
    $str     =   substr($chars,0,$len);

    return $str;
}

/**
 +----------------------------------------------------------
 * 生成一定数量的随机数，并且不重复
 +----------------------------------------------------------
 * @param integer $number 数量
 * @param string $len 长度
 * @param string $type 字串类型 
 * 0 字母 1 数字 其它 混合
 +----------------------------------------------------------
 * @return string
 +----------------------------------------------------------
 */
function build_count_rand ($number,$length=4,$mode=1) { 
        if($mode==1 && $length<strlen($number) ) {
            //不足以生成一定数量的不重复数字
    		return false;        	
        }
        $rand   =  array();
        for($i=0; $i<$number; $i++) {
            $rand[] =   rand_string($length,$mode);
        }
        $unqiue = array_unique($rand);
        if(count($unqiue)==count($rand)) {
            return $rand;
        }
        $count   = count($rand)-count($unqiue);
        for($i=0; $i<$count*3; $i++) {
            $rand[] =   rand_string($length,$mode);
        }
        $rand = array_slice(array_unique ($rand),0,$number);    	
        return $rand;
}

/**
 +----------------------------------------------------------
 *  带格式生成随机字符 支持批量生成 
 *  但可能存在重复
 +----------------------------------------------------------
 * @param string $format 字符格式
 *     # 表示数字 * 表示字母和数字 $ 表示字母
 * @param integer $number 生成数量
 +----------------------------------------------------------
 * @return string | array
 +----------------------------------------------------------
 */
function build_format_rand($format,$number=1) 
{
    $str  =  array();
    $length =  strlen($format);
    for($j=0; $j<$number; $j++) {
        $strtemp   = '';
        for($i=0; $i<$length; $i++) {
            $char = substr($format,$i,1);
            switch($char){
                case "*"://字母和数字混合
                    $strtemp   .= rand_string(1);
                    break;
                case "#"://数字
                    $strtemp  .= rand_string(1,1);
                    break;
                case "$"://大写字母
                    $strtemp .=  rand_string(1,2);
                    break;
                default://其他格式均不转换
                    $strtemp .=   $char;
                    break;
           }
        } 
        $str[] = $strtemp;
    }
    
    return $number==1? $strtemp : $str ;
}

/**
 +----------------------------------------------------------
 * 获取一定范围内的随机数字 位数不足补零
 +----------------------------------------------------------
 * @param integer $min 最小值
 * @param integer $max 最大值
 +----------------------------------------------------------
 * @return string
 +----------------------------------------------------------
 */
function rand_number ($min, $max) {
	Return sprintf("%0".strlen($max)."d", mt_rand($min,$max));
}

/**
 +----------------------------------------------------------
 * 获取登录验证码 默认为4位数字
 +----------------------------------------------------------
 * @param string $fmode 文件名
 +----------------------------------------------------------
 * @return string
 +----------------------------------------------------------
 */
function build_verify ($length=4,$mode=1) { 
    return rand_string($length,$mode);
}

/**
 +----------------------------------------------------------
 * stripslashes扩展 可用于数组 
 +----------------------------------------------------------
 * @param mixed $value 变量
 +----------------------------------------------------------
 * @return mixed
 +----------------------------------------------------------
 */
if(!function_exists('stripslashes_deep')) {
    function stripslashes_deep($value) {
       $value = is_array($value) ? array_map('stripslashes_deep', $value) : stripslashes($value);
       return $value;
    }	
}

/**
 +----------------------------------------------------------
 * 获取Dao对象的缩略方法
 +----------------------------------------------------------
 * @param string $daoClassName Dao对象名称
 +----------------------------------------------------------
 * @return string
 +----------------------------------------------------------
 */
function D($daoClassName) 
{
	static $_dao = array();
    if(!strpos($daoClassName,'Dao')) {
    	$daoClassName =  $daoClassName.'Dao';
    }
	if(isset($_dao[$daoClassName])) {
		return $_dao[$daoClassName];
	}
	import("@.Dao.".$daoClassName);
    if(class_exists($daoClassName)) {
        $dao = new $daoClassName();
		$_dao[$daoClassName] =	$dao;
        return $dao;    	
    }else {
    	return false;
    }
}

// 获取语言定义
function L($name='',$value=null) {
	static $_lang = array();
	if(!is_null($value)) {
		$prev	=	$_lang[strtolower($name)];
		$_lang[strtolower($name)]	=	$value;
		return $prev;
	}
	if(empty($name)) {
		return $_lang;
	}
	if(is_array($name)) {
		$_lang = array_merge($_lang,$name);
		return;
	}
	if(isset($_lang[strtolower($name)])) {
		return $_lang[strtolower($name)];
	}

	$lang = Language::getInstance();
	$_lang[strtolower($name)] = $lang->get(strtolower($name));
	return $_lang[strtolower($name)];
}

// 获取配置值
function C($name='',$value=null) {
	static $_config = array();
	if(!is_null($value)) {
		$prev	=	$_config[strtolower($name)];
		$_config[strtolower($name)]	=	$value;
		return $prev;
	}
	if(empty($name)) {
		return $_config;
	}
	// 缓存全部配置值
	if(is_array($name)) {
		$_config = array_merge($_config,$name);
		return $_config;
	}
	if(isset($_config[strtolower($name)])) {
		return $_config[strtolower($name)];
	}
	if(defined($name)) {
		$_config[strtolower($name)]	=	constant($name);
		return constant($name);
	}

	import('Think.Util.Config');
	$config = Config::getInstance();
	$_config[strtolower($name)]	=	$config->get(strtolower($name));
	return $_config[strtolower($name)];
}

// 缓存设置和读取
function S($name,$value='',$expire='',$type='') {
	static $_cache = array();
	if('' !== $value) {
        //取得缓存对象实例
        $cache  = Cache::getInstance($type);
		if(is_null($value)) {
			// 删除缓存
			$result	=	$cache->rm($name);
			if($result) {
				unset($_cache[$type.'_'.$name]);
			}
			return $result;
		}else{
			// 缓存数据
			$cache->set($name,$value,$expire);
			$_cache[$type.'_'.$name]	 =	 $value;
		}
		return ;
	}
	if(isset($_cache[$type.'_'.$name])) {
		return $_cache[$type.'_'.$name];
	}
	// 取得缓存实例
	$cache  = Cache::getInstance($type);
	// 获取缓存数据
	$value      =  $cache->get($name);
	$_cache[$type.'_'.$name]	 =	 $value;
	return $value;
}

// 快速创建一个对象实例
function I($class,$baseUrl = '',$ext='.class.php') {
	static $_class = array();
	if(isset($_class[$baseUrl.$class])) {
		return $_class[$baseUrl.$class];
	}
	$class_strut = explode(".",$class);
	$className	=	array_pop($class_strut);
	if($className != '*') {
		import($class,$baseUrl,$ext,false);
		$_class[$baseUrl.$class] = & new $className();
		return $_class[$baseUrl.$class];
	}else {
		return false;
	}
}

// xml编码
function xml_encode($data,$encoding='utf-8',$root="think") {
	$xml = '<?xml version="1.0" encoding="'.$encoding.'"?>';
	$xml.= '<'.$root.'>';
	$xml.= data_to_xml($data);   
	$xml.= '</'.$root.'>'; 
	return $xml;
}

function data_to_xml($data) {
	if(is_object($data)) {
		$data = get_object_vars($data);
	}
	$xml = '';
	foreach($data as $key=>$val) {
		is_numeric($key) && $key="item id=\"$key\"";
		$xml.="<$key>";
		$xml.=(is_array($val)||is_object($val))?data_to_xml($val):$val;
		list($key,)=explode(' ',$key);
		$xml.="</$key>";
	}
	return $xml;
}
?>