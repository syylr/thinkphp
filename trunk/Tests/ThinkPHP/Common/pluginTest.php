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

require_once THINK_PATH . '/Common/plugin.php';

class Common_pluginTest extends PHPUnit_Framework_TestCase
{
    /**
     * 确认目录为空
     */
    public function testempty_dir()
    {
        $dir = 'Temp/empty';
        $this->assertFalse(empty_dir('.'));
        if (is_dir($dir)) {
            rmdir($dir);
        }
        mkdir($dir);
        $this->assertTrue(empty_dir($dir));
        rmdir($dir);
    }
    /**
     * 确认get_plugins()返回数组
     */
    public function testget_plugins()
    {
        $dir = 'Temp/empty';
        if (is_dir($dir)) {
            rmdir($dir);
        }
        mkdir($dir);
        $a = array();
        $this->assertEquals($a, get_plugins($dir, 'UnitTest'));
        rmdir($dir);
    }
    /**
     * 测试动态添加过滤器
     */
    public function testadd_filter()
    {
    }
    /**
     * 测试删除动态过滤器
     */
    public function testremove_filter()
    {
    }
    /**
     * 测试执行过滤器
     */
    public function testapply_filter()
    {
    }
}
?>