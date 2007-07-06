
var gIsIE = document.all; 
function format(type, para){
	try
	{
	var f = $('editor');
	f.focus();
	if(!para)
		f.document.execCommand(type)
	else
		f.document.execCommand(type,false,para)
	f.focus();		
	}
	catch (e)
	{
	}

}
function createLink() {
	var sURL=window.prompt("输入链接URL:", "http://");
	if ((sURL!=null) && (sURL!="http://")){
		format("CreateLink", sURL);
	}
}
function createImg()	{
	var sPhoto=prompt("请输入图片位置:", "http://");
	if ((sPhoto!=null) && (sPhoto!="http://")){
		format("InsertImage", sPhoto);
	}
}
function selectImage(url){//__APP__/Attach/select
		if (url == undefined)
		{
			createImg()
		}else {
		var imgurl = PopModalWindow(url,458,358);
		//range = document.selection.createRange();
		//range.text = 'ddfdfdfdf';
		//range.select();
		//format("CreateLink",'http://dddd.com.cn');
		if (imgurl != null)	format("InsertImage", imgurl);	
		}
}
function setFont(){
	format('fontname',font);
}
function setColor(color){
	format('ForeColor',color);
}

function setMode(source,html){
	var sourceEditor = $('content');
	var HtmlEditor = $('editor');
	if(sourceEditor.style.display == "none"){
		sourceEditor.style.display = "block";
		HtmlEditor.style.display = "none";
		sourceEditor.value = HtmlEditor.innerHTML;
	}else{
		$('sourceEditor').style.display = "none";
		HtmlEditor.style.display = "block";
		HtmlEditor.style.width = "100%";
		HtmlEditor.innerHTML = sourceEditor.value;
	}
}
function resetEditor(){
	var sourceEditor = $('content');
	var HtmlEditor = $('editor');
	HtmlEditor.innerHTML = '';
}
