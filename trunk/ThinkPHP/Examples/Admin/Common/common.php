<?php 
// +----------------------------------------------------------------------
// | ThinkPHP                                                             
// +----------------------------------------------------------------------
// | Copyright (c) 2008 http://thinkphp.cn All rights reserved.      
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>                                  
// +----------------------------------------------------------------------
// $Id$

function toDate($time,$format='Y-m-d H:i:s') 
{
	if( empty($time)) {
		return '';
	}
    $format = str_replace('#',':',$format);
	return date(auto_charset($format),$time);
}

function getStatus($status,$imageShow=true) 
{
    switch($status) {
    	case 0:
            $showText   = '禁用';
            $showImg    = '<IMG SRC="'.APP_PUBLIC_URL.'/Images/locked.gif" WIDTH="20" HEIGHT="20" BORDER="0" ALT="禁用">';
            break;
        case 1:
        default:
            $showText   =   '正常';
            $showImg    =   '<IMG SRC="'.APP_PUBLIC_URL.'/Images/ok.gif" WIDTH="20" HEIGHT="20" BORDER="0" ALT="正常">';
            
    }
    return ($imageShow===true)? auto_charset($showImg) : $showText;
}
?>