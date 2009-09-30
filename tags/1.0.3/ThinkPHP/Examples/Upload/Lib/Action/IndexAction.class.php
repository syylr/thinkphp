<?php 
// 附件上传
class IndexAction extends Action{

	public function upload() {
		if(!empty($_FILES)) {
			//如果有文件上传 上传附件
			$this->_upload();
			$this->forward();
		}
	}
	
	// 文件上传
    protected function _upload() 
    {
        import("ORG.Net.UploadFile");
        $upload = new UploadFile();
        //设置上传文件大小
        $upload->maxSize  = 32922 ;
        //设置上传文件类型
        $upload->allowExts  = explode(',','doc,rar,txt');
        //设置附件上传目录
        $upload->savePath =  '../Public/Uploads/'; 

        //执行上传操作
        if(!$upload->upload()) {
            if($this->isAjax() && isset($_POST['_uploadFileResult'])) {
                $uploadSuccess =  false;
                $ajaxMsg  =  $upload->getErrorMsg();
            }else {
                //捕获上传异常
                $this->error($upload->getErrorMsg());            	
            }
        }else{
			$uploadSuccess	=	true;
		}
        // 判断是否有Ajax方式上传附件
        // 并且设置了结果显示Html元素
        if($this->isAjax() && isset($_POST['_uploadFileResult']) ) {
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

            $this->ajaxUploadResult($info);
        }
        return ;
    }

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
            $show .=  'result.innerHTML = "<div style=\"color:#3333FF\">文件上传成功！</div>";';        
            // 如果定义了成功响应方法，执行客户端方法
            // 参数为上传的附件id，多个以逗号分割
            if(isset($info['uploadResponse'])) {
                $show  .= 'window.parent.'.$info['uploadResponse'].'();';
            }
        }else {
            // 上传失败
            // 提示上传失败
            $show .=  'result.innerHTML = "<div style=\"color:#FF0000\">上传失败：'.$info['message'].'</div>";';           	
        }
        $show .= "\n".'</script>';   
        $this->assign('_ajax_upload_',$show);   
        return ;
   	}
} 
?>