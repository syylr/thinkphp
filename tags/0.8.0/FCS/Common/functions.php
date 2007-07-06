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
 * FCS公共函数库
 +------------------------------------------------------------------------------
 * @package    Common
 * @link       http://www.fcs.org.cn
 * @copyright  Copyright (c) 2005-2006 liu21st.com.  All rights reserved. 
 * @author     liu21st <liu21st@gmail.com>
 * @version    $Id$
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
        $_COOKIE['FCS_'.VAR_LANGUAGE] = $langSet;
    } else {
        if ( !isset($_COOKIE[VAR_LANGUAGE]) ) {
            preg_match('/^([a-z\-]+)/i', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $matches);
            $langSet = $matches[1];
            $_COOKIE['FCS_'.VAR_LANGUAGE] = $langSet;
        }
        else {
            $langSet = $_COOKIE['FCS_'.VAR_LANGUAGE];
        }
    }
    return $langSet;
}

/**
 +----------------------------------------------------------
 * 检测浏览器类型
 +----------------------------------------------------------
 * @return string
 +----------------------------------------------------------
 */
function detect_browser_type(){
    $userAgent = strtolower($_SERVER['HTTP_USER_AGENT']);
    if(strpos($userAgent, 'opera') !== false){
        $type = 'opera';
    }else if(strpos($userAgent, 'msie') !== false){
        $type = 'ie';
    }else if(strpos($userAgent, 'firefox') !== false){
        $type = 'firefox';
    }else{
        $type = 'ns';
    }
    return $type;
}

/**
 +----------------------------------------------------------
 * 字节格式化 把字节数格式为 B K M G T 描述的大小
 +----------------------------------------------------------
 * @return string
 +----------------------------------------------------------
 */
function byte_format($input, $dec=0) 
{ 
  $prefix_arr = array(" B", "K", "M", "G", "T"); 
  $value = round($input, $dec); 
  $i=0; 
  while ($value>1024) 
  { 
     $value /= 1024; 
     $i++; 
  } 
  $return_str = round($value, $dec).$prefix_arr[$i]; 
  return $return_str; 
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
    //如果配置文件还没有加载，则使用exit方法输出错误
    if(!defined('TEMPLATE_NAME')) {
        if(is_array($error)) {
            exit($error['message']);
        }else {
            exit($error);
        }
    }
    //读取错误模板文件
    $tpl    =   get_instance_of('Template');
    $tpl->assign('publicCss',WEB_URL.'/'.TMPL_DIR.'/'.TEMPLATE_NAME.'/Public/css/FCS.css');

    if(DEBUG_MODE){//调试模式下输出错误信息
        if(!file_exists(TEMPLATE_PATH.'/Public/debug.html')) {
            exit($error);
        }
        if(is_array($error)){//抛出异常
            $tpl->assign("exception",true);
        }
        $tpl->assign("error",$error);
        $tpl->display(TEMPLATE_PATH.'/Public/debug.html');

    }else {//否则定向到错误页面
        if(ERROR_PAGE!=''){
            redirect(ERROR_PAGE); 
        }else {
            if(!file_exists(TEMPLATE_PATH.'/Public/error.html')) {
                exit(ERROR_MESSAGE);
            }
            $tpl->assign("error",ERROR_MESSAGE);
            $tpl->display(TEMPLATE_PATH.'/Public/error.html');
        }
    }
    exit;
}


/**
 +----------------------------------------------------------
 * URL重定向
 * 
 +----------------------------------------------------------
 * @static
 * @access public 
 +----------------------------------------------------------
 * @param string $url  要定向的URL地址
 * @param integer $time  定向的延迟时间，单位为秒
 * @param string $msg  提示信息
 +----------------------------------------------------------
 * @throws FcsException
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
        header("refresh:{$time};url={$url}");
        if($time!=0) {
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
 * @param string $type 异常类型 默认为FcsException
 * 如果指定的异常类不存在，则直接输出错误信息
 +----------------------------------------------------------
 * @return void
 +----------------------------------------------------------
 */
