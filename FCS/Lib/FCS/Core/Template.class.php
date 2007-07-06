<?php 
// +---------------------------------------------------------------------------+
// | FCS -- Fast,Compatible & Simple OOP PHP Framework                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2005-2006 liu21st.com.  All rights reserved.                |
// | Website: http://www.fcs.org.cn/                                           |
// | Author : Liu21st 流年 <liu21st@gmail.com>                                 |
// +---------------------------------------------------------------------------+
// | This program is free software; you can redistribute it and/or modify it   |
// | under the terms of the GNU General Public License as published by the     |
// | Free Software Foundation; either version 2 of the License,  or (at your   |
// | option) any later version.                                                |
// |                                                                           |
// | This program is distributed in the hope that it will be useful,  but      |
// | WITHOUT ANY WARRANTY; without even the implied warranty of                |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General |
// | Public License for more details.                                          |
// +---------------------------------------------------------------------------+

/**
 +------------------------------------------------------------------------------
 * FCS
 +------------------------------------------------------------------------------
 * @package    Core
 * @link       http://www.fcs.org.cn
 * @copyright  Copyright (c) 2005-2006 liu21st.com.  All rights reserved. 
 * @author     liu21st <liu21st@gmail.com>
 * @version    $Id$
 +------------------------------------------------------------------------------
 */
import('FCS.Util.TagLib');
import('FCS.Util.TagLib.*');

/**
 +------------------------------------------------------------------------------
 * 内置模板引擎类 解析模板标签 可选用其他模板引擎
 +------------------------------------------------------------------------------
 * @package  core
 * @author liu21st <liu21st@gmail.com>
 * @version  0.8.0
 +------------------------------------------------------------------------------
 */
