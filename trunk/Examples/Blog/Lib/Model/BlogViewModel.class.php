<?php 

class BlogViewModel extends Model 
{
	protected $viewModel = true;
	protected $masterModel = 'Blog';
	protected $viewFields = array(
		'Blog'=>array('id','name','title','cTime','categoryId','content','readCount','tags','commentCount','status'),
		'Category'=>array('title'=>'category'),
		);
	protected $viewCondition = array("Blog.categoryId" => array('eqf',"Category.id"));
	protected $blobFields = array('content');
	public function getPk() {
		return 'id';
	}

}
?>