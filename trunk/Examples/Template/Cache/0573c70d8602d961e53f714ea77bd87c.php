<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
 <HEAD>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <TITLE>ThinkPHP示例：内置模板引擎</TITLE>
<link rel='stylesheet' type='text/css' href='__PUBLIC__/Css/common.css'>
 </HEAD>
 <BODY>
 <div class="main">
 <H2>ThinkPHP示例之：内置模板引擎的使用</H2>
 ThinkPHP内置的模板引擎是一个自主创新的XML编译性模板引擎，这里仅仅演示常用的模板标签的用法，包括变量输出、循环、判断、比较等，这些都是比较基础的用法，无法包括ThinkPHP内置模板引擎的全部标签和特性。
 <TABLE  cellpadding=3 cellspacing=3>
 <TR>
	<TD class="tLeft" >
	<pre style="border:1px solid silver;background:#EEEEFF">
	普遍变量输出
	num1=	{$num1}
	数组输出
	id:{$vo['id']} 
	name:{$vo['name']} 
	email:{$vo['email']} 
	对象输出：
	id:{$obj:id} 
	name:{$obj:name} 
	email:{$obj:email} 
	对变量使用函数(可以使用内置函数或者自定义函数)
	{$vo.name|strtolower|ucwords}
	系统常量
	{$Think.now|date='Y-m-d H:i:s',###}
	{$Think.server.PHP_SELF}
	{$Think.session.name}
	Foreach 输出
	< foreach name="vo" key="key" item="item" >
	{$key}:{$item}
	< /foreach></pre>
	
	普通变量输出：num1=	<?php echo ($num1); ?><BR>
	数组变量输出：<BR>id:<?php echo ($vo['id']); ?> <BR>name:<?php echo ($vo['name']); ?> <BR>email:<?php echo ($vo['email']); ?> <BR>
		对象输出：<BR>
	id:<?php echo ($obj->id); ?> <BR>
	name:<?php echo ($obj->name); ?> <BR>
	email:<?php echo ($obj->email); ?> <BR>
	对变量使用函数<BR>
	<?php echo (ucwords(strtolower($vo["name"]))); ?><BR>
		系统常量<BR>
	<?php echo (date('Y-m-d H:i:s',date('Y-m-d g:i a',time()))); ?><BR>
		<?php echo ($_SERVER['PHP_SELF']); ?><BR>
			<?php echo ($_SESSION['name']); ?><BR>
	Foreach 输出<BR>
	<?php if(isset($vo)): ?><?php foreach($vo as $key=>$item): ?><?php echo ($key); ?>:<?php echo ($item); ?><BR><?php endforeach; ?><?php endif; ?>
<P>
	下面这个例子使用了循环标签、Switch标签、比较标签，请去掉标签开头的空白<pre style="border:1px solid silver;background:#EEEEFF">
	< volist name="array" id="val" key="i">[{$i}]
	< eq name="odd" value="1" >偶数行< /eq>
	< eq name="odd" value="0" >奇数行< /eq>
	< gt name="val" value="5">{$val}大于5< /gt>
	< if condition=" $val gt 15" > {$val}大于15
	< elseif condition="$val lt 10" />{$val}小于10
	< /if>
	< switch name="val">
	< case value="1" >数据{$val}< /case>
	< case value="2">数据{$val}< /case>
	< case value="3">数据{$val}< /case>
	< default />其他数据是 {$val}
	< /switch>
	< /volist></pre>
	

	<?php if(is_array($array)): ?><?php $i = 0;?><?php $__LIST__ = $array?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$val): ?><?php ++$i;?><?php $mod = ($i % 2 )?>[<?php echo ($i); ?>]
	<?php if(($odd)  ==  "1"): ?>偶数行<?php endif; ?>
	<?php if(($odd)  ==  "0"): ?>奇数行<?php endif; ?>
	<?php if(($val)  >  "5"): ?><?php echo ($val); ?>大于5<?php endif; ?>
	<?php if( $val > 15): ?><?php echo ($val); ?>大于15
	<?php elseif($val < 10): ?><?php echo ($val); ?>小于10<?php endif; ?>
	<?php switch(strlen($val)): ?><?php case "1":  ?>长度为1<?php break;?>
	<?php case "2":  ?>长度为2<?php break;?>
	<?php case "3":  ?>长度为3<?php break;?>
	<?php default: ?>长度大于3<?php endswitch;?><BR><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
<HR>
	
比较标签<pre style="border:1px solid silver;background:#EEEEFF">
< gt name="num1" value="3">大于3< /gt>
< lt name="num2" value="3">小于3< /lt>
</pre>
	
	
	<?php if(($num1)  >  "3"): ?>大于3<?php endif; ?><BR>
	<?php if(($num2)  <  "3"): ?>小于3<?php endif; ?><BR>

	条件判断<pre style="border:1px solid silver;background:#EEEEFF">
	< if condition=" $num gt 5" > {$num}大于5
	< elseif condition="$num gt 3" />{$num}大于3
	< else />其他{$num}
	< /if></pre>
	

	<?php if( $num > 5): ?><?php echo ($num); ?>大于5
	<?php elseif($num > 3): ?><?php echo ($num); ?>大于3
	<?php else: ?>其他<?php echo ($num); ?><?php endif; ?>
	</TD>
 </TR>
 <TR>
	<TD ><HR> 示例源码<BR>控制器IndexAction类<BR><?php highlight_file(LIB_PATH.'Action/IndexAction.class.php'); ?>
	</TD>
 </TR>
 </TABLE>
</div>
 </BODY>
</HTML>