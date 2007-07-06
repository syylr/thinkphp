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

class PageAction extends Action 
{
	// 分页演示
    function index() 
    {
		C('LIST_NUMBERS',5);
		C('PAGE_NUMBERS',10);
		// 模拟读取100条记录
		$count	=	2740;
		import("ORG.Util.Page");
		$page	=	new Page($count);
		$p	 =	 $page->show();
		$this->assign('page1',$p);

		$page->config = array(
			'header'=>'条记录',
			'prev'=>'<IMG SRC="'.APP_PUBLIC_URL.'/images/list-back.gif" WIDTH="20" HEIGHT="20" BORDER="0" ALT="上一页" align="absmiddle">',
			'next'=>'<IMG SRC="'.APP_PUBLIC_URL.'/images/list-next.gif" WIDTH="20" HEIGHT="20" BORDER="0" ALT="下一页" align="absmiddle">',
			'first'=>'<IMG SRC="'.APP_PUBLIC_URL.'/images/delall.gif" WIDTH="20" HEIGHT="20" BORDER="0" ALT="第一页" align="absmiddle">',
			'last'=>'<IMG SRC="'.APP_PUBLIC_URL.'/images/addall.gif" WIDTH="20" HEIGHT="20" BORDER="0" ALT="最后一页" align="absmiddle">'
		);
		$p	 =	 $page->show();
		$this->assign('page2',$p);
        $this->display();
    }    

}//end class
?>