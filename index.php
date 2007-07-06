<?php 
/*
+--------------------------------------------------------
| 项目: FCS -- Fast,Compatible & Simple OOP PHP Framework 
| 版本: 0.6.1 
| PHP:	4.3.0 以上
| 文件: index.php
| 功能:  网站入口文件
| 最后修改：2006-2-23
+--------------------------------------------------------
| 版权声明: Copyright◎ 2005-2006 世纪流年 版权所有
| 主 页:	http://www.liu21st.com
| 作 者:	Liu21st <流年> liu21st@gmail.com 
+--------------------------------------------------------
*/
//$beginTime = array_sum(split(' ', microtime()));
include_once("./public.php");
$App = & new App(); 
$App->init();
$App->run();
//echo '<div>Process: '.number_format((array_sum(split(' ', microtime())) - $beginTime), 6).'s</div>';
?>