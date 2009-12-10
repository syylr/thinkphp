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
import('FCS.Util.TagLib');
/**
 +------------------------------------------------------------------------------
 * TagLib解析类 : HTML标签库
 +------------------------------------------------------------------------------
 * @package   Util
 * @author    liu21st <liu21st@gmail.com>
 * @version   0.8.0
 +------------------------------------------------------------------------------
 */
class TagLib_Html extends TagLib
{//类定义开始

    /**
     +----------------------------------------------------------
     * 架构函数
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param object $template  当前模板对象
     +----------------------------------------------------------
     */
    function __construct( &$template)
    {
        $this->tpl = $template;
		parent::__construct('html');
    }

    /**
     +----------------------------------------------------------
     * editor标签解析 插入可视化编辑器
     * 格式： <html:editor id="editor" name="remark" type="FCKeditor" content="{$vo.remark}" />
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $attr 标签属性
     +----------------------------------------------------------
     * @return string|void
     +----------------------------------------------------------
     */
    function _editor($attr) 
    {
        $tag        =	$this->parseXmlAttr($attr,'editor');
        $id			=	$tag['id']  | '_editor';       
		$name   	=	$tag['name'];   
        $width		=	$tag['width'] | '100%';    
        $height     =	$tag['height'] | '320px';    
        $content    =   $tag['content'];
        $type       =   $tag['type'] ;
 
		switch($type) {
            case 'FCKeditor':
            	$parseStr   =	'<!-- 编辑器调用开始 --><script type="text/javascript" src="__ROOT__/Public/Js/FCKeditor/fckeditor.js"></script><textarea id="'.$id.'" name="'.$name.'">'.$content.'</textarea><script type="text/javascript"> var oFCKeditor = new FCKeditor( "'.$id.'","'.$width.'","'.$height.'" ) ; oFCKeditor.BasePath = "__ROOT__/Public/Js/FCKeditor/" ; oFCKeditor.ReplaceTextarea() ; </script> <!-- 编辑器调用结束 -->';
            	break;
			case 'netEase':
            default :
                $parseStr   =	'<!-- 编辑器调用开始 --><textarea id="'.$id.'" name="'.$name.'" style="display:none">'.$content.'</textarea><iframe ID="Editor" name="Editor" src="__ROOT__/Public/Js/HtmlEditor/index.html?ID='.$name.'" frameBorder="0" marginHeight="0" marginWidth="0" scrolling="No" style="height:'.$height.';width:'.$width.'"></iframe><!-- 编辑器调用结束 -->';
		
		}

        return $parseStr;
    }

    /**
     +----------------------------------------------------------
     * form标签解析 是否需要引入表单验证脚本
     * 格式： <html:form validation="true" />
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $attr 标签属性
     +----------------------------------------------------------
     * @return string|void
     +----------------------------------------------------------
     */
    function _form($attr,$content) 
    {
        $tag	= $this->parseXmlAttr($attr,'form');
        $text	= $tag['text'];
        $validation  = $tag['validation'];
        $js		= '<script language="JavaScript" src="'.WEB_URL.'/'.TMPL_DIR.'/'.TEMPLATE_NAME.'/public/'.'js/CheckForm.js"></script>';
        $parseStr = '';
        if($validation == 'true' || $validation == 1 ) {
            $attr       = preg_replace('/validation=(.+?)\s/is',' onsubmit="return Check(this);" ',$attr);
            $parseStr  .= $js.'<br/><form '.$attr.' >';

        }else {
            $attr       = preg_replace('/validation=(.+?)\s/is',' ',$attr);
        	$parseStr   = '<form '.$attr.' >';
        }
        $parseStr .= $this->tpl->parse($content);
        $parseStr .= '</form>';
        return $parseStr;
    }


