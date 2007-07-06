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
 * @version    $Id: TwoSelect.js 33 2007-02-25 07:06:02Z liu21st $
 +------------------------------------------------------------------------------
 */
/*使用说明
+--------------------------------------------------------
<script language="JavaScript" src="MultSelect.js"></script>

<select id="selectFrom" size="6" multiple ondblclick="s.addItem()"></select>
<select id="selectTo" size="6" multiple  ondblclick="s.delItem()"></select>
s = new MultSelect('selectFrom','selectTo');

addItem()		添加（选择项）
delItem()		删除（选择项）
addAllItem()	全加
delAllItem()	全删
+--------------------------------------------------------
*/

function MultSelect(fromSelectId,toSelectId){

	var selectSource = document.getElementById(fromSelectId).options;
	var selectTarget = document.getElementById(toSelectId).options;

	this.addItem = function(){
		for(i=0;i<selectSource.length;i++)
			if(selectSource[i].selected){
				selectTarget.add(new Option(selectSource[i].text,selectSource[i].value));
					}
		for(i=0;i<selectTarget.length;i++)
			for(j=0;j<selectSource.length;j++)
				if(selectSource[j].text==selectTarget[i].text) selectSource[j]=null;
	}

	this.delItem = function(){
		for(i=0;i<selectTarget.length;i++)
			if(selectTarget[i].selected){
			selectSource.add(new Option(selectTarget[i].text,selectTarget[i].value));
			}
		for(i=0;i<selectSource.length;i++)
			for(j=0;j<selectTarget.length;j++)
			if(selectTarget[j].text==selectSource[i].text) selectTarget[j]=null;
	}

	this.delAllItem = function(){
		for(i=0;i<selectTarget.length;i++)
			selectSource.add(new Option(selectTarget[i].text,selectTarget[i].value));
		selectTarget.length=0
	}

	this.addAllItem = function(){
		for(i=0;i<selectSource.length;i++)
			selectTarget.add(new Option(selectSource[i].text,selectSource[i].value));
		selectSource.length=0;
	}

}