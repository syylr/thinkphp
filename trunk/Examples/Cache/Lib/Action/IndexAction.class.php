<?php
// 本类由系统自动生成，仅供测试用途
class IndexAction extends Action{
	public function index(){
		if(S('list')) {
			$list	=	S('list');
		}else{
			$Form	=	D("Form");
			$list	=	$Form->findAll();
			S('list',$list);
		}
		$this->assign('list',$list);
		$this->display();
	}
}
?>