<?php


------------------------------------------------------------------------------


------------------------------------------------------------------------------

if (!defined('THINK_PATH')) exit();
$config  =   require '../config.php';
$array   =  array(
		'DEBUG_MODE'=>TRUE, //		开启调试模式
        );
return array_merge($config,$array);

