document.write("<div id='ajaxResult' style='position:absolute;z-index:1000;display:none;padding:6px;width:350px;height:35px;border:1px solid silver;background:#eee;text-align:center;' ></div>");

var ThinkAjax = SmartAjax;
ThinkAjax.response = ThinkAjaxResponse;
ThinkAjax.updateTip	=	'<IMG SRC="ajaxloading.gif" WIDTH="16" HEIGHT="16" BORDER="0" ALT=""> 数据处理中～';
ThinkAjax.target	 =	 'ajaxResult';
// 重载SmartAjax的loading方法
ThinkAjax.loading = function (target){
		showAjaxResult(target);
		$(target).innerHTML = ThinkAjax.updateTip ;
	}
// 显示提示信息
function showAjaxResult(target){
	var arrayPageSize = getPageSize();
	var arrayPageScroll = getPageScroll();
	$(target).style.top = (arrayPageScroll[1] + ((arrayPageSize[3] - 35 - 65) / 2) + 'px');
	$(target).style.left = (((arrayPageSize[0] - 20 - 350) / 2) + 'px');
	$(target).style.display = 'block';
	// 使用效果
	var myFx = new Fx.Style(target, 'opacity',{duration:600}).custom(0,0.7);
}
// Ajax response
function ThinkAjaxResponse(request){
	$return =  eval('(' + request.responseText + ')');
	if (1 == $return.status)
	{
		// 操作成功 记录返回数据
		// 需要在客户端定义ajaxReturn方法
		try
		{
			(ajaxReturn).apply(this,[$return.data]);
		}
		catch (e){}
		// 显示成功提示
		$(ThinkAjax.target).innerHTML	= '<div style="color:#3333FF;font-weight:bold"><IMG SRC="'+PUBLIC+'/images/ok.gif" WIDTH="20" HEIGHT="20" BORDER="0" ALT="" align="absmiddle"> '+$return.info+'</div>';
	}else {
		// 显示错误信息
		$(ThinkAjax.target).innerHTML	= '<div style="color:#FF0000;font-weight:bold"><IMG SRC="'+PUBLIC+'/images/update.gif" WIDTH="20" HEIGHT="20" BORDER="0" ALT="" align="absmiddle"> '+$return.info+'</div>';
	}
	// 提示信息停留5秒
	window.setTimeout(function (){$(ThinkAjax.target).style.display='none';},5000);
}