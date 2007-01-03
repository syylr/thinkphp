<?php 
// +----------------------------------------------------------------------+
// | ThinkCMS                                                             |
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
// $Id$

/**
 +------------------------------------------------------------------------------
 * CMS 首页管理
 +------------------------------------------------------------------------------
 * @author liu21st <liu21st@gmail.com>
 * @version  $Id$
 +------------------------------------------------------------------------------
 */
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

        // 保存文章
        $dao = D("ArticleDao");
        $list  = $dao->findAll("status=1 and type=1");
        $this->assign("saveArtList",$list);

        $list  = $dao->findAll("status=2 and type=1");
        $this->assign("verifyArtList",$list);
        $this->display();
        return ;
    }

    /**
     +----------------------------------------------------------
     * 主持人登录房间 
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function loginRoom() 
    {
        //检查主持人是否在线
        $room    = $_POST['roomId'];
        $girlId   = Session::get('girlId');
        import('ORG.Crypt.Base64');
        // 登录安全码
        // 加密传值，保证在没有前台登录认证情况下的安全
        $auth   = base64_encode(md5(Session::get(USER_AUTH_KEY)).Base64::encrypt($girlId,'_fcs_base64').md5($girlId));
        $url  =  __ROOT__."/Chat/index.php/Chat/index/room/{$room}/auth/{$auth}";
        exit($url);
        //redirect (__ROOT__."/Chat/index.php/Chat/index/room/{$room}/auth/{$auth}");
    }

    function login() 
    {
        $this->display();
        return ;
    }

}//类定义结束
?>