<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2010 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// $Id$

/**
  +------------------------------------------------------------------------------
 * Think 标准模式公共函数库
  +------------------------------------------------------------------------------
 * @category   Think
 * @package  Common
 * @author   liu21st <liu21st@gmail.com>
 * @version  $Id$
  +------------------------------------------------------------------------------
 */

// 错误输出
function halt($error) {
    $e = array();
    if (APP_DEBUG) {
        //调试模式下输出错误信息
        if (!is_array($error)) {
            $trace = debug_backtrace();
            $e['message'] = $error;
            $e['file'] = $trace[0]['file'];
            $e['class'] = $trace[0]['class'];
            $e['function'] = $trace[0]['function'];
            $e['line'] = $trace[0]['line'];
            $traceInfo = '';
            $time = date("y-m-d H:i:m");
            foreach ($trace as $t) {
                $traceInfo .= '[' . $time . '] ' . $t['file'] . ' (' . $t['line'] . ') ';
                $traceInfo .= $t['class'] . $t['type'] . $t['function'] . '(';
                $traceInfo .= implode(', ', $t['args']);
                $traceInfo .=")<br/>";
            }
            $e['trace'] = $traceInfo;
        } else {
            $e = $error;
        }
        // 包含异常页面模板
        include C('TMPL_EXCEPTION_FILE');
    } else {
        //否则定向到错误页面
        $error_page = C('ERROR_PAGE');
        if (!empty($error_page)) {
            redirect($error_page);
        } else {
            if (C('SHOW_ERROR_MSG'))
                $e['message'] = is_array($error) ? $error['message'] : $error;
            else
                $e['message'] = C('ERROR_MESSAGE');
            // 包含异常页面模板
            include C('TMPL_EXCEPTION_FILE');
        }
    }
    exit;
}

// 自定义异常处理
function throw_exception($msg, $type='ThinkException', $code=0) {
    if (class_exists($type, false))
        throw new $type($msg, $code, true);
    else
        halt($msg);        // 异常类型不存在则输出错误信息字串
}

// 浏览器友好的变量输出
function dump($var, $echo=true, $label=null, $strict=true) {
    $label = ($label === null) ? '' : rtrim($label) . ' ';
    if (!$strict) {
        if (ini_get('html_errors')) {
            $output = print_r($var, true);
            $output = "<pre>" . $label . htmlspecialchars($output, ENT_QUOTES) . "</pre>";
        } else {
            $output = $label . print_r($var, true);
        }
    } else {
        ob_start();
        var_dump($var);
        $output = ob_get_clean();
        if (!extension_loaded('xdebug')) {
            $output = preg_replace("/\]\=\>\n(\s+)/m", "] => ", $output);
            $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
        }
    }
    if ($echo) {
        echo($output);
        return null;
    }else
        return $output;
}

 // 区间调试开始
function debug_start($label='') {
    $GLOBALS[$label]['_beginTime'] = microtime(TRUE);
    if (MEMORY_LIMIT_ON)
        $GLOBALS[$label]['_beginMem'] = memory_get_usage();
}

// 区间调试结束，显示指定标记到当前位置的调试
function debug_end($label='') {
    $GLOBALS[$label]['_endTime'] = microtime(TRUE);
    echo '<div style="text-align:center;width:100%">Process ' . $label . ': Times ' . number_format($GLOBALS[$label]['_endTime'] - $GLOBALS[$label]['_beginTime'], 6) . 's ';
    if (MEMORY_LIMIT_ON) {
        $GLOBALS[$label]['_endMem'] = memory_get_usage();
        echo ' Memories ' . number_format(($GLOBALS[$label]['_endMem'] - $GLOBALS[$label]['_beginMem']) / 1024) . ' k';
    }
    echo '</div>';
}