function throw_exception($msg,$type='FcsException',$code=0)
{
    if(class_exists($type)){
        if(version_compare(PHP_VERSION, '5.0.0', '<')){
            $e = & new $type($msg,$code);
            halt($e->__toString());
        }else {
            // PHP5使用 throw关键字抛出异常
            // 出于兼容考虑包含下面语句实现
            // throw new $type($msg,$code);
        	include('_throw_exception.php');
        }
    }else {// 异常类型不存在则输出错误信息字串
        halt($msg);
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
    // format the label
    $label = ($label===null) ? '' : rtrim($label) . ' ';

    // var_dump the variable into a buffer and keep the output
    ob_start();
    var_dump($var);
    $output = ob_get_clean();

    // neaten the newlines and indents
    $output = preg_replace("/\]\=\>\n(\s+)/m", "] => ", $output);
    $output = '<pre>'
            . $label
            . htmlentities($output, ENT_QUOTES)
            . '</pre>';

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
    if( strtoupper($from) === strtoupper($to)){
        return $fContents;
    }
    if(is_string($fContents)) {
        if(function_exists('iconv')){
            Return iconv($from,$to,$fContents);
        }
        elseif(function_exists('mb_convert_encoding')){
            return mb_convert_encoding ($fContents, $to, $from);
        }else{
            halt('您的系统不支持自动编码转换！');
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
		return $fContents;
        //halt('系统不支持对该类型的编码转换！');
    }
}

/**
 +----------------------------------------------------------
 * 字符串截取，支持中文和其他编码
 * 
 * 
 +----------------------------------------------------------
 * @param string $fStr 需要转换的字符串
 * @param string $fStart 需要转换的字符串
 * @param string $fLen 需要转换的字符串
 * @param string $fCode 需要转换的字符串
 +----------------------------------------------------------
 * @return string
 +----------------------------------------------------------
 */
function msubstr (& $fStr, $fStart, $fLen, $fCode = "utf-8",$show='...') {
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
        default :
        $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'.$addChars; 
        break;
    }
    if(version_compare(PHP_VERSION, '4.3.0', '>')) {
        $chars	=	str_shuffle($chars);
        $str     =   substr($chars,1,$len);
    }else {
        while(strlen($str)<$len)   $str.=substr($chars,(mt_rand()%strlen($chars)),1);
    }
    
    return $str;
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

function toDate($time,$format='Y年m月d日 H:i:s') 
{
	if( empty($time)) {
		return '';
	}
	return date($format,$time);
}

function dateDiff($date1,$date2) 
{
	import('FCS.Util.Date');
	$date	=	new Date(intval($date1));
	$return	=	$date->dateDiff($date2);
	if($return<=1) {
		$return =	'今天';
	}elseif($return <=2) {
		$return = '昨天';
	}elseif($return <=7) {
		$return = $date->cWeekday;
	}
	elseif($return>7 && $return <14) {
		$return = '上周';
	}
	else {
		$return = '两周前';
	}
	return $return;
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
function get_instance_of($className,$method='') 
{
    static $_instance = array();
    if (!isset($Instance[$className])) {
		
        if(class_exists($className)){
            $o = & new $className();
            if(method_exists($o,$method))
                $_instance[$className] = $o->$method();
            else 
                $_instance[$className] = $o;
        }
        else 
            halt('实例化一个不存在的类！');
    }
    return $_instance[$className];
}


/**
 +----------------------------------------------------------
 * 注册/获取类的全局对象
 * 
 +----------------------------------------------------------
 * @static
 * @access public 
 +----------------------------------------------------------
 * @param string $name 全局对象名
 * @param object $obj  要注册的对象
 +----------------------------------------------------------
 * @throws FcsException
 +----------------------------------------------------------
 */
function register($name,$obj)
{
    static $_registry = array();
    if(!empty($obj)){
        if(!is_string($name)) {
            throw_exception("参数类型错误！");
        }
        elseif (array_key_exists($name, $_registry)) {
           throw_exception("$name 已经被注册！");
        }
        if (!is_object($obj)) {
           throw_exception("只允许注册对象.");
        }
        foreach ($_registry as $key=>$val) {
            if ($obj === $val) {
                throw_exception("注册的对象已经存在于：\"$key\".");
            }
        }
        $_registry[$name] = &$obj;

    }else {
        if ($name === null) {
            return $_registry;
        }
        else if(!is_string($name)) {
            throw_exception('First argument $name must be a string, or null to list registry.');
        }
        else if (!array_key_exists($name, $_registry)) {
           throw_exception("类的全局对象不存在");
        }
        return $_registry[$name];
    }
}


/**
 +----------------------------------------------------------
 * 系统自动加载FCS基类库 PHP5支持
 +----------------------------------------------------------
 * @param string $classname 对象类名
 +----------------------------------------------------------
 * @return void
 +----------------------------------------------------------
 */
function __autoload($classname)
{
    $autoLoad = array('FCS.core','FCS.util','FCS.db','FCS.exception','Admin.Dao','Admin.Vo',APP_NAME.'.Dao',APP_NAME.'.Vo');
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
    $autoLoad = array('Vo','Dao','Util');
    foreach($autoLoad as $key=>$val) {
        if(import(APP_NAME.'.'.$val.'.'.$classname) || import('Admin.'.$val.'.'.$classname) ) 	return ;
    }
    halt('反序列话的时候缺少类库'.$classname);
}


/**
 +----------------------------------------------------------
 * 系统运行时间调试
 +----------------------------------------------------------
 * @param string $label 调试区间名称
 +----------------------------------------------------------
 * @return void
 +----------------------------------------------------------
 */
function __DEBUG_START($label='')
{
    $GLOBALS[$label]['_beginTime'] = array_sum(split(' ', microtime()));
}

function __DEBUG_END($label='')
{
    $GLOBALS[$label]['_endTime'] = array_sum(split(' ', microtime()));
    echo '<div style="text-align:center;width:100%">Process: '.$label.' 用时'.number_format($GLOBALS[$label]['_endTime']-$GLOBALS[$label]['_beginTime'],6).'s </div>';
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
        if (!isset($_importFiles[strtolower($filename)])) {
            include($filename);
            $_importFiles[strtolower($filename)] = true;
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
        if (!isset($_importFiles[strtolower($filename)])) {
            require($filename);
            $_importFiles[strtolower($filename)] = true;
            return true;
        }
        return false;
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
 * @param string $ext 导入的文件扩展名
 * @param string $subdir 是否导入子目录 默认false
 +----------------------------------------------------------
 * @return boolen
 +----------------------------------------------------------
 */
function import($class,$baseUrl = LIB_PATH,$ext='.class.php',$subdir=false)
{
//echo($class.'<br>');
      static $_importClass = array();
      if(isset($_importClass[$class]))
            return true;
      else 
            $_importClass[$class] = true;
      //if (preg_match('/[^a-z0-9\-_.*]/i', $class)) throw_exception('Import非法的类名或者目录！');
      if(substr($baseUrl, -1) != "/")    $baseUrl .= "/";
      $class_strut = explode(".",$class);
      if(array_pop($class_strut) == "*"){
          //包含 * 符号导入该目录下面所有的类库 包含子目录
           $tmp_base_class = $baseUrl.implode("/",$class_strut);
           $dir = dir($tmp_base_class);
           while (false !== ($entry = $dir->read())) {
                //如果是特殊目录继续
                if($entry == "." || $entry == "..")   continue;
                //如果是目录 ，并且定义了导入子目录，则递归调用import
                if($subdir && is_dir($tmp_base_class.'/'.$entry)){    
                    import('*',$tmp_base_class.'/'.$entry.'/');
                }else{    
                    //导入类库文件 后缀默认为 class.php
                    if(strpos($entry, $ext)){
                        require_cache($tmp_base_class.'/'.$entry);
                    }
                }
           }
           $dir->close(); 
           return true;

      }else{
        //导入目录下的指定类库文件
        $classfile = $baseUrl.str_replace('.', '/', $class).$ext;
        return require_cache($classfile);
      }
   
} 

/**
 +----------------------------------------------------------
 * import方法的别名 
 +----------------------------------------------------------
 * @param string $package 包名
 * @param string $baseUrl 起始路径
 +----------------------------------------------------------
 * @return boolen
 +----------------------------------------------------------
 */
function using($class,$baseUrl = LIB_PATH)
{
    return import($class,$baseUrl);
}


/**
 +----------------------------------------------------------
 * 指定所在包的命名路径 并且检测是否存在同名类库
 +----------------------------------------------------------
 * @param string $package 包名
 * @param string $baseUrl 起始路径
 +----------------------------------------------------------
 * @return boolen
 +----------------------------------------------------------
 */
function package($package,$baseUrl='')
{

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
        $mix = get_resource_type($mix);
    }else
    {
        $mix = serialize($mix);
    }
    return md5($mix);
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
function stripslashes_deep($value) {
   $value = is_array($value) ? array_map('stripslashes_deep', $value) : stripslashes($value);
   return $value;
}

/**
 +----------------------------------------------------------
 * 变量过滤 
 +----------------------------------------------------------
 * @param mixed $value 变量
 +----------------------------------------------------------
 * @return mixed
 +----------------------------------------------------------
 */
function var_filter_deep($value) {
    if(is_array($value)) {
        $return = array_map('var_filter_deep', $value);
        return $return;
    }else {
        $value = htmlspecialchars(trim($value));
        $value = str_replace("javascript", "j avascript", $value);
        return $value;
    }
}

function ubb($Text) { 
  $Text=trim($Text);
  //$Text=htmlspecialchars($Text);  
  $Text=ereg_replace("\n","<br>",$Text); 
  $Text=preg_replace("/\\t/is","  ",$Text); 
  $Text=preg_replace("/\[h1\](.+?)\[\/h1\]/is","<h1>\\1</h1>",$Text); 
  $Text=preg_replace("/\[h2\](.+?)\[\/h2\]/is","<h2>\\1</h2>",$Text); 
  $Text=preg_replace("/\[h3\](.+?)\[\/h3\]/is","<h3>\\1</h3>",$Text); 
  $Text=preg_replace("/\[h4\](.+?)\[\/h4\]/is","<h4>\\1</h4>",$Text); 
  $Text=preg_replace("/\[h5\](.+?)\[\/h5\]/is","<h5>\\1</h5>",$Text); 
  $Text=preg_replace("/\[h6\](.+?)\[\/h6\]/is","<h6>\\1</h6>",$Text); 
  $Text=preg_replace("/\[center\](.+?)\[\/center\]/is","<center>\\1</center>",$Text); 
  $Text=preg_replace("/\[url\](http:\/\/.+?)\[\/url\]/is","<a href=\\1>\\1</a>",$Text); 
  $Text=preg_replace("/\[url\](.+?)\[\/url\]/is","<a href=\"http://\\1\">http://\\1</a>",$Text); 
  $Text=preg_replace("/\[url=(http:\/\/.+?)\](.*)\[\/url\]/is","<a href=\\1>\\2</a>",$Text); 
  $Text=preg_replace("/\[url=(.+?)\](.*)\[\/url\]/is","<a href=http://\\1>\\2</a>",$Text); 
  $Text=preg_replace("/\[img\](.+?)\[\/img\]/is","<img src=\\1>",$Text); 
  $Text=preg_replace("/\[color=(.+?)\](.+?)\[\/color\]/is","<font color=\\1>\\2</font>",$Text); 
  $Text=preg_replace("/\[size=(.+?)\](.+?)\[\/size\]/is","<font size=\\1>\\2</font>",$Text); 
  $Text=preg_replace("/\[sup\](.+?)\[\/sup\]/is","<sup>\\1</sup>",$Text); 
  $Text=preg_replace("/\[sub\](.+?)\[\/sub\]/is","<sub>\\1</sub>",$Text); 
  $Text=preg_replace("/\[pre\](.+?)\[\/pre\]/is","<pre>\\1</pre>",$Text); 
  $Text=preg_replace("/\[email\](.+?)\[\/email\]/is","<a href='mailto:\\1'>\\1</a>",$Text); 
  $Text=preg_replace("/\[i\](.+?)\[\/i\]/is","<i>\\1</i>",$Text); 
  $Text=preg_replace("/\[u\](.+?)\[\/u\]/is","<u>\\1</u>",$Text); 
  $Text=preg_replace("/\[b\](.+?)\[\/b\]/is","<b>\\1</b>",$Text); 
  $Text=preg_replace("/\[quote\](.+?)\[\/quote\]/is","<blockquote><font size='1' face='Courier New'>quote:</font><hr>\\1<hr></blockquote>", $Text); 
  $Text=preg_replace("/\[code\](.+?)\[\/code\]/is","<blockquote><font size='1' face='Times New Roman'>code:</font><hr color='lightblue'><i>\\1</i><hr color='lightblue'></blockquote>", $Text); 
  $Text=preg_replace("/\[sig\](.+?)\[\/sig\]/is","<div style='text-align: left; color: darkgreen; margin-left: 5%'><br><br>--------------------------<br>\\1<br>--------------------------</div>", $Text); 
  return $Text; 
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
function is_instance_of($object, $className){
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
 * 创建一个GUID 可通用于window和unix
 +----------------------------------------------------------
 * @return string
 +----------------------------------------------------------
 */
function create_guid() 
    {
        $charid = strtoupper(md5(uniqid(mt_rand(), true)));
        $hyphen = chr(45);// "-"
        $uuid = chr(123)// "{"
               .substr($charid, 0, 8).$hyphen
               .substr($charid, 8, 4).$hyphen
               .substr($charid,12, 4).$hyphen
               .substr($charid,16, 4).$hyphen
               .substr($charid,20,12)
               .chr(125);// "}"
        return $uuid;
   }
?>