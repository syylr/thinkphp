<?php 
// +---------------------------------------------------------------------------+
// | Think -- Fast,Compatible & Simple OOP PHP Framework                         |
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
 * Think
 +------------------------------------------------------------------------------
 * @package    Core
 * @link       http://www.fcs.org.cn
 * @copyright  Copyright (c) 2005-2006 liu21st.com.  All rights reserved. 
 * @author     liu21st <liu21st@gmail.com>
 * @version    $Id$
 +------------------------------------------------------------------------------
 */
import('Think.Template.TagLib');
/**
 +------------------------------------------------------------------------------
 * TagLib解析类 : CS标签库
 +------------------------------------------------------------------------------
 * @package   Util
 * @author    liu21st <liu21st@gmail.com>
 * @version   0.8.0
 +------------------------------------------------------------------------------
 */
class TagLib_Cx extends TagLib
{//类定义开始

    /**
     +----------------------------------------------------------
     * 架构函数
     * 
     +----------------------------------------------------------
     * @static
     * @access public 
     +----------------------------------------------------------
     * @param object $template  当前模板对象
     +----------------------------------------------------------
     */
    function __construct( &$template)
    {
        $this->tpl = $template;
        parent::__construct('cx');
    }


    /**
     +----------------------------------------------------------
     * include标签解析
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $attr 标签属性
     * @param string $content  标签内容
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function _include($attr) 
    {
        $tag    = $this->parseXmlAttr($attr,'include');
        $file   =   $tag['file'];
        if(is_file($file)) {
            $parseStr = file_get_contents($file);
            return $this->tpl->parse($parseStr);
        }else {
            return $this->tpl->parseInclude($file);
        }
    }

    /**
     +----------------------------------------------------------
     * include标签解析
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $attr 标签属性
     * @param string $content  标签内容
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function _comment($attr) 
    {
        return ;
    }

    /**
     +----------------------------------------------------------
     * iterator标签解析 
     * 输出iterator变量的值，需要配合write标签
     * 格式： 
     * <iterator name="userList" id="user" empty="" >
     * <write name="user" value="username" format="" />
     * <write name="user" value="email" />
     * </iterator>
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $attr 标签属性
     * @param string $content  标签内容
     +----------------------------------------------------------
     * @return string|void
     +----------------------------------------------------------
     */
    function _iterate($attr,$content) 
    {
        static $_iterateParseCache = array();
        //如果已经解析过，则直接返回变量值
        $cacheIterateId = md5($attr.$content);
        if(isset($_iterateParseCache[$cacheIterateId])) 
            return $_iterateParseCache[$cacheIterateId];

        $tag        = $this->parseXmlAttr($attr,'iterate');
        $name       = $tag['name'];
        $id         = $tag['id'];
        $empty      = isset($tag['empty'])?$tag['empty']:'';
        $offset     = isset($tag['offset'])?$tag['offset']:0;
        $length     = isset($tag['length'])?$tag['length']:'';

       // if($this->tpl->get($name)){
            $parseStr  =  '<?php if(isset($'.$name.')): ?>';
            if(!empty($offset)) {
                $parseStr  .= '<?php $'.$name.'= array_slice($'.$name.','.$offset.','.$length.') ?>';
            }
            $parseStr .= '<?php if( count($'.$name.')==0 ) echo "'.$empty.'" ?>';
            $parseStr .= '<?php foreach($'.$name.' as $key=>$'.$id.'): ?>';
            $parseStr .= $this->tpl->parse($content);
            $parseStr .= '<?php endforeach; ?>';
            $parseStr .=  '<?php endif; ?>';
            $_iterateParseCache[$cacheIterateId] = $parseStr;
        //}
        
        if(!empty($parseStr)) {
            return $parseStr;
        }
        return ;
    }

    /**
     +----------------------------------------------------------
     * write标签解析 
     * 输出某个变量的值
     * 格式： <write name="" property="" format="" />
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $attr 标签属性
     +----------------------------------------------------------
     * @return string|void
     +----------------------------------------------------------
     */
    function _write($attr) 
    {
        static $_writeParseCache = array();
        //如果已经解析过，则直接返回变量值
        $cacheWriteId = md5($attr);
        if(isset($_writeParseCache[$cacheWriteId])) return $_writeParseCache[$cacheWriteId];

        $tag        = $this->parseXmlAttr($attr,'write');
        $name       = $tag['name'];
        $key        =  isset($tag['key'])?$tag['key']:'';
        $property   = isset($tag['property'])?$tag['property']:'';
        $format     = isset($tag['format'])?$tag['format']:'';
        $function   = isset($tag['function'])?$tag['function']:'';
        
        if(!empty($property)) {
            $name = '$'.$name.'->'.$property;
        }elseif(!empty($key)) {
            $name = '$'.$name.'[\''.$key.'\']';
        }else {
            $name = '$'.$name;
        }
        if(!empty($isKey)) {
            $name = '$key';
        }
        if(!empty($format)) {
            $name = $this->format($name,$format);
        }
        if(!empty($function)) {
            $function = explode('|',$function);
            $name = $this->tpl->parseVarFunction($name,$function);
        }
        if(!empty($name)) {
            $parseStr = '<?php echo '.$name.' ?>'; 
            $_writeParseCache[$cacheWriteId] = $parseStr;
            return $parseStr;
        }
        return ;
    }


