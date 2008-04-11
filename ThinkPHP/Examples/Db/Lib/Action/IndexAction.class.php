<?php 
class IndexAction extends Action{
	// 首页
	public function index(){
		C('SHOW_RUN_TIME',TRUE);	 //		显示运行时间
		C('SHOW_ADV_TIME',TRUE);	 // 显示高级运行时间
		C('SQL_DEBUG_LOG',1);	// 记录SQL日志
		C('SHOW_PAGE_TRACE',TRUE); // 显示页面Trace信息
		$Form	= D("Form");
		// 普通的列表查询
		$list	=	$Form->findAll('','*','id desc','0,5');
		$this->assign('list',$list);
		// 带条件查询
		$condition['id']	=	array('gt',0);
		$condition['status']	=	1;
		$vo	=	$Form->find($condition,'id,title');
		$this->assign('vo',$vo);
		// 组合查询
		$map['id']=array('NOT IN','1,6,9');
		$map['name,email']	= array(array('like','thinkphp%'),array('like','liu21st%'),'or');
		$list	=	$Form->findAll($map,'*','id desc','0,5');
		$this->assign('list2',$list);
		// 定位查询 查询满足条件的第一个数据
		$vo	=	$Form->first($map,'id desc','*');
		$this->assign('first',$vo);
		// 动态查询
		$list	=	$Form->top3('','id,title','id desc');
		$this->assign('topList',$list);
		$vo	=	$Form->getByEmail('liu21st@gmail.com');
		$this->assign('vo2',$vo);
		$this->display();
	}

} 
?>