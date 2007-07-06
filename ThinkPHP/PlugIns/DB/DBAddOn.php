<?php 
    /*
    Plugin Name: DBaddOn 
    Plugin URI: http://fcs.org.cn
    Description: 数据库支持插件
    Author: 流年
    Version: 1.0
    Author URI: http://blog.liu21st.com/
    */ 

//--------------------------------------------------
// DB_TYPE 数据库类型定义 
//--------------------------------------------------
// Mysql Mysqli 由框架内置支持
// 本插件还支持Mssql 、Oracle、PgSql、Sqlite
//--------------------------------------------------
/*
其他配置参数示例
define('DB_TYPE','mysql'); 
define('DB_HOST','localhost'); 
define('DB_NAME','fcs'); 
define('DB_USER','root'); 
define('DB_PWD',''); 
define('DB_PORT','3306'); 
define('DB_PREFIX','fcs'); 
*/
function addDB() 
{
    $driverPath    = dirname(__FILE__).'/Driver/';
    $dbClass   =  'Db_'.ucwords(strtolower(DB_TYPE));
    $result  =  include_cache($driverPath.$dbClass.'.class.php');
    if($result) {
        // 如果存在数据库驱动类 设置
    	Session::set(strtoupper(DB_TYPE),$dbClass);
    }
    return ;
}
if(defined('DB_TYPE')) {
	add_filter('app_init','addDB');
}
?>