<?php 
/*
+--------------------------------------------------------
| 项目: FCS -- Fast,Compatible & Simple OOP PHP Framework
| 版本: 0.6.1
| PHP:	4.3.0 以上
| 文件: ArrayList.class.php
| 功能: 数组列表类
|	//ArrayList是基于索引的，而不是键名的，要注意
|	//基于key的List可以使用HashMap类
| 最后修改：2006-2-23
+--------------------------------------------------------
| 版权声明: Copyright◎ 2005-2006 世纪流年 版权所有
| 主 页:	http://www.liu21st.com
| 作 者:	Liu21st <流年> liu21st@gmail.com 
+--------------------------------------------------------
*/
import("com.liu21st.util.ListIterator");
class ArrayList extends ListIterator{ //数组列表类


	//+----------------------------------------
	//|	属性
	//+----------------------------------------
	var $_elements = array();

	//+----------------------------------------
	//|	架构函数
	//+----------------------------------------
	function __construct($elements = array())
	{
		if (!empty($elements)) {
			$this->_elements = $elements;
		}
	}

	//+----------------------------------------
	//|	成员函数
	//+----------------------------------------
	function listIterator()
	{
		return new ListIterator($this->_elements);
	}

	//+----------------------------------------
	//|	增加数组
	//+----------------------------------------
	function add($element)
	{
		return (array_push($this->_elements, $element)) ? TRUE : FALSE;
	}

	//+----------------------------------------
	//|	添加数组列表
	//+----------------------------------------
	function addAll($list)
	{
		$before = $this->size();
		if (is_a($list, get_class($this))) {
			$iterator = $list->listIterator();
			while ($iterator->valid()) {
				$this->add($iterator->next());
			}
		}
		$after = $this->size();
		return ($before < $after);
	}

	//+----------------------------------------
	//|	清除数组列表
	//+----------------------------------------
	function clear()
	{
		$this->_elements = array();
	}

	//+----------------------------------------
	//|	是否包含数组
	//+----------------------------------------
	function contains($element)
	{
		return (array_search($element, $this->_elements)) ? TRUE : FALSE;
	}

	//+----------------------------------------
	//|	根据索引得到元素
	//+----------------------------------------
	function get($index)
	{
		return $this->_elements[$index];
	}

	//+----------------------------------------
	//|	查找匹配的第一个数组元素位置
	//+----------------------------------------
	function indexOf($element)
	{
		return array_search($element, $this->_elements);
	}

	//+----------------------------------------
	//|	数组列表是否为空
	//+----------------------------------------
	function isEmpty()
	{
		return empty($this->_elements);
	}

	//+----------------------------------------
	//|	最后一个匹配的数组元素位置
	//+----------------------------------------
	function lastIndexOf($element)
	{
		for ($i = (count($this->_elements) - 1); $i > 0; $i--) {
			if ($element == $this->get($i)) { return $i; }
		}
	}

	//+----------------------------------------
	//|	根据索引移出数组列表
	//+----------------------------------------
	function remove($index)
	{
		$element = $this->get($index);
		if (!is_null($element)) { array_splice($this->_elements, $index, 1); }
		return $element;
	}

	//+----------------------------------------
	//|	移出一定范围的数组列表
	//+----------------------------------------
	function removeRange($start, $end)
	{
		array_splice($this->_elements, $start, $end);
	}

	//+----------------------------------------
	//|	设置数组列表元素
	//+----------------------------------------
	function set($index, $element)
	{
		$previous = $this->get($index);
		$this->_elements[$index] = $element;
		return $previous;
	}

	//+----------------------------------------
	//|	获取数组列表长度
	//+----------------------------------------
	function size()
	{
		return count($this->_elements);
	}

	//+----------------------------------------
	//|	转换成数组
	//+----------------------------------------
	function toArray()
	{
		return $this->_elements;
	}

};
?>