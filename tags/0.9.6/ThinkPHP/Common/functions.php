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
// $Id: functions.php 33 2007-02-25 07:06:02Z liu21st $

/**
 +------------------------------------------------------------------------------
 * Think公共函数库
 +------------------------------------------------------------------------------
 * @copyright  Copyright (c) 2005-2006 liu21st.com.  All rights reserved. 
 * @author     liu21st <liu21st@gmail.com>
 * @version    $Id: functions.php 33 2007-02-25 07:06:02Z liu21st $
 +------------------------------------------------------------------------------
 */
/**
 +----------------------------------------------------------
 * 检测浏览器语言
 +----------------------------------------------------------
 * @return string
 +----------------------------------------------------------
 */
function detect_browser_language()
{
    if ( isset($_GET[VAR_LANGUAGE]) ) {
        $langSet = $_GET[VAR_LANGUAGE];
        setcookie('Think_'.VAR_LANGUAGE,$langSet,time()+3600,'/');
    } else {
        if ( !isset($_COOKIE['Think_'.VAR_LANGUAGE]) ) {
            preg_match('/^([a-z\-]+)/i', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $matches);
            $langSet = $matches[1];
            setcookie('Think_'.VAR_LANGUAGE,$langSet,time()+3600,'/');
        }
        else {
            $langSet = $_COOKIE['Think_'.VAR_LANGUAGE];
        }
    }
    return $langSet;
}

/**
 +----------------------------------------------------------
 * 检测服务器是否支持URL Rewrite
 +----------------------------------------------------------
 * @return string
 +----------------------------------------------------------
 */
function checkModRewrite() 
{
    static $_modRewrite;
    if(isset($_modRewrite)) {
        return $_modRewrite;
    }
    $_modRewrite = true;
    if ( ! IS_APACHE ){
        $_modRewrite = false;
    }elseif ( function_exists('apache_get_modules') ) {
        if ( !in_array('mod_rewrite', apache_get_modules()) )
            $_modRewrite = false;
    }
    /*
    if($_modRewrite) {
    	if(!file_exists(APP_PATH.'.htaccess') && is_writable(APP_PATH)) {
            $content   =  '<IfModule mod_rewrite.c>
RewriteEngine on
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^(.*)$ index.php/$1 [QSA,PT,L]
</IfModule>';
    		file_put_contents(APP_PATH.'.htaccess',$content);
    	}
    }*/
    return $_modRewrite;
}

