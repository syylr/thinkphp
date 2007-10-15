<?php 
import('@.Model.CommonModel');
class CategoryModel extends CommonModel 
{
	var $_validate	 =	 array(
		array('title','require','类别名称必须！'),
		);
}
?>