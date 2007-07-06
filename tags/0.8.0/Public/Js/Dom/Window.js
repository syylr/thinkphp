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
 * Window类
 +------------------------------------------------------------------------------
 * @package    Form
 * @link       http://www.fcs.org.cn
 * @copyright  Copyright (c) 2005-2006 liu21st.com.  All rights reserved. 
 * @author     liu21st <liu21st@gmail.com>
 * @version    $Id$
 +------------------------------------------------------------------------------
 */
function GetScreenWH()
{
	var screenW = 0, screenH = 0;
	if( typeof( window.innerWidth ) == 'number' )
	{
		//Non-IE
		screenW = window.innerWidth;
		screenH = window.innerHeight;
	} 
	else if( document.documentElement && ( document.documentElement.clientWidth || document.documentElement.clientHeight ) ) 
	{
		//IE 6+ in 'standards compliant mode'
		screenW = document.documentElement.clientWidth;
		screenH = document.documentElement.clientHeight;
	} 
	else if( document.body && ( document.body.clientWidth || document.body.clientHeight ) ) 
	{
		//IE 4 compatible
		screenW = document.body.clientWidth;
		screenH = document.body.clientHeight;
	}
	return [screenW,screenH];
}

function GetElementX(el)
{
    var offsetTrail = el;
    var offsetLeft = 0;
    
    // account for IE 6 CSS compatibility mode
    while (offsetTrail) 
    {
        offsetLeft += offsetTrail.offsetLeft;
        offsetTrail = offsetTrail.offsetParent;
    }
    if (navigator.userAgent.indexOf("Mac") != -1 && typeof document.body.leftMargin != "undefined") 
    {
        offsetLeft += document.body.leftMargin;
    }
    return offsetLeft;
}

function GetElementY(el)
{
    var offsetTrail = el;
    var offsetTop = 0;
    
    // account for IE 6 CSS compatibility mode
    while (offsetTrail) 
    {
        offsetTop += offsetTrail.offsetTop;
	//offsetTop -= (offsetTrail.scrollTop?offsetTrail.scrollTop:0);
        offsetTrail = offsetTrail.offsetParent;
    }
    if (navigator.userAgent.indexOf("Mac") != -1 && typeof document.body.leftMargin != "undefined") 
    {
        offsetTop += document.body.topMargin;
    }
    return offsetTop;
}

function SetOpacity(ObjId, opacity)
{
	var cssval = opacity/100;
	var Obj = document.getElementById(objId);
	// IE
 	Obj.style.filter="alpha(style=0,opacity=" + opacity + ")";
	// Mozilla <=1.6
	Obj.style.MozOpacity = cssval;
	// CSS3 Standard
	Obj.style.opacity = cssval;
}

function SetShadowOpacity(ObjId, opacity)
{
	var cssval = opacity/100;
	var Obj = document.getElementById(objId);
	// IE
 	Obj.style.filter="progid:DXImageTransform.Microsoft.Shadow(direction=135,color=#999999,strength=3) alpha(style=0,opacity=" + opacity + ")";
 	// Mozilla <=1.6
 	Obj.style.MozOpacity = cssval;
 	// CSS3 Standard
	Obj.style.opacity = cssval;
}

function MoveLayer(x, y, layer)
{
	var oPix = ( top.document.childNodes ? 'px' : 0 );
	layer.style.left = x + oPix; layer.style.top = y + oPix; 
}