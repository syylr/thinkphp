<?php 
/*
+---------------------------------------------------------+
| 项目: FCS -- Fast,Compatible & Simple OOP PHP Framework |
| 文件: ResultSet.class.php								  |
| 功能: 数据集类										  |
+---------------------------------------------------------+
| 本框架代码基于GPL协议，可免费使用，但必须保留版权信息	  |
| 版权声明: Copyright◎ 2005-2006 世纪流年 版权所有		  |
| 主 页:	http://www.liu21st.com						  |
| 作 者:	Liu21st <流年> liu21st@gmail.com			  |
+---------------------------------------------------------+
*/
import("FCS.util.ListIterator");

class ResultSet extends ListIterator{

	// +----------------------------------------
	// |	私有属性
	// +----------------------------------------
	var $RS;				//数据集
	var $Row;				//当前行

	// +----------------------------------------
	// |	架构函数
	// +----------------------------------------
	function __construct(&$resultSet)
	{
		//直接使用父类的架构方法
		parent::__construct($resultSet);
		$this->RS = &$this->_values;
		$this->Row = &$this->_index;
	}

	// +----------------------------------------
	// |	取得某行记录
	// +----------------------------------------
	function get($row=0){
		return $this->RS[$row];
	}

	// +----------------------------------------
	// |	设置某行记录
	// +----------------------------------------
	function set($row,$value){
		$this->RS[$row] = $value;
	}

	//+----------------------------------------
	//|	取出一定范围的数组列表
	//+----------------------------------------
	function getRange($offset,$length=NULL)
	{
		return array_slice($this->RS,$offset,$length);
	}
};
?>