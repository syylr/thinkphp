<?php 
define('THINK_PATH', '../ThinkPHP');
define('WEB_ROOT','../');

//定义项目名称，如果不定义，默认为入口文件名称
define('APP_NAME', 'Admin');
define('APP_PATH', '.');

// 加载框架公共入口文件 
require(THINK_PATH."/ThinkPHP.php");
require("../config.php");
//实例化一个网站应用实例
$App = App::getInstance(); 
// 执行应用程序
$App->run();
?>