<?php 
/*
+---------------------------------------------------------+
| 项目: FCS -- Fast,Compatible & Simple OOP PHP Framework |
| 文件: FCS.php											  |
| 功能: FCS公共入口文件									  |
+---------------------------------------------------------+
| 本框架代码基于GPL协议									  |
| 版权声明: Copyright◎ 2005-2006 世纪流年 版权所有		  |
| 主 页:	http://www.liu21st.com						  |
| 作 者:	Liu21st <流年> liu21st@gmail.com			  |
+---------------------------------------------------------+
*/
//FCS目录定义
if(!defined('FCS_PATH')) define('FCS_PATH', dirname(__FILE__));

//加载公共配置文件和公共函数库
include_once(FCS_PATH."/Conf/config.ini.php");
include_once(FCS_PATH."/Common/functions.php");

//加载FCS核心基类
import("FCS.core.Base");
import("FCS.core.*");
import("FCS.exception.FCSException");
?>