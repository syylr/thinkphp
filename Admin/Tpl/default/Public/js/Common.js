function showTip(info){
	$('tips').innerHTML	=	info;
}
function sendForm(formId,action,target,response,effect){
	// Ajax方式提交表单
	if (CheckForm($(formId)))//表单数据验证
	{
		$('result').style.display = 'block';
		SmartAjax.sendForm(formId,action,'result',response,effect);
	}
	//Form.reset(formId);

}
rowIndex = 0;

function submitForm(formId,contentId,url){
	var html = window.frames["Editor"].frames['HtmlEditor'].document.getElementsByTagName("BODY")[0].innerHTML;
	if ( (html.toLowerCase() == "<p>&nbsp;</p>") || (html.toLowerCase() == "<p></p>") ){
		html = "";
	}
	$(contentId).value = html;
	sendForm(formId,url);
}

function WriteTo(id){
	var type = $F('outputType');
	switch (type)
	{
	case 'EXCEL':WriteToExcel(id);break;
	case 'WORD':WriteToWord(id);break;
	
	}
	return ;
}

function shortHandle(request){
	if (request.responseText=='ok')
	{
		alert('快捷方式添加成功！');
	}else {
		alert('快捷方式添加失败！');
	}
}
function shortcut(){
	var name	=	 window.prompt("输入该快捷方式的显示名称","");
	if (name !=null)
	{
	var url	=	location.href;
	SmartAjax.send(location.protocol+'//'+location.hostname+APP+'/Shortcut/ajaxInsert/','name='+name+'&url='+url,'',shortHandle);
	}

}

function show(){
	if (document.getElementById('menu').style.display!='none')
	{
	document.getElementById('menu').style.display='none';
	document.getElementById('main').className = 'full';
	}else {
	document.getElementById('menu').style.display='inline';
	document.getElementById('main').className = 'main';
	}
}

function allSelect(){
	var	colInputs = document.getElementsByTagName("input");
	for	(var i=0; i < colInputs.length; i++)
	{
		colInputs[i].checked= true;
	}
}
function allUnSelect(){
	var	colInputs = document.getElementsByTagName("input");
	for	(var i=0; i < colInputs.length; i++)
	{
		colInputs[i].checked= false;
	}
}

function InverSelect(){
	var	colInputs = document.getElementsByTagName("input");
	for	(var i=0; i < colInputs.length; i++)
	{
		colInputs[i].checked= !colInputs[i].checked;
	}
}

function CheckAll(strSection)
	{
		var i;
		var	colInputs = document.getElementById(strSection).getElementsByTagName("input");
		for	(i=1; i < colInputs.length; i++)
		{
			colInputs[i].checked=colInputs[0].checked;
		}
	}
function add(){
 location.href  = URL+"/add/";
}

function showHideSearch(){
	if (document.getElementById('searchM').style.display=='inline')
	{
		document.getElementById('searchM').style.display='none';
		document.getElementById('showText').value ='高 级';
		document.getElementById('key').style.display='inline';
	}else {
		document.getElementById('searchM').style.display='inline';
		document.getElementById('showText').value ='隐 藏';
		document.getElementById('key').style.display='none';

	}
}

function top(id){
	var keyValue;
	if (id)
	{
		keyValue = id;
	}else {
		keyValue = getSelectCheckboxValues();
	}
	if (!keyValue)
	{
		alert('请选择置顶项！');
		return false;
	}
	$('result').style.display = 'block';
	SmartAjax.send(URL+"/top",'ajax=1&id='+keyValue,'result');

}

function recommend(id){
	var keyValue;
	if (id)
	{
		keyValue = id;
	}else {
		keyValue = getSelectCheckboxValues();
	}
	if (!keyValue)
	{
		alert('请选择推荐项！');
		return false;
	}
	$('result').style.display = 'block';
	SmartAjax.send(URL+"/recommend",'ajax=1&id='+keyValue,'result');
	//location.href = URL+"/recommend/id/"+keyValue;

}

function sortBy (field,sort){
	location.href = URL+"/index/order/"+field+"/sort/"+sort;
}

function forbid(id){
	location.href = URL+"/forbid/id/"+id;
}

function resume(id){
	location.href = URL+"/resume/id/"+id;
}
function output(){
	location.href = URL+"/output/";
}
function member(id){
	location.href = URL+"/../Member/edit/id/"+id;
}
function chat(id){
	location.href = URL+"/../Chat/index/girlId/"+id;
}
function login(id){
	location.href = URL+"/../Login/index/type/4/id/"+id;
}
function child(id){
	location.href = URL+"/index/pid/"+id;
}


