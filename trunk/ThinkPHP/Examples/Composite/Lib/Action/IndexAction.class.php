<?php 
class IndexAction extends Action{
	// 首页
	public function index(){
		$Form	= D("Form");
		$list	=	$Form->findAll();
		$this->assign('list',$list);
		$this->display();
	}

	// 处理表单数据
	public function insert() {
		// 实例化Info模型对象
		$Info		=	D("Info");
		if($info	=	$Info->create()) {
			$Form	=	D("Form");
			$Form->Info	=	$info;
			if($Form->add()){
				$this->redirect();
			}else{
				header("Content-Type:text/html; charset=utf-8");
				exit($Form->getError().' [ <A HREF="javascript:history.back()">返 回</A> ]');
			}
		}else{
			header("Content-Type:text/html; charset=utf-8");
			exit($Info->getError().' [ <A HREF="javascript:history.back()">返 回</A> ]');
		}
	}

	// 生成验证码
	public function verify() {
        import("ORG.Util.Image");
       	Image::buildImageVerify(); 
	}
} 
?>