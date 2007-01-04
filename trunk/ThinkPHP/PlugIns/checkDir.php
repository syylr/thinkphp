<?php 
    /*
    Plugin Name: checkDir
    Plugin URI: http://fcs.org.cn
    Description: 目录检查插件
    Author: 流年
    Version: 1.0
    Author URI: http://blog.liu21st.com/
    */ 
    /**
     +----------------------------------------------------------
     * 缓存检查
     * 缓存目录创建、目录权限检查
     +----------------------------------------------------------
     * @access private 
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    function CheckCache()
    {
        //检测模版缓存目录，并尝试创建
        if(!file_exists(CACHE_PATH)) {
        	if (! @ mkdir(CACHE_PATH))
				throw_exception('模版缓存目录：'.CACHE_PATH.'不存在！');
        }
        //检测数据缓存目录，并尝试创建
        if(!file_exists(TEMP_PATH)) {
        	if (! @ mkdir(TEMP_PATH))
				throw_exception('数据缓存目录：'.TEMP_PATH.'不存在！');
        }
        //检测静态缓存目录，并尝试创建
        if(!file_exists(HTML_PATH)) {
        	if (! @ mkdir(HTML_PATH))
				throw_exception('静态缓存目录：'.HTML_PATH.'不存在！');
        }
        //检测日志目录，并尝试创建
        if(!file_exists(LOG_PATH)) {
        	if (! @ mkdir(LOG_PATH))
				throw_exception('日志目录：'.LOG_PATH.'不存在！');
        }
        return ;
    }
    add_filter('app_init','CheckCache');
?>