    /**
     +----------------------------------------------------------
     * select标签解析
     * 格式： <html:select options="name" selected="value" />
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $attr 标签属性
     +----------------------------------------------------------
     * @return string|void
     +----------------------------------------------------------
     */
    function _select($attr) 
    {
        $tag        = $this->parseXmlAttr($attr,'select');
        $name       = $tag['name'];
        $options    = $tag['options'];
        $values     = $tag['values'];
        $output     = $tag['output'];
        $multiple   = $tag['multiple'];
        $id         = $tag['id'];
        $size       = $tag['size'];
        $first      = $tag['first'];
        $selected   = $tag['selected'];
        $style      = $tag['style'];
        $ondblclick = $tag['dblclick'];
		$onchange	= $tag['change'];
        
        if(!empty($multiple)) {
            $parseStr = '<select id="'.$id.'" name="'.$name.'" ondblclick="'.$ondblclick.'" onchange="'.$onchange.'" multiple="multiple" class="'.$style.'" size="'.$size.'" >';
        }else {
        	$parseStr = '<select id="'.$id.'" name="'.$name.'" onchange="'.$onchange.'" ondblclick="'.$ondblclick.'" class="'.$style.'" >';
        }
        if(!empty($first)) {
            $parseStr .= '<option value="" >'.$first.'</option>';
        }
        if(!empty($options)) {
            $parseStr   .= '<?php  foreach($'.$options.' as $key=>$val) { ?>';
            if(!empty($selected)) {
                $parseStr   .= '<?php if(!empty($'.$selected.') && ($'.$selected.' == $key || in_array($key,$'.$selected.'))) { ?>';
                $parseStr   .= '<option selected="selected" value="<?php echo $key ?>"><?php echo $val ?></option>';
                $parseStr   .= '<?php }else { ?><option value="<?php echo $key ?>"><?php echo $val ?></option>';
                $parseStr   .= '<?php } ?>';
            }else {
                $parseStr   .= '<option value="<?php echo $key ?>"><?php echo $val ?></option>';
            }
            $parseStr   .= '<?php } ?>';
        }else if(!empty($values)) {
            $parseStr   .= '<?php  for($i=0;$i<count($'.$values.');$i++) { ?>';
            if(!empty($selected)) {
                $parseStr   .= '<?php if(!empty($'.$selected.') && ($'.$selected.' == $'.$values.'[$i] || in_array($'.$values.'[$i],$'.$selected.'))) { ?>';
                $parseStr   .= '<option selected="selected" value="<?php echo $'.$values.'[$i] ?>"><?php echo $'.$output.'[$i] ?></option>';
                $parseStr   .= '<?php }else { ?><option value="<?php echo $'.$values.'[$i] ?>"><?php echo $'.$output.'[$i] ?></option>';
                $parseStr   .= '<?php } ?>';
            }else {
                $parseStr   .= '<option value="<?php echo $'.$values.'[$i] ?>"><?php echo $'.$output.'[$i] ?></option>';
            }
            $parseStr   .= '<?php } ?>';
        }
        $parseStr   .= '</select>';
        return $parseStr;
    }


    /**
     +----------------------------------------------------------
     * checkbox标签解析
     * 格式： <html:checkbox checkboxs="" checked="" />
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $attr 标签属性
     +----------------------------------------------------------
     * @return string|void
     +----------------------------------------------------------
     */
    function _checkbox($attr) 
    {
        $tag        = $this->parseXmlAttr($attr,'checkbox');
        $name       = $tag['name'];
        $checkboxes = $tag['checkboxes'];
        $checked    = $tag['checked'];
        $separator  = $tag['separator'];
        $checkboxes = $this->tpl->get($checkboxes);
        $checked    = $this->tpl->get($checked);
        $parseStr   = '';
        foreach($checkboxes as $key=>$val) {
            if($checked == $key  || in_array($key,$checked) ) {
                $parseStr .= '<input type="checkbox" checked="checked" name="'.$name.'[]" value="'.$key.'">'.$val.$separator;
            }else {
                $parseStr .= '<input type="checkbox" name="'.$name.'[]" value="'.$key.'">'.$val.$separator;
            }
            
        }
        return $parseStr;
    }

