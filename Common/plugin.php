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

/**
 +----------------------------------------------------------
 * 判断目录是否为空
 +----------------------------------------------------------
 * @return void
 +----------------------------------------------------------
 */
function empty_dir($directory)
{
    $handle = opendir($directory);
    while (($file = readdir($handle)) !== false)
    {
        if ($file != "." && $file != "..")
        {
            closedir($handle);
            return false;
        }
    }
    closedir($handle);
    return true;
}

/**
 +----------------------------------------------------------
 * 读取插件
 +----------------------------------------------------------
 * @param string $path 插件目录
 * @param string $app 所属项目名
 +----------------------------------------------------------
 * @return Array
 +----------------------------------------------------------
 */
function get_plugins($path=PLUGIN_PATH,$app=APP_NAME,$ext='.php')
{
    static $plugins = array ();
    if(isset($plugins[$app])) {
        return $plugins[$app];
    }
    // 如果插件目录为空 返回空数组
    if(empty_dir($path)) {
        return array();
    }
    $path = realpath($path);

    // 缓存无效 重新读取插件文件
    /*
    $dir = glob ( $path . '/*' );
    if($dir) {
       foreach($dir as $val) {
            if(is_dir($val)){
                $subdir = glob($val.'/*'.$ext);
                if($subdir) {
                    foreach($subdir as $file)
                        $plugin_files[] = $file;
                }
            }else{
                if (strrchr($val, '.') == $ext)
                    $plugin_files[] = $val;
            }
       }
       */

    $dir = dir($path);
    if($dir) {
        $plugin_files = array();
        while (false !== ($file = $dir->read())) {
            if($file == "." || $file == "..")   continue;
            if(is_dir($path.'/'.$file)){
                    $subdir =  dir($path.'/'.$file);
                    if ($subdir) {
                        while (($subfile = $subdir->read()) !== false) {
                            if($subfile == "." || $subfile == "..")   continue;
                            if (preg_match('/\.php$/', $subfile))
                                $plugin_files[] = "$file/$subfile";
                        }
                        $subdir->close();
                    }
            }else{
                $plugin_files[] = $file;
            }
        }
        $dir->close();

        //对插件文件排序
        if(count($plugin_files)>1) {
            sort($plugin_files);
        }
        $plugins[$app] = array();
        foreach ($plugin_files as $plugin_file) {
            if ( !is_readable("$path/$plugin_file"))        continue;
            //取得插件文件的信息
            $plugin_data = get_plugin_info("$path/$plugin_file");
            if (empty ($plugin_data['name'])) {
                continue;
            }
            $plugins[$app][] = $plugin_data;
        }
       return $plugins[$app];
    }else {
        return array();
    }
}

/**
 +----------------------------------------------------------
 * 获取插件信息
 +----------------------------------------------------------
 * @param string $plugin_file 插件文件名
 +----------------------------------------------------------
 * @return Array
 +----------------------------------------------------------
 */
function get_plugin_info($plugin_file) {

    $plugin_data = file_get_contents($plugin_file);
    preg_match("/Plugin Name:(.*)/i", $plugin_data, $plugin_name);
    if(empty($plugin_name)) {
        return false;
    }
    preg_match("/Plugin URI:(.*)/i", $plugin_data, $plugin_uri);
    preg_match("/Description:(.*)/i", $plugin_data, $description);
    preg_match("/Author:(.*)/i", $plugin_data, $author_name);
    preg_match("/Author URI:(.*)/i", $plugin_data, $author_uri);
    if (preg_match("/Version:(.*)/i", $plugin_data, $version))
        $version = trim($version[1]);
    else
        $version = '';
    if(!empty($author_name)) {
        if(!empty($author_uri)) {
            $author_name = '<a href="'.trim($author_uri[1]).'" target="_blank">'.$author_name[1].'</a>';
        }else {
            $author_name = $author_name[1];
        }
    }else {
        $author_name = '';
    }
    return array ('file'=>$plugin_file,'name' => trim($plugin_name[1]), 'uri' => trim($plugin_uri[1]), 'description' => trim($description[1]), 'author' => trim($author_name), 'version' => $version);
}

