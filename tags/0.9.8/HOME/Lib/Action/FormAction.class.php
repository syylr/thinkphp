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
// $Id: IndexAction.class.php 33 2007-02-25 07:06:02Z liu21st $

class FormAction extends Action 
{

	// 创建表单数据
    function index() 
    {
		if(!empty($_POST)) {
			$dao = D("Form");
			$vo	=	$dao->createVo('add','',$_POST['type']);
			$this->assign('vo',$vo);
		}
        $this->display();
    }    

	// Ajax方式返回创建数据
	function ajaxResult() {
		sleep(1); // 为了显示加载效果，延时1秒
		$dao = D("Form");
		$vo	=	$dao->createVo('add','',$_POST['type']);
		$this->ajaxReturn(dump($vo,null,true,false));
	}

	// 表单自动验证和自动填充操作
	function formCheck() {
		sleep(0.5); // 为了显示加载效果，延时0.5秒
		$dao = D("Form");
		$vo = $dao->createVo('add','',$_POST['type']);
		if(!$vo) {
			// 验证失败 返回错误提示
			$this->ajaxReturn('',$dao->error,0);
		}else{
			// 验证通过返回自动填充后的Vo数据
			$this->ajaxReturn(dump($vo,null,true,false),'表单验证通过！',1);
		}
	}


}//end class
?>