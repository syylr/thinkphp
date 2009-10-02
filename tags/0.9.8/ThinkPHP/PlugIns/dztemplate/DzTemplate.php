<?php
/*
Plugin Name: DZTemplate
Template URI: http://www.gdutbbs.com
Description: Template模版引擎插件仿Discuz
Author: lyhiving
Version: 1.0
Author URI: http://www.56.com/
*/

function DzTemplate($templateFile,$templateDir='default')
{
   // $templateFile = substr($templateFile,strlen(TMPL_PATH),-5);
    $CacheDir = substr(CACHE_PATH,0,-1);
    require_once("DzTemplate.class.php");
	$tpl = &new DzTemplate;
	$tpl->tpl_dir = TMPL_PATH.$templateDir;
	$tpl->tpl_default_dir = TMPL_PATH.'default';
	$tpl->tpl_refresh_time = TMPL_CACHE_TIME;
	$tpl->tpl_cache_dir = CACHE_PATH;
     return $tpl->tpl($templateFile);
}

function stripvtags($expr, $statement) {
	$expr = str_replace("\\\"", "\"", preg_replace("/\<\?php echo (\\\$.+?)\?\>/s", "\\1", $expr));
	$statement = str_replace("\\\"", "\"", $statement);
	return $expr . $statement;
}

if('DZ'== strtoupper(C('TMPL_ENGINE_TYPE'))) {
    add_compiler('Dz','DzTemplate');
}

?>