    /**
     +----------------------------------------------------------
     * VoList标签解析 
     * 输出Volist的值 需要结合vo标签
     * 格式： 
     * <voList name="userList" id="user" >
     * <vo name="user" property="username" format="" />
     * <vo name="user" property="email" />
     * </voList>
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $attr 标签属性
     * @param string $content  标签内容
     +----------------------------------------------------------
     * @return string|void
     +----------------------------------------------------------
     */
    function _volist($attr,$content) 
    {
        static $_voListParseCache = array();
        //如果已经解析过，则直接返回变量值
        $cacheVoListId = md5($attr.$content);
        if(isset($_voListParseCache[$cacheVoListId])) return $_voListParseCache[$cacheVoListId];
        //解析标签属性
        $tag        = $this->parseXmlAttr($attr,'volist');
        $name       = $tag['name'];
        $empty      = $tag['empty'];
        $id         = isset($tag['id'])?$tag['id']:$name;
        $offset     = isset($tag['offset'])?$tag['offset']:0;
        $length     = isset($tag['length'])?$tag['length']:'';

        //if($this->tpl->get($name)){
            $parseStr  =  '<?php if(isset($'.$name.')): ?>';
            $parseStr  .= '<?php if($'.$name.'->size()==0 ) echo "'.$empty.'" ?>';
            if(!empty($length)) {
                $parseStr  .= '<?php $'.$name.'= $'.$name.'->getRange('.$offset.','.$length.') ?>';
            }
            $parseStr .= '<?php foreach($'.$name.'->toArray() as $'.$id.'): ?>';
            $parseStr .= $content;//$this->tpl->parse($content);
            $parseStr .= '<?php endforeach; ?>';
            $parseStr .=  '<?php endif; ?>';
            $_voListParseCache[$cacheVoListId] = $parseStr;
        //}
        
        if(!empty($parseStr)) {
            return $parseStr;
        }
        return ;
    }


    /**
     +----------------------------------------------------------
     * Vo标签解析 
     * 输出vo的属性
     * 格式： <vo name="" property="" format="" />
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $attr 标签属性
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    function _vo($attr) 
    {
        static $_voParseCache = array();
        //如果已经解析过该Vo，则直接返回变量值
        $cacheVoId = md5($attr);
        if(isset($_voParseCache[$cacheVoId])) return $_voParseCache[$cacheVoId];
        //解析标签属性
        $tag        = $this->parseXmlAttr($attr,'vo');
        $name       = $tag['name'];
        $property   = $tag['property'];
        $format     = isset($tag['format'])?$tag['format']:'';
        $function   = $tag['function'];
        
        $var = '$'.$name.'->'.$property;
        if(!empty($format)) {
            $var = $this->format($var,$format);
        }
        if(!empty($function)) {
            $function   = explode('|',$function);
            $var = $this->tpl->parseVarFunction($var,$function);
        }
        if(!empty($var)) {
            $parseStr = '<?php echo '.$var.' ?>';
            $_voParseCache[$cacheVoId] = $parseStr;
            return $parseStr;
        }
        return ;
    }


    /**
     +----------------------------------------------------------
     * var标签解析 
     * 输出变量的值
     * 格式： <var name="varname" format="yyyy-mm-dd" />
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $attr 标签属性
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    function _var($attr) 
    {
        $tag        = $this->parseXmlAttr($attr,'var');
        $var        = $tag['name'];
        $format     = $tag['format'];
        $function   = $tag['function'];
        
        $var = '$'.$var;
        if(!empty($format)) {
            $var = $this->format($var,$format);
        }
        if(!empty($function)) {
            $function = explode('|',$function);
            $var = $this->tpl->parseVarFunction($var,$function);
        }
        $parseStr = !empty($var)?'<?php echo '.$var.' ?>':'';
        return $parseStr;
    }

    function _isset($attr,$content) 
    {
        $tag        = $this->parseXmlAttr($attr,'isset');
        $name       = $tag['name'];
        $property   = $tag['property'];
        $key        = $tag['key'];
        if(!empty($property)) {
            $name = '$'.$name.'->'.$property;
        }
        elseif(!empty($key)) {
            $name = '$'.$name.'[\''.$key.'\']';
        }
        else {
            if(!defined($name)) {
                $name = '$'.$name;
            }
        }
        $parseStr = '<?php if(isset('.$name.')): ?>';
        $parseStr .= $content.'<?php endif; ?>';

        return $parseStr;  	
    }

    function _defined($attr,$content) 
    {
        $tag        = $this->parseXmlAttr($attr,'defined');
        $name       = $tag['name'];
        $parseStr = '<?php if(defined("'.$name.'")): ?>';
        $parseStr .= $content.'<?php endif; ?>';

        return $parseStr;  	
    }

    /**
     +----------------------------------------------------------
     * equal标签解析 
     * 如果变量的值等于value，则输出内容
     * 格式： <equal name="" property="" value="" >content</equal>
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $attr 标签属性
     * @param string $content  标签内容
     +----------------------------------------------------------
     * @return string|void
     +----------------------------------------------------------
     */
    function _equal($attr,$content) 
    {
        $tag        = $this->parseXmlAttr($attr,'equal');
        $name       = $tag['name'];
        $property   = $tag['property'];
        $key        = $tag['key'];
        $value      = $tag['value'];
        $function   = $tag['function'];

        if(!empty($property)) {
            $name = '$'.$name.'->'.$property;
        }
        elseif(!empty($key)) {
            $name = '$'.$name.'[\''.$key.'\']';
        }
        else {
            if(!defined("$name")) {
                $name = '$'.$name;
            }
        }
        if(!empty($function)) {
            $function = explode('|',$function);
            $name = $this->tpl->parseVarFunction($name,$function);
        }
        $parseStr = '<?php if('.$name.'=="'.$value.'"): ?>';
        $parseStr .= $content.'<?php endif; ?>';

        return $parseStr;
    }


