<?php
/*
Plugin Name: info
Plugin URI: http://thinkphp.cn/
Description: action示例插件2
Author: yhustc
Version: 1.0
Author URI: http://www.yhustc.com/
*/

function showInfo()
{
	echo "<p>插件的函数的参数是可以自定义的</p>";
}

add_action("demo_hook","showInfo");
?>