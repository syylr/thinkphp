<?php 
// 模块和操作伪装
class IndexAction extends Action{
	public function read() {
		$this->display('index');
	}
} 
?>