    /**
     +----------------------------------------------------------
     * notequal标签解析 
     * 如果某个变量的值不等于value 则输出内容
     * 格式： <notequal name="" property="" value="" >content</notequal>
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $attr 标签属性
     * @param string $content  标签内容
     +----------------------------------------------------------
     * @return string|void
     +----------------------------------------------------------
     */
    function _notequal($attr,$content) 
    {
        $tag        = $this->parseXmlAttr($attr,'notequal');
        $name       = $tag['name'];
        $property   = $tag['property'];
        $key        = $tag['key'];
        $value      = $tag['value'];
        $function   = $tag['function'];

        if(!empty($property)) {
            $name = '$'.$name.'->'.$property;
        }
        elseif(!empty($key)) {
            $name = '$'.$name.'[\''.$key.'\']';
        }
        else {
            if(!defined($name)) {
                $name = '$'.$name;
            }
        }
        if(!empty($function)) {
            $function = explode('|',$function);
            $name = $this->tpl->parseVarFunction($name,$function);
        }
        $parseStr  = '<?php if('.$name.'!="'.$value.'"): ?>';
        $parseStr .= $content.'<?php endif; ?>';
        return $parseStr;
    }

    /**
     +----------------------------------------------------------
     * present标签解析 
     * 如果某个变量已经设置 则输出内容
     * 格式： <present name="" >content</present>
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $attr 标签属性
     * @param string $content  标签内容
     +----------------------------------------------------------
     * @return string|void
     +----------------------------------------------------------
     */
    function _present($attr,$content) 
    {
        $tag  = $this->parseXmlAttr($attr,'present');
        $name  = $tag['name'];
        $property   = $tag['property'];
        $key        = $tag['key'];
        if(!empty($property)) {
            $name = '$'.$name.'->'.$property;
        }
        elseif(!empty($key)) {
            $name = '$'.$name.'[\''.$key.'\']';
        }
        else {
            $name = '$'.$name;
        }
        $parseStr  = '<?php if(isset('.$name.')): ?>'.$content.'<?php endif; ?>';
        return $parseStr;
    }


    /**
     +----------------------------------------------------------
     * notpresent标签解析 
     * 如果某个变量没有设置，则输出内容
     * 格式： <notpresent name="" >content</notpresent>
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $attr 标签属性
     * @param string $content  标签内容
     +----------------------------------------------------------
     * @return string|void
     +----------------------------------------------------------
     */
    function _notpresent($attr,$content) 
    {
        $tag  = $this->parseXmlAttr($attr,'notpresent');
        $name  = $tag['name'];
        $property   = $tag['property'];
        $key        = $tag['key'];
        if(!empty($property)) {
            $name = '$'.$name.'->'.$property;
        }
        elseif(!empty($key)) {
            $name = '$'.$name.'[\''.$key.'\']';
        }
        else {
            $name = '$'.$name;
        }
        $parseStr  = '<?php if(!isset('.$name.')): ?>'.$content.'<?php endif; ?>';
        return $parseStr;
    }

