<?php 
/*
+---------------------------------------------------------+
| 项目: FCS -- Fast,Compatible & Simple OOP PHP Framework |
| 文件: index.php										  |
| 功能: 项目入口文件									  |
+---------------------------------------------------------+
| 本框架代码基于GPL协议									  |
| 版权声明: Copyright◎ 2005-2006 世纪流年 版权所有		  |
| 主 页:	http://www.liu21st.com						  |
| 作 者:	Liu21st <流年> liu21st@gmail.com			  |
+---------------------------------------------------------+
*/

$beginTime = array_sum(split(' ', microtime()));
// 定义FCS框架路径和网站路径 （使用绝对路径
define('FCS_PATH', dirname(__FILE__).'/FCS');
define('WEB_ROOT', dirname(__FILE__));
//定义项目名称，如果不定义，默认为入口文件名称
define('APP_NAME', 'Lab');
// 加载FCS框架入口文件
require(FCS_PATH."/FCS.php");
//实例化一个网站应用实例
$App = & new App(); 
$App->Init();
$App->Exec();
echo '<div>Process: '.number_format((array_sum(split(' ', microtime())) - $beginTime), 6).'s</div>';
?>