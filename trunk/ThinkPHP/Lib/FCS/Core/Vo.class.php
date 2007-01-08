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

define('FCS_CACHE_NO',      -1);   //不缓存
define('FCS_CACHE_DYNAMIC', 1);   //动态缓存
define('FCS_CACHE_STATIC',  2);   //静态缓存（永久缓存）

import("FCS.Util.HashMap");

/**
 +------------------------------------------------------------------------------
 * 数据对象类
 +------------------------------------------------------------------------------
 * @package   core
 * @author    liu21st <liu21st@gmail.com>
 * @version   $Id$
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
    function __construct($data=NULL,$strict=true)
    {
        //把Map对象或者关联数组转换成Vo的属性
        if( is_instance_of($data,'HashMap')){
            $data = $data->toArray();
        }elseif( is_instance_of($data,'Base')) {
        	$data = $data->__toArray();
        }
        if(is_array($data)) {
            foreach($data as $key=>$val) {
                if(false===$strict || ($strict && property_exists($this,$key)))
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
    function toMap($strict=true)
    {
        $vars = get_object_vars($this);
        foreach($vars as $key=>$val) {
            if(is_null($val) || is_array($val) || ($strict && !property_exists($this,$key))) {
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
    function toJson($fields=array()) 
    {
        if(!empty($fields)) {
            $array   = $this->toArray();
        	foreach( $array as $key=>$val) {
        		if(!in_array($key,$fields)) {
        			unset($array[$key]);
        		}
        	}
            return json_encode($array);
        }
        return json_encode($this);
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