<?php 
    /*
    Plugin Name: FCSDispatcher
    Plugin URI: http://fcs.org.cn
    Description: PATHINFO 调度器 需要在配置文件中设置DISPATCH_NAME为FCSDispatcher才能生效
    Author: 流年
    Version: 1.0
    Author URI: http://blog.liu21st.com/
    */ 

//--------------------------------------------------
// DISPATCH_NAME 设置为 FCSDispatcher 
//--------------------------------------------------

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

 //支持的URL模式
define('URL_COMMON',      0);   //普通模式
define('URL_PATHINFO',    1);   //PATHINFO模式
define('URL_REWRITE',     2);   //REWRITE模式

/**
 +------------------------------------------------------------------------------
 * Dispatcher
 +------------------------------------------------------------------------------
 * @package   Core
 * @author    liu21st <liu21st@gmail.com>
 * @version   1.0.0
 +------------------------------------------------------------------------------
 */
class Dispatcher extends Base
{//类定义开始

    /**
     +----------------------------------------------------------
     * 架构函数
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     */
    function __construct()
    {

    }

    /**
     +----------------------------------------------------------
     * 初始化定义
     +----------------------------------------------------------
     * @static
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function init() 
    {
        if(URL_MODEL == URL_REWRITE ) {
            //当前项目地址
            define('PHP_FILE',dirname(_PHP_FILE_));
        }else {
            //当前项目地址
            define('PHP_FILE',_PHP_FILE_);
        }
    }

    /**
     +----------------------------------------------------------
     * Url 映射到控制器对象
     +----------------------------------------------------------
     * @static
     +----------------------------------------------------------
     * @param string $urlMode  URL模式
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function dispatch() 
    {
        if(!defined('URL_MODEL')) {
            //如果没有定义 URL_MODEL 默认采用URL_PATHINFO 方式
        	define('URL_MODEL',URL_PATHINFO);
        }
        Dispatcher :: init();
        $urlMode  =  URL_MODEL;
        if($urlMode == URL_PATHINFO || $urlMode == URL_REWRITE) {
            // PATHINFO REWRITE模式下面
            if ($_GET) {
                // 2006-8-20 完善pathinfo和get方式共存的问题
                $_GET  =  array_merge (Dispatcher :: getPathInfo(),$_GET);
                // 设置默认模块和操作
                if(empty($_GET[VAR_MODULE])) $_GET[VAR_MODULE] = DEFAULT_MODULE;
                if(empty($_GET[VAR_ACTION])) $_GET[VAR_ACTION] = DEFAULT_ACTION;
                // 组装新的URL地址
                $_URL = '/';
                if(PATH_MODEL==3) {
                    $_URL .= $_GET[VAR_MODULE].'/'.$_GET[VAR_ACTION].'/';
                    unset($_GET[VAR_MODULE],$_GET[VAR_ACTION]);
                }
                foreach ($_GET as $_VAR => $_VAL) { 
                    switch(PATH_MODEL) {
                    	case 1:
                            $_URL .= $_VAR.PATH_DEPR.$_VAL.'/';
                            break;
                        case 2:
                        	$_URL .= $_VAR.PATH_DEPR.$_VAL.',';
                        	break;
                        case 3:
                        	$_URL .= $_VAR.'/'.$_VAL.'/';
                        	break;
                    }
                }
                if(PATH_MODEL==2) $_URL = substr($_URL, 0, -1).'/';

                //重定向成规范的URL格式
                redirect(PHP_FILE.$_URL); 

            }else {
                //给_GET赋值 以保证可以按照正常方式取_GET值
                $_GET = Dispatcher :: getPathInfo();
                //保证$_REQUEST正常取值
                $_REQUEST = array_merge($_POST,$_GET);
            }
        }else {
            //如果是URL_COMMON 模式
            if(isset($_SERVER['PATH_INFO']) ) {
                $pathinfo = Dispatcher :: getPathInfo(); 
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
        Dispatcher :: MagicQuote();
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
     * 
     +----------------------------------------------------------
     * @static
     * @access public 
     +----------------------------------------------------------
     * @param mixed  $index 要返回的pathInfo数组索引
     +----------------------------------------------------------
     * @return mixed
     +----------------------------------------------------------
     */
    function getPathInfo($index='')
    {
        $pathInfo = array();
        if(isset($_SERVER['PATH_INFO'])) {
            if(PATH_MODEL==3) {
                $paths = explode('/',trim($_SERVER['PATH_INFO'],'/'));
                $pathInfo[VAR_MODULE] = array_shift($paths);
                $pathInfo[VAR_ACTION] = array_shift($paths);
                for($i = 0, $cnt = count($paths); $i <$cnt; $i++){
                    $pathInfo[$paths[$i]] = (string)$paths[++$i];
                }
            }
            else {
                $res = preg_replace('@(\w+)'.PATH_DEPR.'([^,\/]+)@e', '$pathInfo[\'\\1\']="\\2";', $_SERVER['PATH_INFO']);
            }
        }
        return $pathInfo;
    }

}//类定义结束

if(defined('DISPATCH_ON') && DISPATCH_ON) {
    //增加调度器总开关
    if(defined('DISPATCH_NAME') && 'FCSDispatcher'==DISPATCH_NAME ) {
        // 添加默认的FCSDispatcher调度器
        add_filter('app_dispatch',array('Dispatcher','dispatch'));
    }	
}

?>