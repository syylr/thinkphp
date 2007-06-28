<?php 
// +----------------------------------------------------------------------+
// | ThinkPHP                                                             |
// +----------------------------------------------------------------------+
// | Copyright (c) 2006 liu21st.com All rights reserved.                  |
// +----------------------------------------------------------------------+
// | Licensed under the Apache License, Version 2.0 (the 'License');      |
// | you may not use this file except in compliance with the License.     |
// | You may obtain a copy of the License at                              |
// | http://www.apache.org/licenses/LICENSE-2.0                           |
// | Unless required by applicable law or agreed to in writing, software  |
// | distributed under the License is distributed on an 'AS IS' BASIS,    |
// | WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or      |
// | implied. See the License for the specific language governing         |
// | permissions and limitations under the License.                       |
// +----------------------------------------------------------------------+
// | Author: liu21st <liu21st@gmail.com>                                  |
// +----------------------------------------------------------------------+
// $Id: AdminAction.class.php 78 2007-04-01 04:29:15Z liu21st $

class FormVo extends Vo {
	var $id;
	var $name;
	var $age;
	var $magic;
	var $sex;
	var $password;
	var $email;
	var $registerTime;

	var $_auto = array(
		array('registerTime','time','ADD'),	//	在新增的时候写入 time()
		array('password','md5','ALL'),		// 在所有情况下面对password属性使用md5方法
		);
	var $_validate = array(	
		array('name','require','名称必须！'),
		array('age','number','年龄必须是数字'),
		array('email','email','邮箱不符合！'),
		array('sex','require','性别必须!',MUST_TO_VALIDATE),
		array('password','/^[a-z]\w{6,30}$/i','密码必须以字母开头 6位以上'),
		);
}
?>