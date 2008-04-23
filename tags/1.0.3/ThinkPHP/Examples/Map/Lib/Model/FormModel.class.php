<?php 
class FormModel extends Model {
	protected $_map	=	array(
			'name'=>'title',
			'mail'=>'email',
			'remark'=>'content',
		);
	// 自动验证设置
	protected $_validate	 =	 array(
		array('name','require','标题必须！'),
		array('mail','email','邮箱格式错误！',2),
		array('remark','require','内容必须'),
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