function access(id){
	location.href= URL+"/access/id/"+id;
}
function app(id){
	location.href = URL+"/app/groupId/"+id;
}

function module(id){
	location.href = URL+"/module/groupId/"+id;
}
function action(id){
	location.href = URL+"/action/groupId/"+id;
}

function sapp(id){
	$('subFrame').src =  URL+"/sapp/groupId/"+id;
}

function smodule(id){
	$('subFrame').src =  URL+"/smodule/groupId/"+id;
}
function saction(id){
	$('subFrame').src =  URL+"/saction/groupId/"+id;
}

function user(id){
	location.href = URL+"/user/id/"+id;
}

function suser(id){
	$('subFrame').src =  URL+"/suser/id/"+id;
}
	//+---------------------------------------------------
	//|	打开模式窗口，返回新窗口的操作值
	//+---------------------------------------------------
	function PopModalWindow(url,width,height)
	{
		var result=window.showModalDialog(url,"win","dialogWidth:"+width+"px;dialogHeight:"+height+"px;center:yes;status:no;scroll:no;dialogHide:no;resizable:no;help:no;edge:sunken;");
		return result;
	}

function read(id){
	var keyValue;
	if (id)
	{
		keyValue = id;
	}else {
		keyValue = getSelectCheckboxValue();
	}
	if (!keyValue)
	{
		alert('请选择编辑项！');
		return false;
	}
	location.href =  URL+"/read/id/"+keyValue;
}

function edit(id){
	var keyValue;
	if (id)
	{
		keyValue = id;
	}else {
		keyValue = getSelectCheckboxValue();
	}
	if (!keyValue)
	{
		alert('请选择编辑项！');
		return false;
	}
	location.href =  URL+"/edit/id/"+keyValue;
}

function sedit(id){
	var keyValue;
	if (id)
	{
		keyValue = id;
	}else {
		keyValue = getSelectCheckboxValue();
	}
	if (!keyValue)
	{
		alert('请选择编辑项！');
		return false;
	}
	$('subFrame').src =  URL+"/sedit/id/"+keyValue;
}



var selectRowIndex = Array();
function del(id){
	var keyValue;
	if (id)
	{
		keyValue = id;
	}else {
		keyValue = getSelectCheckboxValues();
	}
	if (!keyValue)
	{
		alert('请选择删除项！');
		return false;
	}

	if (window.confirm('确实要删除选择项吗？'))
	{
		$('result').style.display = 'block';
		SmartAjax.send(URL+"/delete/","id="+keyValue+'&_AJAX_SUBMIT_=1','result');
		var Table = $('checkList');
		var len	=	selectRowIndex.length;
		for (var i=len-1;i>=0;i-- )
		{
			//删除表格行
			Table.deleteRow(selectRowIndex[i]);
		}
		selectRowIndex = Array();

	//location.href = URL+"/delete/id/_AJAX_SUBMIT_/1"+keyValue;
	}
}

function publish(id){
	var keyValue;
	if (id)
	{
		keyValue = id;
	}else {
		keyValue = getSelectCheckboxValues();
	}
	if (!keyValue)
	{
		alert('请选择要发布的文章！');
		return false;
	}

	$('result').style.display = 'block';
	SmartAjax.send(URL+"/publish/","id="+keyValue+'&_AJAX_SUBMIT_=1','result');
}

	function delAttach(id,showId){
	var keyValue;
	if (id)
	{
		keyValue = id;
	}else {
		keyValue = getSelectCheckboxValues();
	}
	if (!keyValue)
	{
		alert('请选择删除项！');
		return false;
	}

	if (window.confirm('确实要删除选择项吗？'))
	{
		$('result').style.display = 'block';
		SmartAjax.send(URL+"/delAttach/","id="+keyValue+'&_AJAX_SUBMIT_=1','result');
		if (showId != undefined)
		{
			$(showId).innerHTML = '';
		}
	}
}

function clearData(){
	if (window.confirm('确实要清空全部数据吗？'))
	{
	location.href = URL+"/clear/";
	}
}
function takeback(id){
	var keyValue;
	if (id)
	{
		keyValue = id;
	}else {
		keyValue = getSelectCheckboxValues();
	}
	if (!keyValue)
	{
		alert('请选择回收项！');
		return false;
	}

	if (window.confirm('确实要回收选择项吗？'))
	{
	location.href = URL+"/takeback/id/"+keyValue;
	}
}


function getSelectCheckboxValue(){
	var obj = document.getElementsByName('key');
	var result ='';
	for (var i=0;i<obj.length;i++)
	{
		if (obj[i].checked==true)
				return obj[i].value;

	}
	return false;
}

