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

 if(IS_PHP4){
    /**
     +------------------------------------------------------------------------------
     * XML 解析类 修改自 XML Library, by Keith Devens, version 1.2b 
     * 参考 http://keithdevens.com/software/phpxml
     * PHP5 使用 SimpleXML扩展完成XML解析
     +------------------------------------------------------------------------------
     * @author    liu21st <liu21st@gmail.com>
     * @version   $Id$
     +------------------------------------------------------------------------------
     */
    class XMLParser extends Base
    { 
        /**
         +----------------------------------------------------------
         * XML parser 
         +----------------------------------------------------------
         * @var resource
         * @access protected
         +----------------------------------------------------------
         */
        var $parser;   

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
         * XML structure 
         +----------------------------------------------------------
         * @var array
         * @access protected
         +----------------------------------------------------------
         */
        var $document; 

        /**
         +----------------------------------------------------------
         * XML parent 
         +----------------------------------------------------------
         * @var array
         * @access protected
         +----------------------------------------------------------
         */
        var $parent;  

        /**
         +----------------------------------------------------------
         * XML 同层次的stack 
         +----------------------------------------------------------
         * @var array
         * @access protected
         +----------------------------------------------------------
         */
        var $stack;   

        /**
         +----------------------------------------------------------
         * 最近打开的XML tag 
         +----------------------------------------------------------
         * @var string
         * @access protected
         +----------------------------------------------------------
         */
        var $last_opened_tag; 

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
            $this->parser = &xml_parser_create(); 
            xml_parser_set_option(&$this->parser, XML_OPTION_SKIP_WHITE, 1);
            xml_parser_set_option(&$this->parser, XML_OPTION_CASE_FOLDING, 0); 
            xml_set_object(&$this->parser, &$this); 
            xml_set_element_handler(&$this->parser, 'open','close'); 
            xml_set_character_data_handler(&$this->parser, 'data'); 
            $this->getXml($xml);
        }

        /**
         +----------------------------------------------------------
         * 解析XML数据 支持文件
         * 返回结构数组
         +----------------------------------------------------------
         * @static
         * @access public 
         +----------------------------------------------------------
         * @param mixed $xml XML数据
         +----------------------------------------------------------
         * @return string | null
         +----------------------------------------------------------
         */
        function & parse(&$xml=''){ 
            $this->getXml($xml);
            if(!empty($this->xml)) {
                $this->document = array(); 
                $this->stack    = array(); 
                $this->parent   = &$this->document; 
                return xml_parse(&$this->parser, &$this->xml, true) ? $this->document : NULL;              	
            }else {
            	return null;
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
         * XML 起始元素处理器
         * 
         +----------------------------------------------------------
         * @private
         * @access public 
         +----------------------------------------------------------
         * @param mixed $name 数据
         * @param string $value  数据表名
         +----------------------------------------------------------
         * @return string
         +----------------------------------------------------------
         * @throws ThinkExecption
         +----------------------------------------------------------
         */
        function open(&$parser, $tag, $attributes)
        { 
            $this->data = ''; #stores temporary cdata 
            $this->last_opened_tag = $tag; 
            if(is_array($this->parent) and array_key_exists($tag,$this->parent)){ #if you've seen this tag before 
                if(is_array($this->parent[$tag]) and array_key_exists(0,$this->parent[$tag])){ #if the keys are numeric 
                    #this is the third or later instance of $tag we've come across 
                    $key = $this->count_numeric_items($this->parent[$tag]); 
                }else{ 
                    #this is the second instance of $tag that we've seen. shift around 
                    if(array_key_exists("$tag attr",$this->parent)){ 
                        $arr = array('0 attr'=>&$this->parent["$tag attr"], &$this->parent[$tag]); 
                        unset($this->parent["$tag attr"]); 
                    }else{ 
                        $arr = array(&$this->parent[$tag]); 
                    } 
                    $this->parent[$tag] = &$arr; 
                    $key = 1; 
                } 
                $this->parent = &$this->parent[$tag]; 
            }else{ 
                $key = $tag; 
            } 
            if($attributes) $this->parent["$key attr"] = $attributes; 
            $this->parent  = &$this->parent[$key]; 
            $this->stack[] = &$this->parent; 
        } 

        /**
         +----------------------------------------------------------
         * XML字符数据处理器
         * 
         +----------------------------------------------------------
         * @private
         * @access public 
         +----------------------------------------------------------
         * @param mixed $parser 解析器对象引用
         * @param string $data  数据
         +----------------------------------------------------------
         * @return void
         +----------------------------------------------------------
         */
        function data(&$parser, $data)
        { 
            if($this->last_opened_tag != NULL) #you don't need to store whitespace in between tags 
                $this->data .= $data; 
        } 

        /**
         +----------------------------------------------------------
         * XML结束元素处理器
         * 
         +----------------------------------------------------------
         * @private
         * @access public 
         +----------------------------------------------------------
         * @param mixed $parser 解析器引用
         * @param string $tag  Tag
         +----------------------------------------------------------
         * @return void
         +----------------------------------------------------------
         */
        function close(&$parser, $tag)
        { 
            if($this->last_opened_tag == $tag){ 
                $this->parent = $this->data; 
                $this->last_opened_tag = NULL; 
            } 
            array_pop($this->stack); 
            if($this->stack) $this->parent = &$this->stack[count($this->stack)-1]; 
        } 

        /**
         +----------------------------------------------------------
         * 计算数字项
         * 
         +----------------------------------------------------------
         * @private
         * @access public 
         +----------------------------------------------------------
         * @param mixed $array 数据
         +----------------------------------------------------------
         * @return integer
         +----------------------------------------------------------
         */
        function count_numeric_items(&$array)
        { 
            return is_array($array) ? count(array_filter(array_keys($array), 'is_numeric')) : 0; 
        } 

        /**
         +----------------------------------------------------------
         * 析构函数
         * 
         +----------------------------------------------------------
         * @static
         * @access public 
         +----------------------------------------------------------
         */
        function __destruct()
        { 
            xml_parser_free(&$this->parser); 
        } 

    } 
}else {
    //引入PHP5支持的XMLParser类
	import("Think.Io._XMLParser");	    	
}
?>