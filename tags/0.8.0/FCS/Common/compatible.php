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
 * FCS PHP4 兼容函数库
 +------------------------------------------------------------------------------
 * @package    Common
 * @link       http://www.fcs.org.cn
 * @copyright  Copyright (c) 2005-2006 liu21st.com.  All rights reserved. 
 * @author     liu21st <liu21st@gmail.com>
 * @version    $Id$
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

if(!function_exists('mime_content_type' )) {
    /**
     +----------------------------------------------------------
     * 获取文件的mime_content类型 
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    function mime_content_type($filename) 
    { 
       $contentType = array( 
           '3dmf' => 'x-world/x-3dmf', 
           'a' => 'application/octet-stream', 
           'aab' => 'application/x-authorware-bin', 
           'xwd' => 'image/x-xwd', 
           'xyz' => 'chemical/x-pdb', 
           'z' => 'application/x-compressed', 
           'zip' => 'application/x-zip-compressed', 
           'zoo' => 'application/octet-stream', 
           'zsh' => 'text/x-script.zsh', 
       ); 
       $type = strtolower(substr(strrchr($filename, '.'),1));
       if(isset($contentType[$type])) {
            $mime = $contentType[$type];
       }else {
       	    $mime = 'application/octetstream';
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

?>