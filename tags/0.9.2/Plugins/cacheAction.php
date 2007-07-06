<?php 
/*
Plugin Name: cacheAction
Plugin URI: http://fcs.org.cn/
Description: 可以对模块的Action进行缓存 ，在模块类中使用addActionCache($action,$cacheTime)方法添加某个action的缓存时间
Author: 流年
Version: 1.0
Author URI: http://blog.liu21st.com/
*/ 
    /**
     +----------------------------------------------------------
     *  添加操作缓存
     +----------------------------------------------------------
     */
    function addActionCache($action,$cacheTime=60) 
    {
       	Session::set(MODULE_NAME.'_'.$action,$cacheTime);
        return ;
    }

    /**
     +----------------------------------------------------------
     *  删除操作缓存
     +----------------------------------------------------------
     */
    function removeActionCache($action) 
    {
       	Session::set(MODULE_NAME.'_'.$action,null);
        return ;
    }

    /**
     +----------------------------------------------------------
     * 检查并读取操作缓存
     *
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function checkActionCache() 
    {
        if(Session::is_set(MODULE_NAME.'_'.ACTION_NAME)) {
        	$cacheTime   = Session::get(MODULE_NAME.'_'.ACTION_NAME);
            $cacheFile =  TEMP_PATH.'cache_'.MODULE_NAME.'_'.ACTION_NAME.'.html';
            if(time()<=filemtime($cacheFile)+$cacheTime) {
                //读取缓存Action
                readfile($cacheFile);
                exit();
            }
        }
        return ;
    }


    /**
     +----------------------------------------------------------
     * 写入操作缓存
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function writeActionCache($content) 
    {
        //如果当前操作需要缓存，则生成缓存Action页面
        if(Session::is_set(MODULE_NAME.'_'.ACTION_NAME)) {
            $cacheTime   = Session::get(MODULE_NAME.'_'.ACTION_NAME);
            $cacheFile =  TEMP_PATH.'cache_'.MODULE_NAME.'_'.ACTION_NAME.'.html';
            if(time()>filemtime($cacheFile)+$cacheTime) {
                //缓存无效
                if( false === file_put_contents($cacheFile,trim($content))) {
                    throw_exception('缓存文件写入失败！');
                }
            }else {
            	//缓存有效
                return ;
            }
        }
        return $content;
    }
    add_filter('app_init','checkActionCache');
    add_filter('ob_content','writeActionCache');
?>