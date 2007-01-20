// +----------------------------------------------------------------------+
// | ThinkPHP                                                             |
// +----------------------------------------------------------------------+
// | Copyright (c) 2006 liu21st.com All rights reserved.                  |
// +----------------------------------------------------------------------+
// | Licensed under the Apache License, Version 2.0 (the 'License');      |
// | you may not use this file except in compliance with the License.     |
// | You may obtain a copy of the License at                              |
// | http://www.apache.org/licenses/LICENSE-2.0                           |
// | Unless required by applicable law or agreed to in writing, software  |
// | distributed under the License is distributed on an 'AS IS' BASIS,    |
// | WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or      |
// | implied. See the License for the specific language governing         |
// | permissions and limitations under the License.                       |
// +----------------------------------------------------------------------+
// | Author: liu21st <liu21st@gmail.com>                                  |
// +----------------------------------------------------------------------+
// $Id$

// Ajax for ThinkPHP
document.write("<div id='ThinkAjaxResult' class='ThinkAjax' style='position:absolute;z-index:1000;display:none;padding:15px 5px;width:350px;border:1px dotted silver;background:#eee;text-align:center;' ></div>");
var ThinkAjax = {
	method:'POST',			//发送方法
	bComplete:false,			//是否完成
	updateTip:'数据处理中～',	//后台处理中提示信息
	updateEffect:{'opacity': [0,0.7]},			//更新效果
	target:'ThinkAjaxResult',	//提示信息对象
	showTip:true,	 // 是否显示提示信息，默认开启
	status:0, //返回状态码
	info:'',	//返回信息
	data:'',	//返回数据
	activeRequestCount:0,
	// Ajax连接初始化
	getTransport: function() {
		return Try.these(
		 function() {return new XMLHttpRequest()},
		  function() {return new ActiveXObject('Msxml2.XMLHTTP')},
		  function() {return new ActiveXObject('Microsoft.XMLHTTP')}
		 
		) || false;
	},
	loading:function (target,effect){
		var arrayPageSize = getPageSize();
		var arrayPageScroll = getPageScroll();
		$(target).style.top = (arrayPageScroll[1] + ((arrayPageSize[3] - 35 - 65) / 2) + 'px');
		$(target).style.left = (((arrayPageSize[0] - 20 - 350) / 2) + 'px');
		$(target).style.display = 'block';
		//使用更新效果
		var myEffect = $(target).effects();
		myEffect.custom(effect);
		// 显示正在更新
		$(target).innerHTML = this.updateTip ;
	},
	ajaxResponse:function(request,target,response){
		// 获取ThinkPHP后台返回Ajax信息和数据
		// 此格式为ThinkPHP专用格式
		$return =  eval('(' + request.responseText + ')');
		this.status = $return.status;
		this.info	 =	 $return.info;
		this.data = $return.data;
		// 显示提示信息
		if (this.showTip)
		$(target).innerHTML	= this.info;
		// 处理返回数据
		// 需要在客户端定义ajaxReturn方法
		if (response == undefined)
		{
			try	{(ajaxReturn).apply(this,[this.data,this.status,this.info]);}
			catch (e){}
			 
		}else {
			try	{ (response).apply(this,[this.data,this.status,this.info]);}
			catch (e){}
		}
		// 提示信息停留5秒
		if (this.showTip)
		window.setTimeout(function (){$(target).style.display='none';},5000);
	},
	// 发送Ajax请求
	send:function(url,pars,response,target,effect)
	{
		var xmlhttp = this.getTransport();
		if (target == undefined)	target = this.target;
		if (effect == undefined)	effect = this.updateEffect;
		if (this.showTip)
		{
			this.loading(target,effect);
		}
		
		this.activeRequestCount++;
		this.bComplete = false;
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
			xmlhttp.onreadystatechange = function (){
				if (xmlhttp.readyState == 4 ){
					if( xmlhttp.status == 200 && !_self.bComplete)
					{
						_self.bComplete = true;
						_self.activeRequestCount--;
						_self.ajaxResponse(xmlhttp,target,response);
					}
				}
			}
			xmlhttp.send(pars);
		}
		catch(z) { return false; }
	},
	// 发送表单Ajax操作，暂时不支持附件上传
	sendForm:function(formId,url,response,target,effect)
	{
		vars = Form.serialize(formId);
		this.send(url,vars,response,target,effect);
	},
	// 绑定Ajax到HTML元素和事件
	// event 支持根据浏览器的不同 
	// 包括 focus blur mouseover mouseout mousedown mouseup submit click dblclick load change keypress keydown keyup
	bind:function(source,event,url,vars,response,target,effect)
	{
		var _self = this;
	   $(source).addEvent(event,function (){_self.send(url,vars,response,target,effect)});
	},
	// 页面加载完成后执行Ajax操作
	load:function(url,vars,response,target,effect)
	{
		var _self = this;
	   window.addEvent('load',function (){_self.send(url,vars,response,target,effect)});
	},
	// 延时执行Ajax操作
	time:function(url,vars,time,response,target,effect)
	{
		var _self = this;
		myTimer =  window.setTimeout(function (){_self.send(url,vars,response,target,effect)},time);
	},
	// 定制执行Ajax操作
	repeat:function(url,vars,intervals,response,target,effect)
	{
		var _self = this;
		myTimer = window.setInterval(function (){_self.send(url,vars,response,target,effect)},intervals);
	}
}