    /**
     +----------------------------------------------------------
     * mulitSelect标签解析
     * 格式： <html:list datasource="" show="" />
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $attr 标签属性
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    function _mulitCheckBox($attr) 
    {
        $tag        = $this->parseXmlAttr($attr,'mulitCheckBox');
        $id         = $tag['id'];                   //表格ID
        $name       = $tag['name'];                 //返回表单值
        $source     = $tag['source'];               //原始数据源
        $size       = $tag['size'];                 //下拉列表size
        $style      = $tag['style'];                //表格样式

        $parseStr	= "<!-- FCS 系统多选组件开始 -->\n<div align=\"center\"><TABLE class=\"".$style."\">";
        $parseStr	.= '<tr><td height="5" colspan="3" class="topTd" ></td></tr>';
        $parseStr	.= '<TR><Th width="44%" >'.$sourceTitle.'</Th><Th ></Th><Th width="44%">'.$targetTitle.'</Th></TR>';
        $parseStr	.= '<TR><TD ><div class="solid"><html:select id="sourceSelect" options="'.$source.'" dblclick="addItem()" multiple="true" style="multiSelect" size="'.$size.'" /></div></TD><TD valign="middle"><div style="margin-top:35px"><html:imageBtn value="添加" click="addItem()" style="impBtn vMargin fLeft " /><html:imageBtn type="button" value="全选" click="addAllItem()" style="impBtn vMargin fLeft " /><html:imageBtn value="移除" click="delItem()" style="impBtn vMargin fLeft " /><html:imageBtn  value="全删" click="delAllItem()" style="impBtn vMargin fLeft " /></div></TD>	<TD ><div class="solid"><html:select name="'.$name.'[]" id="targetSelect" options="'.$target.'" dblclick="delItem()" multiple="true" style="multiSelect" size="'.$size.'" /></div></TD></TR><tr><td height="5" colspan="3" class="bottomTd" ></td></tr></TABLE></div>';
        $parseStr	.= "\n<!-- FCS 系统多选组件结束 -->\n";
        return $parseStr;
	}

    /**
     +----------------------------------------------------------
     * radio标签解析
     * 格式： <html:radio options="name" selected="value" />
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $attr 标签属性
     +----------------------------------------------------------
     * @return string|void
     +----------------------------------------------------------
     */
    function _radio($attr) 
    {
        $tag        = $this->parseXmlAttr($attr,'radio');
        $name       = $tag['name'];
        $radios     = $tag['radios'];
        $checked    = $tag['checked'];
        $separator  = $tag['separator'];
        $radios     = $this->tpl->get($radios);
        $checked    = $this->tpl->get($checked);
        $parseStr   = '';
        foreach($radios as $key=>$val) {
            if($checked == $key ) {
                $parseStr .= '<input type="radio" checked="checked" name="'.$name.'[]" value="'.$key.'">'.$val.$separator;
            }else {
                $parseStr .= '<input type="radio" name="'.$name.'[]" value="'.$key.'">'.$val.$separator;
            }
            
        }
        return $parseStr;
    }

    /**
     +----------------------------------------------------------
     * link标签解析
     * 格式： <html:link file="" type="" />
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $attr 标签属性
     +----------------------------------------------------------
     * @return string|void
     +----------------------------------------------------------
     */
    function _link($attr) 
    {
        $tag        = $this->parseXmlAttr($attr,'link');
        $file       = $tag['href'];
        $type       = isset($tag['type'])?
                    strtolower($tag['type']):
                    strtolower(substr(strrchr($file, '.'),1));
        if($type=='js') {
            $parseStr = "<script language='JavaScript' src='".$file."'></script> ";
        }elseif($type=='css') {
            $parseStr = "<link rel='stylesheet' type='text/css' href='".$file."'>";
        }

        return $parseStr;
    }

    /**
     +----------------------------------------------------------
     * link标签解析
     * 格式： <html:link file="" type="" />
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $attr 标签属性
     +----------------------------------------------------------
     * @return string|void
     +----------------------------------------------------------
     */
    function _import($attr) 
    {
        $tag        = $this->parseXmlAttr($attr,'import');
        $file       = $tag['file'];
        $basepath   = !empty($tag['basepath'])?$tag['basepath']:WEB_PUBLIC_URL;
        $type       = !empty($tag['type'])?  strtolower($tag['type']):'js';
        if($type=='js') {
            $parseStr = "<script language='JavaScript' src='".$basepath.'/'.str_replace('.','/',$file).'.js'."'></script> ";
        }elseif($type=='css') {
            $parseStr = "<link rel='stylesheet' type='text/css' href='".$basepath.'/'.str_replace('.','/',$file).'.css'."'>";
        }

        return $parseStr;
    }

