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
     * 加载模板和页面输出 可以返回输出内容
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $templateFile 模板文件名 留空为自动获取
     * @param string $charset 模板输出字符集
     * @param string $contentType 输出类型
     * @param string $varPrefix 模板变量前缀  
     +----------------------------------------------------------
     * @return mixed 
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function display($templateFile='',$charset=OUTPUT_CHARSET,$contentType='text/html',$varPrefix='')
    {
        $this->fetch($templateFile,$charset,$contentType,$varPrefix,true);
    }

    /**
     +----------------------------------------------------------
     * 加载模板和页面输出
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $templateFile 模板文件名 留空为自动获取
     * @param string $charset 模板输出字符集
     * @param string $contentType 输出类型
     * @param string $varPrefix 模板变量前缀      
     * @param string $display 是否输出 
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function fetch($templateFile='',$charset=OUTPUT_CHARSET,$contentType='text/html',$varPrefix='',$display=false) 
    {
		$startTime = array_sum(explode(' ', microtime()));
        if(null===$templateFile) {
            // 使用null参数作为模版名直接返回不做任何输出
        	return ;
        }
        // 网页字符编码
        header("Content-Type:".$contentType."; charset=".$charset);
        header("Cache-control: private");  //支持页面回跳
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
        // 缓存开启后执行的过滤
        apply_filter('ob_start');
        // 模版文件名过滤
        $templateFile = apply_filter('template_file',$templateFile);
        if(''==$templateFile) {
            $templateFile = TMPL_FILE_NAME;
        }        
        // 检查模版文件
        if(!file_exists($templateFile))
            $templateFile =  dirname(TMPL_FILE_NAME).'/'.$templateFile;
        if(!file_exists($templateFile)){
            throw_exception(_TEMPLATE_NOT_EXIST_);        
        }
        // 模版变量过滤
        $this->tVar = apply_filter('template_var',$this->tVar);

        //根据不同模版引擎进行处理
        if('PHP'==strtoupper(TMPL_ENGINE_TYPE) || ''== TMPL_ENGINE_TYPE ) {
        	//使用PHP模版
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
        if($display) {
			$endTime = array_sum(explode(' ', microtime()));
            echo $content;
            if(SHOW_RUN_TIME) {
            echo '<div style="text-align:center;width:100%">Process: '.number_format(($endTime - $GLOBALS['_beginTime']), 3).'s ( Load:'.number_format(($GLOBALS['_loadTime'] -$GLOBALS['_beginTime'] ), 3).'s Init:'.number_format(($GLOBALS['_initTime'] -$GLOBALS['_loadTime'] ), 3).'s Exec:'.number_format(($startTime  -$GLOBALS['_initTime'] ), 3).'s Template:'.number_format(($endTime - $startTime), 3).'s )</div>';
            }
            return null;
        }else {
        	return $content;
        }
    }
}//
?>