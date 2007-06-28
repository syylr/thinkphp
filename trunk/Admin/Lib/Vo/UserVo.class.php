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
// $Id: UserVo.class.php 78 2007-04-01 04:29:15Z liu21st $

/**
 +------------------------------------------------------------------------------
 * 用户数据对象类
 +------------------------------------------------------------------------------
 * @package   Vo
 * @author    liu21st <liu21st@gmail.com>
 * @version   $Ver$
 +------------------------------------------------------------------------------
 */
class UserVo extends Vo
{//类定义开始

    //+----------------------------------------
    //| 数据模型 数据表字段名 
    //+----------------------------------------
    var $id;                    //用户编号
    var $name;                  //用户名
    var $password;              //密码
    var $nickname;              //昵称
    var $status;                //用户状态
    var $remark;                //备注信息
    var $verify;                //验证码
    var $email;                    //邮箱
    var $type;                    //用户类型
    var $childId;                //关联用户编号
	var $lastLoginTime;
    var $registerTime;      //注册时间

	var $_auto	 =	 array(
		array('registerTime','time','ADD'),	//	在新增的时候写入 time()
		array('password','md5','ALL'),		// 在所有情况下面对password属性使用md5方法
		);

	var $_validate = array(
		array('name','/^[a-z]\w{5,}$/i','用户名必须是字母打头，5位以上',1), // MUST_TO_VALIDATE  必须检测
		array('password','require','密码必须',0), // 只是在表单有设置的时候检测
		array('verify','is_numeric','验证码必须是数字',0,1), // 在表单有设置的情况下使用is_numeric 函数进行检测 
		);

}//类定义结束
?>