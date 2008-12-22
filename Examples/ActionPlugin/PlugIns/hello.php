<?php
/*
Plugin Name: hello
Plugin URI: http://thinkphp.cn/
Description: actionÊ¾Àý²å¼þ1
Author: yhustc
Version: 1.0
Author URI: http://www.yhustc.com/
*/

function hello($arg1,$arg2)
{
	echo "<p>{$arg1} {$arg2}</p>";
}

add_action("demo_hook","hello",10,"hello","thinkphp");
?>