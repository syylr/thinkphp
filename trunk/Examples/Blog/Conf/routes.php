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

return array(
	'Category@'=>array(
		array('/^\/(\d+)(\/p\/\d)?$/','Blog','category','id'),
		),
	'Blog@'=>array(
		array('/^\/(\d+)(\/p\/\d)?$/','Blog','read','id'),
		array('/^\/(\d+)\/(\d+)/','Blog','archive','year,month'),
		),
	'Download'=>array('Blog','downFile','id'),
);
?>