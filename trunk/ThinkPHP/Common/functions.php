<?php 
// +----------------------------------------------------------------------
// | ThinkPHP                                                             
// +----------------------------------------------------------------------
// | Copyright (c) 2008 http://thinkphp.cn All rights reserved.      
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>                                  
// +----------------------------------------------------------------------
// $Id$

/**
 +------------------------------------------------------------------------------
 * Think公共函数库
 +------------------------------------------------------------------------------
 * @category   Think
 * @package  Common
 * @author   liu21st <liu21st@gmail.com>
 * @version  $Id$
 +------------------------------------------------------------------------------
 */

function mk_dir($dir, $mode = 0755)
{
  if (is_dir($dir) || @mkdir($dir,$mode)) return true;
  if (!mk_dir(dirname($dir),$mode)) return false;
  return @mkdir($dir,$mode);
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
 * URL组装 支持不同模式和路由
 +----------------------------------------------------------
 * @param string $action 操作名
 * @param string $module 模块名
 * @param string $app 项目名
 * @param string $route 路由名
 * @param array $params 其它URL参数
 +----------------------------------------------------------
 * @return string
 +----------------------------------------------------------
 */
function url($action=ACTION_NAME,$module=MODULE_NAME,$route='',$app=APP_NAME,$params=array()) {
	if(C('DISPATCH_ON') && C('URL_MODEL')>0) {
		switch(C('PATH_MODEL')) {
			case 1:// 普通PATHINFO模式
				$str	=	'/';
				foreach ($params as $var=>$val)
					$str .= $var.'/'.$val.'/';
				$str = substr($str,0,-1);
				if(!empty($route)) {
					$url	=	str_replace(APP_NAME,$app,__APP__).'/'.C('VAR_ROUTER').'/'.$route.'/'.$str;
				}else{
					$url	=	str_replace(APP_NAME,$app,__APP__).'/'.C('VAR_MODULE').'/'.$module.'/'.C('VAR_ACTION').'/'.$action.$str;
				}
				break;
			case 2:// 智能PATHINFO模式
				$depr	=	C('PATH_DEPR');
				$str	=	$depr;
				foreach ($params as $var=>$val)
					$str .= $var.$depr.$val.$depr;
				$str = substr($str,0,-1);
				if(!empty($route)) {
					$url	=	str_replace(APP_NAME,$app,__APP__).'/'.$route.$depr.$str;
				}else{
					$url	=	str_replace(APP_NAME,$app,__APP__).'/'.$module.$depr.$action.$str;
				}
				break;
		}
		if(C('HTML_URL_SUFFIX')) {
			$url .= C('HTML_URL_SUFFIX');
		}	
	}else{
		$params	=	http_build_query($params);
		if(!empty($route)) {
			$url	=	str_replace(APP_NAME,$app,__APP__).'?'.C('VAR_ROUTER').'='.$route.'&'.$params;
		}else{
			$url	=	str_replace(APP_NAME,$app,__APP__).'?'.C('VAR_MODULE').'='.$module.'&'.C('VAR_ACTION').'='.$action.'&'.$params;
		}
	}
	return $url;
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
		if(C('EXCEPTION_TMPL_FILE')) {
			// 定义了异常页面模板
			include C('EXCEPTION_TMPL_FILE');
		}else{
			// 使用默认的异常模板文件
	        include THINK_PATH.'/Tpl/ThinkException.tpl.php';
		}
    }
    else 
    {
        //否则定向到错误页面
		$error_page	=	C('ERROR_PAGE');
        if(!empty($error_page)){
            redirect($error_page); 
        }else {
            $e['message'] = C('ERROR_MESSAGE');
			if(C('EXCEPTION_TMPL_FILE')) {
				// 定义了异常页面模板
				include C('EXCEPTION_TMPL_FILE');
			}else{
				// 使用默认的异常模板文件
				include THINK_PATH.'/Tpl/ThinkException.tpl.php';
			}
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
 * 自定义异常处理
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
    if(isset($_REQUEST[C('VAR_AJAX_SUBMIT')])) {
        header("Content-Type:text/html; charset=utf-8");
        exit($msg);
    }
	if(class_exists($type,false)){
		throw new $type($msg,$code,true);
	}else {
		// 异常类型不存在则输出错误信息字串
		halt($msg);
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
    $GLOBALS[$label]['_beginTime'] = microtime(TRUE);
    if ( MEMORY_LIMIT_ON )	$GLOBALS[$label]['memoryUseStartTime'] = memory_get_usage();
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
    $GLOBALS[$label]['_endTime'] = microtime(TRUE);
    echo '<div style="text-align:center;width:100%">Process '.$label.': Times '.number_format($GLOBALS[$label]['_endTime']-$GLOBALS[$label]['_beginTime'],6).'s ';
    if ( MEMORY_LIMIT_ON )	{
        $GLOBALS[$label]['memoryUseEndTime'] = memory_get_usage();
        echo ' Memories '.number_format(($GLOBALS[$label]['memoryUseEndTime']-$GLOBALS[$label]['memoryUseStartTime'])/1024).' k';
    }
	echo '</div>';
}

/**
 +----------------------------------------------------------
 * 系统调试输出 Log::record 的一个调用方法
 +----------------------------------------------------------
 * @param string $msg 调试信息
 +----------------------------------------------------------
 * @return void
 +----------------------------------------------------------
 */
function system_out($msg)
{
    if(!empty($msg))
        Log::record($msg,WEB_LOG_DEBUG);
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
function dump($var, $echo=true,$label=null, $strict=true)
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
            return iconv($from,$to,$fContents);
        }else{
            halt(L('_NO_AUTO_CHARSET_'));
            return $fContents;
        }
    }
    elseif(is_array($fContents)){
        foreach ( $fContents as $key => $val ) {
			$_key = 	auto_charset($key,$from,$to);
            $fContents[$_key] = auto_charset($val,$from,$to);
			if($key != $_key ) {
				unset($fContents[$key]);
			}
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
	if(empty($args)) {
		$identify	=	$className.$method;
	}else{
		$identify	=	$className.$method.to_guid_string($args);
	}
    if (!isset($_instance[$identify])) {
        if(class_exists($className)){
            $o = new $className();
            if(method_exists($o,$method)){
                if(!empty($args)) {
                	$_instance[$identify] = call_user_func_array(array(&$o, $method), $args);;
                }else {
                	$_instance[$identify] = $o->$method();
                }
            }
            else 
                $_instance[$identify] = $o;
        }
        else 
            halt(L('_CLASS_NOT_EXIST_'));
    }
    return $_instance[$identify];
}

/**
 +----------------------------------------------------------
 * 系统自动加载ThinkPHP基类库和当前项目的model和Action对象
 * 并且支持配置自动加载路径
 +----------------------------------------------------------
 * @param string $classname 对象类名
 +----------------------------------------------------------
 * @return void
 +----------------------------------------------------------
 */
function __autoload($classname)
{
	// 自动加载当前项目的Actioon类和Dao类
	if(substr($classname,-5)=="Model") {
		if(!import('@.Model.'.$classname)){
			// 如果加载失败 尝试加载组件Dao类库
			import("@.*.Model.".$classname);
		}
	}elseif(substr($classname,-6)=="Action"){
		if(!import('@.Action.'.$classname)) {
			// 如果加载失败 尝试加载组件Action类库
			import("@.*.Action.".$classname);
		}
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
	// 根据自动加载路径设置进行尝试搜索
	if(C('CALLBACK_LOAD_PATH')) {
		$paths	=	explode(',',C('CALLBACK_LOAD_PATH'));
		foreach ($paths as $path){
			if(import($path.$classname)) {
				// 如果加载类成功则返回
				return ;
			}
		}
	}
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
            include $filename;
            $_importFiles[$filename] = true;
            return true;
        }
        return false;
    }
    return false;
}

$GLOBALS['include_file'] = 0;
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
            require $filename;
			$GLOBALS['include_file']++;
            $_importFiles[$filename] = true;
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
 * @param string $appName 项目名
 * @param string $ext 导入的文件扩展名
 * @param string $subdir 是否导入子目录 默认false
 +----------------------------------------------------------
 * @return boolen
 +----------------------------------------------------------
 */
function import($class,$baseUrl = '',$ext='.class.php',$subdir=false)
{
      //echo('<br>'.$class.$baseUrl);
      static $_file = array();
	  static $_class = array();
      if(isset($_file[strtolower($class.$baseUrl)]))
            return true;
      else 
            $_file[strtolower($class.$baseUrl)] = true;
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
      elseif(APP_NAME == $class_strut[0]) {
          //加载当前项目应用类库
          $class =  str_replace(APP_NAME.'.',LIB_DIR.'.',$class);
      }
      elseif(in_array(strtolower($class_strut[0]),array('think','org','com'))) {
          //加载ThinkPHP基类库或者公共类库
		  // think 官方基类库 org 第三方公共类库 com 企业公共类库
          $baseUrl =  THINK_PATH.'/'.LIB_DIR.'/';
      }else {
          // 加载其他项目应用类库
          $class =  str_replace($class_strut[0],'',$class);
          $baseUrl =  APP_PATH.'/../'.$class_strut[0].'/'.LIB_DIR.'/';
      }
      if(substr($baseUrl, -1) != "/")    $baseUrl .= "/";
      $classfile = $baseUrl.str_replace('.', '/', $class).$ext;
	  if(false !== strpos($classfile,'*') || false !== strpos($classfile,'?') ) {
			// 导入匹配的文件
			$match	=	glob($classfile);
			if($match) {
			   foreach($match as $key=>$val) {
				   if(is_dir($val)) {
					   if($subdir) import('*',$val.'/',$ext,$subdir);
				   }else{
					   if($ext == '.class.php') {
							// 冲突检测
							$class = basename($val,$ext);
							if(isset($_class[$class])) {
								throw_exception($class.'类名冲突');
							}
							$_class[$class] = $val;
					   }
						//导入类库文件
						$result	=	require_cache($val);
				   }
			   } 
	           return $result;
			}else{
	           return false;
			}
	  }else{
		  if($ext == '.class.php' && file_exists($classfile)) {
				// 冲突检测
				$class = basename($classfile,$ext);
				if(isset($_class[strtolower($class)])) {
					throw_exception('类名冲突:'.$_class[strtolower($class)].' '.$classfile);
				}
				$_class[strtolower($class)] = $classfile;
		  }
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

// 快速导入第三方框架类库
// 所有第三方框架的类库文件统一放到 基类库Vendor目录下面
// 并且默认都是以.php后缀导入
function vendor($class,$baseUrl = '',$ext='.php',$subdir=false)
{
	if(empty($baseUrl)) {
		$baseUrl	=	VENDOR_PATH;
	}
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
	if(is_object($mix) && function_exists('spl_object_hash')) {
		return spl_object_hash($mix);
	}elseif(is_resource($mix)){
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
	return new ArrayObject($values);
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
    return $object instanceof $className;
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
    $_SESSION['_filters']	=	$_filter;
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
	$_filter  = $_SESSION['_filters'];
	if ( isset($_filter[APP_NAME.'_'.$tag]["$priority"]) ) {
		$new_function_list = array();
		foreach($_filter[APP_NAME.'_'.$tag]["$priority"] as $filter) {
			if ( $filter['function'] != $function_to_remove ) {
				$new_function_list[] = $filter;
			}
		}
		$_filter[APP_NAME.'_'.$tag]["$priority"] = $new_function_list;
	}
    $_SESSION['_filters']	=	$_filter;
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
	if (!isset($_SESSION['_filters']) ||  !isset($_SESSION['_filters'][APP_NAME.'_'.$tag]) ) {
		return $string;
	}
	$_filter  = $_SESSION['_filters'][APP_NAME.'_'.$tag];
    ksort($_filter);
    $args = array_slice(func_get_args(), 2);
	foreach ($_filter as $priority => $functions) {
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
 * @static
 * @access public 
 +----------------------------------------------------------
 * @param string $str 需要转换的字符串
 * @param string $start 开始位置
 * @param string $length 截取长度
 * @param string $charset 编码格式
 * @param string $suffix 截断显示字符
 +----------------------------------------------------------
 * @return string
 +----------------------------------------------------------
 */
function msubstr($str, $start=0, $length, $charset="utf-8", $suffix=true)
{
	if(function_exists("mb_substr"))
		return mb_substr($str, $start, $length, $charset);
	elseif(function_exists('iconv_substr')) {
		return iconv_substr($str,$start,$length,$charset);
	}
	$re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
	$re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
	$re['gbk']	  = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
	$re['big5']	  = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
	preg_match_all($re[$charset], $str, $match);
	$slice = join("",array_slice($match[0], $start, $length));
	if($suffix) return $slice."…";
	return $slice;
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
            $chars= str_repeat('0123456789',3); 
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
	if($type!=4) {
		$chars   =   str_shuffle($chars);
		$str     =   substr($chars,0,$len);
	}else{
		// 中文随机字
		for($i=0;$i<$len;$i++){   
		  $str.= msubstr($chars, floor(mt_rand(0,mb_strlen($chars,'utf-8')-1)),1);   
		} 
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

function D($className='',$appName='@') 
{
	static $_model = array();
	if(empty($className)) {
		return new  Model();
	}else{
	   	$className =  C('MODEL_CLASS_PREFIX').$className.C('MODEL_CLASS_SUFFIX');
	}
	if(isset($_model[$className])) {
		return $_model[$className];
	}
	if(strpos($className,C('COMPONENT_DEPR'))) {
		$array	=	explode(C('COMPONENT_DEPR'),$className);
		$className = array_pop($array);
		import($appName.'.'.implode('.',$array).'.Model.'.$className);
	}else{
		if(!import($appName.'.Model.'.$className)) {
			// 如果加载失败 尝试自动匹配
			import($appName.'.*.Model.'.$className);
		}
	}
    if(class_exists($className)) {
        $model = new $className();
		$_model[$className] =	$model;
        return $model;    	
    }else {
    	return false;
    }
}

function A($className,$appName='@') 
{
	static $_action = array();
	$className =  C('CONTR_CLASS_PREFIX').$className.C('CONTR_CLASS_SUFFIX');
	if(isset($_action[$className])) {
		return $_action[$className];
	}
	if(strpos($className,C('COMPONENT_DEPR'))) {
		$array	=	explode(C('COMPONENT_DEPR'),$className);
		$className = array_pop($array);
		import($appName.'.'.implode('.',$array).'.Action.'.$className);
	}else{
		if(!import($appName.'.Action.'.$className)) {
			// 如果加载失败 尝试加载组件类库
			import($appName.'.*.Action.'.$className);
		}
	}
    if(class_exists($className)) {
        $action = new $className();
		$_actioin[$className] =	$action;
        return $action;    	
    }else {
    	return false;
    }
}

// 获取语言定义
function L($name='',$value=null) {
	static $_lang = array();
	if(!is_null($value)) {
		$_lang[strtolower($name)]	=	$value;
		return;
	}
	if(empty($name)) {
		return $_lang;
	}
	if(is_array($name)) {
		$_lang = array_merge($_lang,array_change_key_case($name));
		return;
	}
	if(isset($_lang[strtolower($name)])) {
		return $_lang[strtolower($name)];
	}else{
		return false;
	}
}

// 获取配置值
function C($name='',$value=null) {
	static $_config = array();
	if(!is_null($value)) {
		$_config[strtolower($name)]	=	$value;
		return ;
	}
	if(empty($name)) {
		return $_config;
	}
	// 缓存全部配置值
	if(is_array($name)) {
		$_config = array_merge($_config,array_change_key_case($name));
		return $_config;
	}
	if(isset($_config[strtolower($name)])) {
		return $_config[strtolower($name)];
	}else{
		return false;
	}
}

// 全局缓存设置和读取
function S($name,$value='',$expire='',$type='') {
	static $_cache = array();
	import('Think.Util.Cache');
	//取得缓存对象实例
	$cache  = Cache::getInstance($type);
	if('' !== $value) {
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
	// 获取缓存数据
	$value      =  $cache->get($name);
	$_cache[$type.'_'.$name]	 =	 $value;
	return $value;
}

// 快速文件数据读取和保存 针对简单类型数据 字符串、数组
function F($name,$value='',$expire=-1,$path=DATA_PATH) {
	static $_cache = array();
	$filename	=	$path.$name.'.php';
	if('' !== $value) {
		if(is_null($value)) {
			// 删除缓存
			$result	=	unlink($filename);
			if($result) {
				unset($_cache[$name]);
			}
			return $result;
		}else{
			// 缓存数据
			$content   =   "<?php\n//".sprintf('%012d',$expire)."\nreturn ".var_export($value,true).";\n?>";
			$result  =   file_put_contents($filename,$content);
			$_cache[$name]	 =	 $value;
		}
		return ;
	}
	if(isset($_cache[$name])) {
		return $_cache[$name];
	}
	// 获取缓存数据
	if(file_exists($filename) && false !== $content = file_get_contents($filename)) {
		$expire  =  (int)substr($content,8, 12);
		if($expire != -1 && time() > filemtime($filename) + $expire) { 
			//缓存过期删除缓存文件
			unlink($filename);
			return false;
		}
		$value	=	 eval(substr($content,21,-2));
		$_cache[$name]	 =	 $value;
	}else{
		$value	=	false;
	}
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
		if(class_exists($className)) {
			$_class[$baseUrl.$class] = new $className();
			return $_class[$baseUrl.$class];
		}else{
			return false;
		}
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

// 清除缓存目录
function clearCache($type=0,$path=NULL) {
		if(is_null($path)) {
			switch($type) {
			case 0:// 模版缓存目录
				$path = CACHE_PATH;
				break;
			case 1:// 数据缓存目录
				$path	=	TEMP_PATH;
				break;
			case 2://  日志目录
				$path	=	LOG_PATH;
				break;
			case 3://  数据目录
				$path	=	DATA_PATH;
			}
		}
		import("ORG.Io.Dir");
		Dir::del($path);   
	}

// 创建项目目录结构
function buildAppDir() {
	// 没有创建项目目录的话自动创建
	if(!is_dir(APP_PATH)){
		mk_dir(APP_PATH);
	}
	if(is_writeable(APP_PATH)) {
		if(!is_dir(LIB_PATH)) 
			mkdir(LIB_PATH);				// 创建项目应用目录
		if(!is_dir(CONFIG_PATH)) 
			mkdir(CONFIG_PATH);		//	创建项目配置目录
		if(!is_dir(COMMON_PATH)) 
			mkdir(COMMON_PATH);	//	创建项目公共目录
		if(!is_dir(LANG_PATH)) 
			mkdir(LANG_PATH);			//	创建项目语言包目录
		if(!is_dir(CACHE_PATH)) 
			mkdir(CACHE_PATH);		//	创建模板缓存目录
		if(!is_dir(TMPL_PATH)) 
			mkdir(TMPL_PATH);			//	创建模板目录
		if(!is_dir(TMPL_PATH.'default/')) 
			mkdir(TMPL_PATH.'default/');			//	创建模板默认主题目录
		if(!is_dir(LOG_PATH)) 
			mkdir(LOG_PATH);			//	创建项目日志目录
		if(!is_dir(TEMP_PATH)) 
			mkdir(TEMP_PATH);			//	创建临时缓存目录
		if(!is_dir(DATA_PATH)) 
			mkdir(DATA_PATH);			//	创建数据缓存目录
		if(!is_dir(LIB_PATH.'Model/')) 
			mkdir(LIB_PATH.'Model/');	//	创建模型目录
		if(!is_dir(LIB_PATH.'Action/')) 
			mkdir(LIB_PATH.'Action/');	//	创建控制器目录
		// 目录安全写入
		if(!defined('BUILD_DIR_SECURE')) define('BUILD_DIR_SECURE',false);
		if(BUILD_DIR_SECURE) {
			if(!defined('DIR_SECURE_FILENAME')) define('DIR_SECURE_FILENAME','index.html');
			if(!defined('DIR_SECURE_CONTENT')) define('DIR_SECURE_CONTENT',' ');
			// 自动写入目录安全文件
			$content		=	DIR_SECURE_CONTENT;
			$a = explode(',', DIR_SECURE_FILENAME);
			foreach ($a as $filename){
				file_put_contents(LIB_PATH.$filename,$content);
				file_put_contents(LIB_PATH.'Action/'.$filename,$content);
				file_put_contents(LIB_PATH.'Model/'.$filename,$content);
				file_put_contents(CACHE_PATH.$filename,$content);
				file_put_contents(LANG_PATH.$filename,$content);
				file_put_contents(TEMP_PATH.$filename,$content);
				file_put_contents(TMPL_PATH.$filename,$content);
				file_put_contents(TMPL_PATH.'default/'.$filename,$content);
				file_put_contents(DATA_PATH.$filename,$content);
				file_put_contents(COMMON_PATH.$filename,$content);
				file_put_contents(CONFIG_PATH.$filename,$content);
				file_put_contents(LOG_PATH.$filename,$content);
			}
		}
		// 写入测试Action
		if(!file_exists(LIB_PATH.'Action/IndexAction.class.php')) {
			$content	 =	 
'<?php 
// 本类由系统自动生成，仅供测试用途
class IndexAction extends Action{
	public function index(){
		header("Content-Type:text/html; charset=utf-8");
		echo "<div style=\'font-weight:normal;color:blue;float:left;width:345px;text-align:center;border:1px solid silver;background:#E8EFFF;padding:8px;font-size:14px;font-family:Tahoma\'>^_^ Hello,欢迎使用<span style=\'font-weight:bold;color:red\'>ThinkPHP</span></div>";
	}
} 
?>';
			file_put_contents(LIB_PATH.'Action/IndexAction.class.php',$content);
		}
	}else{
		header("Content-Type:text/html; charset=utf-8");
		exit('<div style=\'font-weight:bold;float:left;width:345px;text-align:center;border:1px solid silver;background:#E8EFFF;padding:8px;color:red;font-size:14px;font-family:Tahoma\'>项目目录不可写，目录无法自动生成！<BR>请使用项目生成器或者手动生成项目目录~</div>');
	}
}
?>