function get_client_ip(){
   if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
       $ip = getenv("HTTP_CLIENT_IP");
   else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
       $ip = getenv("HTTP_X_FORWARDED_FOR");
   else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
       $ip = getenv("REMOTE_ADDR");
   else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
       $ip = $_SERVER['REMOTE_ADDR'];
   else
       $ip = "unknown";
   return($ip);
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
    if(DEBUG_MODE){
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
        if(ERROR_PAGE!=''){
            redirect(ERROR_PAGE); 
        }else {
            $e['message'] = ERROR_MESSAGE;
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
    if(version_compare(PHP_VERSION, '5.0.0', '<')){ 
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
    if(defined('WEB_LOG_RECORD') && !empty($msg))
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
function dump($var, $label=null, $echo=true)
{
    $label = ($label===null) ? '' : rtrim($label) . ' ';
    ob_start();
    var_dump($var);
    $output = ob_get_clean();
    if(!extension_loaded('xdebug')) {
        $output = preg_replace("/\]\=\>\n(\s+)/m", "] => ", $output);
        $output = '<pre>'
                . $label
                . htmlentities($output, ENT_QUOTES,OUTPUT_CHARSET)
                . '</pre>';    	
    }
    if ($echo) {
        echo($output);
    }
    return $output;
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
function auto_charset($fContents,$from=TEMPLATE_CHARSET,$to=OUTPUT_CHARSET){
    if( strtoupper($from) === strtoupper($to) || empty($fContents) || (is_scalar($fContents) && !is_string($fContents)) ){
        //如果编码相同或者非字符串标量则不转换
        return $fContents;
    }
    $from   =  strtoupper($from)=='UTF8'? 'utf-8':$from;
    $to       =  strtoupper($to)=='UTF8'? 'utf-8':$to;
    if(is_string($fContents) ) {
        if(function_exists('iconv')){
            Return iconv($from,$to,$fContents);
        }
        elseif(function_exists('mb_convert_encoding')){
            return mb_convert_encoding ($fContents, $to, $from);
        }else{
            halt(_NO_AUTO_CHARSET_);
            return $fContents;
        }
    }
    elseif(is_array($fContents)){
        foreach ( $fContents AS $key => $val ) {
            $fContents[$key] = auto_charset($val,$from,$to);
        }
        return $fContents;
    }
    elseif(is_instance_of($fContents,'Vo')) {
        foreach($fContents as $key=>$val) {
            $fContents->$key = auto_charset($val,$from,$to);
        }
        return $fContents;
    }
    elseif(is_instance_of($fContents,'VoList')) {
        foreach($fContents->getIterator() as $key=>$vo) {
            $fContents->set($key,auto_charset($vo,$from,$to));
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
            $o = & new $className();
            if(method_exists($o,$method)){
                if(!empty($args)) {
                	$_instance[$className] = call_user_func_array(array(&$o, $method), $args);;
                }else {
                	$_instance[$className] = $o->$method();
                }
            }
            else 
                $_instance[$className] = $o;
        }
        else 
            halt(_CLASS_NOT_EXIST_);
    }
    return $_instance[$className];
}

/**
 +----------------------------------------------------------
 * 系统自动加载ThinkPHP基类库和当前项目的Dao和Vo对象
 * 需要PHP5支持
 +----------------------------------------------------------
 * @param string $classname 对象类名
 +----------------------------------------------------------
 * @return void
 +----------------------------------------------------------
 */
function __autoload($classname)
{
    $autoLoad = array('@.Vo','Admin.Vo');
    foreach($autoLoad as $val){
        if( import($val.'.'.$classname) )    return;
    }
    halt("不能自动载入".$classname." 类库。");
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
        if (!isset($_importFiles[strtolower(basename($filename))])) {
            include($filename);
            $_importFiles[strtolower(basename($filename))] = true;
            //$GLOBALS['LoadFile']++;//统计加载文件数
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
        if (!isset($_importFiles[strtolower(basename($filename))])) {
            require($filename);
            $_importFiles[strtolower(basename($filename))] = true;
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
 * 压缩PHP文件内容和简单加密
 +----------------------------------------------------------
 * @param string $filename 文件名
 * @param boolean $strip 是否去除代码空白和注释
 +----------------------------------------------------------
 * @return false|integer 返回加密文件的字节大小
 +----------------------------------------------------------
 */
function encode_file_contents($filename,$strip=true) 
{
    $type = strtolower(substr(strrchr($filename, '.'),1));
    if('php'==$type && is_file($filename) && is_writeable($filename)) {
        // 如果是PHP文件 并且可写 则进行压缩
    	$contents  =  file_get_contents($filename);
        // 判断文件是否已经被加密
        $pos = strpos($contents,'/*Protected by Think Cryptation*/');
        if( false === $pos  || $pos>100 ) {
            if($strip) {
                // 去除PHP文件注释和空白，减少文件大小
                $contents  =  php_strip_whitespace($filename);            	
            }
            // 去除PHP头部和尾部标识
            $headerPos   =  strpos($contents,'<?php');
            $footerPos    =  strrpos($contents,'?>');
            $contents  =  substr($contents,$headerPos+5,$footerPos-$headerPos);
            // 对文件内容进行加密存储
            $encode   =  base64_encode(gzdeflate($contents));
            $encode   = '<?php'." /*Protected by Think Cryptation*/\n\$cryptCode='".$encode."';eval(gzinflate(base64_decode(\$cryptCode)));\n /*Reverse engineering is illegal and strictly prohibited - (C) Think Cryptation 2006*/\n?>";
            // 重新写入加密内容
            return file_put_contents($filename,$encode);
        } 
    }
    return false;
}

/**
 +----------------------------------------------------------
 * 压缩文件夹下面的PHP文件
 +----------------------------------------------------------
 * @param string $path 路径
 +----------------------------------------------------------
 * @return void
 +----------------------------------------------------------
 */
function encode_dir($path) { 
    if(substr($path, -1) != "/")    $path .= "/";
    $dir=glob($path."*"); 
    foreach($dir as $key=>$val) { 
        if(is_dir($val)) { 
            encode_dir($val); 
        } else{
            encode_file_contents($val);
        } 
    } 
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
      static $_importClass = array();
      if(isset($_importClass[$class.$baseUrl]))
            return true;
      else 
            $_importClass[$class.$baseUrl] = true;
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
      elseif($class_strut[0]=='Think' || $class_strut[0]=='ORG' ) {
          //加载ThinkPHP基类库或者公共类库
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
           /*
           $dir = dir($tmp_base_class);
           while (false !== ($entry = $dir->read())) {
                //如果是特殊目录继续
                if($entry == "." || $entry == "..")   continue;
                //如果是目录 ，并且定义了导入子目录，则递归调用import
                if( is_dir($tmp_base_class.'/'.$entry)){    
                    if ($subdir)  import('*',$tmp_base_class.'/'.$entry.'/',$appName,$ext,$subdir);
                }else{    
                    //导入类库文件 后缀默认为 class.php
                    if(strpos($entry, $ext)){
                        require_cache($tmp_base_class.'/'.$entry);
                    }
                }
           }
           $dir->close(); */
           return true;
      }else{
            //导入目录下的指定类库文件
            return require_cache($classfile);          	
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
function getIterator($values) 
{
    if(version_compare(PHP_VERSION, '5.0.0', '<')){
        //PHP4下面的ArrayObject模拟了Iterator接口
        return new ArrayObject($values);
    }else {
        //ListIterator在PHP5中实现了Iterator接口
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
   if(version_compare(PHP_VERSION, '5.0.0', '<')){
        return is_a($object, $className);
   }
   else{
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
    	return $result;
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
    call_user_func_array($GLOBALS['template_compiler'][strtoupper($tag)],$args);    
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
 * 获取Dao对象的缩略方法
 +----------------------------------------------------------
 * @param string $daoClassName Dao对象名称
 +----------------------------------------------------------
 * @return string
 +----------------------------------------------------------
 */
function D($daoClassName) 
{
    if(!strpos($daoClassName,'Dao')) {
    	$daoClassName =  $daoClassName.'Dao';
    }
	import("@.Dao.".$daoClassName);
    $dao = new $daoClassName();
    return $dao;
}
/**
 +----------------------------------------------------------
 * 获取Vo对象的缩略方法
 +----------------------------------------------------------
 * @param string $voClassName Vo对象名称
 +----------------------------------------------------------
 * @return string
 +----------------------------------------------------------
 */
function V($voClassName,$data=NULL) 
{
    if(!strpos($voClassName,'Vo')) {
    	$voClassName =  $voClassName.'Vo';
    }
	import("@.Vo.".$voClassName);
    $vo = new $voClassName($data);
    return $vo;
}


?>