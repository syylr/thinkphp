<?php 
// +----------------------------------------------------------------------
// | ThinkPHP                                                             
// +----------------------------------------------------------------------
// | Copyright (c) 2008 http://thinkphp.cn All rights reserved.      
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>                                  
// +----------------------------------------------------------------------
// $Id$
import('@.Model.CommonModel');
class UserModel extends CommonModel {
	// 自动验证设置
	protected $_validate	 =	 array(
		array('account','require','用户名必须！',1),
		array('password','require','密码必须！',2),
		array('repassword','password','密码不符',1,'confirm'),
		array('verify','require','验证码必须！'),
		array('account','','用户名已经存在',0,'unique','add'),
		);
	// 自动填充设置
	protected $_auto	 =	 array(
		array('status','1','ADD'),
		array('create_time','time','ADD','function'),
		array('password','md5','ADD','function'),
		);
}
?>