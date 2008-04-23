<?php 
class BlogViewModel extends Model 
{
	protected $viewModel = true;
	protected $viewFields = array(
		'Blog'=>array('id','name','title','cTime','categoryId','content','readCount','tags','commentCount','status'),
		'Category'=>array('title'=>'category'),
		);
	protected $viewCondition = array("Blog.categoryId" => array('eqf',"Category.id"));
	public function getPk() {
		return 'id';
	}
}
?>