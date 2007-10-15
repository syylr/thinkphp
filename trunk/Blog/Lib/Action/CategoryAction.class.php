<?php 

import('@.Action.PublicAction');
class CategoryAction extends PublicAction
{//类定义开始
	function insert() {
		$Model = D("Category");
		if($category = $Model->create()) {
			$result	=	$Model->add();
			if($result) {
				$category->id	=	$result;
				$this->ajaxReturn($category,'类别新增成功！',1);
			}else{
				$this->error('新增失败！');
			}
		}else{
			$this->error($Model->getError());
		}
	}
}//类定义结束
?>