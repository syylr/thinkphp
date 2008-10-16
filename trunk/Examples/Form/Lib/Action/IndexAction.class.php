<?php
class IndexAction extends Action{
	// 首页
	public function index(){
		$Form	= D("Form");
        // 按照id排序显示前6条记录
		$list	=	$Form->order('id desc')->top6();
		$this->assign('list',$list);
		$this->display();
	}

	// 处理表单数据
	public function insert() {
		$Form	=	D("Form");
		if($Form->create()) {
			$Form->add();
			$this->redirect();
		}else{
			header("Content-Type:text/html; charset=utf-8");
			exit($Form->getError().' [ <A HREF="javascript:history.back()">返 回</A> ]');
		}
	}
	// 生成验证码
	public function verify() {
        import("ORG.Util.Image");
       	Image::buildImageVerify();
	}
}
?>