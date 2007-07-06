<?php 
/*
Plugin Name: gb2big5
Plugin URI: http://fcs.org.cn/
Description: 简繁转换插件 在需要的模版页面添加 {$gb2big5} 标签
Author: 流年
Version: 1.0
Author URI: http://blog.liu21st.com/
*/ 
//------------------------------------------------------
// BIG_2_GB 设置为 true 启用简繁转换
// TODO 在PHP5下面效率有待提高
//------------------------------------------------------

    function ToGb(&$buffer) {
        $obj=Big2gb::getInstance();
        return $obj->chg_utfcode($buffer,'gb2312');
    }

    function ToBig5(&$buffer) {
        $obj=Big2gb::getInstance();
        return  $obj->chg_utfcode($buffer,'big5');
    }
    //简繁编码字符集检测
    function CheckCharSet()
    {
        //检测当前字符集
        if ( isset($_GET['charSet']) ) {
            $charSet = $_GET['charSet'];
            setcookie('FCS_charSet',$charSet,time()+COOKIE_EXPIRE,'/');
        } else {
            if ( !isset($_COOKIE['FCS_charSet']) ) {
                if('zh-cn'==LANG_SET) {
                    $charSet   =  'gb' ;
                }elseif('zh-tw'==LANG_SET) {
                    $charSet   =  'big5';
                }else {
                    $charSet   =  'utf-8';   //不作简繁转换
                }
                setcookie('FCS_charSet',$charSet,time()+COOKIE_EXPIRE,'/');
            }
            else {
                $charSet = $_COOKIE['FCS_charSet'];
            }
        }
        define('CHAR_SET',strtolower($charSet));
        return ;
    }
    //简繁转换方法
    function gb2big5($content)
    {
         //支持混合转换
         if('gb' == CHAR_SET) {
            $content = ToGb($content);
         }elseif('big5' == CHAR_SET) {
            $content = ToBig5($content);
         }else {
          //其他情况不作编码转换
         }
         return $content;
    }
    //模版输出标签定义
    function showText($var) 
    {
        if('gb'==CHAR_SET) {
            $output = '<a  href="?charSet=big5" >繁體</a>';
        }elseif('big5'==CHAR_SET) {
            $output = '<a  href="?charSet=gb" >简体</a>';
        }else {
            $output = '';
        }
        //需要在模版中添加{$gb2big5}标签来显示转换链接
        $var['gb2big5']  = $output;
        return $var;
    }

    if(defined('BIG_2_GB') && BIG_2_GB) {
        import('Big2gb',dirname(__FILE__));
        //下面是关键，添加过滤器
        //应用初始化的时候检测字符集
        add_filter('app_init','CheckCharSet');
        //添加模版输出变量，用以显示简繁转换链接
        add_filter('template_var','showText');
        //输出内容转换过滤
        add_filter('ob_content','gb2big5');     	
    }
?>