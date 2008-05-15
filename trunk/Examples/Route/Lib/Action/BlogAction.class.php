<?php 
// URL路由
class BlogAction extends Action{

	public function category() {
		$this->assign('vars',$_GET);
		$this->display('Index:index');
	}
	public function archive() {
		$this->assign('vars',$_GET);
		$this->display('Index:index');
	}
	public function read(){
		$this->assign('vars',$_GET);
		$this->display('Index:index');
	}
} 
?>