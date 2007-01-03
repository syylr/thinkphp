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
 * 本地文件操作类
 +------------------------------------------------------------------------------
 * @package    IO
 * @link       http://www.fcs.org.cn
 * @copyright  Copyright (c) 2005-2006 liu21st.com.  All rights reserved. 
 * @author     liu21st <liu21st@gmail.com>
 * @version    $Id$
 +------------------------------------------------------------------------------
 */
	
var _IS_IE = (navigator.userAgent.toLowerCase().indexOf("msie") > -1)?true:false;
var _IS_MOZ = (document.implementation && document.implementation.createDocument)?true:false;
function initFileObject(){
	//var s="";
	if (_IS_MOZ) document.write("<object TYPE=application/x-filectrl ID='exFileCtrl'></object>");
	else if (_IS_IE) {
		document.write("<object classid='clsid:FDC65D71-83A0-11D2-9075-0020AF05A5B1' id='exFileCtrl' width='0' height='0'  style='display:none'  codebase='/missive/cabs/exFileAccess.CAB#version=6,8,0,13' VIEWASTEXT='请安装客户端文件处理控件'></object>");
		document.write('<SCRIPT LANGUAGE=VBScript\> \n');
		  //document.write('on error resume next \n');
		document.write('function  readFileVB(strFn)\n');
		document.write('  dim  DataStr,fObj,strRet\n');
		document.write('  set fObj = CreateObject("exFileAccess.ExFileSys")\n');
		document.write('  strRet = fObj.ReadFileV(CStr(strFn),DataStr)\n');
		document.write('  readFileVB = DataStr\n');
		document.write('  set fObj = nothing\n');
		document.write('end function\n');
		document.write('function  getFileSizeVB(strFn)\n');
		document.write('  dim  fObj,lRet\n');
		document.write('  set fObj = CreateObject("exFileAccess.ExFileSys")\n');
		document.write('  getFileSizeVB = fObj.getFileSize(CStr(strFn))\n');
		document.write('  set fObj = nothing\n');
		document.write('end function\n');
		document.write('</SCRIPT\> \n');
		//s+="<script language='VBScript'>\n";
		//s+="function  readFileVB(strFn)\n";
		//s+="  dim  DataStr,fObj,strRet\n";
		//s+="  set fObj = CreateObject('exFileAccess.ExFileSys')\n";
		//s+="  strRet = fObj.ReadFileV(CStr(strFn),DataStr)\n";
		//s+="  readFileV = DataStr\n";
		//s+="  set fObj = nothing\n";
		//s+="end function</script>";
	}
	//alert(s);
	//document.write(s);
}

function getFileSize(filename){
	if (_IS_MOZ) {
		var length=document.getElementById("exFileCtrl").FileSize(filename);
		return(length);
	}else if (_IS_IE) {
	   var fso, f;
	   //fso = new ActiveXObject("Scripting.FileSystemObject");
	   //f = fso.GetFile(filename);
	   return getFileSizeVB(filename); // (f.size);
	}
	return 0;
}

function createFile(filename,content){
	if (_IS_MOZ) {
		document.getElementById("exFileCtrl").WriteFile(filename,content,content.length);
	}else if (_IS_IE) {
	}
}

function createFileWithTempName(ext,content){
	if (_IS_MOZ) {
		filename=getTempFileName(ext);
		document.getElementById("exFileCtrl").WriteFile(filename,content,content.length);
		return filename;
	}else if (_IS_IE) {
		var fObject = new ActiveXObject("exFileAccess.ExFileSys");
	    var TempFn = fObject.GetTempName2(ext,"",true);
	    fObject.WriteFile(TempFn,content);
		return TempFn;
	}
	return "";
}

function createFileFromBase64(filename,content){
	if (_IS_MOZ) {
		var strings=atob(content);
		document.getElementById("exFileCtrl").WriteFile(filename,strings,strings.length);
	}else if (_IS_IE) {
	}
}

