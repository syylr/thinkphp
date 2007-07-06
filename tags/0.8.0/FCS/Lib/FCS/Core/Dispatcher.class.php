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
 * FCS
 +------------------------------------------------------------------------------
 * @package    Core
 * @link       http://www.fcs.org.cn
 * @copyright  Copyright (c) 2005-2006 liu21st.com.  All rights reserved. 
 * @author     liu21st <liu21st@gmail.com>
 * @version    $Id$
 +------------------------------------------------------------------------------
 */

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
 * @version   0.8.0
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
     * Url映射到控制器对象
     * 把get方式重定向为pathinfo方式
     +----------------------------------------------------------
     * @static
     * @access public 
     +----------------------------------------------------------
     * @param string $urlMode  URL模式
     +----------------------------------------------------------
     * @return Controller
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function dispatch($urlMode=URL_PATHINFO) 
    {
        if($urlMode == URL_PATHINFO) {
            if ($_GET) {
                $_URL = '/';
                if(!isset($_GET[VAR_MODULE])) $_GET[VAR_MODULE] = DEFAULT_MODULE;
                if(!isset($_GET[VAR_ACTION])) $_GET[VAR_ACTION] = DEFAULT_ACTION;
                if(PATH_MODEL==3) {
                    $_URL .= $_GET[VAR_MODULE].'/'.$_GET[VAR_ACTION].'/';
                    unset($_GET[VAR_MODULE],$_GET[VAR_ACTION]);
                }
                foreach ($_GET as $_VAR => $_VAL) { 
                    if(PATH_MODEL==1){
                        $_URL .= $_VAR.PATH_DEPR.$_VAL.'/';
                    }else if(PATH_MODEL==2){
                        $_URL .= $_VAR.PATH_DEPR.$_VAL.',';
                    }else if(PATH_MODEL==3) {
                        $_URL .= $_VAR.'/'.$_VAL.'/';
                    }
                }

                if(PATH_MODEL==2) $_URL = substr($_URL, 0, -1).'/';
                $jumpUrl = $_SERVER["SCRIPT_NAME"].$_URL;
                //重定向成规范的URL格式
                redirect($jumpUrl); 

            }else {
                //给_GET赋值 以保证可以按照正常方式取_GET值
                $_GET = Dispatcher :: getPathInfo();
                //保证$_REQUEST正常取值
                $_REQUEST = array_merge($_POST,$_GET);
            }
        }else if($urlMode == URL_REWRITE) {
            //TODO
        }else {
            //如果是URL_COMMON 模式则不作任何转换
        }

        //字符转义还原
        Filter :: load('MagicQuote');

        //取得模块和操作名称
        define('MODULE_NAME',   ucwords(Dispatcher :: getModule()));        // Module名称
        define('ACTION_NAME',   Dispatcher :: getAction());        // Action操作
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
                    $pathInfo[$paths[$i]] = @(string)$paths[++$i];
                }
            }
            else {
                $res = preg_replace('@(\w+)'.PATH_DEPR.'([^,\/]+)@e', '$pathInfo[\'\\1\']="\\2";', $_SERVER['PATH_INFO']);
            }
        }
        return $pathInfo;
    }


    /**
     +----------------------------------------------------------
     * 获得模块名称
     * 
     +----------------------------------------------------------
     * @static
     * @access public 
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    function getModule()
    {
        $module = isset($_POST[VAR_MODULE]) ? 
            $_POST[VAR_MODULE] :
            $_GET[VAR_MODULE] ;
        
        if (empty($module)) $module = DEFAULT_MODULE; // 如果 $module 为空，则赋予默认值
        return $module; 
    }


    /**
     +----------------------------------------------------------
     * 获得操作名称
     * 
     +----------------------------------------------------------
     * @static
     * @access public 
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    function getAction()
    {
        $action   = isset($_POST[VAR_ACTION]) ? 
            $_POST[VAR_ACTION] : 
            $_GET[VAR_ACTION];
        if (empty($action)) $action = DEFAULT_ACTION;
        return $action; 
    }

    /**
     +----------------------------------------------------------
     * 自动根据magic_quotes_gpc 设置将转义字符去掉
     * 
     +----------------------------------------------------------
     * @static
     * @access public 
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    function magicQuote() 
    {
        if ( get_magic_quotes_gpc() ) {
           $_POST = stripslashes_deep($_POST);
           $_GET = stripslashes_deep($_GET);
           $_COOKIE = stripslashes_deep($_COOKIE);
           $_REQUEST= stripslashes_deep($_REQUEST);
        } 
        return ;
    }


}//类定义结束
?>