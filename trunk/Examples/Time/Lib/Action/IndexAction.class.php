<?php 
class IndexAction extends Action{
	// 首页
	public function index(){
		C('SHOW_RUN_TIME',TRUE);	 //		显示运行时间
		C('SHOW_ADV_TIME',TRUE);	 // 显示高级运行时间
		C('SHOW_DB_TIMES',TRUE);	 // 显示数据库操作次数
		C('SHOW_USE_MEM',TRUE);	// 显示内存开销
		$Form	= D("Form");
		// 随便进行几个查询，来显示页面的SQL查询记录
		$Form->findAll();
		$Form->getById(1);
		$Form->top3('','id,title','id desc');
		$this->display();
	}
} 
?>