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
 * Think公共函数库
  +------------------------------------------------------------------------------
 * @category   Think
 * @package  Common
 * @author   liu21st <liu21st@gmail.com>
 * @version  $Id$
  +------------------------------------------------------------------------------
 */
// 设置和获取统计数据
function N($key, $step=0) {
    static $_num = array();
    if (!isset($_num[$key])) {
        $_num[$key] = 0;
    }
    if (empty($step))
        return $_num[$key];
    else
        $_num[$key] = $_num[$key] + (int) $step;
}

// URL组装 支持不同模式和路由
// 格式： U('/Admin/User/add/','aaa=1&bbb=2');
// U('__URL__/add/','aaa=1&bbb=2');
function U($url,$vars='',$suffix=true,$redirect=false,$domain=false) {
    $replace =  array(
        '__APP__'       => __APP__,        // 项目地址
        '__GROUP__'   =>   defined('GROUP_NAME')?__GROUP__:__APP__, // 分组地址
        '__URL__'       => __URL__, // 模块地址
        '__ACTION__'    => __ACTION__,     // 操作地址
    );
    $url = str_replace(array_keys($replace),array_values($replace),$url,$count);
    if($count>0) {
        $url   =  substr_replace($url,'',0,strlen(__APP__)); 
    }

    if(is_string($vars)) { // aaa=1&bbb=2 转换成数组
        parse_str($vars,$vars);
    }elseif(!is_array($vars)){
        $vars = array();
    }

    // 分析URL地址
    $info =  parse_url($url);
    $url   =  $info['path'];
    // 子域名解析
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
        }else{
            $domain = $_SERVER['HTTP_HOST'];
        }
    }
    if(substr_count($url,'/') == 2 && substr($url,0,strpos($url,'/')) ==C('DEFAULT_GROUP') ) { // 处理默认分组
        $url   =  strstr($url,'/');
    }

    if(isset($info['query'])) { // 解析地址里面参数 合并到vars
        parse_str($info['query'],$params);
        $vars = array_merge($params,$vars);
    }
    $depr = C('URL_PATHINFO_DEPR');
    if('/' != $depr) {
        // 安全替换
        $url   =  str_replace('/',$depr,$url);
    }
    $url   =  trim($url,$depr);
    if(C('URL_MODEL') == 0) { // 普通模式URL转换
        $path = explode($depr,$url);
        $var  =  array();
        $var[C('VAR_ACTION')] = array_pop($path);
        if(!empty($path)) $var[C('VAR_MODULE')] = array_pop($path);
        if(!empty($path)) $var[C('VAR_GROUP')]   = array_pop($path);
        $url   =  __APP__.'?'.http_build_query($var);
        if(!empty($vars)) {
            $vars = http_build_query($vars);
            $url   .= '&'.$vars;
        }
    }else{ // PATHINFO模式或者兼容URL模式
        $url   =  __APP__.'/'.str_replace(__APP__,'',$url);
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

/**
  +----------------------------------------------------------
 * 字符串命名风格转换
 * type
 * =0 将Java风格转换为C的风格
 * =1 将C风格转换为Java的风格
  +----------------------------------------------------------
 * @access protected
  +----------------------------------------------------------
 * @param string $name 字符串
 * @param integer $type 转换类型
  +----------------------------------------------------------
 * @return string
  +----------------------------------------------------------
 */
function parse_name($name, $type=0) {
    if ($type) {
        return ucfirst(preg_replace("/_([a-zA-Z])/e", "strtoupper('\\1')", $name));
    } else {
        $name = preg_replace("/[A-Z]/", "_\\0", $name);
        return strtolower(trim($name, "_"));
    }
}

// 错误输出
function halt($error) {
    if (IS_CLI)
        exit($error);
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

// 自定义异常处理
function throw_exception($msg, $type='ThinkException', $code=0) {
    if (IS_CLI)
        exit($msg);
    if (class_exists($type, false))
        throw new $type($msg, $code, true);
    else
        halt($msg);        // 异常类型不存在则输出错误信息字串
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

// 优化的require_once
function require_cache($filename) {
    static $_importFiles = array();
    $filename = realpath($filename);
    if (!isset($_importFiles[$filename])) {
        if (file_exists_case($filename)) {
            require $filename;
            $_importFiles[$filename] = true;
        } else {
            $_importFiles[$filename] = false;
        }
    }
    return $_importFiles[$filename];
}

// 区分大小写的文件存在判断
function file_exists_case($filename) {
    if (is_file($filename)) {
        if (IS_WIN && C('APP_FILE_CASE')) {
            if (basename(realpath($filename)) != basename($filename))
                return false;
        }
        return true;
    }
    return false;
}

/**
  +----------------------------------------------------------
 * 导入所需的类库 同java的Import
 * 本函数有缓存功能
  +----------------------------------------------------------
 * @param string $class 类库命名空间字符串
 * @param string $baseUrl 起始路径
 * @param string $ext 导入的文件扩展名
  +----------------------------------------------------------
 * @return boolen
  +----------------------------------------------------------
 */
function import($class, $baseUrl = '', $ext='.class.php') {
    static $_file = array();
    static $_class = array();
    $class = str_replace(array('.', '#'), array('/', '.'), $class);
    if ('' === $baseUrl && false === strpos($class, '/')) {
        // 检查别名导入
        return alias_import($class);
    }    //echo('<br>'.$class.$baseUrl);
    if (isset($_file[$class . $baseUrl]))
        return true;
    else
        $_file[$class . $baseUrl] = true;
    $class_strut = explode("/", $class);
    if (empty($baseUrl)) {
        if ('@' == $class_strut[0] || APP_NAME == $class_strut[0]) {
            //加载当前项目应用类库
            $baseUrl = dirname(LIB_PATH);
            $class = substr_replace($class, 'Lib/', 0, strlen($class_strut[0]) + 1);
        } elseif (in_array(strtolower($class_strut[0]), array('think', 'org', 'com'))) {
            //加载ThinkPHP基类库或者公共类库
            // think 官方基类库 org 第三方公共类库 com 企业公共类库
            $baseUrl = LIBRARY_PATH;
        } else {
            // 加载其他项目应用类库
            $class = substr_replace($class, '', 0, strlen($class_strut[0]) + 1);
            $baseUrl = APP_PATH . '../' . $class_strut[0] . '/' . LIB_DIR . '/';
        }
    }
    if (substr($baseUrl, -1) != "/")
        $baseUrl .= "/";
    $classfile = $baseUrl . $class . $ext;
    if ($ext == '.class.php' && is_file($classfile)) {
        // 冲突检测
        $class = basename($classfile, $ext);
        if (isset($_class[$class]))
            throw_exception(L('_CLASS_CONFLICT_') . ':' . $_class[$class] . ' ' . $classfile);
        $_class[$class] = $classfile;
    }
    //导入目录下的指定类库文件
    return require_cache($classfile);
}

/**
  +----------------------------------------------------------
 * 基于命名空间方式导入函数库
 * load('@.Util.Array')
  +----------------------------------------------------------
 * @param string $name 函数库命名空间字符串
 * @param string $baseUrl 起始路径
 * @param string $ext 导入的文件扩展名
  +----------------------------------------------------------
 * @return void
  +----------------------------------------------------------
 */
function load($name, $baseUrl='', $ext='.php') {
    $name = str_replace(array('.', '#'), array('/', '.'), $name);
    if (empty($baseUrl)) {
        if (0 === strpos($name, '@/')) {
            //加载当前项目函数库
            $baseUrl = APP_PATH . 'Common/';
            $name = substr($name, 2);
        } else {
            //加载ThinkPHP 系统函数库
            $baseUrl = EXTEND_PATH . 'Function/';
        }
    }
    if (substr($baseUrl, -1) != "/")
        $baseUrl .= "/";
    include $baseUrl . $name . $ext;
}

// 快速导入第三方框架类库
// 所有第三方框架的类库文件统一放到 系统的Vendor目录下面
// 并且默认都是以.php后缀导入
function vendor($class, $baseUrl = '', $ext='.php') {
    if (empty($baseUrl))
        $baseUrl = VENDOR_PATH;
    return import($class, $baseUrl, $ext);
}

// 快速定义和导入别名
function alias_import($alias, $classfile='') {
    static $_alias = array();
    if ('' !== $classfile) {
        // 定义别名导入
        $_alias[$alias] = $classfile;
        return;
    }
    if (is_string($alias)) {
        if (isset($_alias[$alias]))
            return require_cache($_alias[$alias]);
    }elseif (is_array($alias)) {
        foreach ($alias as $key => $val)
            $_alias[$key] = $val;
        return;
    }
    return false;
}

/**
  +----------------------------------------------------------
 * D函数用于实例化Model
  +----------------------------------------------------------
 * @param string name Model名称
 * @param string app Model所在项目
  +----------------------------------------------------------
 * @return Model
  +----------------------------------------------------------
 */
function D($name='', $app='') {
    static $_model = array();
    if (empty($name))
        return new Model;
    if (empty($app))
        $app = C('DEFAULT_APP');
    if (isset($_model[$app . $name]))
        return $_model[$app . $name];
    $OriClassName = $name;
    if (strpos($name, '.')) {
        $array = explode('.', $name);
        $name = array_pop($array);
        $className = $name . 'Model';
        import($app . '.Model.' . implode('.', $array) . '.' . $className);
    } else {
        $className = $name . 'Model';
        import($app . '.Model.' . $className);
    }
    if (class_exists($className)) {
        $model = new $className();
    } else {
        $model = new Model($name);
    }
    $_model[$app . $OriClassName] = $model;
    return $model;
}

/**
  +----------------------------------------------------------
 * M函数用于实例化一个没有模型文件的Model
  +----------------------------------------------------------
 * @param string name Model名称
 * @param string tablePrefix 表前缀
 * @param string class 要实例化的模型类名
  +----------------------------------------------------------
 * @return Model
  +----------------------------------------------------------
 */
function M($name='', $tablePrefix='',$class='Model') {
    static $_model = array();
    if (!isset($_model[$name . '_' . $class]))
        $_model[$name . '_' . $class] = new $class($name,$tablePrefix);
    return $_model[$name . '_' . $class];
}

/**
  +----------------------------------------------------------
 * A函数用于实例化Action
  +----------------------------------------------------------
 * @param string name Action名称
 * @param string app Model所在项目
  +----------------------------------------------------------
 * @return Action
  +----------------------------------------------------------
 */
function A($name, $app='@') {
    static $_action = array();
    if (isset($_action[$app . $name]))
        return $_action[$app . $name];
    $OriClassName = $name;
    if (strpos($name, '.')) {
        $array = explode('.', $name);
        $name = array_pop($array);
        $className = $name . 'Action';
        import($app . '.Action.' . implode('.', $array) . '.' . $className);
    } else {
        $className = $name . 'Action';
        import($app . '.Action.' . $className);
    }
    if (class_exists($className,false)) {
        $action = new $className();
        $_action[$app . $OriClassName] = $action;
        return $action;
    } else {
        return false;
    }
}

// 远程调用模块的操作方法
function R($module, $action, $app='@') {
    $class = A($module, $app);
    if ($class)
        return call_user_func(array(&$class, $action));
    else
        return false;
}

// 获取和设置语言定义(不区分大小写)
function L($name=null, $value=null) {
    static $_lang = array();
    // 空参数返回所有定义
    if (empty($name))
        return $_lang;
    // 判断语言获取(或设置)
    // 若不存在,直接返回全大写$name
    if (is_string($name)) {
        $name = strtoupper($name);
        if (is_null($value))
            return isset($_lang[$name]) ? $_lang[$name] : $name;
        $_lang[$name] = $value; // 语言定义
        return;
    }
    // 批量定义
    if (is_array($name))
        $_lang = array_merge($_lang, array_change_key_case($name, CASE_UPPER));
    return;
}

// 获取配置值
function C($name=null, $value=null) {
    static $_config = array();
    // 无参数时获取所有
    if (empty($name))
        return $_config;
    // 优先执行设置获取或赋值
    if (is_string($name)) {
        $name = strtolower($name);
        if (!strpos($name, '.')) {
            if (is_null($value))
                return isset($_config[$name]) ? $_config[$name] : null;
            $_config[$name] = is_array($value)?array_change_key_case($value):$value;
            return;
        }
        // 二维数组设置和获取支持
        $name = explode('.', $name);
        if (is_null($value))
            return isset($_config[$name[0]][$name[1]]) ? $_config[$name[0]][$name[1]] : null;
        $_config[$name[0]][$name[1]] = $value;
        return;
    }
    // 批量设置
    if (is_array($name)){
        foreach ($name as $key=>$val){
            if(is_array($val)) {
                $name[$key]  =  array_change_key_case($val);
            }
        }
        return $_config = array_merge($_config, array_change_key_case($name));
    }
    return null; // 避免非法参数
}

// 处理标签扩展
function tag($tag, &$params=NULL) {
    // 系统标签扩展
    $extends = C('extends.' . $tag);
    // 应用标签扩展
    $tags = C('tags.' . $tag);
    if (!empty($tags)) {
        if(empty($tags['_overlay']) && !empty($extends)) { // 合并扩展
            $tags = array_unique(array_merge($extends,$tags));
            $overlay = true;
        }elseif(isset($tags['_overlay'])){ // 通过设置 '_overlay'=>1 覆盖系统标签
            unset($tags['_overlay']);
        }
    }elseif(!empty($extends)) {
        $tags = $extends;
    }
    if($tags) {
        if(APP_DEBUG) {
            G($tag.'Start');
            Log::record('Tag[ '.$tag.' ] --START--',Log::DEBUG);
        }
        // 执行扩展
        foreach ($tags as $key=>$name) {
            if(!is_int($key)) { // 指定行为类的完整路径 用于模式扩展
                require_cache($name);
                $name   = $key;
            }
            B($name, $params);
        }
        if(APP_DEBUG) { // 记录行为的执行日志
            Log::record('Tag[ '.$tag.' ] --END-- [ RunTime:'.G($tag.'Start',$tag.'End',6).'s ]',Log::DEBUG);
        }
    }else{ // 未执行任何行为 返回false
        return false;
    }
}

// 动态添加行为扩展到某个标签
function add_tag_behavior($tag,$behavior,$path='') {
    $array   =  C('tags.'.$tag);
    if(!$array) {
        $array   =  array();
    }
    if($path) {
        $array[$behavior] = $path;
    }else{
        $array[] =  $behavior;
    }
    C('tags.'.$tag,$array);
}

// 过滤器方法
function filter($name, &$content) {
    $class = $name . 'Filter';
    require_cache(LIB_PATH . 'Filter/' . $class . '.class.php');
    $filter = new $class();
    $content = $filter->run($content);
}

// 执行行为
function B($name, &$params=NULL) {
    $class = $name.'Behavior';
    G('behaviorStart');
    $behavior = new $class();
    $behavior->run($params);
    if(APP_DEBUG) { // 记录行为的执行日志
        G('behaviorEnd');
        Log::record('Run '.$name.' Behavior [ RunTime:'.G('behaviorStart','behaviorEnd',6).'s ]',Log::DEBUG);
    }
}

// 渲染输出Widget
function W($name, $data=array(), $return=false) {
    $class = $name . 'Widget';
    require_cache(LIB_PATH . 'Widget/' . $class . '.class.php');
    if (!class_exists($class))
        throw_exception(L('_CLASS_NOT_EXIST_') . ':' . $class);
    $widget = Think::instance($class);
    $content = $widget->render($data);
    if ($return)
        return $content;
    else
        echo $content;
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

// 去除代码中的空白和注释
function strip_whitespace($content) {
    $stripStr = '';
    //分析php源码
    $tokens = token_get_all($content);
    $last_space = false;
    for ($i = 0, $j = count($tokens); $i < $j; $i++) {
        if (is_string($tokens[$i])) {
            $last_space = false;
            $stripStr .= $tokens[$i];
        } else {
            switch ($tokens[$i][0]) {
                //过滤各种PHP注释
                case T_COMMENT:
                case T_DOC_COMMENT:
                    break;
                //过滤空格
                case T_WHITESPACE:
                    if (!$last_space) {
                        $stripStr .= ' ';
                        $last_space = true;
                    }
                    break;
                case T_START_HEREDOC:
                    $stripStr .= "<<<THINK\n";
                    break;
                case T_END_HEREDOC:
                    $stripStr .= "THINK;\n";
                    for($k = $i+1; $k < $j; $k++) {
                        if(is_string($tokens[$k]) && $tokens[$k] == ";") {
                            $i = $k;
                            break;
                        } else if($tokens[$k][0] == T_CLOSE_TAG) {
                            break;
                        }
                    }
                    break;
                default:
                    $last_space = false;
                    $stripStr .= $tokens[$i][1];
            }
        }
    }
    return $stripStr;
}

//[RUNTIME]
// 编译文件
function compile($filename) {
    $content = file_get_contents($filename);
    // 替换预编译指令
    $content = preg_replace('/\/\/\[RUNTIME\](.*?)\/\/\[\/RUNTIME\]/s', '', $content);
    $content = substr(trim($content), 5);
    if ('?>' == substr($content, -2))
        $content = substr($content, 0, -2);
    return $content;
}

// 根据数组生成常量定义
function array_define($array) {
    $content = '';
    foreach ($array as $key => $val) {
        $key = strtoupper($key);
        $content .= 'if(!defined(\'' . $key . '\')) ';
        if (is_int($val) || is_float($val)) {
            $content .= "define('" . $key . "'," . $val . ");";
        } elseif (is_bool($val)) {
            $val = ($val) ? 'true' : 'false';
            $content .= "define('" . $key . "'," . $val . ");";
        } elseif (is_string($val)) {
            $content .= "define('" . $key . "','" . addslashes($val) . "');";
        }
    }
    return $content;
}

//[/RUNTIME]
// 循环创建目录
function mk_dir($dir, $mode = 0777) {
    if (is_dir($dir) || @mkdir($dir, $mode))
        return true;
    if (!mk_dir(dirname($dir), $mode))
        return false;
    return @mkdir($dir, $mode);
}

// 自动转换字符集 支持数组转换
function auto_charset($fContents, $from='gbk', $to='utf-8') {
    $from = strtoupper($from) == 'UTF8' ? 'utf-8' : $from;
    $to = strtoupper($to) == 'UTF8' ? 'utf-8' : $to;
    if (strtoupper($from) === strtoupper($to) || empty($fContents) || (is_scalar($fContents) && !is_string($fContents))) {
        //如果编码相同或者非字符串标量则不转换
        return $fContents;
    }
    if (is_string($fContents)) {
        if (function_exists('mb_convert_encoding')) {
            return mb_convert_encoding($fContents, $to, $from);
        } elseif (function_exists('iconv')) {
            return iconv($from, $to, $fContents);
        } else {
            return $fContents;
        }
    } elseif (is_array($fContents)) {
        foreach ($fContents as $key => $val) {
            $_key = auto_charset($key, $from, $to);
            $fContents[$_key] = auto_charset($val, $from, $to);
            if ($key != $_key)
                unset($fContents[$key]);
        }
        return $fContents;
    }
    else {
        return $fContents;
    }
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
    if (is_object($data)) {
        $data = get_object_vars($data);
    }
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