function getSelectCheckboxValues(){
	var obj = document.getElementsByName('key');
	var result ='';
	var j= 0;
	for (var i=0;i<obj.length;i++)
	{
		if (obj[i].checked==true){
				selectRowIndex[j] = i+1;
				result += obj[i].value+",";
				j++;
		}
	}
	return result.substring(0, result.length-1);
}

 function   change()   
  {   
	  var   oObj   =   event.srcElement;   
	  if(oObj.tagName.toLowerCase()   ==   "td")   
	  {   
		  	  /*
	  var   oTable   =   oObj.parentNode.parentNode;   
	  for(var   i=1;   i<oTable.rows.length;   i++)   
	  {   
	  oTable.rows[i].className   =   "out";   
	  oTable.rows[i].tag   =   false;   
	  }   */
	var obj= document.getElementById('checkList').getElementsByTagName("input");
	  var   oTr   =   oObj.parentNode; 
	  var row = oObj.parentElement.rowIndex-1;
	  if (oTr.className == 'down')
	  {
		  	oTr.className   =   'out';   
			obj[row].checked = false;
		    oTr.tag   =   true;  
	  }else {
			oTr.className   =   'down';   
			obj[row].checked = true;
		    oTr.tag   =   true;  
	  }
 	  }   
  }   
    
  function   out()   
  {   
  var   oObj   =   event.srcElement;   
  if(oObj.tagName.toLowerCase()   ==   "td")   
  {   
  var   oTr   =   oObj.parentNode;   
  if(!oTr.tag)   
  oTr.className   =   "out";   
  }   
  }   
    
  function   over()   
  {   
  var   oObj   =   event.srcElement;   
  if(oObj.tagName.toLowerCase()   ==   "td")   
  {   
  var   oTr   =   oObj.parentNode;   
  if(!oTr.tag)   
  oTr.className   =   "over";   
  }   
  }   


//---------------------------------------------------------------------
// 多选改进方法 by Liu21st at 2005-11-29
// 
//
//-------------------------begin---------------------------------------

function searchItem(item){
	for(i=0;i<selectSource.length;i++)
		if (selectSource[i].text.indexOf(item)!=-1)
		{selectSource[i].selected = true;break;}
}

function addItem(){
	for(i=0;i<selectSource.length;i++)
		if(selectSource[i].selected){
			selectTarget.add( new Option(selectSource[i].text,selectSource[i].value));
			}
		for(i=0;i<selectTarget.length;i++)
			for(j=0;j<selectSource.length;j++)
				if(selectSource[j].text==selectTarget[i].text)
					selectSource[j]=null;
}

function delItem(){
	for(i=0;i<selectTarget.length;i++)
		if(selectTarget[i].selected){
		selectSource.add(new Option(selectTarget[i].text,selectTarget[i].value));
		
		}
		for(i=0;i<selectSource.length;i++)
			for(j=0;j<selectTarget.length;j++)
			if(selectTarget[j].text==selectSource[i].text) selectTarget[j]=null;
}

function delAllItem(){
	for(i=0;i<selectTarget.length;i++){
		selectSource.add(new Option(selectTarget[i].text,selectTarget[i].value));
		
	}
	selectTarget.length=0;
}
function addAllItem(){
	for(i=0;i<selectSource.length;i++){
		selectTarget.add(new Option(selectSource[i].text,selectSource[i].value));
		
	}
	selectSource.length=0;
}

function getReturnValue(){
	for(i=0;i<selectTarget.length;i++){
		selectTarget[i].selected = true;
	}
}

function loadBar(fl)
//fl is show/hide flag
{
  var x,y;
  if (self.innerHeight)
  {// all except Explorer
    x = self.innerWidth;
    y = self.innerHeight;
  }
  else 
  if (document.documentElement && document.documentElement.clientHeight)
  {// Explorer 6 Strict Mode
   x = document.documentElement.clientWidth;
   y = document.documentElement.clientHeight;
  }
  else
  if (document.body)
  {// other Explorers
   x = document.body.clientWidth;
   y = document.body.clientHeight;
  }

    var el=document.getElementById('loader');
	if(null!=el)
	{
		var top = (y/2) - 50;
		var left = (x/2) - 150;
		if( left<=0 ) left = 10;
		el.style.visibility = (fl==1)?'visible':'hidden';
		el.style.display = (fl==1)?'block':'none';
		el.style.left = left + "px"
		el.style.top = top + "px";
		el.style.zIndex = 2;
	}
}