<?php 
// 模板引擎使用
class IndexAction extends Action{
	public function index() {
		$_SESSION['name']	=	'ThnkPHP Session';
		$vo	=	array('id'=>1,'name'=>'ThinkPHP','email'=>'liu21st@gmail.com');
		$this->assign('vo',$vo);
		$obj	=	(object)$vo;
		$this->assign('obj',$obj);
		$this->assign('array',array(5,260,13,7,40,50,2,1));
		$this->assign('num1',6);
		$this->assign('num2',2);
		$this->assign('num',6);
		$this->display();
	}
} 
?>