    /**
     +----------------------------------------------------------
     * session标签解析 
     * 如果某个session变量已经设置 则输出内容
     * 格式： <session name="" >content</session>
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $attr 标签属性
     * @param string $content  标签内容
     +----------------------------------------------------------
     * @return string|void
     +----------------------------------------------------------
     */
    function _session($attr,$content) 
    {
        $tag  = $this->parseXmlAttr($attr,'session');
        $name  = $tag['name'];
		if(strpos($name,'|')) {
			$array	=	explode('|',$name);
			$parseStr  = '<?php if( ';
			for($i=0; $i<count($array); $i++) {
				$parseStr  .= 'Session::is_set("'.$array[$i].'") || ';
			}
			$parseStr	=	substr($parseStr,0,-3);
			$parseStr  .='): ?>'.$content.'<?php endif; ?>';		
		}elseif(strpos($name,',')) {
			$array	=	explode(',',$name);
			$parseStr  = '<?php if( ';
			for($i=0; $i<count($array); $i++) {
				$parseStr  .= 'Session::is_set("'.$array[$i].'") && ';
			}
			$parseStr	=	substr($parseStr,0,-3);
			$parseStr  .='): ?>'.$content.'<?php endif; ?>';				
		}else {
			$parseStr  = '<?php if(Session::is_set("'.$name.'")): ?>'.$content.'<?php endif; ?>';			
		}
		
		
        return $parseStr;
    }

    /**
     +----------------------------------------------------------
     * notsession标签解析 
     * 如果某个session变量没有设置 则输出内容
     * 格式： <notsession name="" >content</notsession>
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $attr 标签属性
     * @param string $content  标签内容
     +----------------------------------------------------------
     * @return string|void
     +----------------------------------------------------------
     */
    function _nosession($attr,$content) 
    {
        $tag  = $this->parseXmlAttr($attr,'session');
        $name  = $tag['name'];
		if(strpos($name,'|')) {
			$array	=	explode('|',$name);
			$parseStr  = '<?php if( ';
			for($i=0; $i<count($array); $i++) {
				$parseStr  .= '!Session::is_set("'.$array[$i].'") || ';
			}
			$parseStr	=	substr($parseStr,0,-3);
			$parseStr  .='): ?>'.$content.'<?php endif; ?>';		
		}elseif(strpos($name,',')) {
			$array	=	explode(',',$name);
			$parseStr  = '<?php if( ';
			for($i=0; $i<count($array); $i++) {
				$parseStr  .= '!Session::is_set("'.$array[$i].'") && ';
			}
			$parseStr	=	substr($parseStr,0,-3);
			$parseStr  .='): ?>'.$content.'<?php endif; ?>';				
		}else {
			$parseStr  = '<?php if( !Session::is_set("'.$name.'")): ?>'.$content.'<?php endif; ?>';			
		}
		
		
        return $parseStr;
    }

    /**
     +----------------------------------------------------------
     * display标签解析 
     * 如果有模块和操作权限则输出，默认为当前模块和操作
     * 格式： <display module="" action="" >content</display>
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $attr 标签属性
     * @param string $content  标签内容
     +----------------------------------------------------------
     * @return string|void
     +----------------------------------------------------------
     */
    function _display($attr,$content) 
    {
        $tag		= $this->parseXmlAttr($attr,'session');
        $module		= $tag['module']?$tag['module']:MODULE_NAME;
		$action		= $tag['action']?$tag['action']:ACTION_NAME;
        $parseStr  = '<?php if(in_array("'.$module.'",Session::get("_moduleList")) && in_array("'.$action.'",Session::get("_actionList"))): ?>'.$content.'<?php endif; ?>';
        return $parseStr;
    }


    /**
     +----------------------------------------------------------
     * 数据格式化
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $var 数据
     * @param string $format  格式化参数
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function format($var,$format) 
    {

        //$value = $this->tpl->get(ltrim($var,'$'));
        $value = $var;
        return $this->dateFormat($var,$format,true);
        if(strtotime($value)!== false && strtotime($value)!= -1) {
            return $this->dateFormat($var,$format);
        }elseif(is_numeric($value) && intval($value) >57600 ) {
            return $this->dateFormat($var,$format,true);
        }elseif(is_string($value)) {
            return $this->stringFormat($var,$format);
        }
    }

}//类定义结束
?>