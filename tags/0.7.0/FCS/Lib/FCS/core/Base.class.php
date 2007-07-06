<?php 
/*
+---------------------------------------------------------+
| 项目: FCS -- Fast,Compatible & Simple OOP PHP Framework |
| 文件: Base.class.php									  |
| 功能: FCS框架系统基类									  |
+---------------------------------------------------------+
| 本框架代码基于GPL协议，可免费使用，但必须保留版权信息	  |
| 版权声明: Copyright◎ 2005-2006 世纪流年 版权所有		  |
| 主 页:	http://www.liu21st.com						  |
| 作 者:	Liu21st <流年> liu21st@gmail.com			  |
+---------------------------------------------------------+
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
	//|	自动变量设置 支持PHP5
	//| 如果是PHP4直接设置对象的属性即可
	//+----------------------------------------
	function __set($name ,$value)
	{
		if(property_exists($this,$name)){
			$this->$name = $value;
		}
	}

	//+----------------------------------------
	//|	自动变量获取 支持PHP5 PHP5中不包含私有属性
	//| 如果是PHP4直接获取对象的属性即可
	//+----------------------------------------
	function __get($name)
	{
		if(property_exists($this,$name)){
			return $this->$name;
		}else {
			ThrowException("对象的属性[$name]不存在！");   
		}
	}

	//+----------------------------------------
	//| 用于PHP4获取对象的属性
	//+----------------------------------------
	function get($name)
	{
		return $this->__get($name);
	}

	//+----------------------------------------
	//| 用于PHP4设置对象的属性
	//+----------------------------------------
	function set($name,$value)
	{
		$this->__set($name,$value);
	}

   	//+----------------------------------------
	//|	输出对象属性列表
	//+----------------------------------------
    function __varList()
	{
	  return array_keys(get_object_vars($this));
	}

   	//+----------------------------------------
	//|	输出对象属性数组
	//+----------------------------------------
    function __toArray()
	{
	  return get_object_vars($this);
	}

   	//+----------------------------------------
	//|	输出对象初始化属性数组
	//+----------------------------------------
    function __toOraArray()
	{
	  return get_class_vars(get_class($this));
	}

   	//+----------------------------------------
	//|	对象输出 对象的类名
	//+----------------------------------------
    function __toString()
	{
	  return get_class($this);
	}

	//+----------------------------------------
	//|	自动调用方法 PHP5支持
	//+----------------------------------------
	function __call( $func, $args )    
	{        
		ThrowException("方法[ $func ]不存在或参数有误！");        
	}
};

?>