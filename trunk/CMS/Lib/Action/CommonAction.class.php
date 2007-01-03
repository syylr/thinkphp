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
// $Id$

/**
 +------------------------------------------------------------------------------
 * 
 +------------------------------------------------------------------------------
 * @package  core
 * @author liu21st <liu21st@gmail.com>
 * @version  $Id$
 +------------------------------------------------------------------------------
 */
class CommonAction extends Action 
{
    // 公共方法
    function _initialize() 
    {
        // 获取页面
        if(!Session::is_set('PageList')) {
            $dao = D("ArticleDao");
            $list  = $dao->findAll('type=2 and status=3','','*','seqNo');       	
            Session::set('PageList',$list);
        }else {
   	        $list  = Session::get('PageList');
        }
        $this->assign('pages',$list);

        parent::_initialize();
    }    

    // 发表评论
    function comment() 
    {
        $dao = D("CommentDao");
    	$vo  =  $dao->createVo();
        $vo->cTime  =  time();
        $vo->status   = 1;
        $vo->ip = $_SERVER['REMOTE_ADDR'];
        $vo->agent   =  $_SERVER["HTTP_USER_AGENT"];
        $result  =  $dao->add($vo);
        if($result) {
            $this->success('评论发布成功！');
        }else {
        	$this->error('评论保存失败！');
        }
    }

}//end class
?>