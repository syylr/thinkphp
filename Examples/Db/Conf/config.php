<?php
if (!defined('THINK_PATH')) exit();
$config  =   require '../config.php';
$array   =  array(
		'DEBUG_MODE'=>TRUE,
        );
return array_merge($config,$array);

?>