/**
 +------------------------------------------------------------------------------
 * SmartAjax 需要prototype和 mootools效果实现类库支持
 +------------------------------------------------------------------------------
 * @package    Ajax
 * @link       http://www.topthink.com.cn/
 * @copyright  Copyright (c) 2006 www.topthink.com.cn  All rights reserved. 
 * @author     liu21st <liu21st@gmail.com>
 * @version    0.2
 +------------------------------------------------------------------------------
 */
/*
使用方法：
//初始化Ajax
var A = SmartAjax;
A.init();
A.updateTip = '<font color="orange">更新中～</font>';
A.updateEffect = {'font-size':[0,30],'opacity': [1,0.3]};

// 页面加载时执行通过Ajax操作在后台执行load.php
A.load('http://localhost/load.php',"name=liu21st",'result');

// 绑定click操作到id为run的html元素，执行Ajax
A.bind('run','click','http://localhost/hello.php',"name=liu21st",'result');

// 每隔1秒种执行time.php，输出
A.repeat('http://localhost/time.php',"",$('timer'),1000);

// 自定义响应处理方法
function handle(request){
	alert(request.responseText);
}
A.bind('run','click',"__URL__/hello/","name=liu21st",'',handle);
*/
var SmartAjax = {
	method:'POST',			//发送方法
	bComplete:false,			//是否完成
	updateTip:'update...',	//更新提示信息
	updateEffect:'',			//更新效果
	activeRequestCount:0,
	// Ajax连接初始化
	getTransport: function() {
		return Try.these(
		  function() {return new ActiveXObject('Msxml2.XMLHTTP')},
		  function() {return new ActiveXObject('Microsoft.XMLHTTP')},
		  function() {return new XMLHttpRequest()}
		) || false;
	},
	// 发送Ajax请求
	send:function(url,pars,target,response,effect,intervals)
	{
		var xmlhttp = this.getTransport();
		this.activeRequestCount++;
		$(target).innerHTML = this.updateTip ; 
		this.bComplete = false;
		if (effect == undefined)	effect = this.updateEffect;
		try {
			if (this.method == "GET")
			{
				xmlhttp.open(this.method, url+"?"+pars, false);
				pars = "";
			}
			else
			{
				xmlhttp.open(this.method, url, false);
				xmlhttp.setRequestHeader("Method", "POST "+url+" HTTP/1.1");
				xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			}
			var _self = this;
			this.handleAjaxResponse= function (request){
				// 默认的结果处理操作，直接在target位置显示responseText
				$(target).innerHTML =request.responseText ; 
				if (effect != '')	//使用更新效果
				{
					var myEffect = $(target).effects();
					myEffect.custom(effect);
				}
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