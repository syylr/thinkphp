// +---------------------------------------------------------------------------+
// | FCS -- Fast,Compatible & Simple OOP PHP Framework                         |
// | FCS JS 基类库                                                             |
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
 * 基础核心类，集成了最基本的操作
 +------------------------------------------------------------------------------
 * @package    Core
 * @link       http://www.fcs.org.cn
 * @copyright  Copyright (c) 2005-2006 liu21st.com.  All rights reserved. 
 * @author     liu21st <liu21st@gmail.com>
 * @version    $Id: Base.js 73 2006-11-08 10:08:01Z fcs $
 +------------------------------------------------------------------------------
 */

var ImportBasePath = location.protocol+'//'+location.hostname+'/Public/';
/**
 +----------------------------------------------------------
 * 判断对象类型
 * 
 +----------------------------------------------------------
 * @param mixed $obj 数据对象
 +----------------------------------------------------------
 * @return string
 +----------------------------------------------------------
 */
function is_array(obj) {
    return (obj.constructor.toString().indexOf("Array")!= -1);
}

function is_function(obj) {
    return (obj.constructor.toString().indexOf("Function")!= -1);
}

function is_object(obj) {
    return (obj.constructor.toString().indexOf("Object")!= -1);
}

function is_string(obj) {
    return (obj.constructor.toString().indexOf("String")!= -1);
}

function is_number(obj) {
    return (obj.constructor.toString().indexOf("Number")!= -1);
}

function is_boolean(obj) {
    return (obj.constructor.toString().indexOf("Boolean")!= -1);
}

/**
 +----------------------------------------------------------
 * 动态导入Js类或文件 使用 命名空间方式 
 * 目前不支持多文件导入
 +----------------------------------------------------------
 * @param string jsFile 导入的Js文件命名空间路径
 * @param string basePath 导入的根路径 必须是URL路径 
 +----------------------------------------------------------
 * @return void
 +----------------------------------------------------------
 */

function _import(jsFile,basePath) 
{             
	 var head = document.getElementsByTagName('HEAD').item(0); 
	 var script = document.createElement('SCRIPT'); 
	 if (basePath == undefined)
	 {
		 basePath = ImportBasePath;
	 }
	 
	 jsFile = basePath + jsFile.replace(/\./g, '/') + '.js';
	 //alert(jsFile);
	 script.src = jsFile; 
	 script.type = "text/javascript"; 
	 head.appendChild(script); 
 }

	//---------------------------------------------------
	//	getElementById 替代方法
	//---------------------------------------------------
	function $() {
	  var elements = new Array();

	  for (var i = 0; i < arguments.length; i++) {
		var element = arguments[i];
		if (typeof element == 'string')
		  element = document.getElementById(element);

		if (arguments.length == 1)
		  return element;

		elements.push(element);
	  }

	  return elements;
	}

	//---------------------------------------------------
	//	打开新窗口
	//---------------------------------------------------
	function PopWindow(pageUrl,WinWidth,WinHeight) 
	{ 
	var popwin=window.open(pageUrl,"PopWin","scrollbars=yes,toolbar=no,location=no,directories=no,status=no,menubar=no,resizable=no,width="+WinWidth+",height="+WinHeight); 
	return false; 
	} 

	//---------------------------------------------------
	//	打开远程窗口
	//---------------------------------------------------
	function PopRemoteWindow(url) 
	{ 
	var remote=window.open(url,"RemoteWindow","scrollbars=yes,toolbar=yes,location=yes,directories=yes,status=yes,menubar=yes,resizable=yes"); 
	if(remote.opener==null) 
	{ 
	remote.opener=window; 
	} 
	} 

	//+---------------------------------------------------
	//|	打开模式窗口，返回新窗口的操作值
	//+---------------------------------------------------
	function PopModalWindow(url,width,height)
	{
		var result=window.showModalDialog(url,"win","dialogWidth:"+width+"px;dialogHeight:"+height+"px;center:yes;status:no;scroll:no;dialogHide:no;resizable:no;help:no;edge:sunken;");
		return result;
	}

	//+---------------------------------------------------
	//|	打开非模式窗口，返回打开窗口的句柄
	//+---------------------------------------------------
	function PopModelessWindow(url,width,height)
	{
		var win=window.showModelessDialog(url,"win","dialogWidth:"+width+"px;dialogHeight:"+height+"px;center:yes;status:no;scroll:no;dialogHide:no;resizable:no;help:no;edge:sunken;");
		return win;
	}

	//+---------------------------------------------------
	//|	动态加载外部CSS和JS文件
	//+---------------------------------------------------
	function ImportCss(cssFile) 
        {             
             document.createStyleSheet(cssFile); 
        } 
      
	function ImportJS(jsFile) 
	{             
		 var head = document.getElementsByTagName('HEAD').item(0); 
		 var script = document.createElement('SCRIPT'); 
		 script.src = jsFile; 
		 script.type = "text/javascript"; 
		 head.appendChild(script); 
	 }

	//+---------------------------------------------------
	//|	创建网页元素
	//+---------------------------------------------------
	 function CreateElement(type,owner)
	 {
		 var element = document.createElement(type); 
		 owner.appendChild(element);
	 }
	 
	//+---------------------------------------------------
	//|	获取HTML页面参数 flag 为1 获取详细参数
	//+---------------------------------------------------
	function getHTMLParm(flag)
	{
		var parastr = window.location.search;
		if (flag)
		{
			var parm= Array();
			var tempstr="";
			if (str.indexOf("&")>0){
				 para = parastr.split("&");
				 for(i=0;i<para.length;i++)
				 {
					 tempstr1 = para[i];
					 
					 pos = tempstr1.indexOf("=");
					 parm[i] = [tempstr1.substring(0,pos),tempstr1.substring(pos+1)];
				 }
			 }
			 return parm;
		}
		 return parastr;
	}

