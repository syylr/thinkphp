<?php
/*
Plugin Name: htmlCache
Plugin URI: http://thinkphp.cn
Description: 静态缓存支持，根据REQUEST_URI 访问生成静态文件
Author: 流年 killerman
Version: 1.0
Author URI: http://blog.liu21st.com/
*/

// 配置文件增加设置项目如下
// HTML_CACHE_ON 是否启用静态缓存
// HTML_CACHE_TIME 静态缓存有效时间 -1 为永久
// HTML_CACHE_DIR_DEEP 0为不变,1为一级目录，2为二级目录，3为三级目录，如果数据量不大的话，建议使用2级目录，默认为2
function readHTMLCache()
{
        //如果开启HTML功能，载入静态页面，无需再次执行
        if( C('HTML_CACHE_ON') ){
			//缓存目录深度
			//C('HTML_CACHE_DIR_DEEP') || C('HTML_CACHE_DIR_DEEP', 2);
            //生成唯一的静态文件名
			$cacheName = md5($_SERVER['REQUEST_URI']).C('HTMLFILE_SUFFIX');
			//根据缓存目录深度创建
			$cacheFilePath = HTML_PATH.MODULE_NAME.'/'.getHTMLCachePath( $cacheName , C('HTML_CACHE_DIR_DEEP') );
			//判断目录是否存在，不存在则创建
			is_dir( $cacheFilePath ) || _mk_dirs( $cacheFilePath );
			//目录是否可写，不可写则...
			is_writeable( $cacheFilePath ) || chmod( $cacheFilePath, 0755 );

            define('HTML_FILE_NAME',$cacheFilePath . $cacheName);
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
//				  之所以这里注释掉，是因为上面已经创建了
//                if(!file_exists(dirname(HTML_FILE_NAME))) {
//                    mkdir(dirname(HTML_FILE_NAME));
//                }
                if( false === file_put_contents( HTML_FILE_NAME , $content )) {
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

/**
 +----------------------------------------------------------
 * 根据缓存目录深度取得缓存路径
 +----------------------------------------------------------
 * @access public
 +----------------------------------------------------------
 * @param string $cacheFile  缓存文件名
 * @param int $cacheDeep  缓存深度
 +----------------------------------------------------------
 * @return string $cache_path		缓存路径
 +----------------------------------------------------------
 */
function getHTMLCachePath ( $cache_file , $cache_deep = 0){
	if ( !intVal( $cache_deep ) ){
		return '';
	}
	for ( $i = 0; $i < $cache_deep; $i++ ){
		$cache_path .= subStr( $cache_file , $i , 2 ) . '/';
	}
	return $cache_path;
}

/**
 +----------------------------------------------------------
 * 创建目录
 +----------------------------------------------------------
 * @access public
 +----------------------------------------------------------
 * @param string $path  缓存文件名
 +----------------------------------------------------------
 * @return void
 +----------------------------------------------------------
 */
function _mk_dirs ( $path ){
	$pathArray = explode( '/', $path );
	foreach ( $pathArray as $pathValue ){
		$_path .= $pathValue . '/';
		mk_dir( $_path);
	}
}

add_filter('app_init','readHTMLCache');
add_filter('ob_content','writeHTMLCache');

?>