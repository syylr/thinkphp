<?php 
class IndexAction extends Action{
	// 首页
	public function index(){
		C('SHOW_RUN_TIME',TRUE);	 //		显示运行时间
		C('SHOW_ADV_TIME',TRUE);	 // 显示高级运行时间
		C('SQL_DEBUG_LOG',1);	// 记录SQL日志
		C('SHOW_PAGE_TRACE',TRUE); // 显示页面Trace信息

		$Form	= D("Form");
		// 随便进行几个查询，来显示页面的SQL查询记录
		$Form->findAll('','id,title','id desc','0,6');
		$vo	=	$Form->find();
		$Form->top3('','id,title','id desc');

		// 增加自己的调试Trace信息
		$this->trace('调试数据',dump($vo,false));
		$this->trace('Cookie信息',dump($_COOKIE,false));
		$this->display();
	}

} 
?>