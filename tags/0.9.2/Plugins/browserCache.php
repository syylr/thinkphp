<?php 
    /*
    Plugin Name: browserCache
    Plugin URI: http://fcs.org.cn
    Description: 浏览器缓存支持. 使用Header信息
    Author: 流年
    Version: 1.0
    Author URI: http://blog.liu21st.com/
    */ 
    function broswerCache($content) 
    {
    	//浏览器缓存 高配置服务器可以开启
        $hash       = md5($content);
        $headers   = getallheaders();
        if (isset($headers['If-None-Match']) && strstr($hash,$headers['If-None-Match']))
        {
            header('HTTP/1.1 304 Not Modified');
            exit;
        }
        header("ETag: {$hash}");
        return $content;
    }

    function set_header() 
    {
        // Header信息设置
        header("Cache-control: private");  //支持页面回跳
        if(DATA_CACHE_ON) {
            header("Last-Modified:". gmdate("D, d M Y H:i:s") . " GMT");
            header("Expires:".gmdate("D, d M Y H:i:s", time() + DATA_CACHE_TIME ) . " GMT");
        }    	
    }

if(defined('BROWSER_CACHE') && BROWSER_CACHE) {
    add_filter('ob_start','set_header');
    add_filter('ob_content','broswerCache');	
}
?>