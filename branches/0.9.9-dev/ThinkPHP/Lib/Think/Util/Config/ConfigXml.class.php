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

import('Think.Util.Config');
/**
 +------------------------------------------------------------------------------
 * XML配置文件类
 +------------------------------------------------------------------------------
 * @author    liu21st <liu21st@gmail.com>
 * @version   $Id$
 +------------------------------------------------------------------------------
 */
class ConfigXml extends Config
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
        if(file_exists($config)) {
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
     * @throws ThinkExecption
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
     * @throws ThinkExecption
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
     * @throws ThinkExecption
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