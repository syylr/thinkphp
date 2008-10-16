<?php
// 视图模型
class IndexAction extends Action{
	public function index() {
		$Blog	=	D("BlogView");
		// 日志列表
		$list	=	$Blog->field('id,cTime,category,title,readCount,commentCount')->order('id desc')->findAll();
		$this->assign('list',$list);
		$this->display();
	}
}
?>