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
 * @version    $Id: Crypt.class.php 73 2006-11-08 10:08:01Z fcs $
 +------------------------------------------------------------------------------
 */

/**
 +------------------------------------------------------------------------------
 * Crypt 加密实现类
 +------------------------------------------------------------------------------
 * @package   Util
 * @author    liu21st <liu21st@gmail.com>
 * @version   $Ver$
 +------------------------------------------------------------------------------
 */
class Crypt extends Base
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
    function encrypt($str,$key,$toBase64=false){ 
        $r = md5($key); 
        $c=0; 
        $v = ""; 
        for ($i=0;$i<strlen($str);$i++){ 
         if ($c==strlen($r)) $c=0; 
         $v.= substr($r,$c,1) . 
             (substr($str,$i,1) ^ substr($r,$c,1)); 
         $c++; 
        } 
        if($toBase64) {
            return base64_encode(Crypt::ed($v,$key)); 
        }else {
            return Crypt::ed($v,$key); 
        }
        
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
    function decrypt($str,$key,$toBase64=false) { 
        if($toBase64) {
            $str = Crypt::ed(base64_decode($str),$key); 
        }else {
            $str = Crypt::ed($str,$key); 
        }
        $v = ""; 
        for ($i=0;$i<strlen($str);$i++){ 
         $md5 = substr($str,$i,1); 
         $i++; 
         $v.= (substr($str,$i,1) ^ $md5); 
        } 
        return $v; 
    } 


   function ed($str,$key) { 
      $r = md5($key); 
      $c=0; 
      $v = ""; 
      for ($i=0;$i<strlen($str);$i++) { 
         if ($c==strlen($r)) $c=0; 
         $v.= substr($str,$i,1) ^ substr($r,$c,1); 
         $c++; 
      } 
      return $v; 
   } 
}
?>