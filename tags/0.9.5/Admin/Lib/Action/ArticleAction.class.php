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
// $Id: ArticleAction.class.php 2 2007-01-03 07:52:09Z liu21st $

/**
 +------------------------------------------------------------------------------
 * CMS 文章管理
 +------------------------------------------------------------------------------
 * @author liu21st <liu21st@gmail.com>
 * @version  $Id: ArticleAction.class.php 2 2007-01-03 07:52:09Z liu21st $
 +------------------------------------------------------------------------------
 */
  import('@.Action.AdminAction');
class ArticleAction extends AdminAction 
{
    function _filter(&$map) 
    {
    	$map->put('type',1);
    }
	function _before_insert() 
	{
		$_POST['cTime'] = time();
        $_POST['userId'] = Session::get(USER_AUTH_KEY);
	}

    function _before_update() 
    {
    	$_POST['mTime'] = time();
    }

    function publish() 
    {
    	$_POST['status'] = 3;
        $_POST['cTime'] = time();
        $_POST['userId'] = Session::get(USER_AUTH_KEY);
        $this->insert();
    }

    function top() 
    {
        //删除指定记录
        $dao        = D("ArticleDao");
        $id         = $_REQUEST['id'];
        if(isset($id)) {
            $condition = $dao->pk.' in ('.$id.')'; 
            if($dao->top($condition)){
                $this->success('置顶成功！');
            }else {
                $this->error('置顶失败');
            }        	
        }else {
        	$this->error('非法操作');
        }
        $this->forward();    	
    }

    function recommend() 
    {
        //删除指定记录
        $dao        = D("ArticleDao");
        $id         = $_REQUEST['id'];
        if(isset($id)) {
            $condition = $dao->pk.' in ('.$id.')'; 
            if($dao->recommend($condition)){
                $this->success('推荐成功！');
            }else {
                $this->error('操作失败');
            }        	
        }else {
        	$this->error('非法操作');
        }
        $this->forward();    	
    }

    function autoSave() 
    {
    	$_POST['type']  =  0;
        $this->insert();
    }
}//end class
?>