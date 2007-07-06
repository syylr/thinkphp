<?php 
/*
+--------------------------------------------------------
| 项目: 6Path | Simple OOP PHP Framework
| 版本: 0.6.0
| PHP:	4.3.0 以上或者 5.0
| 文件: dbconfig.php
| 功能:  数据库配置文件
| 最后修改：2006-1-19
+--------------------------------------------------------
| 版权声明: Copyright◎ 2004-2005 世纪流年 版权所有
| 主 页:	http://www.liu21st.com
| 作 者:	Liu21st <流年> liu21st@gmail.com 
+--------------------------------------------------------
*/
if (!defined('FCS_PATH')) {
	exit();
}

//数据库设置 MYSQL
define('DB_TYPE', 'mysqlite');
define('DB_HOST', "localhost");
define('DB_NAME',"information_schema");
define('DB_USER',"root");
define('DB_PWD',"");
define('DB_COMPANY',"");
define('DB_ADMINMAIL',"");

//用于textDb
define('DB_DATA_PATH',DATA_PATH);
define('DB_CACHE_PATH',FCS_PATH."/Temp/");
?>