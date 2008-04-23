<?php 
// URL伪静态
class BlogAction extends Action{
	public function index() {
		echo C('TMPL_FILE_NAME');EXIT;
		$this->display();
	}
} 
?>