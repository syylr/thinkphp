<?php 
/*
Plugin Name: Hello,world!
Plugin URI: http://fcs.org.cn/
Description: Hello,world!插件实现示例.
Author: 流年
Version: 1.0
Author URI: http://blog.liu21st.com/
*/ 

    /**
     +----------------------------------------------------------
     * 检测浏览器类型
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    function detect_browser_type(){
        $userAgent = strtolower($_SERVER['HTTP_USER_AGENT']);
        if( false !== strpos($userAgent, 'opera')){
            $type = 'opera';
        }else if( false !== strpos($userAgent, 'msie') ){
            $type = 'ie';
        }else if( false !== strpos($userAgent, 'firefox') ){
            $type = 'firefox';
        }else{
            $type = 'ns';
        }
        return $type;
    }
    function compress() 
    {
        if( COMPRESS_PAGE && empty($zlibCompress) ){
            // 页面压缩缓存无效，手动方式进行页面压缩
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

?>