class Template extends Base
{
    /**
     +----------------------------------------------------------
     * 模板页面中引入的标签库列表
     +----------------------------------------------------------
     * @var array
     * @access protected
     +----------------------------------------------------------
     */
    var $tagLib     =  array();

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
     * @param string $name 
     * @param mixed $value  
     +----------------------------------------------------------
     */
    function assign($name,$value){
        $this->tVar[$name] = $value;
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
     * @throws FcsException
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
     * @throws FcsException
     +----------------------------------------------------------
     */
    function display($templateFile='',$charset=OUTPUT_CHARSET,$contentType='text/html',$varPrefix=''){
        //缓存页面
        ini_set('output_buffering',4096);
        ob_start();    
        ob_implicit_flush(0); 
        //支持页面回跳
        header("Cache-control: private");  
        //网页字符编码
        header("Content-Type:".$contentType."; charset=".$charset);

        //加载模板
        $this->loadTemplate($templateFile,$varPrefix,$charset);

        //获取并清空缓存
        $content = ob_get_clean();
        if(HTML_CACHE_ON){//开启HTML功能
                //检查并重写HTML文件
                if (!$this->checkHTML()) {
                    file_put_contents(HTML_FILE_NAME,$content);
                }
            }

        if(COMPRESS_PAGE){//采用页面压缩 如果采用了mode_gzip 进行页面压缩，则需要关闭本参数
            if(strpos($_SERVER['HTTP_ACCEPT_ENCODING'],COMPRESS_METHOD)!==FALSE){
                //获取更加安全的页面压缩方式
                $browser = detect_browser_type();
                if(strtoupper($browser)=='IE') {
                    $compress = 'deflate';
                }else {
                    $compress = 'gzip';
                }
                //支持该压缩方式
                switch($compress){
                    case 'deflate':
                        $content = gzdeflate($content,COMPRESS_LEVEL); 
                        break;
                    case 'gzip':
                        $content = "\x1f\x8b\x08\x00\x00\x00\x00\x00" . gzcompress($content, COMPRESS_LEVEL); 
                        break;
                    default:
                        throw_exception('系统暂时不支持该页面压缩方式：'.$compress);
                }
                header("Content-Encoding: ".$compress);
            }
        }
        //输出缓存内容
        echo $content;
        return ;
    }

    /**
     +----------------------------------------------------------
     * 加载主模板并缓存
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $tmplTemplateFile 模板文件
     * @param string $varPrefix  模板变量前缀
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function loadTemplate ($tmplTemplateFile='',$varPrefix='',$charset=OUTPUT_CHARSET)
    {
        $tmplContent = '';
        if(empty($tmplTemplateFile))    $tmplTemplateFile = TMPL_FILE_NAME;
        if(!file_exists($tmplTemplateFile)){
            throw_exception(_TEMPLATE_NOT_EXIST_);        
        }
        $tmplCacheFile = CACHE_PATH.APP_NAME.'/'.md5($tmplTemplateFile).CACHFILE_SUFFIX;

        // 检查Cache文件是否需要更新
        if (!$this->checkCache($tmplTemplateFile)) { 
            $tmplContent .= file_get_contents($tmplTemplateFile);        //读出原模板内容
            //编译模板内容
            $tmplContent = $this->compiler($tmplContent,$charset);
            //重写Cache文件
            if( false === file_put_contents($tmplCacheFile,$tmplContent)) {
                throw_exception($tmplCacheFile.'写入失败！');
            }
        }
        //编码转换
        $this->tVar = auto_charset($this->tVar,TEMPLATE_CHARSET,$charset);

        // 模板阵列变量分解成为独立变量
        extract($this->tVar, empty($varPrefix)? EXTR_OVERWRITE : EXTR_PREFIX_ALL,$varPrefix); 

        include_once($tmplCacheFile);    //载入Cache文件
        return;
    }


    /**
     +----------------------------------------------------------
     * 编译模板文件内容
     * 包括模板解析、同步路径和编码转换
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param mixed $tmplContent 模板内容
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function compiler (& $tmplContent,$charset=OUTPUT_CHARSET)
    {
        //模板解析
        $tmplContent = $this->parse($tmplContent);
        //同步路径
        $tmplContent = $this->syncPath($tmplContent);
        //编码转换
        $tmplContent = auto_charset($tmplContent,TEMPLATE_CHARSET,$charset);

        return $tmplContent;
    }


    /**
     +----------------------------------------------------------
     * 替换模板文件变量
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $tmplContent  模板内容
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function tmplVarReplace(& $tmplContent)
    {
        // 替换模板变量{$var} 为 $var 格式，方便替换变量值
        $tmplContent = preg_replace('/(\{\$)(.+?)(\})/is', '$\\2', $tmplContent); 
        extract($this->tVar, EXTR_OVERWRITE); // 模板阵列变量分解成为独立变量
        $temp  = AddSlashes($tmplContent);
        eval( "\$temp = \"$temp\";" );
        $temp  = StripSlashes($temp);
        return $temp;
    }

    /**
     +----------------------------------------------------------
     * 同步模板中的外部文件调用路径
     * 公共模板和模块目录在同一级别，名称为public
     * 还包括了对charset的编码同步转换
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $tmplContent  模板内容
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function syncPath($tmplContent)
    {
        //替换项目公共目录
        $tmplContent = str_ireplace('../Public',APP_PUBLIC_URL,$tmplContent);
        //替换系统公共目录
        $tmplContent = str_replace('__PUBLIC__',SYS_PUBLIC_URL,$tmplContent);
        $tmplContent = str_replace('__CURRENT__',__CURRENT__,$tmplContent);
        //替换网站根目录
        $tmplContent = str_replace('__ROOT__',__ROOT__,$tmplContent);
        //替换当前模块地址
        $tmplContent = str_replace('__URL__',__URL__,$tmplContent);
		$tmplContent = str_replace('__ACT__',__ACT__,$tmplContent);

        //替换当前项目地址
        $tmplContent = str_replace('__APP__',__APP__,$tmplContent);
        $tmplContent = str_ireplace('charset='.TEMPLATE_CHARSET, 'charset='.OUTPUT_CHARSET, $tmplContent);

        return $tmplContent;
    }

    /**
     +----------------------------------------------------------
     * 检查缓存文件是否有效
     * 如果无效则需要重新更新
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $tmplTemplateFile  模板文件名
     +----------------------------------------------------------
     * @return boolen
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function checkCache($tmplTemplateFile)
    {
        $tmplCacheFile = CACHE_PATH.APP_NAME.'/'.md5($tmplTemplateFile).CACHFILE_SUFFIX;
        if(!file_exists($tmplCacheFile)){
            $appDir = CACHE_PATH.APP_NAME;
            if (!file_exists($appDir)) { // 项目缓存目录不存在则创建
                mkdir($appDir);
            }
            return False;
        }
        elseif (!TMPL_CACHE_ON){
            return false;
        }elseif (filemtime($tmplTemplateFile) > filemtime($tmplCacheFile)) { 
            // 模板文件如果有更新则缓存需要更新
            return False; 
        } elseif (TMPL_CACHE_TIME != -1 && time() > filemtime($tmplCacheFile)+TMPL_CACHE_TIME) { 
            // 缓存是否在有效期
            return False; 
        }
        return True;
    }


    /**
     +----------------------------------------------------------
     * 检查静态HTML文件是否有效
     * 如果无效需要重新更新
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $tmplHTMLFile  数据表名
     +----------------------------------------------------------
     * @return boolen
     +----------------------------------------------------------
     */
    function checkHTML($tmplHTMLFile = HTML_FILE_NAME)
    {
        if(!file_exists($tmplHTMLFile)){
            $appDir = HTML_PATH.APP_NAME;
            if (!file_exists($appDir)) { // 项目静态目录不存在则创建
                mkdir($appDir);
            }
            return False;
        }
        elseif (!HTML_CACHE_ON){    
            return False;
        }
        elseif (filemtime(TMPL_FILE_NAME) > filemtime($tmplHTMLFile)) { 
            // 模板文件如果更新静态文件需要更新
            return False; 
        }
        elseif (HTML_CACHE_TIME != -1 && time() > filemtime($tmplHTMLFile)+HTML_CACHE_TIME) { 
            // 文件是否在有效期
            return False; 
        }
        return True;
    }


    /**
     +----------------------------------------------------------
     * 清除缓存或者静态文件
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $filename  缓存文件名
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function cleanCache($filename) 
    { 
        if(file_exists($filename)){
            @unlink($filename);
        }
        return;
    } 

    /**
     +----------------------------------------------------------
     * 清除缓存目录下面的文件
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $cacheDir  缓存目录名
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function cleanDir($cacheDir) 
    {
        if ( $dir = @opendir( $cacheDir ) )
        {
            while ( $file = @readdir( $dir ) )
            {
                $check = is_dir( $file );
                if ( !$check )
                    @unlink( $cacheDir . $file );
            }
            @closedir( $dir );
            return true;
        }
    }


    /**
     +----------------------------------------------------------
     * 模板解析部分
     +----------------------------------------------------------
     */

    /**
     +----------------------------------------------------------
     * 模板解析入口
     * 支持普通标签和TagLib解析 支持自定义标签库
     * TODO: 更多方便的HTML标签库支持
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $content 要解析的模板内容
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function parse(& $content)
    {
        // 获取引入的标签库列表
        // 标签库只需要定义一次，允许引入多个一次
        // 一般放在文件的最前面
        // 格式：<taglib name="cx,html" />
        $this->getIncludeTagLib($content);

        if(!empty($this->tagLib)) {
            // 如果有引入TagLib库
            // 则对导入的TagLib进行解析 
            foreach($this->tagLib as $tagLib) {
                $this->parseTagLib($tagLib,$content);
            }
        }

        // 内置了CX标签库支持 无需使用taglib标签导入就可以使用
        // 并且无需添加cx前缀 ，可以直接写成
        // <include file='' /> 
        // <volist id='' name='' ></volist>
        // <var name='' />
        // 的形式
        $this->parseTagLib('cx',$content,true);

        //解析普通模板标签 {tagName:}
        $content = preg_replace('/('.TMPL_L_DELIM.')(.+?)('.TMPL_R_DELIM.')/eis',"\$this->parseTag('\\2')",$content);

        return $content;
    }

    /**
     +----------------------------------------------------------
     * 搜索模板页面中包含的TagLib库
     * 并返回列表
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $content  模板内容
     +----------------------------------------------------------
     * @return string|false
     +----------------------------------------------------------
     */
    function getIncludeTagLib(& $content) 
    {
        //搜索是否有TagLib标签
        $find = preg_match('/<taglib\sname=[\'|\"](.+?)[\'|\"]\s\/>\W/eis',$content,$matches);
        if($find) {
            //把TagLib标签
            $content = str_replace($matches[0],'',$content);
            $tagLibs = $matches[1];
            $tagLibList = explode(',',$tagLibs);
            $this->tagLib = $tagLibList;
        }
        return;
    }


    /**
     +----------------------------------------------------------
     * TagLib库解析
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $tagLib 要解析的标签库
     * @param string $content 要解析的模板内容
     * @param boolen $hide 是否隐藏标签库前缀
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    function parseTagLib($tagLib,&$content,$hide=false) 
    {
        $tLib = new TagLib($tagLib);
        if($tLib->valid()) {
            //如果标签库有效则取出支持标签列表
            $tagList =  $tLib->getTagList();
            //遍历标签列表进行模板标签解析
            foreach($tagList as $tag) {
                if( !$hide) {
                    $startTag = $tagLib.':'.$tag['name'];
                }else {
                	$startTag = $tag['name'];
                }
                $endTag = $startTag;
                if($tag['content'] !='empty') {//闭合标签解析
                    $content = preg_replace('/<'.$startTag.'\s(.+?)>(.+?)<\/'.$endTag.'>/eis',"\$this->parseXmlTag('".$tagLib."','".$tag['name']."','\\1','\\2')",$content);
                    
                }else {//开放标签解析
                    $content = preg_replace('/<'.$startTag.'\s(.+?)><\/'.$endTag.'>/eis',"\$this->parseXmlTag('".$tagLib."','".$tag['name']."','\\1','\\2')",$content);
                    $content = preg_replace('/<'.$startTag.'\s(.+?)\/>/eis',"\$this->parseXmlTag('".$tagLib."','".$tag['name']."','\\1','')",$content);
                }
            }
        }
    }


    /**
     +----------------------------------------------------------
     * 解析标签库的标签
     * 需要调用对应的标签库文件解析类
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $tagLib  标签库名称
     * @param string $tag  标签名
     * @param string $attr  标签属性
     * @param string $content  标签内容
     +----------------------------------------------------------
     * @return string|false
     +----------------------------------------------------------
     */
    function parseXmlTag($tagLib,$tag,$attr,$content) 
    {
        $attr = stripslashes($attr);
        $content = stripslashes($content);
        $content = trim($content);
        $tlClass = 'TagLib_'.ucwords(strtolower($tagLib));
        $parse = '_'.$tag;
        $tl = new $tlClass($this);
        if($tl->valid()) {
            return $tl->$parse($attr,$content);
        }

    }


    /**
     +----------------------------------------------------------
     * 模板标签解析
     * 格式： {TagName:args [|content] }
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $tagStr 标签内容
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function parseTag($tagStr){
        $tagStr = stripslashes($tagStr);
        //还原非模板标签 
        if(preg_match('/^[\s|\d]/is',$tagStr)){
            //过滤空格和数字打头的标签
            return '{'.$tagStr.'}';
        }
        //解析模板变量 格式 {$varName}
        if(substr($tagStr,0,1)=='$'){
            return $this->parseVar(substr($tagStr,1));
        }
        $tagStr = trim($tagStr);
        //注释标签
        if(substr($tagStr,0,2)=='//' || (substr($tagStr,0,2)=='/*' && substr($tagStr,0,2)=='*/')){
            return '';
        }
        //解析其它标签
        //统一标签格式 {TagName:args [|content]}
        $varArray = explode(':',$tagStr);
        //取得标签名称
        $tag = trim(array_shift($varArray));