/**
 +----------------------------------------------------------
 * 动态添加模版编译引擎
 +----------------------------------------------------------
 * @param string $tag 模版引擎定义名称
 * @param string $compiler 编译器名称
 +----------------------------------------------------------
 * @return boolean
 +----------------------------------------------------------
 */
function add_compiler($tag,$compiler)
{
    $GLOBALS['template_compiler'][strtoupper($tag)] = $compiler ;
    return ;
}

/**
 +----------------------------------------------------------
 * 使用模版编译引擎
 +----------------------------------------------------------
 * @param string $tag 模版引擎定义名称
 +----------------------------------------------------------
 * @return boolean
 +----------------------------------------------------------
 */
function use_compiler($tag)
{
    $args = array_slice(func_get_args(), 1);
    if(is_callable($GLOBALS['template_compiler'][strtoupper($tag)])) {
        call_user_func_array($GLOBALS['template_compiler'][strtoupper($tag)],$args);
    }else{
        throw_exception(L('_TEMPLATE_ERROR_').'：'.C('TMPL_ENGINE_TYPE'));
    }
    return ;
}

/**
 +----------------------------------------------------------
 * 动态添加过滤器
 +----------------------------------------------------------
 * @param string $tag 过滤器标签
 * @param string $function 过滤方法名
 * @param integer $priority 执行优先级
 * @param integer $args 参数
 +----------------------------------------------------------
 * @return boolean
 +----------------------------------------------------------
 */
function add_filter($tag,$function,$priority = 10,$args = 1)
{
    static $_filter = array();
    if ( isset($_filter[APP_NAME.'_'.$tag]["$priority"]) ) {
        foreach($_filter[APP_NAME.'_'.$tag]["$priority"] as $filter) {
            if ( $filter['function'] == $function ) {
                return true;
            }
        }
    }
    $_filter[APP_NAME.'_'.$tag]["$priority"][] = array('function'=> $function,'args'=> $args);
    $_SESSION['_filters']   =   $_filter;
    return true;
}

/**
 +----------------------------------------------------------
 * 删除动态添加的过滤器
 +----------------------------------------------------------
 * @param string $tag 过滤器标签
 * @param string $function 过滤方法名
 * @param integer $priority 执行优先级
 +----------------------------------------------------------
 * @return boolean
 +----------------------------------------------------------
 */
function remove_filter($tag, $function_to_remove, $priority = 10) {
    $_filter  = $_SESSION['_filters'];
    if ( isset($_filter[APP_NAME.'_'.$tag]["$priority"]) ) {
        $new_function_list = array();
        foreach($_filter[APP_NAME.'_'.$tag]["$priority"] as $filter) {
            if ( $filter['function'] != $function_to_remove ) {
                $new_function_list[] = $filter;
            }
        }
        $_filter[APP_NAME.'_'.$tag]["$priority"] = $new_function_list;
    }
    $_SESSION['_filters']   =   $_filter;
    return true;
}

/**
 +----------------------------------------------------------
 * 执行过滤器
 +----------------------------------------------------------
 * @param string $tag 过滤器标签
 * @param string $string 参数
 +----------------------------------------------------------
 * @return boolean
 +----------------------------------------------------------
 */
function apply_filter($tag,$string='')
{
    if (!isset($_SESSION['_filters']) ||  !isset($_SESSION['_filters'][APP_NAME.'_'.$tag]) ) {
        return $string;
    }
    $_filter  = $_SESSION['_filters'][APP_NAME.'_'.$tag];
    ksort($_filter);
    $args = array_slice(func_get_args(), 2);
    foreach ($_filter as $priority => $functions) {
        if ( !is_null($functions) ) {
            foreach($functions as $function) {
                if(is_callable($function['function'])) {
                    $args = array_merge(array($string), $args);
                    $string = call_user_func_array($function['function'],$args);
                }
            }
        }
    }
    return $string;
}
?>