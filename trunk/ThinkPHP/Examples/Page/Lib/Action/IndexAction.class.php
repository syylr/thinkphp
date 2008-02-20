<?php 
// Hello
class IndexAction extends Action{
	public function index() {
		// 模拟读取100条记录
		$count	=	2740;
		import("ORG.Util.Page");
		$page	=	new Page($count);
		$p	 =	 $page->show();
		$this->assign('page1',$p);

		$page->setConfig('header', '个结果');
		$page->setConfig('prev','<IMG SRC="'.WEB_PUBLIC_URL.'/Images/list-back.gif" WIDTH="20" HEIGHT="20" BORDER="0" ALT="上一页" align="absmiddle">');
		$page->setConfig('next','<IMG SRC="'.WEB_PUBLIC_URL.'/Images/list-next.gif" WIDTH="20" HEIGHT="20" BORDER="0" ALT="下一页" align="absmiddle">');
		$page->setConfig('first','<IMG SRC="'.WEB_PUBLIC_URL.'/Images/delall.gif" WIDTH="20" HEIGHT="20" BORDER="0" ALT="第一页" align="absmiddle">');
		$page->setConfig('last','<IMG SRC="'.WEB_PUBLIC_URL.'/Images/addall.gif" WIDTH="20" HEIGHT="20" BORDER="0" ALT="最后一页" align="absmiddle">');
		$p	 =	 $page->show();
		$this->assign('page2',$p);
		$this->display();
	}
} 
?>