    /**
     +----------------------------------------------------------
     * imageLink标签解析
     * 格式： <html:imageLink type="" value="" />
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $attr 标签属性
     +----------------------------------------------------------
     * @return string|void
     +----------------------------------------------------------
     */
    function _imgLink($attr) 
    {
        $tag        = $this->parseXmlAttr($attr,'imgLink');
        $name       = $tag['name'];                //名称
        $alt        = $tag['alt'];                //文字
        $id         = $tag['id'];                //ID
        $style      = $tag['style'];                //样式名
        $click      = $tag['click'];                //点击
        $type       = $tag['type'];                //点击
        if(empty($type)) {
            $type = 'button';
        }
       	$parseStr   = '<span class="'.$style.'" ><INPUT title="'.$alt.'" TYPE="'.$type.'" id="'.$id.'"  name="'.$name.'" onmouseover="this.style.filter=\'alpha(opacity=100)\'" onmouseout="this.style.filter=\'alpha(opacity=80)\'" onclick="'.$click.'" align="absmiddle" class="'.$name.' imgLink"></span>';

        return $parseStr;
    }

    /**
     +----------------------------------------------------------
     * swf标签解析 插入flash文件
     * 格式： <html:swf type="" value="" />
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $attr 标签属性
     +----------------------------------------------------------
     * @return string|void
     +----------------------------------------------------------
     */
    function _swf($attr,$content) 
    {
        $tag        =	$this->parseXmlAttr($attr,'swf');
        $id			=	$tag['id'];           
        $src		=	$tag['src'];           
        $width		=	$tag['width'];    
		$parm		=	$tag['parm'];
		$vars		=	$tag['vars'];
		$bgcolor	=	$tag['bgcolor'];
        $height     =	$tag['height'];      
        $version    =	$tag['version'];     
        $autoinstall=	$tag['autoinstall'];  
		
        $parseStr   = '<div id="flashcontent">'.$content.'</div><script type="text/javascript">';
		$parseStr	.='// <![CDATA['."\r\n";
		$parseStr	.= 'var so = new SWFObject("'.$src.'", "'.$id.'", "'.$width.'", "'.$height.'", "'.$version.'", "'.$bgcolor.'","'.$autoinstall.'");'."\r\n";

		$parseStr	.=	'so.addVariable("var", "value");'."\r\n";
		$parseStr	.=	'so.addParam("scale", "noscale");'."\r\n";
		$parseStr	.=	'so.write("flashcontent");'."\r\n";
		$parseStr	.= '// ]]></script>';

        return $parseStr;
    }


    /**
     +----------------------------------------------------------
     * imageBtn标签解析
     * 格式： <html:imageBtn type="" value="" />
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $attr 标签属性
     +----------------------------------------------------------
     * @return string|void
     +----------------------------------------------------------
     */
    function _imageBtn($attr) 
    {
        $tag        = $this->parseXmlAttr($attr,'imageBtn');
        $name       = $tag['name'];                //名称
        $value      = $tag['value'];                //文字
        $id         = $tag['id'];                //ID
        $style      = $tag['style'];                //样式名
        $click      = $tag['click'];                //点击
        $type       = empty($tag['type'])?'button':$tag['type'];                //按钮类型

        if(!empty($name)) {
            $parseStr   = '<div class="'.$style.'" ><INPUT TYPE="'.$type.'" id="'.$id.'" name="'.$name.'" value="'.$value.'" onclick="'.$click.'" class="'.$name.' imgButton"></div>';
        }else {
        	$parseStr   = '<div class="'.$style.'" ><INPUT TYPE="'.$type.'" id="'.$id.'"  name="'.$name.'" value="'.$value.'" onclick="'.$click.'" class="button"></div>';
        }
        

        return $parseStr;
    }

