<?php 
import('@.Model.CommonModel');
class BlogModel extends CommonModel 
{
	// 验证信息、自动填充信息、关联信息定义 
	protected $_validate	 =	 array(
		array('title','require','标题必须！'),
		array('content','require','内容必须'),
		array('verify','require','验证码必须！'),
		array('verify','CheckVerify','验证码错误',0,'callback'),
		);

	protected $_auto	 =	 array(
		array('status','1','ADD'),
		);

	protected $blobFields = array('content');
}
?>