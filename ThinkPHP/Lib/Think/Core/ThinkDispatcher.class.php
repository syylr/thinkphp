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

 //支持的URL模式
define('URL_COMMON',      0);   //普通模式
define('URL_PATHINFO',    1);   //PATHINFO模式
define('URL_REWRITE',     2);   //REWRITE模式
define('URL_ROUTER',     3);   // URL路由模式

class ThinkDispatcher extends Base
{//类定义开始

    /**
     +----------------------------------------------------------
     * Url 映射到控制器对象
     +----------------------------------------------------------
     */
    function dispatch() 
    {
        $urlMode  =  C('URL_MODEL');
        if(empty($urlMode)) {
            //如果没有定义 URL_MODEL 默认采用URL_PATHINFO 方式
        	$urlMode = URL_PATHINFO ;
        }
        if($urlMode == URL_REWRITE ) {
            //当前项目地址
            define('PHP_FILE',dirname(_PHP_FILE_));
        }else {
            //当前项目地址
            define('PHP_FILE',_PHP_FILE_);
        }
        if($urlMode == URL_PATHINFO || $urlMode == URL_REWRITE) {
            // PATHINFO REWRITE模式下面
            if (!empty($_GET)) {
                // 2006-8-20 完善pathinfo和get方式共存的问题
                $_GET  =  array_merge (ThinkDispatcher :: getPathInfo(),$_GET);
                // 设置默认模块和操作
                if(empty($_GET[C('VAR_MODULE')])) $_GET[C('VAR_MODULE')] = C('DEFAULT_MODULE');
                if(empty($_GET[C('VAR_ACTION')])) $_GET[C('VAR_ACTION')] = C('DEFAULT_ACTION');
                // 组装新的URL地址
                $_URL = '/';
                if(C('PATH_MODEL')==3) {
                    $_URL .= $_GET[C('VAR_MODULE')].'/'.$_GET[C('VAR_ACTION')].'/';
                    unset($_GET[C('VAR_MODULE')],$_GET[C('VAR_ACTION')]);
                }
                foreach ($_GET as $_VAR => $_VAL) { 
                    switch(C('PATH_MODEL')) {
                    	case 1:
                            $_URL .= $_VAR.C('PATH_DEPR').$_VAL.'/';
                            break;
                        case 2:
                        	$_URL .= $_VAR.C('PATH_DEPR').$_VAL.',';
                        	break;
                        case 3:
                        	$_URL .= $_VAR.'/'.$_VAL.'/';
                        	break;
                    }
                }
                if(C('PATH_MODEL')==2) $_URL = substr($_URL, 0, -1).'/';

                //重定向成规范的URL格式
                redirect(PHP_FILE.$_URL); 

            }else {
                //给_GET赋值 以保证可以按照正常方式取_GET值
                $_GET = ThinkDispatcher :: getPathInfo();
                //保证$_REQUEST正常取值
                $_REQUEST = array_merge($_POST,$_GET);
            }
        }elseif($urlMode == URL_ROUTER){
			// URL路由模式
			$paths = explode('/',trim($_SERVER['PATH_INFO'],'/'));
			// 获取路由名称
			$routeName = array_shift($paths);	
			if(!empty($_GET) && isset($_GET[C('VAR_ROUTER')])) {
				$routeName	=	$_GET[C('VAR_ROUTER')];
				unset($_GET[C('VAR_ROUTER')]);
			}
			// 获取路由解析参数 不解析get方式传递的参数
			$ruoteVar = array();
			$count	=	count($paths);
			for($i=0;$i<$count;$i++) {
				$routeVar[]	=	array_shift($paths);
			}

			// 搜索路由映射 把路由名称解析为对应的模块和操作
			// 如果找不到匹配的路由设置 则采用默认的模块和操作
			if(file_exists(CONFIG_PATH.'_routes.php')) {
				// 读取路由规则文件
				$routes = include CONFIG_PATH.'_routes.php';
				if(!is_array($router)) {
					$routes	=	$_routes;
				}
				if(isset($routes[$routeName])) {
					// 读取当前路由名称的路由规则
					$route = $routes[$routeName];
					define('MODULE_NAME',$route[0]);	// 获取当前模块名
					define('ACTION_NAME',$route[1]);	// 获取当前操作名
					//	获取当前路由参数对应的变量
					$vars	 =	 explode(',',$route[2]);
					for($i=0;$i<count($routeVar);$i++) {
						$_GET[$vars[$i]]	 =	 $routeVar[$i];	
					}
					if(isset($route[3])) {
						// 路由里面本身包含固定参数 形式为 a=111&b=222
						parse_str($route[3],$params);
						$_GET	=	array_merge($_GET,$params);
					}
					//保证$_REQUEST正常取值
					$_REQUEST = array_merge($_POST,$_GET);
				}
			}
		}else {
            //如果是URL_COMMON 模式
            if(isset($_SERVER['PATH_INFO']) ) {
                $pathinfo = ThinkDispatcher :: getPathInfo(); 
                $_GET = array_merge($_GET,$pathinfo);
                if(!empty($_POST)) {
                    $_POST = array_merge($_POST,$pathinfo);
                }else {
                    // 把pathinfo方式转换成query变量
                    $jumpUrl = PHP_FILE.'?'.http_build_query($_GET);
                    //重定向成规范的URL格式
                    redirect($jumpUrl);       
                }
            }else {
                // 正常模式
                // 过滤重复的query_string
                $query  = explode('&',trim($_SERVER['QUERY_STRING'],'&'));
                if(count($query) != count($_GET) && count($_GET)>0) {
                    $_URL  =  '';  
                    foreach ($_GET as $_VAR => $_VAL) { 
                        $_URL .= $_VAR.'='.$_VAL.'&';
                    }
                    $jumpUrl = PHP_FILE.'?'.substr($_URL,0,-1);
                    //重定向成规范的URL格式
                    redirect($jumpUrl);             	
                }            	
            }
        }
        //字符转义还原
        ThinkDispatcher :: MagicQuote();
    }

    /**
     +----------------------------------------------------------
     * 字符MagicQuote转义过滤
     +----------------------------------------------------------
     */
	function MagicQuote() 
	{
        if ( get_magic_quotes_gpc() ) {
           $_POST = stripslashes_deep($_POST);
           $_GET = stripslashes_deep($_GET);
           $_COOKIE = stripslashes_deep($_COOKIE);
           $_REQUEST= stripslashes_deep($_REQUEST);
        } 
	}

    /**
     +----------------------------------------------------------
     * 获得PATH_INFO信息
     +----------------------------------------------------------
     */
    function getPathInfo()
    {
        $pathInfo = array();
        if(isset($_SERVER['PATH_INFO'])) {
            if(C('PATH_MODEL')==3) {
                $paths = explode('/',trim($_SERVER['PATH_INFO'],'/'));
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

}//类定义结束

?>