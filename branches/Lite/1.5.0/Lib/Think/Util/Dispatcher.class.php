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
        if($urlMode == URL_PATHINFO || $urlMode == URL_REWRITE || $urlMode == URL_COMPAT) {
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
            //给_GET赋值 以保证可以按照正常方式取_GET值
            $_GET = array_merge(self :: getPathInfo(),$_GET);
            //保证$_REQUEST正常取值
            $_REQUEST = array_merge($_POST,$_GET);
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

}//类定义结束
?>