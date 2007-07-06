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
 * @package    Util
 * @link       http://www.fcs.org.cn
 * @copyright  Copyright (c) 2005-2006 liu21st.com.  All rights reserved. 
 * @author     liu21st <liu21st@gmail.com>
 * @version    $Id: _XMLParser.class.php 73 2006-11-08 10:08:01Z fcs $
 +------------------------------------------------------------------------------
 */

/**
 +------------------------------------------------------------------------------
 * XML 解析类 for PHP5
 +------------------------------------------------------------------------------
 * @package   Util
 * @author    liu21st <liu21st@gmail.com>
 * @version   0.8.0
 +------------------------------------------------------------------------------
 */
class XMLParser extends Base
{//类定义开始

    /**
     +----------------------------------------------------------
     * XML 数据 
     +----------------------------------------------------------
     * @var string
     * @access protected
     +----------------------------------------------------------
     */
    var $xml; 

    /**
     +----------------------------------------------------------
     * 架构函数
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param mixed $xml XML数据
     +----------------------------------------------------------
     * @param string $elements  初始化数组元素
     +----------------------------------------------------------
     */
    function __construct($xml='')
    {
        $this->getXml($xml);
    }

    /**
     +----------------------------------------------------------
     * XML解析
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return mixed
     +----------------------------------------------------------
     */
    function parse($xml='')
    {
        $this->getXml($xml);
        $result  =  simplexml_load_string($this->xml);
        if(false !== $result ) {
        	return $this->objToArray($result);
        }else {
        	return false;
        }
    }

    /**
     +----------------------------------------------------------
     * 获取XML 数据
     * 
     +----------------------------------------------------------
     * @private
     * @access public 
     +----------------------------------------------------------
     * @param mixed $xml 数据
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    function getXml($xml='') 
    {
        if(!empty($xml)) {
             if(is_file($xml)) {
                $this->xml = file_get_contents($xml);
            }elseif(is_string($xml)) {
                $this->xml = $xml;
            }
        }
    }

    /**
     +----------------------------------------------------------
     * SimpleXMLElement 对象转数组
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return mixed
     +----------------------------------------------------------
     */
    function objToArray($object)
    {
       $return = NULL;
           
       if(is_array($object))
       {
           foreach($object as $key => $value)
               $return[$key] = $this->objToArray($value);
       } 
       else 
       {
           $var = get_object_vars($object);
               
           if($var) 
           {
               foreach($var as $key => $value)
                   $return[$key] = ($key && !$value) ? NULL : $this->objToArray($value);
           } 
           else return $object;
       }

       return $return;
    } 
}//类定义结束
?>