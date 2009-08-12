<?php
// +----------------------------------------------------------------------
// | ThinkPHP
// +----------------------------------------------------------------------
// | Copyright (c) 2009 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// $Id$

/**
 +------------------------------------------------------------------------------
 * ThinkPHP内置的Dispatcher类
 * 完成URL解析、路由和调度
 +------------------------------------------------------------------------------
 * @category   Think
 * @package  Think
 * @subpackage  Core
 * @author    liu21st <liu21st@gmail.com>
 * @version   $Id$
 +------------------------------------------------------------------------------
 */
class Dispatcher extends Think
{//类定义开始

    /**
     +----------------------------------------------------------
     * URL映射到控制器
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    static public function dispatch()
    {
        $urlMode  =  C('URL_MODEL');
        if($urlMode == URL_REWRITE ) {
            //当前项目地址
            $url    =   dirname(_PHP_FILE_);
            if($url == '/' || $url == '\\')
                $url    =   '';
            define('PHP_FILE',$url);
        }elseif($urlMode == URL_COMPAT){
            define('PHP_FILE',_PHP_FILE_.'?'.C('VAR_PATHINFO').'=');
        }else {
            //当前项目地址
            define('PHP_FILE',_PHP_FILE_);
        }
        if($urlMode) {
			// 获取PATHINFO信息
            self::getPathInfo();
            if (!empty($_GET) && !isset($_GET[C('VAR_ROUTER')])) {
                $_GET  =  array_merge (self :: parsePathInfo(),$_GET);
                $_varGroup =   C('VAR_GROUP'); // 分组变量
                $_varModule =   C('VAR_MODULE');
                $_varAction =   C('VAR_ACTION');
                $_depr  =   C('PATH_DEPR');
                $_pathModel =   C('PATH_MODEL');
                if (C('APP_GROUP')) {
                    if(empty($_GET[$_varGroup]))
                        $_GET[$_varGroup] = C('DEFAULT_GROUP');
                }else {
                    $_GET[$_varGroup] = '';
                }
                // 设置默认模块和操作
                if(empty($_GET[$_varModule])) $_GET[$_varModule] = C('DEFAULT_MODULE');
                if(empty($_GET[$_varAction])) $_GET[$_varAction] = C('DEFAULT_ACTION');
                // 组装新的URL地址
                $_URL = '/';
                if($_pathModel==2) {
                    // groupName/modelName/actionName/
                    $_URL .= $_GET[$_varGroup].($_GET[$_varGroup]?$_depr:'').$_GET[$_varModule].$_depr.$_GET[$_varAction].$_depr;
                    unset($_GET[$_varGroup],$_GET[$_varModule],$_GET[$_varAction]);
                }
                foreach ($_GET as $_VAR => $_VAL) {
                    if('' != trim($_GET[$_VAR])) {
                        if($_pathModel==2) {
                            $_URL .= $_VAR.$_depr.rawurlencode($_VAL).$_depr;
                        }else{
                            $_URL .= $_VAR.'/'.rawurlencode($_VAL).'/';
                        }
                    }
                }
                if($_depr==',') $_URL = substr($_URL, 0, -1).'/';
                //重定向成规范的URL格式
                redirect(PHP_FILE.$_URL);
            }else{
                if(C('ROUTER_ON')) self::routerCheck();   // 检测路由规则
                //给_GET赋值 以保证可以按照正常方式取_GET值
                $_GET = array_merge(self :: parsePathInfo(),$_GET);
                //保证$_REQUEST正常取值
                $_REQUEST = array_merge($_POST,$_GET);
            }
        }else{
            // 普通URL模式 检查路由规则
            if(isset($_GET[C('VAR_ROUTER')])) self::routerCheck();
        }
    }

    /**
     +----------------------------------------------------------
     * 分析PATH_INFO的参数
     +----------------------------------------------------------
     * @access private
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    private static function parsePathInfo()
    {
        $pathInfo = array();
        if(C('PATH_MODEL')==2){
            $paths = explode(C('PATH_DEPR'),trim($_SERVER['PATH_INFO'],'/'));
            $groupApp = C('APP_GROUP');
            if ($groupApp) {
                $arr = array_map('strtolower',explode(',',$groupApp));
                $pathInfo[C('VAR_GROUP')] = in_array(strtolower($paths[0]),$arr)? array_shift($paths) : C('DEFAULT_GROUP');
            }
            $pathInfo[C('VAR_MODULE')] = array_shift($paths);
            $pathInfo[C('VAR_ACTION')] = array_shift($paths);
            for($i = 0, $cnt = count($paths); $i <$cnt; $i++){
                if(isset($paths[$i+1])) {
                    $pathInfo[$paths[$i]] = (string)$paths[++$i];
                }elseif($i==0) {
                    $pathInfo[$pathInfo[C('VAR_ACTION')]] = (string)$paths[$i];
                }
            }
        }else {
            $res = preg_replace('@(\w+)'.C('PATH_DEPR').'([^,\/]+)@e', '$pathInfo[\'\\1\']="\\2";', $_SERVER['PATH_INFO']);
        }
        return $pathInfo;
    }

    /**
    +----------------------------------------------------------
    * 获得服务器的PATH_INFO信息
     +----------------------------------------------------------
     * @access private
    +----------------------------------------------------------
    * @return void
    +----------------------------------------------------------
    */
    private static function getPathInfo()
    {
        if(!empty($_GET[C('VAR_PATHINFO')])) {
            // 兼容PATHINFO 参数
            $path = $_GET[C('VAR_PATHINFO')];
            unset($_GET[C('VAR_PATHINFO')]);
        }elseif(!empty($_SERVER['PATH_INFO'])){
            $pathInfo = $_SERVER['PATH_INFO'];
            if(0 === strpos($pathInfo,$_SERVER['SCRIPT_NAME']))
                $path = substr($pathInfo, strlen($_SERVER['SCRIPT_NAME']));
            else
                $path = $pathInfo;
        }elseif(!empty($_SERVER['ORIG_PATH_INFO'])) {
            $pathInfo = $_SERVER['ORIG_PATH_INFO'];
            if(0 === strpos($pathInfo, $_SERVER['SCRIPT_NAME']))
                $path = substr($pathInfo, strlen($_SERVER['SCRIPT_NAME']));
            else
                $path = $pathInfo;
        }elseif (!empty($_SERVER['REDIRECT_PATH_INFO'])){
            $path = $_SERVER['REDIRECT_PATH_INFO'];
        }elseif(!empty($_SERVER["REDIRECT_Url"])){
            $path = $_SERVER["REDIRECT_Url"];
            if(empty($_SERVER['QUERY_STRING']) || $_SERVER['QUERY_STRING'] == $_SERVER["REDIRECT_QUERY_STRING"])
            {
                $parsedUrl = parse_url($_SERVER["REQUEST_URI"]);
                if(!empty($parsedUrl['query'])) {
                    $_SERVER['QUERY_STRING'] = $parsedUrl['query'];
                    parse_str($parsedUrl['query'], $GET);
                    $_GET = array_merge($_GET, $GET);
                    reset($_GET);
                }else {
                    unset($_SERVER['QUERY_STRING']);
                }
                reset($_SERVER);
            }
        }
        if(C('HTML_URL_SUFFIX') && !empty($path)) {
            $suffix =   substr(C('HTML_URL_SUFFIX'),1);
            $path   =   preg_replace('/\.'.$suffix.'$/','',$path);
        }
        $_SERVER['PATH_INFO'] = empty($path) ? '/' : $path;
    }

    /**
     +----------------------------------------------------------
     * 路由检测
     +----------------------------------------------------------
     * @access private
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    private static function routerCheck() {
        require_cache(dirname(__FILE__).'/Router.class.php');
        Router::check();
    }
}//类定义结束
?>