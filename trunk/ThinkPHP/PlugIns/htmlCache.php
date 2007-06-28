<?php 
/*
Plugin Name: htmlCache
Plugin URI: http://fcs.org.cn/
Description: 静态缓存支持，根据REQUEST_URI 访问生成静态文件
Author: 流年
Version: 1.0
Author URI: http://blog.liu21st.com/
*/ 

// 配置文件增加设置项目如下
// HTML_CACHE_ON 是否启用静态缓存
// HTML_CACHE_TIME 静态缓存有效时间 -1 为永久
// 
function readHTMLCache() 
{
        //如果开启HTML功能，载入静态页面，无需再次执行
        if( C('HTML_CACHE_ON') ){
            //生成唯一的静态文件名
            define('HTML_FILE_NAME',HTML_PATH.MODULE_NAME.'/'.md5($_SERVER['REQUEST_URI']).C('HTMLFILE_SUFFIX'));
            if (checkHTMLCache()) {//静态页面有效
                if(substr_count(HTML_PATH,WEB_ROOT)) {
                    // 如果静态目录在WEB下，重定向到静态页面
                	redirect(str_replace(WEB_ROOT,WEB_URL,dirname(HTML_FILE_NAME)).'/'.basename(HTML_FILE_NAME));
                }else {
                    // 如果静态目录不在WEB下，读取静态页面
                	readfile(HTML_FILE_NAME);
                    exit();
                }
                return;
            }
        }	
}


function writeHTMLCache(&$content) 
{
    //静态文件写入
    if(C('HTML_CACHE_ON')){
            // 如果开启HTML功能 检查并重写HTML文件
            // 没有模版的操作不生成静态文件
            if(MODULE_NAME != 'Public' && !checkHTMLCache()) {
                if(!file_exists(dirname(HTML_FILE_NAME))) {
                    mkdir(dirname(HTML_FILE_NAME));
                }
                if( false === file_put_contents(HTML_FILE_NAME,$content)) {
                    throw_exception(L('文件写入失败！'));
                }
            }
    } 
    return $content;
}

/**
 +----------------------------------------------------------
 * 检查静态HTML文件是否有效
 * 如果无效需要重新更新
 +----------------------------------------------------------
 * @access public 
 +----------------------------------------------------------
 * @param string $tmplHTMLFile  数据表名
 +----------------------------------------------------------
 * @return boolen
 +----------------------------------------------------------
 */
function checkHTMLCache($tmplHTMLFile = HTML_FILE_NAME)
{
    if(!file_exists($tmplHTMLFile)){
        return False;
    }
    elseif (!C('HTML_CACHE_ON')){    
        return False;
    }
    elseif (filemtime(TMPL_FILE_NAME) > filemtime($tmplHTMLFile)) { 
        // 模板文件如果更新静态文件需要更新
        return False; 
    }
    elseif (C('HTML_CACHE_TIME') != -1 && time() > filemtime($tmplHTMLFile)+C('HTML_CACHE_TIME')) { 
        // 文件是否在有效期
        return False; 
    }
    //静态文件有效
    return True;
}

	add_filter('app_init','readHTMLCache');
    add_filter('ob_content','writeHTMLCache');

?>