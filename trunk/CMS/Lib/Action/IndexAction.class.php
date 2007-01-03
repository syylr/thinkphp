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
import("@.Action.CommonAction");
class IndexAction extends CommonAction 
{
    // 首页
    function index() 
    {

        // 获取最新发表
        if(!Session::is_set('lastArticleList')) {
            $dao = D("ArticleDao");
            $list  = $dao->findAll('type=1','','*','cTime desc','0,5');     	  
            Session::set('lastArticleList',$list);
        }else {
        	$list  = Session::get('lastArticleList');    
        }
        $this->assign('lastArticles',$list);


        $this->display();
        return ;
    }    

    function _404() 
    {
    	$this->display(TEMPLATE_PATH.'/Public/404'.TEMPLATE_SUFFIX);
    }

}//end class
?>