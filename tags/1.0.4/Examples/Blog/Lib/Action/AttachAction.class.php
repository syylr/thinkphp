<?php 

import('@.Action.PublicAction');
class AttachAction extends PublicAction
{//类定义开始

	public function select() {
		$Attach = D("Attach");
		$list	=	$Attach->findAll("module='Public'");
		$this->assign("list",$list);
		$this->display();
	}
}//类定义结束
?>