function readFile(filename){
	if (_IS_MOZ) {
		var size=document.getElementById("exFileCtrl").FileSize(filename);
		return (document.getElementById("exFileCtrl").ReadFile(filename,size));
	}else if (_IS_IE) {
		return readFileVB(filename);
		//var fso, f, s;
		//fso = new ActiveXObject("exFileAccess.ExFileSys");
		//fso.readFileVB(filename,s);
		//return s;
	}
}

function readFileAsBase64(filename){
	if (_IS_MOZ) {
		var size=document.getElementById("exFileCtrl").FileSize(filename);
		return btoa(document.getElementById("exFileCtrl").ReadFile(filename,size));
	}else if (_IS_IE) {
		var fso, f, s;
		fso = new ActiveXObject("exFileAccess.ExFileSys");
		fso.readFileV(filename,s);
		return encode64(s);
	}
}

function readFileToNode(filename,node){
	if (_IS_MOZ) {
		var size=document.getElementById("exFileCtrl").FileSize(filename);
		node.textContent=btoa(document.getElementById("exFileCtrl").ReadFile(filename,size));
	}else if (_IS_IE) {
		node.dataType = "bin.base64";	
		node.nodeTypedValue = readFileVB(filename);
	}
	return node;
}

function readFileFromServer(urlIe,urlMoz,ext){
	var tempFilename="";
	var xmlHttpObj = getCommonXMLHttpObject();
	var DataStr;
	if (_IS_MOZ) {
		xmlHttpObj.open("get", urlMoz, false);   	
	}else if (_IS_IE) {
		xmlHttpObj.open("get", urlIe, false);   	
	}
	xmlHttpObj.send("");
	if (_IS_MOZ) {
		DataStr = xmlHttpObj.responseText; //Body;
	}else if (_IS_IE) {
		DataStr = xmlHttpObj.responseBody;
	}
	//alert(DataStr);
	xmlHttpObj = null;
	//alert("-1");
	if (_IS_MOZ) {
		tempFilename = createFileWithTempName(ext,decode64(DataStr));
	}else if (_IS_IE) {
		tempFilename = createFileWithTempName(ext,DataStr);
	}
	return tempFilename;
}

function deleteFile(filename){
	if (_IS_MOZ) {
		if(document.getElementById("exFileCtrl").FileExists(filename)){
			document.getElementById("exFileCtrl").DeleteFile(filename);
			return true;
		}
	}else if (_IS_IE) {
		var fso, f, s;
		FObj = new ActiveXObject("exFileAccess.ExFileSys");
		FObj.KillFile(filename);
		FObj = null;
		return true;
	}
	return false;
}

function fileExists(filename){
	if (_IS_MOZ) {
		return (document.getElementById("exFileCtrl").FileExists(filename));
	}else if (_IS_IE) {
		var fso, f, s;
		len=getFileSizeVB(filename);
		if (len>0) return true;
		//fso = new ActiveXObject("Scripting.FileSystemObject");
		//return fso.FileExists(filename);
	}
	return false;
}

function copyFile(fromFilename,toFilename){
	if (_IS_MOZ) {
		return (document.getElementById("exFileCtrl").CopyFile(fromFilename,toFilename));
	}else if (_IS_IE) {
		var fso, f, s;
		//fso = new ActiveXObject("Scripting.FileSystemObject");
		//return fso.CopyFile(fromFilename,toFilename,true);
		FObj = new ActiveXObject("exFileAccess.ExFileSys");
		srcContent=readFileVB(fromFilename);
		FObj.WriteFile(toFilename,srcContent);
		FObj=null;
	}
}

function getTempFileName(ext){
	if (_IS_MOZ) {
		var temp=new Date();
		filename="/tmp/"+temp.getYear()+""+temp.getMonth()+""+temp.getDay()+""+temp.getHours()+""+temp.getMinutes()+""+temp.getSeconds()+"."+ext;
		return filename;
	}else if (_IS_IE) {
		var fso, f, s;
		fObj = new ActiveXObject("exFileAccess.ExFileSys");
		return fObj.GetTempName2(ext,"",true);
	}
	return false;
}

function  getFileExt(fileName)
{
  var ext = "";
  var iIndex = fileName.lastIndexOf(".");
  ext = fileName.substring(iIndex+1,fileName.length);
  ext = ext.toLowerCase();
  return ext;
}