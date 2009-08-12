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

class Router extends Think {
    static public function check() {
        // 搜索路由映射 把路由名称解析为对应的模块和操作
        $routes = C('_routes_');
        if(!empty($routes)) {
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
                if(strpos($route[0],C('GROUP_DEPR'))) {
                    $array   =  explode(C('GROUP_DEPR'),$route[0]);
                    $_GET[C('VAR_MODULE')]  =   array_pop($array);
                    $_GET[C('VAR_GROUP')]  =   implode(C('GROUP_DEPR'),$array);
                }else{
                    $_GET[C('VAR_MODULE')]  =   $route[0];
                }
                $_GET[C('VAR_ACTION')]  =   $route[1];
                //  获取当前路由参数对应的变量
                if(!isset($_GET[C('VAR_ROUTER')])) {
                    $vars    =   explode(',',$route[2]);
                    for($i=0;$i<count($vars);$i++)
                        $_GET[$vars[$i]]     =   array_shift($paths);
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
                    $rule    =   $route[0];// 路由正则
                    // 匹配路由定义
                    if(preg_match($rule,$regx,$matches)) {
                        // 检测是否存在分组 2009/06/23
                        $temp = explode(C('GROUP_DEPR'),$route[1]);
                        if ($temp[1]) {
                            $_GET[C('VAR_GROUP')]  = $temp[0];
                            $_GET[C('VAR_MODULE')] = $temp[1];
                        }else {
                            $_GET[C('VAR_MODULE')] = $temp[0];
                        }
                        $_GET[C('VAR_ACTION')]  =   $route[2];
                        //  获取当前路由参数对应的变量
                        if(!isset($_GET[C('VAR_ROUTER')])) {
                            $vars    =   explode(',',$route[3]);
                            for($i=0;$i<count($vars);$i++)
                                $_GET[$vars[$i]]     =   $matches[$i+1];
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
}
?>