<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>系统发生错误</title>
<meta http-equiv="content-type" content="text/html;charset=utf-8"/>
<meta name="Generator" content="EditPlus"/>
<style>
body{
	font-family: Verdana;
	font-size:14px;
}
a{text-decoration:none;color:#174B73;}
a:hover{ text-decoration:none;color:#FF6600;}
h2{
	border-bottom:2px solid #DDD;
	padding:8px 0;
    font-size:25px;
}
.title{
	margin:4px 0;
	color:#F60;
	font-weight:bold;
}
.message,#trace{
	padding:1em;
	border:solid 1px #000;
	margin:10px 0;
	background:#FFD;
	line-height:150%;
}
.message{
	background:#FFD;
}
#trace{
	background:#E7F7FF;
}
.red{
	color:red;
	font-weight:bold;
}
</style>
</head>
<body>
<div class="notice">
<h2>系统发生错误 </h2>
<div >您可以选择 [ <A HREF="<?php echo($_SERVER['PHP_SELF'])?>">重试</A> ] [ <A HREF="javascript:history.back()">返回</A> ] 或者 [ <A HREF="<?php echo(__APP__);?>">回到首页</A> ]</div>
<p><strong>错误位置:</strong>　FILE: <span class="red"><?php echo $e['file'] ;?></span>　LINE: <span class="red"><?php echo $e['line'];?></span></p>
<p class="title">[错误信息]</p>
<p class="message"><?php echo $e['message'];?></p>
<?php if(isset($e['trace'])) {?>
<p class="title">[TRACE]</p>
<p id="trace">
<?php echo nl2br($e['trace']);?>
</p>	
<?php }?>
</div>
<div align="center" style="color:#FF3300;margin:5pt;font-weight:bold"> ThinkPHP <sup style='color:gray;font-size:9pt'><?php echo THINK_VERSION;?></sup><span style='color:silver'> { Fast,Compatible & Simple OOP PHP Framework }</span>
</div>
</body>
</html>