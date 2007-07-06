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

import('FCS.Util.Config.Config_Xml');

/**
 +------------------------------------------------------------------------------
 * TagLib解析类 调用ConfigXml类
 *
 * 要在模板页面中引入标签库，使用taglib标签，例如:
 * <taglib name='cs' />
 * 如果要引入多个标签库，可以使用
 * <taglib name='cs,mx,html' />
 *
 * 系统内置引入了cs标签库，所以，如果需要使用cs标签库，无需使用taglib标签引入
 * 但是无需写cs前缀
 * 例如 <cs:vo name='user' value='id' />
 * 应该写成 <vo name='user' value='id' />
 +------------------------------------------------------------------------------
 * @package   Util
 * @author    liu21st <liu21st@gmail.com>
 * @version   0.8.0
 +------------------------------------------------------------------------------
 */
class TagLib extends Config_Xml
{//类定义开始

    /**
     +----------------------------------------------------------
     * 标签库定义XML文件
     +----------------------------------------------------------
     * @var string
     * @access protected
     +----------------------------------------------------------
     */
    var $xml;

    /**
     +----------------------------------------------------------
     * 标签库名称
     +----------------------------------------------------------
     * @var string
     * @access protected
     +----------------------------------------------------------
     */
    var $tagLib;

    /**
     +----------------------------------------------------------
     * 标签库标签列表
     +----------------------------------------------------------
     * @var string
     * @access protected
     +----------------------------------------------------------
     */
    var $tagList;

    /**
     +----------------------------------------------------------
     * 标签库分析数组
     +----------------------------------------------------------
     * @var string
     * @access protected
     +----------------------------------------------------------
     */
    var $parse;

    /**
     +----------------------------------------------------------
     * 标签库是否有效
     +----------------------------------------------------------
     * @var string
     * @access protected
     +----------------------------------------------------------
     */
    var $valid;

    /**
     +----------------------------------------------------------
     * 当前模板对象
     +----------------------------------------------------------
     * @var object
     * @access protected
     +----------------------------------------------------------
     */
    var $tpl;



    /**
     +----------------------------------------------------------
     * 取得标签库实例对象
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return App
     +----------------------------------------------------------
     */
    function getInstance() 
    {
        return get_instance_of(__CLASS__);
    }


    /**
     +----------------------------------------------------------
     * 架构函数
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     */
    function __construct($tagLib,$filename='')
    {
        $this->tagLib = $tagLib;
        if(!empty($filename)) {
            $this->xml = $filename;
        }else {
            $this->xml = TAG_PATH.$tagLib.'.xml';
        }
    }


    /**
     +----------------------------------------------------------
     * 分析TagLib文件的信息是否有效
     * 有效则转换成数组
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param mixed $name 数据
     * @param string $value  数据表名
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function valid() 
    {
        if(is_file($this->xml)) {
            $xml = file_get_contents($this->xml);
            $array = $this->xmlToArray($xml);
            if($array !== false) {
                $this->parse = $array;
                return true;
            }else {
            	return false;
            }
        }
        return false;
    }


    /**
     +----------------------------------------------------------
     * 获取TagLib名称
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function getTagLib() 
    {
        return $this->tagLib;
    }


    /**
     +----------------------------------------------------------
     * 获取Tag列表
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function getTagList() 
    {
        if(!isset($this->tagList)) {
            $tags = $this->parse['tag'];
            $list = array();
            foreach($tags as $tag) {
                $list[] =  array(
                                'name'=>$tag['name'],
                                'content'=>$tag['bodycontent'],
                                'attribute'=>$tag['attribute'],
                                );
            }
            $this->tagList = $list;
        }

        return $this->tagList;
    }


    /**
     +----------------------------------------------------------
     * 获取某个Tag属性的信息
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function getTagAttrList($tagName) 
    {
        static $_tagCache   = array();
        $_tagCacheId        =   md5($this->tagLib.$tagName);
        if(isset($_tagCache[$_tagCacheId])) {
            return $_tagCache[$_tagCacheId];
        }
        $list = array();
        $tags = $this->getTagList();
        foreach($tags as $tag) {
            if( strtolower($tag['name']) == strtolower($tagName)) {
                foreach($tag['attribute'] as $attr) {
                    $list[] = array(
                                    'name'=>$attr['name'],
                                    'required'=>$attr['required']
                                    );
                }	
            }
        }
        $_tagCache[$_tagCacheId]    =   $list;
        return $list;
    }

    /**
     +----------------------------------------------------------
     * TagLib标签属性分析 返回标签属性数组
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $tagStr 标签内容
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
     */
    function parseXmlAttr($attr,$tag) 
    {
        //XML解析安全过滤
        $attr = str_replace("<","&lt;", $attr);
        $attr = str_replace(">","&gt;", $attr);
        $xml =  '<tpl><tag '.$attr.' /></tpl>';
        $result = & new Config_Xml($xml);
        $array  = $result->toArray();
        $array  = array_change_key_case($array['tag']);
        $attrs	= $this->getTagAttrList($tag);
        foreach($attrs as $val) {
            if($val['required']!='true'  && !isset($array[$val['name']])) {
                $array[$val['name']] = '';
            }
        }
        return $array;
    }

    /**
     +----------------------------------------------------------
     * 日期格式化 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $var 变量
     * @param string $format 格式
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    function dateFormat($var,$format,$true=false) 
    {
        if($true) {
            $tmplContent = 'date( "'.$format.'", intval('.$var.') )';
        }else {
        	$tmplContent = 'date( "'.$format.'", strtotime('.$var.') )';
        }
        return $tmplContent;
    }


    /**
     +----------------------------------------------------------
     * 字符串格式化 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $var 变量
     * @param string $format 格式
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    function stringFormat($var,$format) 
    {
        $tmplContent = 'sprintf("'.$format.'", '.$var.')';
        return $tmplContent;
    }


    /**
     +----------------------------------------------------------
     * 字符串格式化 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $var 变量
     * @param string $format 格式
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    function numericFormat($var,$format) 
    {
        $tmplContent = 'number_format("'.$var.'")';
        return $tmplContent;
    }


}//类定义结束
?>