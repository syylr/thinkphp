<?php 
class IndexAction extends Action {
	// 首页显示数据列表
	public function index() {
		$Blob	= D("Blob");
		$list	=	$Blob->top6('','*','id desc');
		$this->assign('list',$list);
		$this->display();
	}

	// 保存数据 自动处理文本字段
	public function insert() {
		$Blob	=	D("Blob");
		if($Blob->create()) {
			$Blob->add();
			$this->redirect();
		}else{
			header("Content-Type:text/html; charset=utf-8");
			exit($Blob->getError().' [ <A HREF="javascript:history.back()">返 回</A> ]');
		}
	}
}
?>