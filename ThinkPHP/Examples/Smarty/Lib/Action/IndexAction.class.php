<?php 
// 第三方模板引擎
class IndexAction extends Action{
	public function index(){
		$this->assign('var','This is Smarty!');
		$this->assign('num',5);
		$this->assign('array',array('id'=>1,'name'=>'ThinkPHP','email'=>'liu21st@gmail.com'));
		$this->display();
	}
} 
?>