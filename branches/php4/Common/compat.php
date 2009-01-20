<?php
// +----------------------------------------------------------------------
// | ThinkPHP
// +----------------------------------------------------------------------
// | Copyright (c) 2007 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// $Id$

/**
 +------------------------------------------------------------------------------
 * Think兼容函数库 针对5.2.0以下版本
 +------------------------------------------------------------------------------
 * @category   Think
 * @package  Common
 * @author   liu21st <liu21st@gmail.com>
 * @version  $Id$
 +------------------------------------------------------------------------------
 */

if (!function_exists('json_encode')) {
     function format_json_value(&$value)
    {
        if(is_bool($value)) {
            $value = $value?'true':'false';
        }elseif(is_int($value)) {
            $value = intval($value);
        }elseif(is_float($value)) {
            $value = floatval($value);
        }elseif(defined($value) && $value === null) {
            $value = strval(constant($value));
        }elseif(is_string($value)) {
            $value = '"'.addslashes($value).'"';
        }
        return $value;
    }

    function json_encode($data)
    {
        if(is_object($data)) {
            //对象转换成数组
            $data = get_object_vars($data);
        }else if(!is_array($data)) {
            // 普通格式直接输出
            return format_json_value($data);
        }
        // 判断是否关联数组
        if(empty($data) || is_numeric(implode('',array_keys($data)))) {
            $assoc  =  false;
        }else {
            $assoc  =  true;
        }
        // 组装 Json字符串
        $json = $assoc ? '{' : '[' ;
        foreach($data as $key=>$val) {
            if(!is_null($val)) {
                if($assoc) {
                    $json .= "\"$key\":".json_encode($val).",";
                }else {
                    $json .= json_encode($val).",";
                }
            }
        }
        if(strlen($json)>1) {// 加上判断 防止空数组
            $json  = substr($json,0,-1);
        }
        $json .= $assoc ? '}' : ']' ;
        return $json;
    }
}
if (!function_exists('json_decode')) {
    function json_decode($json,$assoc=false)
    {
        // 目前不支持二维数组或对象
        $begin  =  substr($json,0,1) ;
        if(!in_array($begin,array('{','['))) {
            // 不是对象或者数组直接返回
            return $json;
        }
        $parse = substr($json,1,-1);
        $data  = explode(',',$parse);
        if($flag = $begin =='{' ) {
            // 转换成PHP对象
            $result   = new stdClass();
            foreach($data as $val) {
                $item    = explode(':',$val);
                $key =  substr($item[0],1,-1);
                $result->$key = json_decode($item[1],$assoc);
            }
            if($assoc) {
                $result   = get_object_vars($result);
            }
        }else {
            // 转换成PHP数组
            $result   = array();
            foreach($data as $val) {
                $result[]  =  json_decode($val,$assoc);
            }
        }
        return $result;
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

/**
 +----------------------------------------------------------
 * stripslashes扩展 可用于数组
 +----------------------------------------------------------
 * @param mixed $value 变量
 +----------------------------------------------------------
 * @return mixed
 +----------------------------------------------------------
 */
if(!function_exists('stripslashes_deep')) {
    function stripslashes_deep($value) {
       $value = is_array($value) ? array_map('stripslashes_deep', $value) : stripslashes($value);
       return $value;
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
        $fp = fopen($filename, 'rb');
        if (!is_resource($fp)) return false;
        flock($fp, LOCK_SH);
        $data = fread($fp, filesize($filename));
        fclose($fp);
        return $data;
    }
}

if (!function_exists('com_create_guid')){
    /**
     +----------------------------------------------------------
     * 生成一个GUID 适用window和*nix
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
        while(ob_get_length() !== false) ob_end_clean();
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
               closedir($dirlist);
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

if(!function_exists('stripos')) {
function stripos($haystack, $needle){
   return strpos($haystack, stristr( $haystack, $needle ));
}
}
?>