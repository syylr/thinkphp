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
// $Id: UserTypeAction.class.php 78 2007-04-01 04:29:15Z liu21st $

import('@.Action.AdminAction');
/**
 +------------------------------------------------------------------------------
 * 用户类型管理
 +------------------------------------------------------------------------------
 * @package   core
 * @author    liu21st <liu21st@gmail.com>
 * @version   $Ver$
 +------------------------------------------------------------------------------
 */
class UserTypeAction extends AdminAction
{//类定义开始

    /**
     +----------------------------------------------------------
     * 触发器定义
     * 
     +----------------------------------------------------------
     * @static
     * @access public 
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
	function _trigger($vo) 
	{
		if(ACTION_NAME=='insert') {
			//新增用户类型的时候
			//自动创建用户类型组
			import('@.Dao.GroupDao');
			$group = new GroupDao();
			$map = new HashMap();
			$map->put('name',$vo->name);
			$map->put('remark',$vo->remark);
			$map->put('status',$vo->status);
			$map->put('parentId',0);
			$group->add($map);
		}
	}

	//其他操作采用系统默认操作

}//类定义结束
?>