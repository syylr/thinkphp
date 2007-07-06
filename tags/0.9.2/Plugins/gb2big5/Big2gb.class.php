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
 * @package    Util
 * @link       http://www.fcs.org.cn
 * @copyright  Copyright (c) 2005-2006 liu21st.com.  All rights reserved. 
 * @author     liu21st <liu21st@gmail.com>
 * @version    $Id: Big2gb.class.php 92 2006-11-11 08:47:04Z fcs $
 +------------------------------------------------------------------------------
 */

/**
 +------------------------------------------------------------------------------
 * 简繁转换类
 * 参考 CRLin http://web.dhjh.tcc.edu.tw/~gzqbyr/forum/
 +------------------------------------------------------------------------------
 * @package   core
 * @author    liu21st <liu21st@gmail.com>
 * @version   $Ver$
 +------------------------------------------------------------------------------
 */
class  Big2gb extends Base
{//类定义开始

    /**
     +----------------------------------------------------------
     * 架构函数
     * 
     +----------------------------------------------------------
     * @static
     * @access public 
     +----------------------------------------------------------
     */
    function __construct()
    {

    }

    /**
     +----------------------------------------------------------
     * 取得应用实例对象
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return App
     +----------------------------------------------------------
     */
    function getInstance() 
    {
        return get_instance_of(__CLASS__);
    }

	function chg_utfcode($str,$charset='big5')
	{
        $mapPath =  dirname(__FILE__).'/Big2gb/';
		if ($charset=='big5')
		{
			$str1 = file_get_contents ($mapPath."gb2big.map");
		}
		else 
		{
			$str1 = file_get_contents ($mapPath."big2gb.map");
		}
		// convert to unicode and map code
		$chg_utf = array();
		for ($i=0;$i<strlen($str1);$i=$i+4)
		{
			$ch1=ord(substr($str1,$i,1))*256;
			$ch2=ord(substr($str1,$i+1,1));
			$ch1=$ch1+$ch2;
			$ch3=ord(substr($str1,$i+2,1))*256;
			$ch4=ord(substr($str1,$i+3,1));
			$ch3=$ch3+$ch4;
			$chg_utf[$ch1]=$ch3;
		}
		// convert to UTF-8
		$outstr='';
		for ($k=0;$k<strlen($str);$k++)
		{
			$ch=ord(substr($str,$k,1));
			if ($ch<0x80)
			{
				$outstr.=substr($str,$k,1);
			}
			else
			{
				if ($ch>0xBF && $ch<0xFE)
				{
					if ($ch<0xE0) {
						$i=1;
						$uni_code=$ch-0xC0;
					} elseif ($ch<0xF0)	{
						$i=2;
						$uni_code=$ch-0xE0;
					} elseif ($ch<0xF8)	{
						$i=3;
						$uni_code=$ch-0xF0;
					} elseif ($ch<0xFC)	{
						$i=4;
						$uni_code=$ch-0xF8;
					} else {
						$i=5;
						$uni_code=$ch-0xFC;
					}
				}
	    		$ch1=substr($str,$k,1);
				for ($j=0;$j<$i;$j++)
				{
					$ch1 .= substr($str,$k+$j+1,1);
					$ch=ord(substr($str,$k+$j+1,1))-0x80;
					$uni_code=$uni_code*64+$ch;
				}
				if (isset($chg_utf[$uni_code]) && $chg_utf[$uni_code])
				{
					$outstr.=$this->uni2utf($chg_utf[$uni_code]);
				}
				else
				{
					$outstr.=$ch1;
				}
				$k += $i;
			}
		}
		return $outstr;
	}

	// Return utf-8 character
	function uni2utf($uni_code)
	{
		if ($uni_code<0x80) return chr($uni_code);
		$i=0;
		$outstr='';
		while ($uni_code>63) // 2^6=64
		{
			$outstr=chr($uni_code%64+0x80).$outstr;
			$uni_code=floor($uni_code/64);
			$i++;
		}
		switch($i)
		{
			case 1:
				$outstr=chr($uni_code+0xC0).$outstr;break;
			case 2:
				$outstr=chr($uni_code+0xE0).$outstr;break;
			case 3:
				$outstr=chr($uni_code+0xF0).$outstr;break;
			case 4:
				$outstr=chr($uni_code+0xF8).$outstr;break;
			case 5:
				$outstr=chr($uni_code+0xFC).$outstr;break;
			default:
				echo "unicode error!!";exit;
		}
		return $outstr;
	}

}//类定义结束
?>