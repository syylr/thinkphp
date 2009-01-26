<?php
// +----------------------------------------------------------------------
// | ThinkPHP Lite
// +----------------------------------------------------------------------
// | Copyright (c) 2008 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// $Id$

/**
 +------------------------------------------------------------------------------
 * ThinkPHP系统基类 抽象类
 +------------------------------------------------------------------------------
 * @category   Think
 * @package  Think
 * @subpackage  Core
 * @author    liu21st <liu21st@gmail.com>
 * @version   $Id$
 +------------------------------------------------------------------------------
 */
abstract class Base
{

    /**
     +----------------------------------------------------------
     * 自动变量设置
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param $name 属性名称
     * @param $value  属性值
     +----------------------------------------------------------
     */
    public function __set($name ,$value)
    {
        if(property_exists($this,$name)){
            $this->$name = $value;
        }
    }

    /**
     +----------------------------------------------------------
     * 自动变量获取
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param $name 属性名称
     +----------------------------------------------------------
     * @return mixed
     +----------------------------------------------------------
     */
    public function __get($name)
    {
        if(isset($this->$name)){
            return $this->$name;
        }else {
            return null;
        }
    }

    /**
     +----------------------------------------------------------
     * 字符串命名风格转换
     * type
     * =0 将Java风格转换为C的风格
     * =1 将C风格转换为Java的风格
     +----------------------------------------------------------
     * @access protected
     +----------------------------------------------------------
     * @param string $name 字符串
     * @param integer $type 转换类型
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    protected function parseName($name,$type=0) {
        if($type) {
            return ucfirst(preg_replace("/_([a-zA-Z])/e", "strtoupper('\\1')", $name));
        }else{
            $name = preg_replace("/[A-Z]/", "_\\0", $name);
            return strtolower(trim($name, "_"));
        }
    }

}//类定义结束
?>