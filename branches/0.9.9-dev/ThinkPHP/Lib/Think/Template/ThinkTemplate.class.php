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

import('Think.Template.TagLib');
/**
 +------------------------------------------------------------------------------
 * 内置模板引擎类 解析模板标签并输出
 * 支持缓存和页面压缩
 +------------------------------------------------------------------------------
 * @author liu21st <liu21st@gmail.com>
 * @version  $Id$
 +------------------------------------------------------------------------------
 */
class  ThinkTemplate extends Base
{//类定义开始

    // 模板页面中引入的标签库列表
    var $tagLib     =  array();
	// 当前模板文件
    var $templateFile   =  '';
	// 模板变量
	var $tVar = array();

    /**
     +----------------------------------------------------------
     * 取得模板实例对象 
     * 静态方法
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return App
     +----------------------------------------------------------
     */
    function  getInstance() 
    {
        return get_instance_of(__CLASS__);
    }

	// 模板变量获取和设置
	function get($name) {
		return $this->tVar[$name];
	}

	function set($name,$value) {
		$this->tVar[$name]=	$value;
	}

	// 加载模板
	function load($templateFile,$charset,$templateVar,$varPrefix) {
		$this->tVar = $templateVar;
		$templateCacheFile  =  $this->loadTemplate($templateFile,$charset);
		// 模板阵列变量分解成为独立变量
		extract($templateVar, empty($varPrefix)? EXTR_OVERWRITE : EXTR_PREFIX_ALL,$varPrefix); 
		//载入模版缓存文件
		include_once($templateCacheFile);
	}

