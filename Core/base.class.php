<?php 
/*
+--------------------------------------------------------
| 项目: FCS -- Fast,Compatible & Simple OOP PHP Framework
| 版本: 0.6.1
| PHP:	4.3.0 以上
| 文件: base.class.php
| 功能:  应用程序基类
| 最后修改：2006-2-9
+--------------------------------------------------------
| 版权声明: Copyright◎ 2005-2006 世纪流年 版权所有
| 主 页:	http://www.liu21st.com
| 作 者:	Liu21st <流年> liu21st@gmail.com 
+--------------------------------------------------------
*/
class Base{
	
	//+----------------------------------------
	//|	架构函数
	//+----------------------------------------
	function Base()
	{
		//让PHP4支持__construct和__destruct
	   $args = func_get_args();
       if (method_exists($this, '__destruct'))
       {
           register_shutdown_function(array(&$this, '__destruct'));
       }
       call_user_func_array(array(&$this, '__construct'), $args);
	}


   	//+----------------------------------------
	//|	对象输出
	//+----------------------------------------
    function toString()
	{
	  return get_class($this);
	}


};

?>