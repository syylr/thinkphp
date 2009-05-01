<?php
class IndexAction extends Action{
	// 首页
	public function index(){
		$Form	= D("Form");
		$list	=	$Form->field('id,title,content')->order('id desc')->select();
		// 手动记录SQL日志
		Log::record($Form->getLastSql(),Log::SQL);
		// 手动记录错误日志
		Log::record('模拟写入的错误信息',Log::ERR);
		// 手动写入调试日志
		Log::record('list='.dump($list,false),Log::DEBUG);
		// 真实写入日志文件
		Log::save();
		// 直接写入调试日志
		Log::write('list='.dump($list,false),Log::DEBUG);
		$this->display();
	}
}
?>