	/**
     +----------------------------------------------------------
     * 加载主模板并缓存
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $tmplTemplateFile 模板文件
     * @param string $varPrefix  模板变量前缀
     * @param string $charset  模板输出字符集
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function loadTemplate ($tmplTemplateFile='',$charset='')
    {
        if(empty($tmplTemplateFile))    $tmplTemplateFile = TMPL_FILE_NAME;
		if(empty($charset)) $charset = C('OUTPUT_CHARSET');
        if(!file_exists($tmplTemplateFile)){
            $tmplTemplateFile =  dirname(TMPL_FILE_NAME).'/'.$tmplTemplateFile.C('TEMPLATE_SUFFIX');
            if(!file_exists($tmplTemplateFile)){
                throw_exception(L('_TEMPLATE_NOT_EXIST_'));        
            }
        }
        $this->templateFile    =  $tmplTemplateFile;

        //根据模版文件名定位缓存文件
        $tmplCacheFile = CACHE_PATH.md5($tmplTemplateFile).C('CACHFILE_SUFFIX');
        $tmplContent = '';
        // 检查Cache文件是否需要更新
        if (!$this->checkCache($tmplTemplateFile)) { 
            // 需要更新模版 读出原模板内容
            $tmplContent = file_get_contents($tmplTemplateFile);     
            //编译模板内容
            $tmplContent = $this->compiler($tmplContent,$charset); 
            //重写Cache文件
            if( false === file_put_contents($tmplCacheFile,trim($tmplContent))) {
                throw_exception(L('模版缓存文件写入失败！'));
            }
        }
        return $tmplCacheFile;
    }

    /**
     +----------------------------------------------------------
     * 重新编译项目全部模版
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param mixed $tmplContent 模板内容
     * @param string $charset  模板输出字符集
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function buildAllTemplate($tmplPath=TMPL_PATH) 
    {
        // 遍历模版目录
        $themes =  scandir($tmplPath);
        foreach($themes as $key=>$theme) {
        	$modules  = scandir($tmplPath.$theme);
            foreach($modules as $key=>$module) {
            	$actions = scandir($tmplPath.$theme.'/'.$module);
                foreach($actions as $key=>$file) {
                    //读出原模板内容
                    $tmplTemplateFile =  ($tmplPath.$theme.'/'.$module.'/'.$file);
                    $tmplContent = file_get_contents($tmplTemplateFile);        
                    //编译模板内容
                    $tmplContent = $this->compiler($tmplContent); 
                    //重写Cache文件
                    $tmplCacheFile = CACHE_PATH.md5($tmplTemplateFile).C('CACHFILE_SUFFIX');
                    if( false === file_put_contents($tmplCacheFile,trim($tmplContent))) {
                        system_out('模版缓存文件'.$tmplCacheFile.'写入失败！');
                    }                  	
                }
            }
        }
    }
    /**
     +----------------------------------------------------------
     * 编译模板文件内容
     * 包括模板解析、同步路径和编码转换
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param mixed $tmplContent 模板内容
     * @param string $charset  模板输出字符集
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function compiler ( $tmplContent,$charset='')
    {
        //模板解析
        $tmplContent = $this->parse($tmplContent);
		if(empty($charset))  $charset = C('OUTPUT_CHARSET');
        //项目公共目录
        $tmplContent = str_ireplace('../public',APP_PUBLIC_URL,$tmplContent);
        //网站公共目录
        $tmplContent = str_replace('__PUBLIC__',WEB_PUBLIC_URL,$tmplContent);
        //网站根目录
        $tmplContent = str_replace('__ROOT__',__ROOT__,$tmplContent);
        //当前项目地址
        $tmplContent = str_replace('__APP__',__APP__,$tmplContent);
        //当前模块地址
        $tmplContent = str_replace('__URL__',__URL__,$tmplContent);
        //当前项目操作地址
		$tmplContent = str_replace('__ACTION__',__ACTION__,$tmplContent);
        //当前页面操作地址
		$tmplContent = str_replace('__SELF__',__SELF__,$tmplContent);
        //编码替换
        if(C('TEMPLATE_CHARSET') != $charset) {
        	$tmplContent = str_ireplace('charset='.C('TEMPLATE_CHARSET'), 'charset='.$charset, $tmplContent);
        }
        //模版过滤
        $tmplContent =  apply_filter('tmpl_replace',$tmplContent);

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
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function checkCache($tmplTemplateFile)
    {
        $tmplCacheFile = CACHE_PATH.md5($tmplTemplateFile).C('CACHFILE_SUFFIX');
        if(!file_exists($tmplCacheFile)){
            return False;
        }
        elseif (!C('TMPL_CACHE_ON')){
            return false;
        }elseif (filemtime($tmplTemplateFile) > filemtime($tmplCacheFile)) { 
            // 模板文件如果有更新则缓存需要更新
            return False; 
        } elseif (C('TMPL_CACHE_TIME') != -1 && time() > filemtime($tmplCacheFile)+C('TMPL_CACHE_TIME')) { 
            // 缓存是否在有效期
            return False; 
        }
        //缓存有效
        return True;
    }

    /**
     +----------------------------------------------------------
     * 清除缓存或者静态文件
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $filename  缓存文件名
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function cleanCache($filename) 
    { 
        if(file_exists($filename)){
            unlink($filename);
        }
        return;
    } 

    /**
     +----------------------------------------------------------
     * 清除缓存目录下面的文件
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $cacheDir  缓存目录名
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function cleanDir($cacheDir=CACHE_PATH) 
    {
        if ( $dir = opendir( $cacheDir ) )
        {
            while ( $file = readdir( $dir ) )
            {
                $check = is_dir( $file );
                if ( !$check )
                    unlink( $cacheDir . $file );
            }
            closedir( $dir );
            return true;
        }
    }

    /**
     +----------------------------------------------------------
     * 模板解析入口
     * 支持普通标签和TagLib解析 支持自定义标签库
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $content 要解析的模板内容
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function parse($content)
    {
        // 获取引入的标签库列表
        // 标签库只需要定义一次，允许引入多个一次
        // 一般放在文件的最前面
        // 格式：<taglib name="cx,html" class="Think.Util.TagLib.TagLib_Cx,Think.Util.TagLib.TagLib_Html" />
        $this->getIncludeTagLib($content);
        if(!empty($this->tagLib)) {
            // 如果有引入TagLib库
            // 则对导入的TagLib进行解析 
            foreach($this->tagLib as $tagLibName=>$tagLibClass) {
                if(empty($tagLibClass)) {
                	import('Think.Template.TagLib.TagLib_'.ucwords(strtolower($tagLibName)));
                }else {
                	import($tagLibClass);
                }
                $this->parseTagLib($tagLibName,$content);
            }
        }
        // 内置了CX标签库支持 无需使用taglib标签导入就可以使用
        // 并且无需添加cx前缀 ，可以直接写成
        // <include file='' /> 
        // <volist id='' name='' ></volist>
        // <var name='' />
        // 的形式
        import('Think.Template.TagLib.TagLib_Cx');
        $this->parseTagLib('cx',$content,true);
        //解析普通模板标签 {tagName:}
        $content = preg_replace('/('.C('TMPL_L_DELIM').')(\S.+?)('.C('TMPL_R_DELIM').')/eis',"\$this->parseTag('\\2')",$content);
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
        $find = preg_match('/'.C('TAGLIB_BEGIN').'taglib\s(.+?)\s\/'.C('TAGLIB_END').'\W/is',$content,$matches);
        if($find) {
            //替换TagLib标签
            $content = str_replace($matches[0],'',$content);
            //解析TagLib标签
            $tagLibs = $matches[1];
            $xml =  '<tpl><tag '.$tagLibs.' /></tpl>';
            $result = new Config_Xml($xml);
            $array  = $result->toArray();
            $tagLibName =  explode(',',$array['tag']['name']);
            $tagLibClass  =  isset($array['tag']['class'])?explode(',',$array['tag']['class']):array_fill(0,count($tagLibName),'');
            $tagLibList  = array_combine($tagLibName,$tagLibClass);
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
        $tLib = &new TagLib($tagLib);
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
                    $content = preg_replace('/'.C('TAGLIB_BEGIN').$startTag.'\s(.*?)'.C('TAGLIB_END').'(.+?)'.C('TAGLIB_BEGIN').'\/'.$endTag.C('TAGLIB_END').'/eis',"\$this->parseXmlTag('".$tagLib."','".$tag['name']."','\\1','\\2')",$content);
                    
                }else {//开放标签解析
                    //$content = preg_replace('/'.C('TAGLIB_BEGIN').$startTag.'\s(.*?)'.C('TAGLIB_END').'(.*?)'.C('TAGLIB_BEGIN').'\/'.$endTag.C('TAGLIB_END').'/eis',"\$this->parseXmlTag('".$tagLib."','".$tag['name']."','\\1','\\2')",$content);
					// 开始标签必须有一个空格
                    $content = preg_replace('/'.C('TAGLIB_BEGIN').$startTag.'\s(.*?)\/'.C('TAGLIB_END').'/eis',"\$this->parseXmlTag('".$tagLib."','".$tag['name']."','\\1','')",$content);
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
        //if (MAGIC_QUOTES_GPC) {
            $attr = stripslashes($attr);
            $content = stripslashes($content);
        //}
        $content = trim($content);
        $tlClass = 'TagLib_'.ucwords(strtolower($tagLib));
        $parse = '_'.$tag;
        $tl =  & new $tlClass($this);
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
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function parseTag($tagStr){
        //if (MAGIC_QUOTES_GPC) 
			$tagStr = stripslashes($tagStr);
        //还原非模板标签 
        if(preg_match('/^[\s|\d]/is',$tagStr)){
            //过滤空格和数字打头的标签
            return C('TMPL_L_DELIM') . $tagStr .C('TMPL_R_DELIM');
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
            //这里扩展其它标签
            //…………
            default:
                //还原非模版标签
                $parseStr = C('TMPL_L_DELIM') . $tagStr .C('TMPL_R_DELIM');
                break;
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
     * @throws ThinkExecption
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
            //非法变量过滤 不允许在变量里面使用 ->
            //TODO：还需要继续完善
            if(preg_match('/->/is',$var)){
                return '';
            }
            if(substr($var,0,6)=='Think.'){
				// 所有以Think.打头的以特殊变量对待 无需模板赋值就可以输出
                $name = $this->parseThinkVar($var);
            }
            elseif(strpos($var,'.')!== false) {
                //支持 {$var.property} 方式输出对象的属性或者数组，自动判断
				$vars = explode('.',$var);
                $name = 'is_array($'.$vars[0].')?$'.$vars[0].'["'.$vars[1].'"]:$'.$vars[0].'->'.$vars[1];
                $var  = $vars[0];
            }
			elseif(strpos($var,':')!==false){
                //支持 {$var:property} 方式输出对象的属性
				$vars = explode(':',$var);
                $var  =  str_replace(':','->',$var);
				$name = "$".$var;
                $var  = $vars[0];
			}
            elseif(strpos($var,'[')!== false) {
                //支持 {$var['key']} 方式输出数组
                $name = "$".$var;
                preg_match('/(.+?)\[(.+?)\]/is',$var,$match);
                $var = $match[1];
            }
            else {
                $name = "$$var";
            }
            //检测变量是否有定义，防止输出Notice错误
            if(substr($var,0,6)!='Think.' && !isset($this->tVar[$var]) && !isset($var) ) 
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
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function parseVarFunction($name,$varArray){
        //对变量使用函数
        $length = count($varArray);
        //取得模板禁止使用函数列表
        $template_deny_funs = explode(',',C('TMPL_DENY_FUNC_LIST'));
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
     * 格式 以 $Think. 打头的变量属于特殊模板变量
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $varStr  变量字符串
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function parseThinkVar($varStr){
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
                case 'LANG':$parseStr = 'L("'.$vars[2].'")';break;
				case 'CONFIG':$parseStr = 'C("'.$vars[2].'")';break;
                default:break;
            }
        }else if(count($vars)==2){
            switch($vars[1]){
                case 'NOW':$parseStr = "date('Y-m-d g:i a',time())";break;
                case 'VERSION':$parseStr = 'THINK_VERSION';break;    
                case 'TEMPLATE':$parseStr = 'TMPL_FILE_NAME';break;
                case 'LDELIM':$parseStr = 'C("TMPL_L_DELIM")';break;
                case 'RDELIM':$parseStr = 'C("TMPL_R_DELIM")';break;
            }
            if(defined($vars[1])){ $parseStr = strtoupper($vars[1]);}
        }
        return $parseStr;
    }


    /**
     +----------------------------------------------------------
     * 解析Vo对象
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $name Vo对象名
     * @param string $val  标签属性
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws ThinkExecption
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
     * 加载公共模板并缓存 和当前模板在同一路径，否则使用相对路径
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $tmplPublicName  公共模板文件名
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws ThinkExecption
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
            $tmplTemplateFile = dirname($this->templateFile).'/';
            $tmplTemplateFile .=  trim($tmplPublicName).C('TEMPLATE_SUFFIX');
            $parseStr = file_get_contents($tmplTemplateFile);        
        }
        //再次对包含文件进行模板分析
        return $this->parse($parseStr);
    }

}//类定义结束
?>