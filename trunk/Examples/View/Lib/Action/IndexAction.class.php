<?php 
// 视图模型
class IndexAction extends Action{
	public function index() {
		$Blog	=	D("BlogView");
		// 日志列表
		$list	=	$Blog->top5('','id,cTime,category,title,readCount,commentCount','id desc');
		$this->assign('list',$list);
		$this->display();
	}
} 
?>