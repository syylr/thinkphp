<?php 
/*
+---------------------------------------------------------+
| 项目: FCS -- Fast,Compatible & Simple OOP PHP Framework |
| 文件: Log.class.php									  |
| 功能: Log处理类										  |
+---------------------------------------------------------+
| 本框架代码基于GPL协议，可免费使用，但必须保留版权信息	  |
| 版权声明: Copyright◎ 2005-2006 世纪流年 版权所有		  |
| 主 页:	http://www.liu21st.com						  |
| 作 者:	Liu21st <流年> liu21st@gmail.com			  |
+---------------------------------------------------------+
*/

class Log extends Base{

	//+----------------------------------------
	//|	架构函数
	//+----------------------------------------
	function __construct()
	{

	}

	//+----------------------------------------
	//|	日志写入
	//+----------------------------------------
	function Write($message,$type=WEB_LOG_ERROR,$file=''){
		if(WEB_LOG_RECORD){
			$now = date('[ y-m-d H:i:s ]');
			switch($type){
				case WEB_LOG_DEBUG:
					$logType ='[调试]';
					$destination = $file == ''? LOG_PATH."systemOut.log" : $file;
					break;
				default :
					$logType ='[错误]';
					$destination = $file == ''? LOG_PATH."systemErr.log" : $file;
			}
			if(!is_dir(LOG_PATH)){
				mkdir(LOG_PATH);
			}
			if(!is_writable(LOG_PATH)){
				halt('目录(文件)'.$destination.'不可写');
			}
			error_log($now.$message."\n\r", FILE_LOG,$destination );

		}
	}
};
?>