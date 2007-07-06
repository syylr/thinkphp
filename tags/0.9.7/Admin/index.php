<?php 
define('THINK_PATH', '../ThinkPHP');
define('WEB_ROOT','../');

//定义项目名称，如果不定义，默认为入口文件名称
define('APP_NAME', 'Admin');
define('APP_PATH', '.');

// 加载FCS框架公共入口文件 
require(THINK_PATH."/ThinkPHP.php");

//实例化一个网站应用实例
require("../config.php");

$App = new App(); 
//应用程序初始化
$App->init();

//启动应用程序
$App->exec();

?>