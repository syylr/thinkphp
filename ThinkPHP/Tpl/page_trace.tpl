<div id="think_page_trace" style="background:white;margin:6px;font-size:14px;border:1px dashed silver;padding:8px">
<fieldset id="querybox" style="margin:5px;">
<legend style="color:gray;font-weight:bold">页面Trace信息</legend>
<div style="overflow:auto;height:300px;text-align:left;">
<?php foreach ($trace as $key=>$info){
if(is_array($info)){
echo $key.':<br/>';
 foreach ($info as $k=>$val){
 echo $k.' : '.(!is_scalar($val)?print_r($val,true):$val).'<br/>';
 }
}else{
echo $key.' : '.(!is_scalar($info)?print_r($info,true):$info).'<br/>';
}
}?>
</div>
</fieldset>
</div>