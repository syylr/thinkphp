<?php 
    /*
    Plugin Name: Cache 
    Plugin URI: http://fcs.org.cn
    Description: 缓存支持插件
    Author: 流年
    Version: 1.0
    Author URI: http://blog.liu21st.com/
    */ 

//--------------------------------------------------
// DATA_CACHE_TYPE 缓存类型定义 
//--------------------------------------------------
// 目前支持
// File 无需插件即可支持 CACHE_SERIAL_HEADER  CACHE_SERIAL_FOOTER 设置缓存文件头部和尾部
// Db 数据库缓存 需要设置 DATA_CACHE_TABLE
// Shmop 共享内存 需要设置SHARE_MEM_SIZE 共享内存大小
// Sqlite Sqlite缓存 
// APc APC方式缓存 需要模块支持
// APachenote  需要模块支持
// Eaccelerator 需要模块支持
// memcache  需要模块支持
//---------------------------------------------------
// DATA_CACHE_CHECK 是否需要校验
//---------------------------------------------------
// DATA_CACHE_COMPRESS 是否压缩数据
//---------------------------------------------------

function addCache() 
{
    $classPath  = dirname(__FILE__).'/Cache/';
    $classFile   = 'Cache_'.ucwords(strtolower(DATA_CACHE_TYPE));
    $result  =  include_cache($classPath.$classFile.'.class.php');
    if($result) {
        
    	Session::set(strtoupper(DATA_CACHE_TYPE),$classFile);  
    }
    return ;
}
add_filter('app_init','addCache');
?>