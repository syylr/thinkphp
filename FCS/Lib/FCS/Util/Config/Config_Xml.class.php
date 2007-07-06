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
 * @version    $Id: Config_Xml.class.php 73 2006-11-08 10:08:01Z fcs $
 +------------------------------------------------------------------------------
 */

import('FCS.Util.Config');
/**
 +------------------------------------------------------------------------------
 * XML配置文件类
 +------------------------------------------------------------------------------
 * @package   Util
 * @author    liu21st <liu21st@gmail.com>
 * @version   0.8.0
 +------------------------------------------------------------------------------
 */
class Config_Xml extends Config
{//类定义开始

    /**
     +----------------------------------------------------------
     * 架构函数
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     */
    function __construct($config)
    {
        if(is_file($config)) {
            $xml = file_get_contents($config);
        }elseif(is_string($config)) {
            $xml = $config;
        }
        $result = $this->xmlToArray($xml);
        if($result == false ) {
            $this->_connect = false;
        }else {
            $this->_config = $result;
            $this->_connect = true;
        }
    }

    /**
     +----------------------------------------------------------
     * 是否正常加载配置文件
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function connect() 
    {
        return $this->_connect;
    }


    /**
     +----------------------------------------------------------
     * 把XML数据转换成数组
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $xml  XML数据
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function xmlToArray($xml)
    { 
        $values = array(); 
        $index  = array(); 
        $array  = array(); 
        $parser = xml_parser_create(); 
        xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
        if(0===xml_parse_into_struct($parser, $xml, $values, $index)) {
            return false;
        }
        xml_parser_free($parser);
        $i = 0; 
        $name = $values[$i]['tag']; 
        $array[$name] = isset($values[$i]['attributes']) ? $values[$i]['attributes'] : ''; 
        $array[$name] = $this->_struct_to_array($values, $i); 

        return $array[$name]; 
    }

    /**
     +----------------------------------------------------------
     * 把XML结构转换成数组
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $values  XML结构
     * @param integer $i  节点索引
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function _struct_to_array($values, &$i)
    {
        $child = array(); 
        if (isset($values[$i]['value'])) array_push($child, $values[$i]['value']); 
        
        while ($i++ < count($values)) { 
            switch ($values[$i]['type']) { 
                case 'cdata': 
                    array_push($child, $values[$i]['value']); 
                    break; 
                
            	case 'complete': 
                    $name = $values[$i]['tag']; 
                	if( !empty($name)){
                        $child[$name]= isset($values[$i]['value'])?($values[$i]['value']):''; 
                        if(isset($values[$i]['attributes'])) {                    
                            $child[$name] = $values[$i]['attributes']; 
                        } 
                    }    
                    break; 
                
                case 'open': 
                    $name = $values[$i]['tag']; 
                    $size = isset($child[$name]) ? sizeof($child[$name]) : 0;
                    $child[$name][$size] = $this->_struct_to_array($values, $i); 
                    break;
                
                case 'close': 
                    return $child; 
                    break; 
            }
        }
        return $child; 
    }

}//类定义结束
?>