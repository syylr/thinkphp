<?php
class PublicAction extends Action
{//类定义开始
	public function _initialize()
	{
		import("Think.Util.Cookie");
		import("Think.Util.Session");
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
        $model  = $this->getModelClass();
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
			$vo     = $model->find($model->getPk()."='$id'");
			if(!$vo) {
				throw_exception(L('_SELECT_NOT_EXIST_'));
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

}//类定义结束
?>