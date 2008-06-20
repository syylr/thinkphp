<?php 
// 本类由系统自动生成，仅供测试用途
class IndexAction extends Action{
	public function index(){
		C('DATA_CACHE_TYPE','File');	 //	 设置缓存方式为File
		C('DATA_CACHE_TIME',10);	// 设置缓存有效期
		C('SHOW_RUN_TIME',TRUE);	 //		显示运行时间
		C('SHOW_ADV_TIME',TRUE);	 // 显示高级运行时间
		C('SQL_DEBUG_LOG',1);	// 记录SQL日志
		C('SHOW_PAGE_TRACE',TRUE); // 显示页面Trace信息
		if(S('list')) {
			$list	=	S('list');
		}else{
			$Form	=	D("Form");
			$list	=	$Form->findAll();
			S('list',$list);
		}
		$this->assign('list',$list);
		$this->display();
	}
} 
?>