        //解析标签内容
        $args = explode('|',$varArray[0],2);
        switch(strtoupper($tag)){
            case 'INCLUDE':
                $parseStr = $this->parseInclude(trim($args[0]));
                break;
            case 'VO':
                $parseStr = $this->parseVo($args[0],$args[1]);
                break;
            case 'VOLIST':
                $parseStr = $this->parseVoList($args[0],$args[1]);
                break;
            //这里扩展其它标签
            //…………
            default:$parseStr = '';break;
        }

        return $parseStr;
    }

    /**
     +----------------------------------------------------------
     * 模板变量解析,支持使用函数
     * 格式： {$varname|function1|function2=arg1,arg2}
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $varStr 变量数据
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function parseVar($varStr){
        $varStr = trim($varStr);
        static $_varParseList = array();
        //如果已经解析过该变量字串，则直接返回变量值
        if(isset($_varParseList[$varStr])) return $_varParseList[$varStr];
        $parseStr =''; 
        $varExists = true;
        if(!empty($varStr)){
            $varArray = explode('|',$varStr);
            //取得变量名称
            $var = array_shift($varArray);
            //非法变量过滤 只允许使用 {$var} 形式模板变量
            //TODO：还需要继续完善
            if(preg_match('/->/is',$var)){
                return '';
            }
            //特殊变量
            if(substr($var,0,4)=='FCS.'){
                $name = $this->parseFCSVar($var);
            }
            elseif(strpos($var,'.')!== false) {
                //支持 {$var.property} 方式输出对象的属性
                $vars = explode('.',$var);
                $var  = $vars[0];
                $name = "$$vars[0]->$vars[1]";
            }
            elseif(strpos($var,'[')!== false) {
                //支持 {$var['key']} 方式输出数组
                preg_match('/(.+?)\[(.+?)\]/is',$var,$match);
                $var = $match[1];
                $name = "$$match[1][$match[2]]";
            }
            else {
                $name = "$$var";
            }
            //检测变量是否有定义，防止输出Notice错误
            if(substr($var,0,4)!='FCS.' && !isset($this->tVar[$var]) && !isset($var) ) 
                $varExists = false;
            //对变量使用函数
            if(count($varArray)>0) {
                $name = $this->parseVarFunction($name,$varArray);
            }

            if( empty($name) ) $varExists = false;

            //变量存在而且有值就echo
            if( $varExists ){
                $parseStr = '<?php echo '.$name.' ?>';
            }

        }
        $_varParseList[$varStr] = $parseStr;
        return $parseStr;
    }


    /**
     +----------------------------------------------------------
     * 对模板变量使用函数
     * 格式 {$varname|function1|function2=arg1,arg2}
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $name 变量名
     * @param array $varArray  函数列表
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function parseVarFunction($name,$varArray){
        //对变量使用函数
        $length = count($varArray);
        //取得模板禁止使用函数列表
        $template_deny_funs = explode(',',TMPL_DENY_FUNC_LIST);
        for($i=0;$i<$length ;$i++ ){
            $args = explode('=',$varArray[$i]);
            //模板函数过滤
            $args[0] = trim($args[0]);
            if(!in_array($args[0],$template_deny_funs)){
                if(isset($args[1])){
                    if(strstr($args[1],'###')){
                        $args[1] = str_replace('###',$name,$args[1]);
                        $name = "$args[0]($args[1])";
                    }else{
                        $name = "$args[0]($name,$args[1])";
                    }
                }else if(!empty($args[0])){
                    $name = "$args[0]($name)";
                }
            }
        }
        return $name;
    }

    /**
     +----------------------------------------------------------
     * 特殊模板变量解析
     * 格式 以 $FCS. 打头的变量属于特殊模板变量
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $varStr  变量字符串
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function parseFCSVar($varStr){
        $vars = explode('.',$varStr);
        $vars[1] = strtoupper(trim($vars[1]));
        $parseStr = '';

        if(count($vars)==3){
            $vars[2] = trim($vars[2]);
            switch($vars[1]){
                case 'SERVER':$parseStr = '$_SERVER[\''.$vars[2].'\']';break;
                case 'GET':$parseStr = '$_GET[\''.$vars[2].'\']';break;
                case 'POST':$parseStr = '$_POST[\''.$vars[2].'\']';break;
                case 'COOKIE':$parseStr = '$_COOKIE[\''.$vars[2].'\']';break;
                case 'SESSION':$parseStr = '$_SESSION[\''.$vars[2].'\']';break;
                case 'ENV':$parseStr = '$_ENV[\''.$vars[2].'\']';break;
                case 'REQUEST':$parseStr = '$_REQUEST[\''.$vars[2].'\']';break;
                case 'CONST':$parseStr = strtoupper($vars[2]);break;
                default:break;
            }
        }else if(count($vars)==2){
            switch($vars[1]){
                case 'NOW':$parseStr = "date('Y-m-d g:i a',time())";break;
                case 'VERSION':$parseStr = 'FCS_VERSION';break;    
                case 'TEMPLATE':$parseStr = 'TMPL_FILE_NAME';break;
                case 'LDELIM':$parseStr = 'TMPL_L_DELIM';break;
                case 'RDELIM':$parseStr = 'TMPL_R_DELIM';break;
            }
            if(defined($vars[1])){ $parseStr = strtoupper($vars[1]);}
        }
        return $parseStr;
    }


    /**
     +----------------------------------------------------------
     * 解析Vo对象
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $name Vo对象名
     * @param string $val  标签属性
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function parseVo($name,$val){
         $name = trim($name);
         $varArray = explode('|',$val);
         //取得Vo对象的属性名称
         $property = trim(array_shift($varArray));
         if(substr($property,0,1)=='$'){
             $property = substr($property,1);
         }
		 $parseStr = '$'.$name.'->'.$property;
         if(count($varArray)>0){
             $parseStr = $this->parseVarFunction($parseStr,$varArray);
         }
         $parseStr = '<?php echo '.$parseStr.' ?>';
         return  $parseStr;
    }

    /**
     +----------------------------------------------------------
     * 解析VoList对象
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $name Vo对象名
     * @param string $val  标签属性
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function ParseVoList($name,$content){
         $name = trim($name);
         $content  = trim($content);
            $volist = $this->get($name);
            $parseStr .= '<?php foreach($'.$name.'->toArray() as $_'.$name.'){ ?>';
            foreach ($volist->get(0) as $property=>$val){
                $content = str_replace('$'.$property,'<?php echo $_'.$name.'->'.$property.' ?>',$content);
            }
            $parseStr .= $content;
            $parseStr .= '<?php } ?>';

        return  $parseStr;
    }

    /**
     +----------------------------------------------------------
     * 加载公共模板并缓存 和当前模板在同一路径，否则使用相对路径
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $tmplPublicName  公共模板文件名
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function parseInclude($tmplPublicName){
        if(is_file($tmplPublicName)) {
            $parseStr = file_get_contents($tmplPublicName);
        }else {
            $tmplPublicName = trim($tmplPublicName);
            //支持加载变量文件名
            if(substr($tmplPublicName,0,1)=='$'){
                $tmplPublicName = $this->get(substr($tmplPublicName,1));
            }
            $tmplTemplateFile = TEMPLATE_PATH.'/'.TEMPLATE_MODULE_PATH;
            $tmplTemplateFile .=  trim($tmplPublicName).TEMPLATE_SUFFIX;
            $parseStr = file_get_contents($tmplTemplateFile);        
        }
        //再次对包含文件进行模板分析
        return $this->parse($parseStr);
    }
}//
?>