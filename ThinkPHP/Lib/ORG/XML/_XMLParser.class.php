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
// $Id: _XMLParser.class.php 33 2007-02-25 07:06:02Z liu21st $

/**
 +------------------------------------------------------------------------------
 * XML 解析类 for PHP5
 +------------------------------------------------------------------------------
 * @author    liu21st <liu21st@gmail.com>
 * @version   $Id: _XMLParser.class.php 33 2007-02-25 07:06:02Z liu21st $
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