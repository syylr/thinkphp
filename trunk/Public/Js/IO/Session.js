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
 * @version    $Id$
 +------------------------------------------------------------------------------
 */
/*使用说明
+--------------------------------------------------------
封装的userdata类

+--------------------------------------------------------
*/

function Session()
{
    var SessionObj = null;
	//+---------------------------------------------------
	//|	初始化
	//+---------------------------------------------------
    this.init = function()
        {
            SessionObj = document.createElement('input');
            SessionObj.type = "hidden";
            SessionObj.id = "Sessionid";
            SessionObj.style.behavior = "url('#default#userData')" 
			document.body.appendChild(SessionObj);
        }

	//+---------------------------------------------------
	//|	读取session变量
	//+---------------------------------------------------
   this.load = function(sessionName)
        {
            if (sessionName != null && sessionName != "")
                {
                    SessionObj.load("s");
                        return SessionObj.getAttribute(sessionName);
                }
        }

	//+---------------------------------------------------
	//|	保存session
	//+---------------------------------------------------
        this.save = function(objId,attribute,sessionName)
        {
            var obj = null;
                if (document.getElementById(objId) != null) obj = document.getElementById(objId)
                else return;
            var value = obj[attribute];
        
        if (sessionName != null && sessionName != "")
                {
                    SessionObj.setAttribute(sessionName,value)
                        SessionObj.save("s")
            }
        }

        this.init(); 
}
