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
// $Id: PageAction.class.php 9 2007-01-03 09:32:19Z liu21st $

/**
 +------------------------------------------------------------------------------
 * 
 +------------------------------------------------------------------------------
 * @package  core
 * @author liu21st <liu21st@gmail.com>
 * @version  $Id: PageAction.class.php 9 2007-01-03 09:32:19Z liu21st $
 +------------------------------------------------------------------------------
 */
import("@.Action.CommonAction");
class PageAction extends CommonAction 
{
    function index() 
    {
    	$id   =  $_REQUEST['id'];
        if(!empty($id)) {
            $dao = D("ArticleDao");
            $vo  = $dao->find('type=2 and id="'.$id.'"');
            if(false !== $vo) {
                $this->assign('vo',$vo);
                if($vo->status == 4) {
                	// 关闭文章
                    $this->assign('closeComment',true);
                }
                $dao = D("CommentDao");
                $list  = $dao->findAll('articleId="'.$id.'"');
                $this->assign('comments',$list);
                $this->display();              	
            }else {
                $this->forward('_404','Index');
            }
      	
        }else {
        	$this->forward('_404','Index');
        }
        return ;
    }

    function comment() 
    {
        $dao = D("CommentDao");
    	$vo  =  $dao->createVo();
        $vo->cTime  =  time();
        $vo->ip = $_SERVER['REMOTE_ADDR'];
        $vo->agent   =  $_SERVER["HTTP_USER_AGENT"];
        $result  =  $dao->add($vo);
        if($result) {
            $this->delCacheVoList('CommentVo',$vo->articleId);
            $this->success('评论发布成功！');
        }else {
        	$this->error('评论保存失败！');
        }
    }

}//end class
?>