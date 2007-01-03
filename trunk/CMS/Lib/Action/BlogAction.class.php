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
class BlogAction extends CommonAction 
{
    // 公共方法
    function _initialize() 
    {
        // 获取日志分类
        if(!Session::is_set('CategoryList')) {
            $dao = D("CategoryDao");
            $list  = $dao->findAll('status=1');        	
            Session::set('CategoryList',$list);
        }else {
            $list  = Session::get('CategoryList');        	
        }
        $this->assign('category',$list);

        // 获取最新发表
        $dao = D("ArticleDao");
        $list  = $dao->findAll('type=1','','*','cTime desc','0,5');     	
        $this->assign('lastArticles',$list);

        $top = $dao->findAll('isTop=1','','id,title,cTime,userId','cTime desc');
        $this->assign('top',$top);

        // 获取最新评论
        $dao = D("CommentDao");
        $list  = $dao->findAll("module='article'","","*","cTime desc",'0,5');
        $this->assign('lastComments',$list);

        parent::_initialize();
    }    

    // BLOG首页
    function index() 
    {
        $dao = D("ArticleDao");
        $list  = $dao->findAll('','','*','cTime desc','0,15');         
        $this->assign('list',       $list);
        $this->display();
        return ;
    }
    
    // 查看日志
    function article() 
    {
    	$id   =  $_REQUEST['id'];
        if(!empty($id)) {
            $dao = D("ArticleDao");
            $vo     = $dao->getById($id);
            if(!$vo) {
                $this->forward('_404','Index');
                return ;
            }
            //输出vo对象
            $this->assign('vo',$vo);
            if($vo->commentStatus) {
                // 允许评论
                $dao = D("CommentDao");
                $list  = $dao->findAll("module='article' and recordId=$id");
                if($vo->status == 4) {
                	// 关闭文章
                    $this->assign('closeComment',true);
                }
                $this->assign('comments',$list);
            }
            
        }else {
                $this->forward('_404','Index');
                return ;            
        }
        $this->display(); 
    }

    // 查看分类日志
    function category() 
    {
    	$id   =  $_REQUEST['id'];
        if(!empty($id)) {
        	$dao = D("CategoryDao");
            $category  = $dao->getById($id);
            if($category) {
                $this->assign('categoryName',$category->title);
                $dao = D("ArticleDao");
                //取得满足条件的记录数
                $count      = $dao->getCount("categoryId='$id' and status=3");
                import("ORG.Util.Page");
                //创建分页对象
                if(!empty($_REQUEST['listRows'])) {
                    $listRows  =  $_REQUEST['listRows'];
                }
                $p          = new Page($count,$listRows);
                //分页查询数据
                $voList     = $dao->findAll("categoryId='$id' and status=3",'','*','cTime desc',$p->firstRow.','.$p->listRows);         
                //分页显示
                $page       = $p->show();
                //模板赋值显示
                $this->assign('list',       $voList);
                $this->assign("page",       $page);
            }else {
            	$this->forward('_404','Index');
                return ;
            }

        }else {
        	$this->forward('_404','Index');
        }
        $this->display(); 
        return ;
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
            // 更新日志评论数
            $dao = D("ArticleDao");
            $article =  $dao->getById($vo->recordId);
            $data['id']  = $vo->recordId;
            $data['commentCount']   = $article->commentCount+1;
            $dao->save($data);   
            $this->success('评论发布成功！');    	
        }else {
        	$this->error('评论保存失败！');
        }
    }

    // 日志搜索
    function search() 
    {
    	$search   =  $_REQUEST['keyword'];
        $dao = D("ArticleDao");
        $list  =  $dao->findAll("title like '%".$search."%' OR content like '%".$search."%'");
        $this->assign('list',$list);
        $this->display();
    }

    // 查看页面
    function page() 
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

    function rss2() 
    {
        $channel = array(
             'title' => 'ThinkPHP',
             'link' => 'http://thinkphp.cn',
             'description' => TAG_LINE,
             'webmaster' => 'liu21st@gmail.com',
             'copyright' => 'ThinkPHP 2006',
        );
        $image = array(
             'url' => '图片的链接(必备)',
             'title' => 'ThinkPHP',
             'link' => 'http://thinkphp.cn',
               );
        import('ORG.Text.Rss2');
        $rss  = new Rss2($channel);
        //$rss->image($image);
        if($_GET['go']=='comment') {
            $dao    = D("CommentDao");
            $artdao = D("ArticleDao");
            $list  = $dao->findAll("module='article'",'','*','cTime desc','0,15');
            foreach($list->getIterator() as $key=>$comment) {
                $article =  $artdao->getById($comment->recordId);
                $item['title']   = '[评论]'.$article->title;
                $item['link']   =  'http://thinkphp.cn'.__URL__.'/article/id/'.$article->id.'#comment-'.$comment->id;
                $item['description'] = $comment->content;
                $item['author']   = $comment->author;
                $item['pubdate'] = $comment->cTime;
                $rss->item($item);
            }        	
        }else {
            $dao    = D("ArticleDao");
            $list  = $dao->findAll('','','*','cTime desc','0,15');
            foreach($list->getIterator() as $key=>$article) {
                $item['title']   = $article->title;
                $item['link']   =  'http://thinkphp.cn'.__URL__.'/article/id/'.$article->id;
                $item['description'] = $article->content;
                $item['author']   = getUserName($article->userId);
                $item['category'] = getCategoryName($article->categoryId);
                $item['pubdate'] = $article->cTime;
                $rss->item($item);
            }        	
        }

        $rss->generate();
        exit();
    }


}//end class
?>