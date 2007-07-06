<?php 
/*
+--------------------------------------------------------
| 项目:  | 基于流年PHP开发框架
| 版本: 1.0.1
| PHP:	4.0 and 5.0
| 文件: index.php
| 功能: 框架首页文件 本页面不需要修改
| 最后修改时间：2002-9-13
+--------------------------------------------------------
| 版权声明: Copyright◎ 2004-2005 世纪流年 版权所有
| 主 页:	http://www.liu21st.com
| 作 者:	Liu21st <流年> liu21st@gmail.com 
+--------------------------------------------------------
*/
//$beginTime = array_sum(split(' ', microtime()));
include_once("./public.php");
$App = & new App('web'); 
$App->init();
$App->run();
//echo 'Process: '.number_format((array_sum(split(' ', microtime())) - $beginTime), 6).'s';
?>