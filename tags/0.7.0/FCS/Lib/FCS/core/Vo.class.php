<?php 
/*
+---------------------------------------------------------+
| 项目: FCS -- Fast,Compatible & Simple OOP PHP Framework |
| 文件: Vo.class.php									  |
| 功能: 数据对象基础类									  |
+---------------------------------------------------------+
| 本框架代码基于GPL协议，可免费使用，但必须保留版权信息	  |
| 版权声明: Copyright◎ 2005-2006 世纪流年 版权所有		  |
| 主 页:	http://www.liu21st.com						  |
| 作 者:	Liu21st <流年> liu21st@gmail.com			  |
+---------------------------------------------------------+
*/
import("FCS.util.HashMap");

class Vo extends Base{

	//+----------------------------------------
	//|	公共变量
	//+----------------------------------------
	// 不需要定义任何公共属性，否则会导致错误

	//+----------------------------------------
	//|	架构函数
	//+----------------------------------------
	function __construct($Data=NULL)
	{
		//把Map对象或者关联数组转换成Vo的属性
		if( is_a($Data,'HashMap')){
			$keys = array_values( $Data->keySet() );
			$vals = array_values( $Data->Values() );
		}
		else if(is_object($Data)){
			$Map = get_object_vars($Data);
			$keys = array_keys( $Map );
			$vals = array_values( $Map );
		}
		else if(is_array($Data)){
			$keys = array_keys( $Data );
			$vals = array_values( $Data );
		}
		if(isset($keys) && is_array($keys)){
			foreach($keys as $index => $key){
			   if(property_exists($this,$key))
				   $this->$key = $vals[$index];
			}
		}
	}

	//+----------------------------------------
	//|	toMap
	//+----------------------------------------
	function toMap()
	{
		$map= new HashMap(get_object_vars($this));
		return $map;
	}
};
?>