// URL组装 支持不同模式
// 格式：U('[分组/模块/操作]?参数','参数','伪静态后缀','是否跳转','显示域名')
function U($url,$vars='',$suffix=true,$redirect=false,$domain=false) {
    // 解析URL
    $info =  parse_url($url);
    $url   =  !empty($info['path'])?$info['path']:ACTION_NAME;
    // 解析子域名
    if($domain===true){
        $domain = $_SERVER['HTTP_HOST'];
        if(C('APP_SUB_DOMAIN_DEPLOY') ) { // 开启子域名部署
            $domain = $domain=='localhost'?'localhost':'www'.strstr($_SERVER['HTTP_HOST'],'.');
            // '子域名'=>array('项目[/分组]');
            foreach (C('APP_SUB_DOMAIN_RULES') as $key => $rule) {
                if(false === strpos($key,'*') && 0=== strpos($url,$rule[0])) {
                    $domain = $key.strstr($domain,'.'); // 生成对应子域名
                    $url   =  substr_replace($url,'',0,strlen($rule[0]));
                    break;
                }
            }
        }
    }

    // 解析参数
    if(is_string($vars)) { // aaa=1&bbb=2 转换成数组
        parse_str($vars,$vars);
    }elseif(!is_array($vars)){
        $vars = array();
    }
    if(isset($info['query'])) { // 解析地址里面参数 合并到vars
        parse_str($info['query'],$params);
        $vars = array_merge($params,$vars);
    }

    // URL组装
    $depr = C('URL_PATHINFO_DEPR');
    if($url) {
        if(0=== strpos($url,'/')) {// 定义路由
            $route   =  true;
            $url   =  substr($url,1);
            if('/' != $depr) {
                $url   =  str_replace('/',$depr,$url);
            }
        }else{
            if('/' != $depr) { // 安全替换
                $url   =  str_replace('/',$depr,$url);
            }
            // 解析分组、模块和操作
            $url   =  trim($url,$depr);
            $path = explode($depr,$url);
            $var  =  array();
            $var[C('VAR_ACTION')] = !empty($path)?array_pop($path):ACTION_NAME;
            $var[C('VAR_MODULE')] = !empty($path)?array_pop($path):MODULE_NAME;
            if(C('URL_CASE_INSENSITIVE')) {
                $var[C('VAR_MODULE')] =  parse_name($var[C('VAR_MODULE')]);
            }
            if(C('APP_GROUP_LIST')) {
                $group   = !empty($path)?array_pop($path):GROUP_NAME;
                if($group != C('DEFAULT_GROUP')) {
                    $var[C('VAR_GROUP')]  =   $group;
                }
            }
        }
    }

    if(C('URL_MODEL') == 0) { // 普通模式URL转换
        $url   =  __APP__.'?'.http_build_query($var);
        if(!empty($vars)) {
            $vars = http_build_query($vars);
            $url   .= '&'.$vars;
        }
    }else{ // PATHINFO模式或者兼容URL模式
        if(isset($route)) {
            $url   =  __APP__.'/'.$url;
        }else{
            $url   =  __APP__.'/'.implode($depr,array_reverse($var));
        }
        if(!empty($vars)) { // 添加参数
            $vars = http_build_query($vars);
            $url .= $depr.str_replace(array('=','&'),$depr,$vars);
        }
        if($suffix) {
            $suffix   =  $suffix===true?C('URL_HTML_SUFFIX'):$suffix;
            if($suffix) {
                $url  .=  '.'.ltrim($suffix,'.');
            }
        }
    }
    if($domain) {
        $url   =  'http://'.$domain.$url;
    }
    if($redirect) // 直接跳转URL
        redirect($url);
    else
        return $url;
}

