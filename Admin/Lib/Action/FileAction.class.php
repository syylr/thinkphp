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
// $Id: FileAction.class.php 2 2007-01-03 07:52:09Z liu21st $

/**
 +------------------------------------------------------------------------------
 * CMS 文件管理
 +------------------------------------------------------------------------------
 * @author liu21st <liu21st@gmail.com>
 * @version  $Id: FileAction.class.php 2 2007-01-03 07:52:09Z liu21st $
 +------------------------------------------------------------------------------
 */
import('@.Action.AdminAction');
/**
 +------------------------------------------------------------------------------
 * 广告管理
 +------------------------------------------------------------------------------
 * @package   core
 * @author    liu21st <liu21st@gmail.com>
 * @version   $Ver$
 +------------------------------------------------------------------------------
 */
class FileAction extends AdminAction
{//类定义开始

    function index() 
    {

        import("@.Vo.FileVo");
        $file =  base64_decode($_GET['f']);
        if(!empty($file)) {
        	if(is_dir($file)) {
                $this->_listDir($file);
        	}else if(is_file($file)) {
        		if(is_readable($file)) {
        			$content   =  file_get_contents($file);
                    $this->assign('content',$content);
                    $this->assign('filename',$file);
                    $this->assign('writeable',is_writable($file));
        		}
                $this->_listDir(dirname($file));
        	}
        }else {
            $this->_listDir(WEB_ROOT);
        }
        $this->display();
    }

    function _listDir($path) 
    {
        import("ORG.Io.Dir");
        static $_filetype = array();
        $_filetype = explode(',','php,html,js,css,htm');
        $dir = new Dir($path);
        $list  = new VoList();
        foreach($dir->getIterator() as $key=>$file) {
            if(in_array($file['ext'],$_filetype)|| $file['isDir']) {
            	$list->add(new FileVo($file));
            }
        }    
        Session::set('_currentDir',$path);
        $this->assign('list',$list);    	
    }

    function upDir() 
    {
    	$path = Session::get('_currentDir');
        redirect(__URL__.'/sindex/f/'.base64_encode(dirname($path)));
    }

    function read() 
    {
    	$file  = $_REQUEST['filename'];
        $source  = $_REQUEST['source'];
        $file  = base64_decode($file);
        if(is_file($file)) {
        	$content   =  file_get_contents($file);
            if(!empty($source)) {
            	$content = highlight_code($content,false);
            }
            header("Content-Type:text/html; charset=utf-8");
            exit($content);
        }else {
            header("Content-Type:text/html; charset=utf-8");
            exit();
        	exit(' <div style="color:#FF9900;padding:10px;font-weight:bold">要查看的文件[ '.$file.' ]不存在！请确认文件路径是否正确～</div>');
        }  
    }

    function edit() 
    {
    	$file  = $_REQUEST['editfile'];
        redirect(__URL__.'/index/f/'.base64_encode($file));
    }

    function save() 
    {
    	$filename  = $_REQUEST['filename'];
        $file  = base64_decode($filename);
        $content  = $_REQUEST['content'];
        $result  =  file_put_contents($file,$content);
        if(false !== $result) {
        	$this->success('文件保存成功！');
        }else {
            //file_put_contents($file,$old);
        	$this->error('文件保存失败！');
        }
    }

    function saveAs() 
    {
    	$file  = $_REQUEST['filename'];
        $content  = $_REQUEST['content'];
        $result  =  file_put_contents($file,$content);
        if(false !== $result) {
        	$this->success('文件保存成功！');
        }else {
            //file_put_contents($file,$old);
        	$this->error('文件保存失败！');
        }
    }

    function downFile() 
    {
    	$filename  = $_REQUEST['filename'];
        $file  = base64_decode($filename);
        import("ORG.Net.Http");
        Http::download($file);
    }
}//类定义结束
?>