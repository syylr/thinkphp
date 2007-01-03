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
 * 调试类
 +------------------------------------------------------------------------------
 * @package    Debug
 * @link       http://www.fcs.org.cn
 * @copyright  Copyright (c) 2005-2006 liu21st.com.  All rights reserved. 
 * @author     liu21st <liu21st@gmail.com>
 * @version    $Id$
 +------------------------------------------------------------------------------
 */
	
//+---------------------------------------------------
//|	显示对象的所有属性
//+---------------------------------------------------
function getProperties(o)
{
var str		= '<div style="color:gray;">+--------------------------------------------------------------------------<br>'
			+ '| <b>对象<span style="color:#0000FF">'+o+'</span>的属性列表和当前值：</b><br>'
			+ '+--------------------------------------------------------------------------<br>';
var i = 1;
 for(x in o) 
   {
	 str +='|'+ i +' ['+typeof(o[x])+']<span style="color:#6666FF">'+ x + '</span> : <span style="color:#FF3300"> '+o[x]+ '</span><br>+--------------------------------------------------------------------------<br>';
	 i++;
   }
 str += '</div>';
 document.write(str);
}

//+---------------------------------------------------
//|	显示对象支持的所有样式属性
//+---------------------------------------------------
function getStyles(o)
{
	var str = '<div style="color:gray;">+--------------------------------------------------------------------------<br>'
			+ '| 对象<span style="color:#0000FF">'+o+'</span>的样式列表和当前值：<br>'
			+ '+--------------------------------------------------------------------------<br>';
	var o = o.style;
	var i = 1;
	for(x in o) 
	{
	str += '|'+ i +'  <span style="color:#6666FF">'+ x + '</span> :'+ o[x]+"<br>+--------------------------------------------------------------------------<br>" ;
		if (typeof(o[x])=='object') getProperties(o[x]);
		i++;
	}

	str += '</div>';
	document.write(str);
}

function hello(){

	alert('hello');
}
alert('dff');