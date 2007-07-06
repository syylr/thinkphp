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

//常量
if(!defined('E_STRICT'))  define('E_STRICT',2048);

/**
 +------------------------------------------------------------------------------
 * FCS PHP4 兼容函数库
 +------------------------------------------------------------------------------
 * @package    Common
 * @link       http://www.fcs.org.cn
 * @copyright  Copyright (c) 2005-2006 liu21st.com.  All rights reserved. 
 * @author     liu21st <liu21st@gmail.com>
 * @version    $Id: compat.php 80 2006-11-09 15:55:35Z fcs $
 +------------------------------------------------------------------------------
 */
if (!function_exists('array_diff_key')) {
    /**
     +----------------------------------------------------------
     * 使用键名比较计算数组的差集 PHP5.1.0以上已经定义 
     +----------------------------------------------------------
     * 每个数组不能存在相同的值
     * @param string $property 属性名称
     +----------------------------------------------------------
     * @return boolen
     +----------------------------------------------------------
     */
    function array_diff_key()
    {
       $args = func_get_args();
       return array_flip(call_user_func_array('array_diff',
               array_map('array_flip',$args)));
    }
}

if (!function_exists('property_exists')) {
    /**
     +----------------------------------------------------------
     * 判断对象的属性是否存在 PHP5.1.0以上已经定义 
     +----------------------------------------------------------
     * @param object $class 对象实例
     * @param string $property 属性名称
     +----------------------------------------------------------
     * @return boolen
     +----------------------------------------------------------
     */
    function property_exists($class, $property) {
        if (is_object($class))
         $class = get_class($class);
        return array_key_exists($property, get_class_vars($class));
    }
}

if(!function_exists('array_combine')){
    /**
     +----------------------------------------------------------
     * 合并数组 用一个数组的值作为其键名，另一个数组的值作为其值  
     +----------------------------------------------------------
     * @param array $keys 键名数组
     * @param array $vals 键值数组
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
     */
    function array_combine($keys,$vals){
        $combine = array();
        foreach($keys as $index => $key)
           $combine[$key] = $vals[$index];
        return $combine ;
    }
}


