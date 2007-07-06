<?php 
// +---------------------------------------------------------------------------+
// | FCS -- Fast,Compatible & Simple OOP PHP Framework                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2005-2006 liu21st.com.  All rights reserved.                |
// | Website: http://www.fcs.org.cn/                                           |
// | Author : Liu21st <liu21st@gmail.com>                                      |
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

define('FCS_CACHE_NO',      -1);   //不缓存
define('FCS_CACHE_DYNAMIC', 1);   //动态缓存
define('FCS_CACHE_STATIC',  2);   //静态缓存（永久缓存）
/**
 +------------------------------------------------------------------------------
 * FCS
 +------------------------------------------------------------------------------
 * @package    Core
 * @link       http://www.fcs.org.cn
 * @copyright  Copyright (c) 2005-2006 liu21st.com.  All rights reserved. 
 * @author     liu21st <liu21st@gmail.com>
 * @version    $Id: Vo.class.php 80 2006-11-09 15:55:35Z fcs $
 +------------------------------------------------------------------------------
 */

import("FCS.Util.HashMap");

/**
 +------------------------------------------------------------------------------
 * 数据对象类
 +------------------------------------------------------------------------------
 * @package   core
 * @author    liu21st <liu21st@gmail.com>
 * @version   0.8.0
 +------------------------------------------------------------------------------
 */
class Vo extends Base
{

    /**
     +----------------------------------------------------------
     * 额外的vo信息 toMap的时候会自动过滤
     +----------------------------------------------------------
     * @var Array
     * @access protected
     +----------------------------------------------------------
     */
    var $_info  =   array();                

    /**
     +----------------------------------------------------------
     * 架构函数
     * 支持根据数组 对象 或者map对象构建Vo对象
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param mixed $data 数据
     +----------------------------------------------------------
     */
    function __construct($data=NULL)
    {
        //把Map对象或者关联数组转换成Vo的属性
        if( is_instance_of($data,'HashMap')){
            $data = $data->toArray();
        }elseif( is_instance_of($data,'Base')) {
        	$data = $data->__toArray();
        }
        if(is_array($data)) {
            foreach($data as $key=>$val) {
                if(property_exists($this,$key))
                    $this->$key = $val; 
            }        	
        }

    }

    /**
     +----------------------------------------------------------
     * 把Vo对象转换为HashMap对象
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return HashMap
     +----------------------------------------------------------
     */
    function toMap()
    {
        $vars = get_object_vars($this);
        foreach($vars as $key=>$val) {
            if(is_null($val) || is_array($val) || !property_exists($this,$key)) {
                    unset($vars[$key]);	
            }
        }
        $map= new HashMap($vars);
        return $map;
    }

    /**
     +----------------------------------------------------------
     * 取得当前Dao对象的名称
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function getDao()
    {
        return substr($this->__toString(),0,-2).'Dao';
    }


    /**
     +----------------------------------------------------------
     * 把Vo对象转换为数组
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    function toArray() 
    {
        return $this->__toArray();
    }


    /**
     +----------------------------------------------------------
     * 把Vo对象转换为Json
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    function toJson() 
    {
        $vars = get_object_vars($this);
        $json = '{';
        foreach($vars as $key=>$val) {
            $json .= "\"$key\"".':'."\"$val\",";
        }
        $json  = substr($json,0,-1);
        $json .= '}';
        return $json;	
    }
    
    /**
     +----------------------------------------------------------
     * Vo对象是否为空
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return HashMap
     +----------------------------------------------------------
     */
    function isEmpty()
    {
        return $this->__toArray() == $this->__toOraArray();
    }
};
?>