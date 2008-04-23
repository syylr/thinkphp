<?php 
class IndexAction extends Action{
	// 首页
	public function index(){
		$Form	= D("Form");
		if(!empty($_GET['connect'])) {
			if($_GET['connect']==1) {
				$db_config = array (
					'dbms'     => 'mysql', 
					'username' => 'root', 
					'password' => '', 
					'hostname' => 'localhost', 
					'hostport' => '3306', 
					'database' => 'demo',
				);
			}elseif($_GET['connect']==2){
				$db_config = array (
					'dbms'     => 'mysqli', 
					'username' => 'root', 
					'password' => '', 
					'hostname' => '127.0.0.1', 
					'hostport' => '3306', 
					'database' => 'demo',
				);
			}
			// 增加数据库连接
			// 可以配置不同类型的数据库 
			// 这里仅仅是用mysql和mysqli作示范
			$Form->addConnect($db_config,1,false);
			// 切换到连接1
			$Form->switchConnect(1);
		}
		$list	=	$Form->findAll();
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