// URL重定向
function redirect($url, $time=0, $msg='') {
    //多行URL地址支持
    $url = str_replace(array("\n", "\r"), '', $url);
    if (empty($msg))
        $msg = "系统将在{$time}秒之后自动跳转到{$url}！";
    if (!headers_sent()) {
        // redirect
        if (0 === $time) {
            header("Location: " . $url);
        } else {
            header("refresh:{$time};url={$url}");
            echo($msg);
        }
        exit();
    } else {
        $str = "<meta http-equiv='Refresh' content='{$time};URL={$url}'>";
        if ($time != 0)
            $str .= $msg;
        exit($str);
    }
}

// 全局缓存设置和读取
function S($name, $value='', $expire='', $type='',$options=null) {
    static $_cache = array();
    alias_import('Cache');
    //取得缓存对象实例
    $cache = Cache::getInstance($type,$options);
    if ('' !== $value) {
        if (is_null($value)) {
            // 删除缓存
            $result = $cache->rm($name);
            if ($result)
                unset($_cache[$type . '_' . $name]);
            return $result;
        }else {
            // 缓存数据
            $cache->set($name, $value, $expire);
            $_cache[$type . '_' . $name] = $value;
        }
        return;
    }
    if (isset($_cache[$type . '_' . $name]))
        return $_cache[$type . '_' . $name];
    // 获取缓存数据
    $value = $cache->get($name);
    $_cache[$type . '_' . $name] = $value;
    return $value;
}

// 快速文件数据读取和保存 针对简单类型数据 字符串、数组
function F($name, $value='', $path=DATA_PATH) {
    static $_cache = array();
    $filename = $path . $name . '.php';
    if ('' !== $value) {
        if (is_null($value)) {
            // 删除缓存
            return unlink($filename);
        } else {
            // 缓存数据
            $dir = dirname($filename);
            // 目录不存在则创建
            if (!is_dir($dir))
                mkdir($dir);
            return file_put_contents($filename, strip_whitespace("<?php\nreturn " . var_export($value, true) . ";\n?>"));
        }
    }
    if (isset($_cache[$name]))
        return $_cache[$name];
    // 获取缓存数据
    if (is_file($filename)) {
        $value = include $filename;
        $_cache[$name] = $value;
    } else {
        $value = false;
    }
    return $value;
}

// 取得对象实例 支持调用类的静态方法
function get_instance_of($name, $method='', $args=array()) {
    static $_instance = array();
    $identify = empty($args) ? $name . $method : $name . $method . to_guid_string($args);
    if (!isset($_instance[$identify])) {
        if (class_exists($name)) {
            $o = new $name();
            if (method_exists($o, $method)) {
                if (!empty($args)) {
                    $_instance[$identify] = call_user_func_array(array(&$o, $method), $args);
                } else {
                    $_instance[$identify] = $o->$method();
                }
            }
            else
                $_instance[$identify] = $o;
        }
        else
            halt(L('_CLASS_NOT_EXIST_') . ':' . $name);
    }
    return $_instance[$identify];
}

// 根据PHP各种类型变量生成唯一标识号
function to_guid_string($mix) {
    if (is_object($mix) && function_exists('spl_object_hash')) {
        return spl_object_hash($mix);
    } elseif (is_resource($mix)) {
        $mix = get_resource_type($mix) . strval($mix);
    } else {
        $mix = serialize($mix);
    }
    return md5($mix);
}

// xml编码
function xml_encode($data, $encoding='utf-8', $root="think") {
    $xml = '<?xml version="1.0" encoding="' . $encoding . '"?>';
    $xml.= '<' . $root . '>';
    $xml.= data_to_xml($data);
    $xml.= '</' . $root . '>';
    return $xml;
}

function data_to_xml($data) {
    $xml = '';
    foreach ($data as $key => $val) {
        is_numeric($key) && $key = "item id=\"$key\"";
        $xml.="<$key>";
        $xml.= ( is_array($val) || is_object($val)) ? data_to_xml($val) : $val;
        list($key, ) = explode(' ', $key);
        $xml.="</$key>";
    }
    return $xml;
}

