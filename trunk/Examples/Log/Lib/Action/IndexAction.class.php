<?php 
class IndexAction extends Action{
	// 首页
	public function index(){
		$Form	= D("Form");
		$list	=	$Form->top3('','id,title,content','id desc');
		// 手动记录SQL日志
		Log::record($Form->getLastSql(),SQL_LOG_DEBUG);
		// 手动记录错误日志
		Log::record('模拟写入的错误信息',WEB_LOG_ERROR);
		// 手动写入调试日志
		Log::record('list='.dump($list,false),WEB_LOG_DEBUG);
		// 真实写入日志文件
		Log::save();
		// 直接写入调试日志
		Log::write('list='.dump($list,false),WEB_LOG_DEBUG);
		$this->display();
	}
} 
?>