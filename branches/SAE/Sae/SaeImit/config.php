<?php
// +----------------------------------------------------------------------
// | sae模拟器配置
// +----------------------------------------------------------------------
// | Author: luofei614<www.3g4k.com>
// +----------------------------------------------------------------------
// $Id$
$appConfig=  include APP_PATH.'Conf/config.php';
return array(
    'db_host'=>$appConfig['DB_HOST'],
    'db_user'=>$appConfig['DB_USER'],
    'db_pass'=>$appConfig['DB_PWD'],
    'db_name'=>$appConfig['DB_NAME'],
    'db_charset'=>$appConfig['DB_CHARSET'],
    'storage_url'=>trim(dirname($_SERVER['SCRIPT_NAME']),'/\\').'/',
    'storage_dir'=>'./',
    'debug_file'=>APP_PATH."Runtime/Logs/sae_debug.log"
     
);