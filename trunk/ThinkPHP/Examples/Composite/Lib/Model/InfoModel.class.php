<?php 
class InfoModel extends Model {
	protected $composite = true;	// 虚拟模型定义
	// 自动验证设置
	protected $_validate	 =	 array(
		array('title','require','标题必须！'),
		array('email','email','邮箱格式错误！',2),
		array('content','require','内容必须'),
		array('verify','require','验证码必须！'),
		array('verify','CheckVerify','验证码错误',0,'callback'),
		);

	// 自动填充设置
	protected $_auto	 =	 array(
		array('status','1','ADD'),
		array('create_time','time','ADD','function'),
		);

	public function CheckVerify() {
		return md5($_POST['verify']) == $_SESSION['verify'];
	}
}
?>