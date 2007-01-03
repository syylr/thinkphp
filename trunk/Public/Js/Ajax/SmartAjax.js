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
 * FCS Ajax 实现JS类 需要mootools效果实现类库支持
 +------------------------------------------------------------------------------
 * @package    Ajax
 * @link       http://www.fcs.org.cn
 * @copyright  Copyright (c) 2005-2006 liu21st.com.  All rights reserved. 
 * @author     liu21st <liu21st@gmail.com>
 * @version    $Id$
 +------------------------------------------------------------------------------
 */

var SmartAjax = {
	method:'POST',			//发送方法
	bComplete:false,			//是否完成
	updateTip:'update...',	//更新提示信息
	updateEffect:'',			//更新效果
	responseMethod:'text',	//返回类型 text eval
	activeRequestCount:0,
	// Ajax连接初始化
	getTransport: function() {
		return Try.these(
		  function() {return new ActiveXObject('Msxml2.XMLHTTP')},
		  function() {return new ActiveXObject('Microsoft.XMLHTTP')},
		  function() {return new XMLHttpRequest()}
		) || false;
	},
	loading:function (target){
		$(target).innerHTML = this.updateTip ;
	},
	// 发送Ajax请求
	send:function(url,pars,target,response,effect,intervals)
	{
		var xmlhttp = this.getTransport();
		this.loading(target);
		this.activeRequestCount++;
		this.bComplete = false;
		if (effect == undefined)	effect = this.updateEffect;
		try {
			if (this.method == "GET")
			{
				xmlhttp.open(this.method, url+"?"+pars, true);
				pars = "";
			}
			else
			{
				xmlhttp.open(this.method, url, true);
				xmlhttp.setRequestHeader("Method", "POST "+url+" HTTP/1.1");
				xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			}
			var _self = this;
			this.handleAjaxResponse= function (request){
				if (this.responseMethod=='eval')
				{
					// 使用eval方式返回
					eval(request.responseText);
				}
				else if (target !='' && target!= undefined)
				{
					// 默认的结果处理操作，直接在target位置显示responseText
					$(target).innerHTML =request.responseText ; 
				}
				if (effect != '')	
				{
					//使用更新效果
					var myEffect = $(target).effects();
					myEffect.custom(effect);
				}else {
					// 默认效果
					var myFx = new Fx.Style(target, 'opacity',{duration:600}).custom(0.1,1);
					window.setTimeout(function (){$(target).style.display='none';},3000);
				}
				/*
				if (window.opener)
				{
					window.setTimeout(function (){window.opener.location.reflash();},3000);
				}
				if (window.parent != window)
				{
					window.setTimeout(function (){parent.location.reload();},3000);
				}*/
			}
			xmlhttp.onreadystatechange = function (){
				if (xmlhttp.readyState == 4 ){
					if( xmlhttp.status == 200 && !_self.bComplete)
					{
						_self.bComplete = true;
						_self.activeRequestCount--;
						if (response==undefined)
						{
							// 使用默认的处理
							_self.handleAjaxResponse(xmlhttp);
						}else {
							// 使用自定义处理方法
							(response).apply(this,[xmlhttp]);
						}
						if (!isNaN(intervals) && intervals >0)	// 是否定时执行
						{	
							window.setTimeout(function (){_self.send(url,pars,target,response,effect,intervals)},intervals);
						}
					}
				}
			}
			xmlhttp.send(pars);
		}
		catch(z) { return false; }
	},
	// 发送表单Ajax操作，暂时不支持附件上传
	sendForm:function(formId,url,target,response,effect)
	{
		vars = Form.serialize(formId);
		this.send(url,vars,target,response,effect);
	},
	// 绑定Ajax到HTML元素和事件
	// event 支持根据浏览器的不同 
	// 包括 focus blur mouseover mouseout mousedown mouseup submit click dblclick load change keypress keydown keyup
	bind:function(source,event,url,vars,target,response,effect)
	{
		var _self = this;
	   $(source).addEvent(event,function (){_self.send(url,vars,target,response,effect)});
	},
	// 页面加载完成后执行Ajax操作
	load:function(url,vars,target,response,effect)
	{
		var _self = this;
	   window.addEvent('load',function (){_self.send(url,vars,target,response,effect)});
	},
	// 延时执行Ajax操作
	time:function(url,vars,target,time,response,effect)
	{
		var _self = this;
		myTimer =  window.setTimeout(function (){_self.send(url,method,vars,target,response,effect)},time);
	},
	// 定制执行Ajax操作
	repeat:function(url,vars,target,intervals,response,effect)
	{
		this.send(url,vars,target,response,effect,intervals);
	}
}