if (!function_exists('file_put_contents')){
    /**
     +----------------------------------------------------------
     * 文件写入 
     +----------------------------------------------------------
     * @param string $filename 文件名
     * @param string $data 数据
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    function file_put_contents($filename,$data) 
    { 
        $len = strlen($data);
        if ( $len > 0 ) {
            $fp = fopen($filename, 'wb'); 
            flock($fp, LOCK_EX);
            $filesize =   fwrite($fp, $data,$len); 
            flock($fp, LOCK_UN);
            fclose($fp); 
            return $filesize;
        }else {
            return false;
        }
    }
}

if (!function_exists('file_get_contents')){
    /**
     +----------------------------------------------------------
     * 读取文件内容 
     +----------------------------------------------------------
     * @param string $filename 文件名
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    function file_get_contents($filename) 
    { 
        $fp = @fopen($filename, 'rb');
        if (!is_resource($fp)) return false;
        @flock($fp, LOCK_SH);
        $data = @fread($fp, filesize($filename));
        @fclose($fp);
        return $data;
    } 
}

if (!function_exists('com_create_guid')){
    /**
     +----------------------------------------------------------
     * 生成一个GUID 
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    function com_create_guid() 
    {
        $charid = strtoupper(md5(uniqid(mt_rand(), true)));
        $hyphen = chr(45);// "-"
        $uuid = chr(123)// "{"
               .substr($charid, 0, 8).$hyphen
               .substr($charid, 8, 4).$hyphen
               .substr($charid,12, 4).$hyphen
               .substr($charid,16, 4).$hyphen
               .substr($charid,20,12)
               .chr(125);// "}"
        return $uuid;
   }
}

if( !function_exists ('mime_content_type')) {
    /**
     +----------------------------------------------------------
     * 获取文件的mime_content类型 
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    function mime_content_type($filename) 
    { 
        if(IS_UNIX && function_exists('system') ) {
        	//return system( trim( 'file -bi ' . escapeshellarg ($filename) ) ) ;
        }
       static $contentType = array( 
			'ai'	=> 'application/postscript',
				'aif'	=> 'audio/x-aiff',
				'aifc'	=> 'audio/x-aiff',
				'aiff'	=> 'audio/x-aiff',
				'asc'	=> 'application/pgp', //changed by skwashd - was text/plain
				'asf'	=> 'video/x-ms-asf',
				'asx'	=> 'video/x-ms-asf',
				'au'	=> 'audio/basic',
				'avi'	=> 'video/x-msvideo',
				'bcpio'	=> 'application/x-bcpio',
				'bin'	=> 'application/octet-stream',
				'bmp'	=> 'image/bmp',
				'c'	=> 'text/plain', // or 'text/x-csrc', //added by skwashd
				'cc'	=> 'text/plain', // or 'text/x-c++src', //added by skwashd
				'cs'	=> 'text/plain', //added by skwashd - for C# src
				'cpp'	=> 'text/x-c++src', //added by skwashd
				'cxx'	=> 'text/x-c++src', //added by skwashd
				'cdf'	=> 'application/x-netcdf',
				'class'	=> 'application/octet-stream',//secure but application/java-class is correct
				'com'	=> 'application/octet-stream',//added by skwashd
				'cpio'	=> 'application/x-cpio',
				'cpt'	=> 'application/mac-compactpro',
				'csh'	=> 'application/x-csh',
				'css'	=> 'text/css',
				'csv'	=> 'text/comma-separated-values',//added by skwashd
				'dcr'	=> 'application/x-director',
				'diff'	=> 'text/diff',
				'dir'	=> 'application/x-director',
				'dll'	=> 'application/octet-stream',
				'dms'	=> 'application/octet-stream',
				'doc'	=> 'application/msword',
				'dot'	=> 'application/msword',//added by skwashd
				'dvi'	=> 'application/x-dvi',
				'dxr'	=> 'application/x-director',
				'eps'	=> 'application/postscript',
				'etx'	=> 'text/x-setext',
				'exe'	=> 'application/octet-stream',
				'ez'	=> 'application/andrew-inset',
				'gif'	=> 'image/gif',
				'gtar'	=> 'application/x-gtar',
				'gz'	=> 'application/x-gzip',
				'h'	=> 'text/plain', // or 'text/x-chdr',//added by skwashd
				'h++'	=> 'text/plain', // or 'text/x-c++hdr', //added by skwashd
				'hh'	=> 'text/plain', // or 'text/x-c++hdr', //added by skwashd
				'hpp'	=> 'text/plain', // or 'text/x-c++hdr', //added by skwashd
				'hxx'	=> 'text/plain', // or 'text/x-c++hdr', //added by skwashd
				'hdf'	=> 'application/x-hdf',
				'hqx'	=> 'application/mac-binhex40',
				'htm'	=> 'text/html',
				'html'	=> 'text/html',
				'ice'	=> 'x-conference/x-cooltalk',
				'ics'	=> 'text/calendar',
				'ief'	=> 'image/ief',
				'ifb'	=> 'text/calendar',
				'iges'	=> 'model/iges',
				'igs'	=> 'model/iges',
				'jar'	=> 'application/x-jar', //added by skwashd - alternative mime type
				'java'	=> 'text/x-java-source', //added by skwashd
				'jpe'	=> 'image/jpeg',
				'jpeg'	=> 'image/jpeg',
				'jpg'	=> 'image/jpeg',
				'js'	=> 'application/x-javascript',
				'kar'	=> 'audio/midi',
				'latex'	=> 'application/x-latex',
				'lha'	=> 'application/octet-stream',
				'log'	=> 'text/plain',
				'lzh'	=> 'application/octet-stream',
				'm3u'	=> 'audio/x-mpegurl',
				'man'	=> 'application/x-troff-man',
				'me'	=> 'application/x-troff-me',
				'mesh'	=> 'model/mesh',
				'mid'	=> 'audio/midi',
				'midi'	=> 'audio/midi',
				'mif'	=> 'application/vnd.mif',
				'mov'	=> 'video/quicktime',
				'movie'	=> 'video/x-sgi-movie',
				'mp2'	=> 'audio/mpeg',
				'mp3'	=> 'audio/mpeg',
				'mpe'	=> 'video/mpeg',
				'mpeg'	=> 'video/mpeg',
				'mpg'	=> 'video/mpeg',
				'mpga'	=> 'audio/mpeg',
				'ms'	=> 'application/x-troff-ms',
				'msh'	=> 'model/mesh',
				'mxu'	=> 'video/vnd.mpegurl',
				'nc'	=> 'application/x-netcdf',
				'oda'	=> 'application/oda',
				'patch'	=> 'text/diff',
				'pbm'	=> 'image/x-portable-bitmap',
				'pdb'	=> 'chemical/x-pdb',
				'pdf'	=> 'application/pdf',
				'pgm'	=> 'image/x-portable-graymap',
				'pgn'	=> 'application/x-chess-pgn',
				'pgp'	=> 'application/pgp',//added by skwashd
				'php'	=> 'application/x-httpd-php',
				'php3'	=> 'application/x-httpd-php3',
				'pl'	=> 'application/x-perl',
				'pm'	=> 'application/x-perl',
				'png'	=> 'image/png',
				'pnm'	=> 'image/x-portable-anymap',
				'po'	=> 'text/plain',
				'ppm'	=> 'image/x-portable-pixmap',
				'ppt'	=> 'application/vnd.ms-powerpoint',
				'ps'	=> 'application/postscript',
				'qt'	=> 'video/quicktime',
				'ra'	=> 'audio/x-realaudio',
				'ram'	=> 'audio/x-pn-realaudio',
				'ras'	=> 'image/x-cmu-raster',
				'rgb'	=> 'image/x-rgb',
				'rm'	=> 'audio/x-pn-realaudio',
				'roff'	=> 'application/x-troff',
				'rpm'	=> 'audio/x-pn-realaudio-plugin',
				'rtf'	=> 'text/rtf',
				'rtx'	=> 'text/richtext',
				'sgm'	=> 'text/sgml',
				'sgml'	=> 'text/sgml',
				'sh'	=> 'application/x-sh',
				'shar'	=> 'application/x-shar',
				'shtml'	=> 'text/html',
				'silo'	=> 'model/mesh',
				'sit'	=> 'application/x-stuffit',
				'skd'	=> 'application/x-koan',
				'skm'	=> 'application/x-koan',
				'skp'	=> 'application/x-koan',
				'skt'	=> 'application/x-koan',
				'smi'	=> 'application/smil',
				'smil'	=> 'application/smil',
				'snd'	=> 'audio/basic',
				'so'	=> 'application/octet-stream',
				'spl'	=> 'application/x-futuresplash',
				'src'	=> 'application/x-wais-source',
				'stc'	=> 'application/vnd.sun.xml.calc.template',
				'std'	=> 'application/vnd.sun.xml.draw.template',
				'sti'	=> 'application/vnd.sun.xml.impress.template',
				'stw'	=> 'application/vnd.sun.xml.writer.template',
				'sv4cpio'	=> 'application/x-sv4cpio',
				'sv4crc'	=> 'application/x-sv4crc',
				'swf'	=> 'application/x-shockwave-flash',
				'sxc'	=> 'application/vnd.sun.xml.calc',
				'sxd'	=> 'application/vnd.sun.xml.draw',
				'sxg'	=> 'application/vnd.sun.xml.writer.global',
				'sxi'	=> 'application/vnd.sun.xml.impress',
				'sxm'	=> 'application/vnd.sun.xml.math',
				'sxw'	=> 'application/vnd.sun.xml.writer',
				't'	=> 'application/x-troff',
				'tar'	=> 'application/x-tar',
				'tcl'	=> 'application/x-tcl',
				'tex'	=> 'application/x-tex',
				'texi'	=> 'application/x-texinfo',
				'texinfo'	=> 'application/x-texinfo',
				'tgz'	=> 'application/x-gtar',
				'tif'	=> 'image/tiff',
				'tiff'	=> 'image/tiff',
				'tr'	=> 'application/x-troff',
				'tsv'	=> 'text/tab-separated-values',
				'txt'	=> 'text/plain',
				'ustar'	=> 'application/x-ustar',
				'vbs'	=> 'text/plain', //added by skwashd - for obvious reasons
				'vcd'	=> 'application/x-cdlink',
				'vcf'	=> 'text/x-vcard',
				'vcs'	=> 'text/calendar',
				'vfb'	=> 'text/calendar',
				'vrml'	=> 'model/vrml',
				'vsd'	=> 'application/vnd.visio',
				'wav'	=> 'audio/x-wav',
				'wax'	=> 'audio/x-ms-wax',
				'wbmp'	=> 'image/vnd.wap.wbmp',
				'wbxml'	=> 'application/vnd.wap.wbxml',
				'wm'	=> 'video/x-ms-wm',
				'wma'	=> 'audio/x-ms-wma',
				'wmd'	=> 'application/x-ms-wmd',
				'wml'	=> 'text/vnd.wap.wml',
				'wmlc'	=> 'application/vnd.wap.wmlc',
				'wmls'	=> 'text/vnd.wap.wmlscript',
				'wmlsc'	=> 'application/vnd.wap.wmlscriptc',
				'wmv'	=> 'video/x-ms-wmv',
				'wmx'	=> 'video/x-ms-wmx',
				'wmz'	=> 'application/x-ms-wmz',
				'wrl'	=> 'model/vrml',
				'wvx'	=> 'video/x-ms-wvx',
				'xbm'	=> 'image/x-xbitmap',
				'xht'	=> 'application/xhtml+xml',
				'xhtml'	=> 'application/xhtml+xml',
				'xls'	=> 'application/vnd.ms-excel',
				'xlt'	=> 'application/vnd.ms-excel',
				'xml'	=> 'application/xml',
				'xpm'	=> 'image/x-xpixmap',
				'xsl'	=> 'text/xml',
				'xwd'	=> 'image/x-xwindowdump',
				'xyz'	=> 'chemical/x-xyz',
				'z'	=> 'application/x-compress',
				'zip'	=> 'application/zip',
       ); 
       $type = strtolower(substr(strrchr($filename, '.'),1));
       if(isset($contentType[$type])) {
            $mime = $contentType[$type];
       }else {
       	    $mime = 'text/plain';
       }
       return $mime; 
    } 
}

if(!function_exists('image_type_to_extension'))
{
   function image_type_to_extension($imagetype)
   {
       if(empty($imagetype)) return false;
       switch($imagetype)
       {
           case IMAGETYPE_GIF    : return 'gif';
           case IMAGETYPE_JPEG    : return 'jpg';
           case IMAGETYPE_PNG    : return 'png';
           case IMAGETYPE_SWF    : return 'swf';
           case IMAGETYPE_PSD    : return 'psd';
           case IMAGETYPE_BMP    : return 'bmp';
           case IMAGETYPE_TIFF_II : return 'tiff';
           case IMAGETYPE_TIFF_MM : return 'tiff';
           case IMAGETYPE_JPC    : return 'jpc';
           case IMAGETYPE_JP2    : return 'jp2';
           case IMAGETYPE_JPX    : return 'jpf';
           case IMAGETYPE_JB2    : return 'jb2';
           case IMAGETYPE_SWC    : return 'swc';
           case IMAGETYPE_IFF    : return 'aiff';
           case IMAGETYPE_WBMP    : return 'wbmp';
           case IMAGETYPE_XBM    : return 'xbm';
           default                : return false;
       }
   }
}


if(!function_exists('str_ireplace')) {
    /**
     +----------------------------------------------------------
     * 字符串替换，不区分大小写，PHP5已经内置支持 
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    function str_ireplace($needle,$replace,$haystack,$pos=0)    {
       if(is_array($needle)) {
           foreach($needle AS $key => $value) {
               $haystack = str_ireplace($value, ((is_array($replace) && isset($replace[$key]))? $replace[$key] : $replace),$haystack,$pos);
           }
           return $haystack;
       }
       $b=explode(strtolower($needle),strtolower($haystack));
       foreach($b AS $k => $v)    {
           $b[$k]=substr($haystack,$pos,strlen($v));
           $pos+=strlen($v)+strlen($needle);
       }
       return implode($replace,$b);
   }
}

if (!function_exists("ob_get_clean")) {
    /**
     +----------------------------------------------------------
     * 获取并清空缓存 
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    function ob_get_clean() {
        $ob_contents = ob_get_contents();
        while(ob_get_length() !== false) @ob_end_clean(); 
        return $ob_contents;
    }
}


if (!function_exists ('php_strip_whitespace'))
{
    /**
     +----------------------------------------------------------
     * 去掉代码中的注释和空格  参数可以是文件名或者代码字符串
     × PHP5 自带 但只能接受文件名作为变量
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    if (!defined ('T_ML_COMMENT'))  define ('T_ML_COMMENT', T_COMMENT);
    if (!defined ('T_DOC_COMMENT')) define ('T_DOC_COMMENT', T_ML_COMMENT);
    function php_strip_whitespace ($filename)
    {
        if ( is_file($filename))
        {
            $content = file_get_contents ($filename);
        }elseif(is_string($filename)) {
            $content = $filename;
        }else {
            return false;
        }
        $stripStr = '';
        //分析php源码
        $tokens =   token_get_all ($content);
        $last_space = false;
        for ($i = 0, $j = count ($tokens); $i < $j; $i++)
        {
            if (is_string ($tokens[$i]))
            {
                $last_space = false;
                $stripStr .= $tokens[$i];
            }
            else
            {
                switch ($tokens[$i][0])
                {
                    //过滤各种PHP注释
                    case T_COMMENT:
                    case T_ML_COMMENT:
                    case T_DOC_COMMENT:
                        break;
                    //过滤空格
                    case T_WHITESPACE:
                        if (!$last_space)
                        {
                            $stripStr .= ' ';
                            $last_space = true;
                        }
                        break;
                    default:
                        $last_space = false;
                        $stripStr .= $tokens[$i][1];
                }
            }
        }
        return $stripStr;
    }
}

if(!function_exists('http_build_query')) {
   function http_build_query( $formdata, $numeric_prefix = null, $key = null ) {
       $res = array();
       foreach ((array)$formdata as $k=>$v) {
           $tmp_key = urlencode(is_int($k) ? $numeric_prefix.$k : $k);
           if ($key) $tmp_key = $key.'['.$tmp_key.']';
           $res[] = ( ( is_array($v) || is_object($v) ) ? http_build_query($v, null, $tmp_key) : $tmp_key."=".urlencode($v) );
       }
       $separator = ini_get('arg_separator.output');
       return implode($separator, $res);
   }
}

if( !function_exists('memory_get_usage') )
{
   function memory_get_usage()
   {
       $pid = getmypid();
       if ( IS_WIN ) 
       {
           exec( 'tasklist /FI "PID eq ' . $pid . '" /FO LIST',$output);
           return preg_replace( '/[^0-9]/', '', $output[5] ) * 1024;
       }else{
           exec("ps -eo%mem,rss,pid | grep $pid", $output);
           $output = explode("  ", $output[0]);
           return $output[1] * 1024;
       }
   }
}

if(!function_exists('scandir')) {
   function scandir($dir, $sortorder = 0) {
       if(is_dir($dir)){
           static $_list = array();
           if(!isset($_list[$dir])) {
               $dirlist = opendir($dir);
               while( ($file = readdir($dirlist)) !== false) {
                   if(!is_dir($file)) {
                       $files[] = $file;
                   }
               }
               closedir($dir);
               ($sortorder == 0) ? asort($files) : rsort($files); // arsort was replaced with rsort
               $_list[$dir] = $files;
               return $files;           	
           }else {
           	    return $_list[$dir];
           }

       } else {
           return FALSE;
       }
   }
}

?>