/**
  +----------------------------------------------------------
 * Cookie 设置、获取、清除
  +----------------------------------------------------------
 * 1 获取cookie: cookie('name')
 * 2 清空当前设置前缀的所有cookie: cookie(null)
 * 3 删除指定前缀所有cookie: cookie(null,'think_') | 注：前缀将不区分大小写
 * 4 设置cookie: cookie('name','value') | 指定保存时间: cookie('name','value',3600)
 * 5 删除cookie: cookie('name',null)
  +----------------------------------------------------------
 * $option 可用设置prefix,expire,path,domain
 * 支持数组形式对参数设置:cookie('name','value',array('expire'=>1,'prefix'=>'think_'))
 * 支持query形式字符串对参数设置:cookie('name','value','prefix=tp_&expire=10000')
 */
function cookie($name, $value='', $option=null) {
    // 默认设置
    $config = array(
        'prefix' => C('COOKIE_PREFIX'), // cookie 名称前缀
        'expire' => C('COOKIE_EXPIRE'), // cookie 保存时间
        'path' => C('COOKIE_PATH'), // cookie 保存路径
        'domain' => C('COOKIE_DOMAIN'), // cookie 有效域名
    );
    // 参数设置(会覆盖黙认设置)
    if (!empty($option)) {
        if (is_numeric($option))
            $option = array('expire' => $option);
        elseif (is_string($option))
            parse_str($option, $option);
        $config = array_merge($config, array_change_key_case($option));
    }
    // 清除指定前缀的所有cookie
    if (is_null($name)) {
        if (empty($_COOKIE))
            return;
        // 要删除的cookie前缀，不指定则删除config设置的指定前缀
        $prefix = empty($value) ? $config['prefix'] : $value;
        if (!empty($prefix)) {// 如果前缀为空字符串将不作处理直接返回
            foreach ($_COOKIE as $key => $val) {
                if (0 === stripos($key, $prefix)) {
                    setcookie($key, '', time() - 3600, $config['path'], $config['domain']);
                    unset($_COOKIE[$key]);
                }
            }
        }
        return;
    }
    $name = $config['prefix'] . $name;
    if ('' === $value) {
        return isset($_COOKIE[$name]) ? $_COOKIE[$name] : null; // 获取指定Cookie
    } else {
        if (is_null($value)) {
            setcookie($name, '', time() - 3600, $config['path'], $config['domain']);
            unset($_COOKIE[$name]); // 删除指定cookie
        } else {
            // 设置cookie
            $expire = !empty($config['expire']) ? time() + intval($config['expire']) : 0;
            setcookie($name, $value, $expire, $config['path'], $config['domain']);
            $_COOKIE[$name] = $value;
        }
    }
}

// 加载扩展配置文件
function load_ext_file() {
    // 加载自定义外部文件
    if(C('LOAD_EXT_FILE')) {
        $files =  explode(',',C('LOAD_EXT_FILE'));
        foreach ($files as $file){
            $file   = COMMON_PATH.$file.'.php';
            if(is_file($file)) include $file;
        }
    }
    // 加载自定义的动态配置文件
    if(C('LOAD_EXT_CONFIG')) {
        $configs =  C('LOAD_EXT_CONFIG');
        if(is_string($configs)) $configs =  explode(',',$configs);
        foreach ($configs as $key=>$config){
            $file   = CONFIG_PATH.$config.'.php';
            if(is_file($file)) {
                is_numeric($key)?C(include $file):C($key,include $file);
            }
        }
    }
}

// 获取客户端IP地址
function get_client_ip() {
    static $ip = NULL;
    if ($ip !== NULL) return $ip;
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $pos =  array_search('unknown',$arr);
        if(false !== $pos) unset($arr[$pos]);
        $ip   =  trim($arr[0]);
    }elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    // IP地址合法验证
    $ip = (false !== ip2long($ip)) ? $ip : '0.0.0.0';
    return $ip;
}