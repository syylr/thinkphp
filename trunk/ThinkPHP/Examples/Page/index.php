<?php 

// 定义ThinkPHP框架路径
define('THINK_PATH', '../../');
//定义项目名称
define('APP_NAME', 'Page');
define('APP_PATH', '.');
// 加载框架公共入口文件 
require(THINK_PATH."/ThinkPHP.php");

//实例化一个网站应用实例
$App = new App(); 
//应用程序初始化
$App->run();
?>