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
 * 内置模板引擎类 解析模板标签并输出
 * 支持缓存和页面压缩
 +------------------------------------------------------------------------------
 * @package  core
 * @author liu21st <liu21st@gmail.com>
 * @version  $Id$
 +------------------------------------------------------------------------------
 */
class Template extends Base
{
    /**
     +----------------------------------------------------------
     * 模板页面显示变量，未经定义的变量不会显示在页面中
     +----------------------------------------------------------
     * @var array
     * @access protected
     +----------------------------------------------------------
     */
    var $tVar        =  array();

   /**
     +----------------------------------------------------------
     * 架构函数
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     */
    function __construct()
    {

    }

   /**
     +----------------------------------------------------------
     * 取得模板对象实例
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return Template
     +----------------------------------------------------------
     */
    function getInstance() {
        return get_instance_of(__CLASS__);
    }

    /**
     +----------------------------------------------------------
     * 模板赋值和显示部分
     +----------------------------------------------------------
     */

    /**
     +----------------------------------------------------------
     * 模板变量赋值
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param mixed $name 
     * @param mixed $value  
     +----------------------------------------------------------
     */
    function assign($name,$value=''){
        if(is_array($name)) {
        	$this->tVar   =  array_merge($this->tVar,$name);
        }else {
   	        $this->tVar[$name] = $value;
        }
    }


    /**
     +----------------------------------------------------------
     * 取得模板变量的值
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $name 
     +----------------------------------------------------------
     * @return mixed 
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function get($name){
        if(isset($this->tVar[$name])) {
            return $this->tVar[$name];
        }else {
        	return false;
        }
    }

    /**
     +----------------------------------------------------------
     * 加载模板和页面输出
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $templateFile 模板文件名 留空为自动获取
     * @param string $varPrefix 模板变量前缀 
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function display($templateFile='',$charset=OUTPUT_CHARSET,$contentType='text/html',$varPrefix='')
    {
        if(null===$templateFile) {
            // 使用null参数作为模版名直接返回不做任何输出
        	return ;
        }
        // 设置输出缓存
        ini_set('output_buffering',4096);
        if(COMPRESS_PAGE) {//开启页面压缩输出
            $zlibCompress   =  ini_get('zlib.output_compression');
            if(empty($zlibCompress) && function_exists('ini_set')) {
                ini_set( 'zlib.output_compression', 1 );
                $zlibCompress   =  1;
            } 
        }
        // 缓存初始化过滤
        apply_filter('ob_init');
        //页面缓存
       	ob_start(); 
        ob_implicit_flush(0); 
        // 网页字符编码
        header("Content-Type:".$contentType."; charset=".$charset);
        header("Cache-control: private");  //支持页面回跳
        // 缓存开启后执行的过滤
        apply_filter('ob_start');
        // 模版文件名过滤
        $templateFile = apply_filter('template_file',$templateFile);
        if(''==$templateFile) {
            $templateFile = TMPL_FILE_NAME;
        }        
        // 模版变量过滤
        $this->tVar = apply_filter('template_var',$this->tVar);
        //根据不同模版引擎进行处理
        if('PHP'==strtoupper(TMPL_ENGINE_TYPE) || ''== TMPL_ENGINE_TYPE ) {
        	//使用PHP模版
            if(!file_exists($templateFile))
                $templateFile =  dirname(TMPL_FILE_NAME).'/'.$templateFile;
            if(!file_exists($templateFile)){
                throw_exception(_TEMPLATE_NOT_EXIST_);        
            }
            include_once ($templateFile);
        }else {
        	// 使用外挂模版引擎
            // 通过插件的方式扩展
            use_compiler(TMPL_ENGINE_TYPE,$templateFile,$this->tVar,$charset,$varPrefix);
        }
        // 获取并清空缓存
        $content = ob_get_clean();
        // 输出编码转换
        $content = auto_charset($content,TEMPLATE_CHARSET,$charset);
        // 输出过滤
        $content = apply_filter('ob_content',$content);
        $runtime   = number_format((array_sum(split(' ', microtime())) - $GLOBALS['_beginTime']), 6);
        //输出缓存内容
        echo $content;
        if(SHOW_RUN_TIME )  {
            echo '<div style="text-align:center;width:100%">Process: '.$runtime.'s</div>';
        }
        return ;
    }

}//
?>