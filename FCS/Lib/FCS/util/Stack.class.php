<?php
/*
+---------------------------------------------------------+
| 项目: FCS -- Fast,Compatible & Simple OOP PHP Framework |
| 文件: Stack.class.php								      |
| 功能: Stack类											  |
+---------------------------------------------------------+
| 本框架代码基于GPL协议，可免费使用，但必须保留版权信息	  |
| 版权声明: Copyright◎ 2005-2006 世纪流年 版权所有		  |
| 主 页:	http://www.liu21st.com						  |
| 作 者:	Liu21st <流年> liu21st@gmail.com			  |
+---------------------------------------------------------+
*/
import("FCS.util.ArrayList");

class Stack extends ArrayList
{
	// +----------------------------------------
	// |	架构函数
	// +----------------------------------------
	function __construct($values = array())
	{
		parent::__construct($values);
	}

	// +----------------------------------------
	// |	
	// +----------------------------------------
	function peek()
	{
		return reset($this->toArray());
	}

	// +----------------------------------------
	// |	最后一个元素出栈
	// +----------------------------------------
	function pop()
	{
		$el_array = $this->toArray();
		$return_val = array_pop($el_array);
		$this->_elements = $el_array;
		return $return_val;
	}

	//+----------------------------------------
	//|	元素进栈
	//+----------------------------------------
	function push($value)
	{
		$this->add($value);
		return $value;
	}
}
?>
