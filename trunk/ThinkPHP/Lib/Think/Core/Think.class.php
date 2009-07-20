<?php
// +----------------------------------------------------------------------
// | ThinkPHP
// +----------------------------------------------------------------------
// | Copyright (c) 2009 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// $Id$

/**
 +------------------------------------------------------------------------------
 * ThinkPHP系统基类
 +------------------------------------------------------------------------------
 * @category   Think
 * @package  Think
 * @subpackage  Core
 * @author    liu21st <liu21st@gmail.com>
 * @version   $Id$
 +------------------------------------------------------------------------------
 */
class Think
{
    private static $_instance = array();

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
     * 系统自动加载ThinkPHP类库
     * 并且支持配置自动加载路径
     +----------------------------------------------------------
     * @param string $classname 对象类名
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    public static function autoload($classname)
    {
        // 检查是否存在别名定义
        if(alias_import($classname)) return ;
        // 自动加载当前项目的Actioon类和Model类
        if(substr($classname,-5)=="Model") {
            require_cache(LIB_PATH.'Model/'.$classname.'.class.php');
        }elseif(substr($classname,-6)=="Action"){
            require_cache(LIB_PATH.'Action/'.$classname.'.class.php');
        }else {
            // 根据自动加载路径设置进行尝试搜索
            if(C('AUTO_LOAD_PATH')) {
                $paths  =   explode(',',C('AUTO_LOAD_PATH'));
                foreach ($paths as $path){
                    if(import($path.$classname)) {
                        // 如果加载类成功则返回
                        return ;
                    }
                }
            }
        }
        return ;
    }

    /**
     +----------------------------------------------------------
     * 取得对象实例 支持调用类的静态方法
     +----------------------------------------------------------
     * @param string $className 对象类名
     * @param string $method 类的静态方法名
     * @param array $args 调用参数
     +----------------------------------------------------------
     * @return object
     +----------------------------------------------------------
     */
    static public function instance($className,$method='',$args=array())
    {
        if(empty($args)) {
            $identify   =   $className.$method;
        }else{
            $identify   =   $className.$method.to_guid_string($args);
        }
        if (!isset(self::$_instance[$identify])) {
            if(class_exists($className)){
                $o = new $className();
                if(method_exists($o,$method)){
                    if(!empty($args)) {
                        self::$_instance[$identify] = call_user_func_array(array(&$o, $method), $args);;
                    }else {
                        self::$_instance[$identify] = $o->$method();
                    }
                }
                else
                    self::$_instance[$identify] = $o;
            }
            else
                halt(L('_CLASS_NOT_EXIST_'));
        }
        return self::$_instance[$identify];
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