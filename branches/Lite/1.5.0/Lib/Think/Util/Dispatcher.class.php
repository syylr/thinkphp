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
class Dispatcher extends Base
{//类定义开始

    /**
     +----------------------------------------------------------
     * URL映射到控制器对象
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    static function dispatch()
    {
        $urlMode  =  C('URL_MODEL');
        if($urlMode == URL_REWRITE ) {
            //当前项目地址
            $url    =   dirname(_PHP_FILE_);
            if($url == '/' || $url == '\\') {
                $url    =   '';
            }
            define('PHP_FILE',$url);
        }elseif($urlMode == URL_COMPAT){
            define('PHP_FILE',_PHP_FILE_.'?'.C('VAR_PATHINFO').'=');
        }else {
            //当前项目地址
            define('PHP_FILE',_PHP_FILE_);
        }
        if($urlMode) {
            // 检查PATHINFO
            if(!empty($_GET[C('VAR_PATHINFO')])) {
                // 兼容PATHINFO 参数
                $_SERVER['PATH_INFO']   =   $_GET[C('VAR_PATHINFO')];
                unset($_GET[C('VAR_PATHINFO')]);
            }elseif(!isset($_SERVER["PATH_INFO"]))
            {
                $_SERVER['PATH_INFO'] = "";
            }elseif (empty($_SERVER["PATH_INFO"]))
            {
                // 在FastCGI模式下面 $_SERVER["PATH_INFO"] 为空
                $_SERVER['PATH_INFO'] = str_replace($_SERVER['SCRIPT_NAME'], "", $_SERVER['REQUEST_URI']);
            }
            if(C('ROUTER_ON')) {
                // 检测路由规则
                self::routerCheck();
            }
            //给_GET赋值 以保证可以按照正常方式取_GET值
            $_GET = array_merge(self :: getPathInfo(),$_GET);
            //保证$_REQUEST正常取值
            $_REQUEST = array_merge($_POST,$_GET);
        }else{
            // 普通URL模式
            //  检查路由规则
            if(isset($_GET[C('VAR_ROUTER')])) {
                self::routerCheck();
            }
        }
    }

    /**
     +----------------------------------------------------------
     * 获得PATH_INFO信息
     +----------------------------------------------------------
     * @access private
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    private static function getPathInfo()
    {
        $pathInfo = array();
        if(!empty($_SERVER['PATH_INFO'])) {
            if(C('HTML_URL_SUFFIX')) {
                $suffix =   substr(C('HTML_URL_SUFFIX'),1);
                $_SERVER['PATH_INFO']   =   preg_replace('/\.'.$suffix.'$/','',$_SERVER['PATH_INFO']);
            }
            if(C('PATH_MODEL')==2){
                $paths = explode(C('PATH_DEPR'),trim($_SERVER['PATH_INFO'],'/'));
                $pathInfo[C('VAR_MODULE')] = array_shift($paths);
                $pathInfo[C('VAR_ACTION')] = array_shift($paths);
                for($i = 0, $cnt = count($paths); $i <$cnt; $i++){
                    if(isset($paths[$i+1])) {
                        $pathInfo[$paths[$i]] = (string)$paths[++$i];
                    }elseif($i==0) {
                        $pathInfo[$pathInfo[C('VAR_ACTION')]] = (string)$paths[$i];
                    }
                }
            }
            else {
                $res = preg_replace('@(\w+)'.C('PATH_DEPR').'([^,\/]+)@e', '$pathInfo[\'\\1\']="\\2";', $_SERVER['PATH_INFO']);
            }
        }
        return $pathInfo;
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
        // 搜索路由映射 把路由名称解析为对应的模块和操作
        if(file_exists_case(CONFIG_PATH.'routes.php')) {
            $routes = include CONFIG_PATH.'routes.php';
            if(!is_array($routes)) {
                $routes =   $_routes;
            }
            if(C('HTML_URL_SUFFIX')) {
                $suffix =   substr(C('HTML_URL_SUFFIX'),1);
                $_SERVER['PATH_INFO']   =   preg_replace('/\.'.$suffix.'$/','',$_SERVER['PATH_INFO']);
            }
            if(isset($_GET[C('VAR_ROUTER')])) {
                // 存在路由变量
                $routeName  =   $_GET[C('VAR_ROUTER')];
                unset($_GET[C('VAR_ROUTER')]);
            }else{
                $paths = explode(C('PATH_DEPR'),trim($_SERVER['PATH_INFO'],'/'));
                // 获取路由名称
                $routeName  =   array_shift($paths);
            }
            if(isset($routes[$routeName])) {
                // 读取当前路由名称的路由规则
                // 路由定义格式 routeName=>array(‘模块名称’,’操作名称’,’参数定义’,’额外参数’)
                $route = $routes[$routeName];
                $_GET[C('VAR_MODULE')]  =   $route[0];
                $_GET[C('VAR_ACTION')]  =   $route[1];
                //  获取当前路由参数对应的变量
                if(!isset($_GET[C('VAR_ROUTER')])) {
                    $vars    =   explode(',',$route[2]);
                    for($i=0;$i<count($vars);$i++) {
                        $_GET[$vars[$i]]     =   array_shift($paths);
                    }
                    // 解析剩余的URL参数
                    $res = preg_replace('@(\w+)\/([^,\/]+)@e', '$_GET[\'\\1\']="\\2";', implode('/',$paths));
                }
                if(isset($route[3])) {
                    // 路由里面本身包含固定参数 形式为 a=111&b=222
                    parse_str($route[3],$params);
                    $_GET   =   array_merge($_GET,$params);
                }
                unset($_SERVER['PATH_INFO']);
            }elseif(isset($routes[$routeName.'@'])){
                // 存在泛路由
                // 路由定义格式 routeName@=>array(
                // array('路由正则1',‘模块名称’,’操作名称’,’参数定义’,’额外参数’),
                // array('路由正则2',‘模块名称’,’操作名称’,’参数定义’,’额外参数’),
                // ...)
                $routeItem = $routes[$routeName.'@'];
                $regx = str_replace($routeName,'',trim($_SERVER['PATH_INFO'],'/'));
                foreach ($routeItem as $route){
                    $rule    =   $route[0];             // 路由正则
                    if(preg_match($rule,$regx,$matches)) {
                        // 匹配路由定义
                        $_GET[C('VAR_MODULE')]  =   $route[1];
                        $_GET[C('VAR_ACTION')]  =   $route[2];
                        //  获取当前路由参数对应的变量
                        if(!isset($_GET[C('VAR_ROUTER')])) {
                            $vars    =   explode(',',$route[3]);
                            for($i=0;$i<count($vars);$i++) {
                                $_GET[$vars[$i]]     =   $matches[$i+1];
                            }
                            // 解析剩余的URL参数
                            $res = preg_replace('@(\w+)\/([^,\/]+)@e', '$_GET[\'\\1\']="\\2";', str_replace($matches[0],'',$regx));
                        }
                        if(isset($route[4])) {
                            // 路由里面本身包含固定参数 形式为 a=111&b=222
                            parse_str($route[4],$params);
                            $_GET   =   array_merge($_GET,$params);
                        }
                        break;
                    }
                }
            }
        }
    }
}//类定义结束
?>