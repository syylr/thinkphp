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
// $Id: PageAction.class.php 2 2007-01-03 07:52:09Z liu21st $

/**
 +------------------------------------------------------------------------------
 * CMS 文章管理
 +------------------------------------------------------------------------------
 * @author liu21st <liu21st@gmail.com>
 * @version  $Id: PageAction.class.php 2 2007-01-03 07:52:09Z liu21st $
 +------------------------------------------------------------------------------
 */
  import('@.Action.AdminAction');
class PageAction extends AdminAction 
{
    function index() 
    {
    	$dao = D("ArticleDao");
        $map = $this->_search('ArticleVo');	
        $map->put('type',2);
        $this->_list($dao,$map);
    }

    function insert() 
    {
        $dao = D("ArticleDao");
    	$vo = $dao->createVo();
        $vo->cTime  =  time();
        $vo->userId  = Session::get('userId');
        //保存当前Vo对象
        $result = $dao->add($vo);
        if($result) {
        	$this->success('页面新增成功！');
        }else {
        	$this->error('页面新增错误！');
        }
    }

    function edit() 
    {
        //取得编辑的Vo对象
        $dao = D("ArticleDao");
        $id     = $_REQUEST[$dao->pk];
        // 判断是否存在缓存Vo
        $vo=$this->getCacheVo($this->getVo(),$id);
        if(false === $vo) {
   	        $vo     = $dao->find($dao->pk."='$id'");
            if(!$vo) {
                throw_exception(_SELECT_NOT_EXIST_);
            }
            // 缓存Vo对象，便于下次显示
            $this->cacheVo($vo,$vo->{$dao->pk});
        }    	
        $this->assign('vo',$vo);
        $this->display();
    }

    function update() 
    {
    	$dao = D("ArticleDao");
        $vo   = $dao->createVo('edit');
        $vo->mTime = time();
        $result  =  $dao->save($vo);
        if($result) {
        	$this->success('页面修改成功！');
        }else {
        	$this->error('页面修改错误！');
        }
    }
    function sort() 
    {
		$dao	= D("ArticleDao");
        $sortList   =   $dao->findAll('type=2','','*','seqNo asc');
        $this->assign("sortList",$sortList);
        $this->display();
        return ;
    }

    function saveSort() 
    {
        $seqNoList  =   $_POST['seqNoList'];
        if(!empty($seqNoList)) {
            //更新数据对象
            $dao    = D("ArticleDao");
            $map    =   new HashMap();
            $col    =   explode(',',$seqNoList);
            //启动事务
            $dao->startTrans();
            foreach($col as $val) {
                $val    =   explode(':',$val);
                $map->put('id',$val[0]);
                $map->put('seqNo',$val[1]);
                $result =   $dao->save($map);
                if(!$result) {
                    break;
                }
            }
            //提交事务
            $dao->commit();
            if($result) {
                //采用普通方式跳转刷新页面
                $this->assign("message",'更新成功');
                $this->assign("jumpUrl",$this->getReturnUrl());
            }else {
                $this->error = $dao->error;
            }
            //页面跳转
            $this->forward();        	
        }    	
    }
    function forbid() 
    {
        $dao = D("ArticleDao");
    	parent::forbid($dao);
    }
    function resume() 
    {
        $dao = D("ArticleDao");
    	parent::resume($dao);    	
    }
    function delete() 
    {
        $dao = D("ArticleDao");
        $id         = $_REQUEST[$dao->pk];
        if(isset($id)) {
            $condition = $dao->pk.' in ('.$id.')'; 
            if($dao->delete($condition)){
                $this->assign("message",_DELETE_SUCCESS_);
                $this->assign("jumpUrl",$this->getReturnUrl());
            }else {
                $this->error(_DELETE_FAIL_);
            }        	
        }else {
        	$this->error('非法操作');
        }
        $this->forward();    	
    }
}//end class
?>