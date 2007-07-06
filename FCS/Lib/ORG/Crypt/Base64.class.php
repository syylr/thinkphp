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
 * @version    $Id: Base64.class.php 73 2006-11-08 10:08:01Z fcs $
 +------------------------------------------------------------------------------
 */

/**
 +------------------------------------------------------------------------------
 * Base64 加密实现类
 +------------------------------------------------------------------------------
 * @package   Util
 * @author    liu21st <liu21st@gmail.com>
 * @version   $Ver$
 +------------------------------------------------------------------------------
 */
class Base64 extends Base
{
	
    /**
     +----------------------------------------------------------
     * 加密字符串
     * 
     +----------------------------------------------------------
     * @access static 
     +----------------------------------------------------------
     * @param string $str 字符串
     * @param string $key 加密key
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function encrypt($data,$key) 
    { 
        $key    =   md5($key); 
        $data   =   base64_encode($data); 
        $x=0; 
        for ($i=0;$i<strlen($data);$i++) 
        { 
            if ($x==strlen($key)) $x=0; 
            $char   .=substr($key,$x,1); 
            $x++;     
        } 
        for ($i=0;$i<strlen($data);$i++) 
        { 
            $str    .=chr(ord(substr($data,$i,1))+(ord(substr($char,$i,1)))%256);     
        } 
        return $str; 
    }       
    /**
     +----------------------------------------------------------
     * 解密字符串
     * 
     +----------------------------------------------------------
     * @access static 
     +----------------------------------------------------------
     * @param string $str 字符串
     * @param string $key 加密key
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function decrypt($data,$key) 
    { 
        $key    =   md5($key); 
        $x=0; 
        for ($i=0;$i<strlen($data);$i++) 
        { 
            if ($x==strlen($key)) $x=0; 
            $char   .=substr($key,$x,1); 
            $x++;     
        } 
        for ($i=0;$i<strlen($data);$i++) 
        { 
            if (ord(substr($data,$i,1))<ord(substr($char,$i,1))) 
            { 
                $str    .=chr((ord(substr($data,$i,1))+256)-ord(substr($char,$i,1)));     
            } 
            else 
            { 
                $str    .=chr(ord(substr($data,$i,1))-ord(substr($char,$i,1))); 
            } 
        } 
        return base64_decode($str); 
    } 
}
?>