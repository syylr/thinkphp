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
 * 下拉列表多选类
 +------------------------------------------------------------------------------
 * @package    Form
 * @link       http://www.fcs.org.cn
 * @copyright  Copyright (c) 2005-2006 liu21st.com.  All rights reserved. 
 * @author     liu21st <liu21st@gmail.com>
 * @version    $Id: Office.js 73 2006-11-08 10:08:01Z fcs $
 +------------------------------------------------------------------------------
 */
//+-------------------------------------------------------
//|	指定页面区域内容导入Excel，包含页面样式
//+-------------------------------------------------------
 function CopyToExcel(tableId) 
 {
  var oXL = new ActiveXObject("Excel.Application"); 
  var oWB = oXL.Workbooks.Add(); 
  var oSheet = oWB.ActiveSheet;  
  var sel=document.body.createTextRange();
  sel.moveToElementText(document.getElementById(tableId));
  sel.select();
  sel.execCommand("Copy");
  oSheet.Paste();
  oXL.Visible = true;
 }

//+-------------------------------------------------------
//|	指定页面区域“单元格”内容导入Excel,不包含页面样式
//+-------------------------------------------------------
 function WriteToExcel(tableId) 
 {
  var oXL = new ActiveXObject("Excel.Application"); 
  var oWB = oXL.Workbooks.Add(); 
  var oSheet = oWB.ActiveSheet; 
  var Lenr = PrintA.rows.length;
  var obj = document.getElementById(tableId);
  for (i=0;i<Lenr;i++) 
  { 
   var Lenc = obj.rows(i).cells.length; 
   for (j=0;j<Lenc;j++) 
   { 
    oSheet.Cells(i+1,j+1).value = obj.rows(i).cells(j).innerText; 
   } 
  } 
  oXL.Visible = true; 
 }
