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
// $Id: IndexAction.class.php 78 2007-04-01 04:29:15Z liu21st $

import('@.Action.AdminAction');
/**
 +------------------------------------------------------------------------------
 * 首页
 +------------------------------------------------------------------------------
 * @package   core
 * @author    liu21st <liu21st@gmail.com>
 * @version   $Ver$
 +------------------------------------------------------------------------------
 */
class IndexAction extends AdminAction
{//类定义开始


    /**
     +----------------------------------------------------------
     * 管理员首页
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function index() 
    {
		//最近登录时间
		$this->assign('lastLoginTime',Session::get('lastLoginTime'));
        $this->display();
        return ;
    }

}//类定义结束
?>