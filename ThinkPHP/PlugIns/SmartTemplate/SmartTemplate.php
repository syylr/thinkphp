<?php
/*
Plugin Name: SmartTemplate
Template URI:
Description: SmartTemplate模版引擎插件
Author: LorryChenls
Version: 0.1
Author URI: http://www.chenliansheng.cn/
*/
function SmartTemplate($templateFile,$templateVar,$charset,$varPrefix='')
{
    $templateFile=substr($templateFile,strlen(TMPL_PATH));
    include_once(PLUGIN_PATH.'SmartTemplate/class.smarttemplate.php');
    $tpl = new SmartTemplate($templateFile);
    $tpl->caching = true;
    $tpl->template_dir = TMPL_PATH;
    $tpl->temp_dir = CACHE_PATH ;
    $tpl->cache_dir = TEMP_PATH ;
    $tpl->assign($templateVar);
    $tpl->output();
    return ;
}
if('SMART'== strtoupper(C('TMPL_ENGINE_TYPE'))) {
add_compiler('SmartTemplate','SmartTemplate');
}
?>