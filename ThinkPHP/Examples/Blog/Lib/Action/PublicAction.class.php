<?php 

class PublicAction extends Action
{//类定义开始

	public function getAttach() {
        //读取附件信息
		$id	=	$_GET['id'];
        $dao = D('Attach');
        $attachs = $dao->findAll("module='".MODULE_NAME."' and recordId='$id'");
		if(count($attachs)>0) {
		//模板变量赋值
		$this->assign("attachs",$attachs);
		}
	}

	// 保存文章的标签
	public function _trigger($vo) {
		if(ACTION_NAME=='insert') {
			// 补充附件表信息
			$dao	=	D("Attach");
			$attach['verify']	=	0;
			$attach['recordId']	=	$vo->id;
			$dao->save($attach,"verify='".$_SESSION['attach_verify']."'");
		}
		$this->saveTag($vo->tags,$vo->id);
	}

    /**
     +----------------------------------------------------------
     * 默认新增保存操作
     * 
     * 如果需要额外操作可以重定义操作方法，
     * 并且在子类中通过parent::insert调用
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    public function insert() 
    {
        //对表单提交处理进行处理或者增加非表单数据
        if(method_exists($this,'_operation')) {
            $this->_operation();
        }

        //保存新增数据对象
        $model        = $this->getModelClass();
        if(!empty($model)) {
        	$this->_insert($model);
        }
    }

    /**
     +----------------------------------------------------------
     * 插入记录
     +----------------------------------------------------------
     * @access protected 
     +----------------------------------------------------------
     * @param Model $model 数据对象 
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    protected function _insert($model) 
    {
        $vo = $model->create();
        if(false === $vo) {
        	$this->error($model->getError());
        }
        //保存当前Vo对象
        $id = $model->add($vo);
        if($id) { //保存成功
			if(is_array($vo)) {
	           	$vo[$model->getPk()]  =  $id;
			}else{
	           	$vo->{$model->getPk()}  =  $id;
			}
            // 缓存数据对象
			$guid   = $model->getModelName().'_'.$id;
	        S($guid,$vo);
            //数据保存触发器
            if(method_exists($this,'_trigger')) {
                $this->_trigger($vo);
            }
            if(!empty($_FILES)) {//如果有文件上传
                //调用上传操作上传文件
                //并且保存附件信息到数据库
                $this->_upload(MODULE_NAME,$id);
            }
            //成功提示
            $this->success(L('_INSERT_SUCCESS_'));
        }else { 
            //失败提示
            $this->error(L('_INSERT_FAIL_'));
        }
    }

    /**
     +----------------------------------------------------------
     * 默认编辑操作
     * 
     * 如果需要额外操作可以重定义操作方法，
     * 并且在子类中通过parent::edit调用
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param Model $model 数据对象 
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    public function edit($model='')
    {
        //取得编辑的Vo对象
        if(empty($model)) {
        	$model    = $this->getModelClass();
        }
        if(!empty($model)) {
            $id     = (int)$_REQUEST[$model->getPk()];
            if(false === $vo) {
                $vo     = $model->find($model->getPk()."='$id'");
                if(!$vo) {
                    throw_exception(L('_SELECT_NOT_EXIST_'));
                }
            }
			$this->cacheLockVersion($vo);
            $this->assign('vo',$vo);
            if($this->get('ajax')) {
                $this->ajaxReturn($vo);
            }
        }
        $this->display();
        return;
    }

    /**
     +----------------------------------------------------------
     * 默认查看详细操作
     * 这里调用了edit方法，区别在于模板不同
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    public function read()
    {
        $this->edit();
        return;
    }

    /**
     +----------------------------------------------------------
     * 默认更新操作
     * 
     * 如果需要额外操作可以重定义操作方法，
     * 并且在子类中通过parent::update调用
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    public function update() 
    {
        //处理表单数据
        if(method_exists($this,'_operation')) {
            $this->_operation();
        }

        //更新数据对象
        $model    = $this->getModelClass();
        if(!empty($model)) {
        	$this->_update($model);
        }
    }

    /**
     +----------------------------------------------------------
     * 更新一个数据对象
     +----------------------------------------------------------
     * @access protected 
     +----------------------------------------------------------
     * @param Model $model 数据对象 
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    protected function _update($model) 
    {
        $vo = $model->create('','edit');
		if(!$vo) {
			$this->error($model->getError());
		}
		$id	=	is_array($vo)?$vo[$model->getPk()]:$vo->{$model->getPk()};
    	$result  = $model->save($vo);
        if($result) {
			$vo	=	$model->getById($id);
            //数据保存触发器
            if(method_exists($this,'_trigger')) {
                $this->_trigger($vo);
            }
            if(!empty($_FILES)) {//如果有文件上传
                //执行默认上传操作
                //保存附件信息到数据库
                $this->_upload(MODULE_NAME,$id);
            }
            //成功提示
            $this->success(L('_UPDATE_SUCCESS_'));
        }else {
            //错误提示
            $this->error($model->getError());
        }
    }

   /**
     +----------------------------------------------------------
     * 默认上传操作
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
	public function upload() {
		if(!empty($_FILES)) {//如果有文件上传
			// 上传附件并保存信息到数据库
			$this->_upload(MODULE_NAME);
			$this->forward();
		}
	}
	
    /**
     +----------------------------------------------------------
     * 文件上传功能，支持多文件上传、保存数据库、自动缩略图
     +----------------------------------------------------------
     * @access protected 
     +----------------------------------------------------------
     * @param string $module 附件保存的模块名称
     * @param integer $id 附件保存的模块记录号
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    protected function _upload($module='',$recordId='') 
    {
        import("ORG.Net.UploadFile");
        $upload = new UploadFile();
        //检查客户端上传文件参数设置
        if(isset($_POST['_uploadFileSize']) && is_numeric($_POST['_uploadFileSize'])) {
            //设置上传文件大小
            $upload->maxSize  = $_POST['_uploadFileSize'] ;
        }
        if(!empty($_POST['_uploadFileType'])) {
            //设置上传文件类型
            $upload->allowExts  = explode(',',strtolower($_POST['_uploadFileType']));
        }
        if(!empty($_POST['_uploadSavePath'])) {
            //设置附件上传目录
            $upload->savePath =  $_POST['_uploadSavePath']; 
        }
        if(isset($_POST['_uploadSaveRule'])) {
            //设置附件命名规则
            $upload->saveRule =  $_POST['_uploadSaveRule']; 
        }
        if(!empty($_POST['_uploadFileTable'])) {
            //设置附件关联数据表
            $module =  $_POST['_uploadFileTable']; 
        }
        if(!empty($_POST['_uploadRecordId'])) {
            //设置附件关联记录ID
            $recordId =  $_POST['_uploadRecordId']; 
        }
        if(!empty($_POST['_uploadFileId'])) {
            //设置附件记录ID
            $id =  $_POST['_uploadFileId']; 
        }
        if(!empty($_POST['_uploadFileVerify'])) {
            //设置附件验证码
            $verify =  $_POST['_uploadFileVerify']; 
        }
        if(!empty($_POST['_uploadUserId'])) {
            //设置附件上传用户ID
            $userId =  $_POST['_uploadUserId']; 
        }else {
        	$userId = isset($_SESSION[C('USER_AUTH_KEY')])?$_SESSION[C('USER_AUTH_KEY')]:0;
        }
        if(!empty($_POST['_uploadImgThumb'])) {
            //设置需要生成缩略图，仅对图像文件有效
            $upload->thumb =  $_POST['_uploadImgThumb']; 
        }
        if(!empty($_POST['_uploadThumbSuffix'])) {
            //设置需要生成缩略图的文件后缀
            $upload->thumbSuffix =  $_POST['_uploadThumbSuffix']; 
        }
        if(!empty($_POST['_uploadThumbMaxWidth'])) {
            //设置缩略图最大宽度
            $upload->thumbMaxWidth =  $_POST['_uploadThumbMaxWidth']; 
        }
        if(!empty($_POST['_uploadThumbMaxHeight'])) {
            //设置缩略图最大高度
            $upload->thumbMaxHeight =  $_POST['_uploadThumbMaxHeight']; 
        }
		// 支持图片压缩文件上传后解压
		if(!empty($_POST['_uploadZipImages'])) {
			$upload->zipImages = true;
		}
        $uploadReplace =  false;
        if(isset($_POST['_uploadReplace']) && 1==$_POST['_uploadReplace']) {
            //设置附件是否覆盖
            $upload->uploadReplace =  true;
			$uploadReplace = true;
        }
		$uploadFileVersion = false;
        if(isset($_POST['_uploadFileVersion']) && 1==$_POST['_uploadFileVersion']) {
            //设置是否记录附件版本
            $uploadFileVersion =  true;
        }
        $uploadRecord  =  true;
        if(isset($_POST['_uploadRecord']) && 0==$_POST['_uploadRecord']) {
            //设置附件数据是否保存到数据库
            $uploadRecord =  false;
        }
        // 记录上传成功ID
        $uploadId =  array();
        $savename = array();
        //执行上传操作
        if(!$upload->upload()) {
            if($this->get('ajax') && isset($_POST['_uploadFileResult'])) {
                $uploadSuccess =  false;
                $ajaxMsg  =  $upload->getErrorMsg();
            }else {
                //捕获上传异常
                $this->error($upload->getErrorMsg());            	
            }
        }else {
            if($uploadRecord) {
            	// 附件数据需要保存到数据库
                 //取得成功上传的文件信息
                $uploadList = $upload->getUploadFileInfo();
				$remark	 =	 $_POST['remark'];
                //保存附件信息到数据库
                $Attach    = D('Attach');
                //启动事务
                //$Attach->startTrans();
                foreach($uploadList as $key=>$file) {
                    //记录模块信息
                    $file['module']     =   $module;
                    $file['recordId']   =   $recordId?$recordId:0;
                    $file['userId']     =   $userId;
					$file['verify']	=	$verify?$verify:'';
					$file['remark']	 =	 $remark[$key]?$remark[$key]:($remark?$remark:'');
                    //保存附件信息到数据库
                    if($uploadReplace ) {
						if(!empty($id)) {
	                        $vo  =  $Attach->getById($id);
						}else{
	                        $vo  =  $Attach->find("module='".$module."' and recordId='".$recordId."'");
						}
						if(is_object($vo)) {
							$vo	=	get_object_vars($vo);
						}
                        if(false !== $vo) {
                            // 如果附件为覆盖方式 且已经存在记录，则进行替换 
							$id	=	$vo[$Attach->getPk()];
							if($uploadFileVersion) {
								// 记录版本号
								$file['version']	 =	 $vo['version']+1;
								// 备份旧版本文件
								$oldfile	=	$vo['savepath'].$vo['savename'];
								if(is_file($oldfile)) {
									if(!file_exists(dirname($oldfile).'/_version/')) {
										mkdir(dirname($oldfile).'/_version/');
									}
									$bakfile	=	dirname($oldfile).'/_version/'.$id.'_'.$vo['version'].'_'.$vo['savename'];
									$result = rename($oldfile,$bakfile);
								}
							}
							// 覆盖模式
							$file['updateTime']	=	time();
							$Attach->save($file,"id='".$id."'");     
							$uploadId[]   = $id;

                        }else {
			                $file['uploadTime'] =   time();
                            $uploadId[] = $Attach->add($file);
                        }
                    }else {
                        //保存附件信息到数据库
		                $file['uploadTime'] =   time();
                        $uploadId[] =  $Attach->add($file);
                    }
                    $savename[] =  $file['savename'];
                }
                //提交事务
                //$Attach->commit();
            }
            $uploadSuccess =  true;
            $ajaxMsg  =  '';
        }

        // 判断是否有Ajax方式上传附件
        // 并且设置了结果显示Html元素
        if($this->get('ajax') && isset($_POST['_uploadFileResult']) ) {
            // Ajax方式上传参数信息
            $info = Array();
            $info['success']  =  $uploadSuccess;
            $info['message']   = $ajaxMsg;
            //设置Ajax上传返回元素Id
            $info['uploadResult'] =  $_POST['_uploadFileResult']; 
            if(isset($_POST['_uploadFormId'])) {
                //设置Ajax上传表单Id
                $info['uploadFormId'] =  $_POST['_uploadFormId']; 
            }
            if(isset($_POST['_uploadResponse'])) {
                //设置Ajax上传响应方法名称
                $info['uploadResponse'] =  $_POST['_uploadResponse']; 
            }
            if(!empty($uploadId)) {
                $info['uploadId'] = implode(',',$uploadId);            	
            }
            $info['savename']   = implode(',',$savename);
            $this->ajaxUploadResult($info);
        }
        return ;
                	
    }

    /**
     +----------------------------------------------------------
     * Ajax上传页面返回信息
     +----------------------------------------------------------
     * @access protected 
     +----------------------------------------------------------
     * @param array $info 附件信息
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    protected function ajaxUploadResult($info) 
    {
        // Ajax方式附件上传提示信息设置
        // 默认使用mootools opacity效果
        $show   = '<script language="JavaScript" src="'.WEB_PUBLIC_URL.'/Js/mootools.js"></script><script language="JavaScript" type="text/javascript">'."\n";    
        $show  .= ' var parDoc = window.parent.document;';    
        $show  .= ' var result = parDoc.getElementById("'.$info['uploadResult'].'");';   
        if(isset($info['uploadFormId'])) {
   	        $show  .= ' parDoc.getElementById("'.$info['uploadFormId'].'").reset();';
        }
        $show  .= ' result.style.display = "block";';   
        $show .= " var myFx = new Fx.Style(result, 'opacity',{duration:600}).custom(0.1,1);";
        if($info['success']) {
            // 提示上传成功
            $show .=  'result.innerHTML = "<div style=\"color:#3333FF\"><IMG SRC=\"'.APP_PUBLIC_URL.'/images/ok.gif\" align=\"absmiddle\" BORDER=\"0\"> 文件上传成功！</div>";';        
            // 如果定义了成功响应方法，执行客户端方法
            // 参数为上传的附件id，多个以逗号分割
            if(isset($info['uploadResponse'])) {
                $show  .= 'window.parent.'.$info['uploadResponse'].'("'.$info['uploadId'].'","'.$info['savename'].'");';
            }
        }else {
            // 上传失败
            // 提示上传失败
            $show .=  'result.innerHTML = "<div style=\"color:#FF0000\"><IMG SRC=\"'.APP_PUBLIC_URL.'/images/update.gif\" align=\"absmiddle\" BORDER=\"0\"> 上传失败：'.$info['message'].'</div>";';           	
        }
        $show .= "\n".'</script>';   
        $this->assign('_ajax_upload_',$show);   
        return ;
   	}


    /**
     +----------------------------------------------------------
     * 验证码显示
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    public function verify() 
    {
		$type	 =	 isset($_GET['type'])?$_GET['type']:'gif';
        import("ORG.Util.Image");
        Image::buildImageVerify(4,1,$type);
    }

    public function delete($model='')
    {
        //删除指定记录
		if(empty($model)) {
	        $model        = $this->getModelClass();
		}
        if(!empty($model)) {
            $id         = $_REQUEST[$model->getPk()];
            if(isset($id)) {
                $condition[$model->getPk()]	=	$id; 
                if($model->delete($condition)){
					if($this->get('ajax')) {
						$this->ajaxReturn($id,L('_DELETE_SUCCESS_'),1);
					}else{
	                    $this->success(L('_DELETE_SUCCESS_'));
					}
                }else {
                    $this->error(L('_DELETE_FAIL_'));
                }        	
            }else {
                $this->error(L('_ERROR_ACTION_'));
            }        	
        }
    }
	
	public function getComment() {
        //读取附件信息
		$id	=	$_GET['id'];
		import("ORG.Util.Page");
        $Comment = D('Comment');
		$count	=	$Comment->count("module='".MODULE_NAME."' and recordId='$id'");
		$p          = new Page($count);
		$p->setConfig('header' ,'条评论');
        $comments = $Comment->findAll("module='".MODULE_NAME."' and recordId='$id'",'id,content,author,email,recordId,cTime','id asc',$p->firstRow.','.$p->listRows);
		if(count($comments)>0) {
		//模板变量赋值
		$this->assign("comments",$comments);
		$page  = $p->show();
		$this->assign("page",       $page);
		}
	}
    // 发表评论
    public function comment() 
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

    public function saveTag($tags,$id,$module=MODULE_NAME) 
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
    public function delComment() 
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

    public function delAttach()
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

    public function download()
    {
        import("ORG.Net.Http");
        $id         =   $_GET['id'];
        $dao        =   D("Attach");
        $attach	    =   $dao->getById($id);
        $filename   =   $attach->savepath.$attach->savename;
        if(is_file($filename)) {
			if(!isset($_SESSION['attach_down_count_'.$id])) {
				// 下载计数
				$dao->setInc('downCount',"id=".$id);
				$_SESSION['attach_down_count_'.$id]	=	true;
			}
            Http::download($filename,auto_charset($attach->name,'utf-8','gbk'));
        }
    }

}//类定义结束
?>