    /**
     +----------------------------------------------------------
     * mulitSelect标签解析
     * 格式： <html:list datasource="" show="" />
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $attr 标签属性
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    function _multiSelect($attr) 
    {
        $tag        = $this->parseXmlAttr($attr,'mulitSelect');
        $id         = $tag['id'];                   //表格ID
        $name       = $tag['name'];                 //返回表单值
        $source     = $tag['source'];               //原始数据源
        $target     = $tag['target'];               //目标数据源
        $size       = $tag['size'];                 //下拉列表size
        $style      = $tag['style'];                //表格样式
        $multiple   = $tag['multiple'];             //是否多选
        $sourceTitle   = $tag['sourcetitle'];       //原始标题
        $targetTitle   = $tag['targettitle'];       //目标标题

        $parseStr	= "<!-- FCS 系统多选组件开始 -->\n<div align=\"center\"><TABLE class=\"".$style."\">";
        $parseStr	.= '<tr><td height="5" colspan="3" class="topTd" ></td></tr>';
        $parseStr	.= '<TR><Th width="44%" >'.$sourceTitle.'</Th><Th ></Th><Th width="44%">'.$targetTitle.'</Th></TR>';
        $parseStr	.= '<TR><TD ><div class="solid"><html:select id="sourceSelect" options="'.$source.'" dblclick="addItem()" multiple="true" style="multiSelect" size="'.$size.'" /></div></TD><TD valign="middle"><div style="margin-top:35px"><html:imageBtn value="添加" click="addItem()" style="impBtn vMargin fLeft " /><html:imageBtn type="button" value="全选" click="addAllItem()" style="impBtn vMargin fLeft " /><html:imageBtn value="移除" click="delItem()" style="impBtn vMargin fLeft " /><html:imageBtn  value="全删" click="delAllItem()" style="impBtn vMargin fLeft " /></div></TD>	<TD ><div class="solid"><html:select name="'.$name.'[]" id="targetSelect" options="'.$target.'" dblclick="delItem()" multiple="true" style="multiSelect" size="'.$size.'" /></div></TD></TR><tr><td height="5" colspan="3" class="bottomTd" ></td></tr></TABLE></div>';
        $parseStr	.= "\n<!-- FCS 系统多选组件结束 -->\n";
        return $parseStr;
	}


    /**
     +----------------------------------------------------------
     * list标签解析
     * 格式： <html:list datasource="" show="" />
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $attr 标签属性
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    function _list($attr) 
    {
        $tag        = $this->parseXmlAttr($attr,'list');
        $id         = $tag['id'];                       //表格ID
        $datasource = $tag['datasource'];               //列表显示的数据源VoList名称
        $pk         = empty($tag['pk'])?'id':$tag['pk'];//主键名，默认为id
        $style      = $tag['style'];                    //样式名
        $name       = !empty($tag['name'])?$tag['name']:'vo';                 //Vo对象名
        $action     = $tag['action'];                   //是否显示功能操作
        $checkbox   = $tag['checkbox'];                 //是否显示Checkbox
        if(isset($tag['actionlist'])) {
            $actionlist = explode(',',trim($tag['actionlist']));    //指定功能列表
        }
        
        if(substr($tag['show'],0,1)=='$') {
            $show   = $this->tpl->get(substr($tag['show'],1));
        }else {
            $show   = $tag['show'];
        }
        $show       = explode(',',$show);                //列表显示字段列表

        //计算表格的列数
        $colNum     = count($show);           
        if(!empty($checkbox))   $colNum++;
        if(!empty($action))     $colNum++;

        //显示开始
		$parseStr	= "<!-- FCS 系统列表组件开始 -->\n";
        $parseStr  .= '<TABLE id="'.$id.'" class="'.$style.'" cellpadding=0 cellspacing=0 >';
        $parseStr  .= '<tr><td height="5" colspan="'.$colNum.'" class="topTd" ></td></tr>';
        $parseStr  .= '<TR class="row" >';
        //列表需要显示的字段
        $fields = array();
        foreach($show as $key=>$val) {
        	$fields[] = explode(':',$val);
        }
        if(!empty($checkbox)) {//如果指定需要显示checkbox列
            $parseStr .='<th width="8"><input type="checkbox" id="check" onclick="CheckAll(\''.$id.'\')"></th>';            
        }
        foreach($fields as $field) {//显示指定的字段
            $property = explode('|',$field[0]);
            $showname = explode('|',$field[1]);
            if(isset($showname[1])) {
                $parseStr .= '<Th width="'.$showname[1].'">';
            }else {
                $parseStr .= '<Th>';
            }
            $showname[2] = isset($showname[2])?$showname[2]:$showname[0];
            $parseStr .= '<A HREF="javascript:sortBy(\''.$property[0].'\',\'{$sort}\',\''.ACTION_NAME.'\')" title="按照'.$showname[2].'<var name=\'sortType\' />">'.$showname[0].'<equal name="order" value="'.$property[0].'" ><IMG SRC="../public/images/<var name=\'sortImg\' />.gif" WIDTH="12" HEIGHT="17" BORDER="0" align="absmiddle"></equal></A></Th>';
        }
        if(!empty($action)) {//如果指定显示操作功能列
            $parseStr .= '<th >操作</th>';
        }
        
        $parseStr .= '</TR>';
        $parseStr .= '<volist name="'.$datasource.'" id="'.$name.'" ><TR class="row" onmouseover="over()" onmouseout="out()" onclick="change()" >';	//支持鼠标移动单元行颜色变化 具体方法在js中定义

        if(!empty($checkbox)) {//如果需要显示checkbox 则在每行开头显示checkbox
            $parseStr .= '<td><input type="checkbox" name="key"	value="<vo name="'.$name.'" property="'.$pk.'" />"></td>';
        }
        foreach($fields as $field) {
            //显示定义的列表字段
            $parseStr   .=  '<TD>';
            if(!empty($field[2])) {
                // 支持列表字段链接功能 具体方法由JS函数实现
                $href = explode('|',$field[2]);
                if(count($href)>1) {
                    //指定链接传的字段值
                    $parseStr .= '<a href="javascript:'.$href[0].'(<vo name="'.$name.'" property="'.$href[1].'" />)">';
                }else {
                    //如果没有指定默认传编号值
                    $parseStr .= '<a href="javascript:'.$field[2].'(<vo name="'.$name.'" property="'.$pk.'" />)">';
                }
            }
            $property = explode('|',$field[0]);
            if(count($property)>1) {
                $parseStr .= '<vo name="'.$name.'" property="'.$property[0].'" function="'.$property[1].'" />';
            }else {
                $parseStr .= '<vo name="'.$name.'" property="'.$field[0].'" />';
            }
            if(!empty($field[2])) {
                $parseStr .= '</a>';
            }
            $parseStr .= '</TD>';
            
        }
        if(!empty($action)) {//显示功能操作
            if(!empty($actionlist[0])) {//显示指定的功能项
                $parseStr .= '<TD>';
                foreach($actionlist as $val) {
                    // edit:编辑 表示 脚本方法名:显示名称
                    $a = explode(':',$val);
                    $b = explode('|',$a[1]);
                    if(count($b)>1) {
                        $c = explode('|',$a[0]);
                        if(count($c)>1) {
                            $parseStr .= '<A HREF="javascript:'.$c[1].'({vo:'.$name.'|'.$pk.'})"><?php if($'.$name.'->status=="0"){ ?>'.$b[1].'<?php } ?></A><A HREF="javascript:'.$c[0].'({vo:'.$name.'|id})"><?php if($'.$name.'->status=="1"){ ?>'.$b[0].'<?php } ?></A> ';
                        }else {
                            $parseStr .= '<A HREF="javascript:'.$a[0].'({vo:'.$name.'|'.$pk.'})"><?php if($'.$name.'->status=="0"){ ?>'.$b[1].'<?php } ?><?php if($'.$name.'->status=="1"){ ?>'.$b[0].'<?php } ?></A> ';
                        }
                        
                    }else {
                        $parseStr .= '<A HREF="javascript:'.$a[0].'({vo:'.$name.'|'.$pk.'})">'.$a[1].'</A> ';
                    }
                    
                }
                $parseStr .= '</TD>';
            }else { //显示默认的功能项，包括 详细、编辑、删除
                $parseStr .= '<TD><A HREF="javascript:edit({vo:'.$name.'|id})">编辑</A> <A HREF="javascript:del({vo:'.$name.'|'.$pk.'})">删除</A></TD>';
            }

        }
        $parseStr	.= '</TR></volist><tr><td height="5" colspan="'.$colNum.'" class="bottomTd"></td></tr></TABLE>';
        $parseStr	.= "\n<!-- FCS 系统列表组件结束 -->\n";
        return $parseStr;
    }

}//类定义结束
?>