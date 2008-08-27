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
 * ThinkPHP系统基类 抽象类
 +------------------------------------------------------------------------------
 * @category   Think
 * @package  Think
 * @subpackage  Core
 * @author    liu21st <liu21st@gmail.com>
 * @version   $Id$
 +------------------------------------------------------------------------------
 */
class Base
{

    /**
     +----------------------------------------------------------
     * 支持PHP4和PHP5的构造方法
     *
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     */
    function Base()
    {
        if(version_compare(PHP_VERSION, '5.0.0', '<')){
            //让PHP4支持__construct和__destruct
            $args = func_get_args();
            if (method_exists($this, '__destruct'))
            {
               register_shutdown_function(array(&$this, '__destruct'));
            }
            if (method_exists($this, '__construct'))
                call_user_func_array(array(&$this, '__construct'), $args);
        }
    }

}//类定义结束
?>