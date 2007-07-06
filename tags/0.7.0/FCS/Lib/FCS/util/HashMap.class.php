<?php
/*
+---------------------------------------------------------+
| 项目: FCS -- Fast,Compatible & Simple OOP PHP Framework |
| 文件: HashMap.class.php								  |
| 功能: HashMap类										  |
+---------------------------------------------------------+
| 本框架代码基于GPL协议，可免费使用，但必须保留版权信息	  |
| 版权声明: Copyright◎ 2005-2006 世纪流年 版权所有		  |
| 主 页:	http://www.liu21st.com						  |
| 作 者:	Liu21st <流年> liu21st@gmail.com			  |
+---------------------------------------------------------+
*/
import("FCS.util.ListIterator");

class HashMap extends ListIterator {

	//+----------------------------------------
	//|	属性
	//+----------------------------------------
	var $_values = array();

	//+----------------------------------------
	//|	架构函数
	//+----------------------------------------
	function __construct($values = array())
	{
		if (!empty($values)) {
			$this->_values = $values;
		}
	}

	//+----------------------------------------
	//|	清楚所有Map
	//+----------------------------------------
	function clear()
	{
		$this->_values = array();
	}

	//+----------------------------------------
	//|	Map中是否指定key
	//+----------------------------------------
	function containsKey($key)
	{
		return array_key_exists($key, $this->_values);
	}

	//+----------------------------------------
	//|	Map是否包含指定value
	//+----------------------------------------
	function containsValue($value)
	{
		return in_array($value, $this->_values);
	}

	//+----------------------------------------
	//|	Map中是否包含指定的key和对应的value
	//+----------------------------------------
	function contains($key, $value)
	{
		if ($this->containsKey($key))
		{
			return ($this->get($key) == $value);
		}
		return false;
	}

	//+----------------------------------------
	//|	根据Key取得Map中的value
	//+----------------------------------------
	function get($key)
	{
		if ($this->containsKey($key)) {
			return $this->_values[$key];
		} else {
			return null;
		}
	}
	//+----------------------------------------
	//|	Map是否为空
	//+----------------------------------------
	function isEmpty()
	{
		return empty($this->_values);
	}

	//+----------------------------------------
	//|	转换为数组
	//+----------------------------------------
	function toArray()
	{
		return $this->_values;
	}

	//+----------------------------------------
	//|	返回Map中的key数组
	//+----------------------------------------
	function keySet()
	{
		return array_keys($this->_values);
	}

	//+----------------------------------------
	//|	放入指定的Key和value到Map
	//+----------------------------------------
	function put($key, $value)
	{
		$previous = $this->get($key);
		$this->_values[$key] =&$value;
		return $previous;
	}

	//+----------------------------------------
	//|	批量放入values到Map
	//+----------------------------------------
	function putAll($values)
	{
		if (is_array($values)) {
			foreach ($values as $key => $value) {
				$this->put($key, $value);
			}
		}
	}

	//+----------------------------------------
	//|	根据key移出Map中value
	//+----------------------------------------
	function remove($key)
	{
		$value = $this->get($key);
		if (!is_null($value)) { unset($this->_values[$key]); }
		return $value;
	}

	//+----------------------------------------
	//|	Map中key长度
	//+----------------------------------------
	function size()
	{
		return count($this->_values);
	}

	//+----------------------------------------
	//|	Map中value数组
	//+----------------------------------------
	function values()
	{
		return array_values($this->_values);
	}

}
?>
