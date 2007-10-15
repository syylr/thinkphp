<?php 

class BlogViewModel extends Model 
{
	var $viewModel = true;
	var $masterModel = 'Blog';
	var $viewFields = array(
		'Blog'=>array('id','name','title','cTime','categoryId','content','readCount','commentCount','status'),
		'Category'=>array('title'=>'category'),
		);
	var $viewCondition = array("Blog.categoryId" => array('eqf',"Category.id"));
	var $blobFields = array('content');
	function getPk() {
		return 'id';
	}

}
?>