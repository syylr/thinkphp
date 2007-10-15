<?php 

class PublicAction extends Action
{//类定义开始

	function getAttach() {
        //读取附件信息
		$id	=	$_GET['id'];
        $dao = D('Attach');
        $attachs = $dao->findAll("module='".MODULE_NAME."' and recordId='$id'");
		if($attachs->count()>0) {
		//模板变量赋值
		$this->assign("attachs",$attachs);
		}
	}

	// 保存文章的标签
	function _trigger($vo) {
		if(ACTION_NAME=='insert') {
			// 补充附件表信息
			$dao	=	D("Attach");
			$attach['verify']	=	0;
			$attach['recordId']	=	$vo->id;
			$dao->save($attach,"verify='".Session::get('attach_verify')."'");
		}
		$this->saveTag($vo->tags,$vo->id);
	}

	function getComment() {
        //读取附件信息
		$id	=	$_GET['id'];
		import("ORG.Util.Page");
        $Comment = D('Comment');
		$count	=	$Comment->count("module='".MODULE_NAME."' and recordId='$id'");
		$p          = new Page($count);
		$p->setConfig('header' ,'条评论');
        $comments = $Comment->findAll("module='".MODULE_NAME."' and recordId='$id'",'id,content,author,email,recordId,cTime','id asc',$p->firstRow.','.$p->listRows);
		if($comments->count()>0) {
		//模板变量赋值
		$this->assign("comments",$comments);
		$page  = $p->show();
		$this->assign("page",       $page);
		}
	}
    // 发表评论
    function comment() 
    {
        // 创建评论对象
        $dao = D("Comment");
    	$vo  =  $dao->create();
		if(!$vo) {
			$this->error($dao->getError());
		}
        // 保存评论
        $result  =  $dao->add();
        if($result) {
            // 更新评论数
			$objDao = D($vo->module);
			$objDao->setInc('commentCount',"id='".$vo->recordId."'");
			// 返回客户端数据
            $vo->cTime  =  toDate($vo->cTime,'Y-m-d H:i:s');
            $vo->content = nl2br(ubb(trim($vo->content)));
			$vo->id	 =	 $result;
            $this->ajaxReturn($vo,'评论发表成功');
        }else {
        	$this->ajaxReturn($vo,$dao->getError().'评论失败！',0);
        }
    }

    function saveTag($tags,$id,$module=MODULE_NAME) 
    {
        if(!empty($tags) && !empty($id)) {
            $dao = D("Tag");
            $taggedDao   = D("Tagged");
            // 记录已经存在的标签
            $exists_tags  = $taggedDao->getFields("id,tagId","module='{$module}' and recordId='{$id}'");
            $taggedDao->deleteAll("module='{$module}' and recordId='{$id}'");
            $tags = explode(' ',$tags);
            foreach($tags as $key=>$val) {
                $val  = trim($val);
                if(!empty($val)) {
                    $tag =  $dao->find("module='{$module}' and name='$val'");
                    if($tag) {
                        // 标签已经存在
                        if(!in_array($tag->id,$exists_tags)) {
							$dao->setInc('count','id='.$tag->id);
                        }

                    }else {
                        // 不存在则添加
						$tag = new stdClass();
                        $tag->name =  $val;
                        $tag->count  =  1;
                        $tag->module   =  $module;
                        $result  = $dao->add($tag);
                        $tag->id   =  $result;
                    }
                    // 记录tag关联信息
                    $t = new stdClass();
                    $t->module   = $module;
                    $t->recordId =  $id;
                    $t->tagTime  = time();
                    $t->tagId  = $tag->id;
                    $taggedDao->add($t);                	
                }
            }           	
        }
    }

    // 删除评论
    function delComment() 
    {
        //删除指定记录
        $dao        = D("Comment");
        $id         = $_REQUEST['id'];
        if(isset($id)) {
            $comment   =  $dao->getById($id);
            if(!$comment) {
            	$this->error('评论不存在！');
            }
            if($dao->deleteById($id)){
                // 更新日志评论数
                $dao = D($comment->module);
				$dao->setDec('commentCount',"id=".$comment->recordId);
                $this->ajaxReturn($id,"评论删除成功",1);
            }else {
                $this->error('操作失败');
            }        	
        }else {
        	$this->error('非法操作');
        }
    }

    function delAttach()
    {
        //删除指定记录
        $dao        = D("Attach");
        $id         = $_REQUEST[$dao->getPk()];
        //id 安全验证
        if(!preg_match('/^\d+(\,\d+)?$/',$id)) {
            throw_exception('非法Id');
        }
        $condition = $dao->getPk().' in ('.$id.')'; 
		$list	=	$dao->findAll($condition,'savename,savepath');
        if($dao->delete($condition)){
			// 删除附件
			foreach ($list as $file){
				if(is_file($file->savepath.$file->savename)) {
					unlink($file->savepath.$file->savename);
				}elseif(is_dir($file->savepath.$file->savename)){
			        import("ORG.Io.Dir");
			        Dir::del($file->savepath.$file->savename);					
				}
			}
            $this->ajaxReturn($id,'删除成功！',1);
        }else {
            $this->error( '删除失败！');
        }
    }

    function download()
    {
        import("ORG.Net.Http");
        $id         =   $_GET['id'];
        $dao        =   D("Attach");
        $attach	    =   $dao->getById($id);
        $filename   =   $attach->savepath.$attach->savename;
        if(is_file($filename)) {
			if(!Session::is_set('attach_down_count_'.$id)) {
				// 下载计数
				$dao->setInc('downCount',"id=".$id);
				Session::set('attach_down_count_'.$id,true);
			}
            Http::download($filename,auto_charset($attach->name,'utf-8','gbk'));
        }
    }

}//类定义结束
?>