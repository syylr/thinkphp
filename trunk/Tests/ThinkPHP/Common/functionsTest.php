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

unittest_require(THINK_PATH . '/Common/functions.php');

class Common_functionsTest extends PHPUnit_Framework_TestCase
{
    /**
     * 确认get_client_ip()返回客户端IP地址
     */
    public function testget_client_ip()
    {
        $this->assertNotNull(get_client_ip());
        $ip = '127.0.0.1';
        $_SERVER['REMOTE_ADDR'] = $ip;
        $this->assertEquals($ip, get_client_ip());
    }
    /**
     * 确认url()返回预期的字符串
     */
    public function testurl()
    {
        define('__APP__', 'index.php');

        C('VAR_MODULE', 'module');
        C('VAR_ACTION', 'action');
        C('VAR_ROUTER', 'route');

        // 测试通常模式URL
        $uri = url('Index', 'Home', '', APP_NAME, array('q' => 'test', 'msg' => 'OK'));
        $this->assertEquals('index.php?module=Home&action=Index&q=test&msg=OK', $uri);

        // 测试通常模式路由
        $uri = url('Index', 'Home', 'default', APP_NAME, array('q' => 'test', 'msg' => 'OK'));
        $this->assertEquals('index.php?route=default&q=test&msg=OK', $uri);

        
        C('DISPATCH_ON', true);
        C('URL_MODEL', 1);
        C('PATH_MODEL', 1);
        
        // 测试普通PATHINFO模式URL
        $uri = url('Index', 'Home', '', APP_NAME, array('q' => 'test', 'msg' => 'OK'));
        $this->assertEquals('index.php/module/Home/action/Index/q/test/msg/OK', $uri);

        // 测试普通PATHINFO模式路由
        $uri = url('Index', 'Home', 'default', APP_NAME, array('q' => 'test', 'msg' => 'OK'));
        $this->assertEquals('index.php/route/default/q/test/msg/OK', $uri);

        C('PATH_MODEL', 2);
        C('PATH_DEPR', '/');

        // 测试智能PATHINFO模式URL
        $uri = url('Index', 'Home', '', APP_NAME, array('q' => 'test', 'msg' => 'OK'));
        $this->assertEquals('index.php/Home/Index/q/test/msg/OK', $uri);

        // 测试智能PATHINFO模式路由
        $uri = url('Index', 'Home', 'default', APP_NAME, array('q' => 'test', 'msg' => 'OK'));
        $this->assertEquals('index.php/default/q/test/msg/OK', $uri);
    }
    /**
     * 测试自动转换字符集函数
     */
    public function testauto_charset()
    {
        $str = "测试";
        $expected = "b2e2cad4";
        $result = bin2hex(auto_charset($str, "utf-8", "gbk"));
        $this->assertEquals($expected, $result);
        
        $expected = array("test" => $expected);
        $result = auto_charset(array("test" => $str), "utf-8", "gbk");
        if (isset($result['test'])) {
            $result['test'] = bin2hex($result['test']);
        }
        $this->assertEquals($expected, $result);
    }
    /**
     * 测试获取对象实例函数
     */
    public function testget_instance_of()
    {
        require_once 'Temp/Common/MyClass.php';

        $expected = "MyClass.test()";
        $c = get_instance_of('MyClass');
        $result = '';
        $result = $c->test();
        $this->assertEquals($expected, $result);

        $result = get_instance_of('MyClass', 'test');
        $this->assertEquals($expected, $result);

        $result = get_instance_of('MyClass', 'test', 'Fun');
        $this->assertEquals('Fun', $result);
    }
    /**
     * 确认include_cache()函数按预期包含文件
     */
    public function testinclude_cache()
    {
        $result = include_cache('Temp/Common/MyInclude.php');
        $this->assertTrue($result);

        $result = include_cache('Temp/Common/My.php');
        $this->assertFalse($result);
    }
    /**
     * 确认require_cache()函数按预期包含文件
     */
    public function testrequire_cache()
    {
        C('CHECK_FILE_CASE', true);

        $result = require_cache('Temp/Common/MyRequire.php');
        $this->assertTrue($result);

        $result = require_cache('Temp/Common/My.php');
        $this->assertFalse($result);
    }
    /**
     * 测试区分大小写的文件存在
     */
    public function testfile_exists_case()
    {
        C('CHECK_FILE_CASE', true);

        $this->assertFalse(file_exists_case('temp/common/MyClass.php'));
        $this->assertTrue(file_exists_case('Temp/Common/MyClass.php'));
    }
    /**
     * 测试导入类库函数
     */
    public function testimport()
    {
        import('Common.Import.*', 'Temp/', '.php', true);
        $this->assertTrue(class_exists('MyImport'));
        $this->assertTrue(class_exists('MySub'));

        import('Common.Import2.MyHello', 'Temp/');
        $this->assertTrue(class_exists('MyHello'));
    }
    /**
     * 确认to_guid_string()返回字符串
     */
    public function testto_guid_string()
    {
        $this->assertTrue(is_string(to_guid_string($this)));
        $this->assertTrue(is_string(to_guid_string(-1)));
    }
    /**
     * 确认is_instance_of()返回是否为对象实例
     */
    public function testis_instance_of()
    {
        $this->assertTrue(is_instance_of($this, 'Common_functionsTest'));
        $this->assertFalse(is_instance_of($this, 'FunClass'));
    }
    /**
     * 确认字符串截取函数返回预期字符串
     */
    public function testmsubstr()
    {
        
    }
    /**
     * 测试随机字符串函数
     */
    public function testrand_string()
    {
    }
    /**
     * 确认D()能创建模型
     */
    public function testD()
    {
    }
    /**
     * 确认A()能加载用户类
     */
    public function testA()
    {
    }
    /**
     * 确认L()能正确设置和读取语言
     */
    public function testL()
    {
    }
    /**
     * 确认C()能正确设置和读取配置
     */
    public function testC()
    {
    }
    /**
     * 确认S()能正确设置和读取缓存
     */
    public function testS()
    {
    }
    /**
     * 确认F()能读取和保存文件数据
     */
    public function testF()
    {
    }
    /**
     * 确认I()能创建对象实例
     */
    public function testI()
    {
    }
    /**
     * 确认xml_encode()返回正确的XML编码
     */
    public function testxml_encode()
    {
        $expected = '<?xml version="1.0" encoding="iso-8859-1"?><root><name>Test</name><message>Hello</message></root>';
        $data = array('name' => 'Test', 'message' => 'Hello');
        $result = xml_encode($data, 'iso-8859-1', 'root');
        $this->assertEquals($expected, $result);
    }
    /**
     * 测试数据转换XML函数
     */
    public function testdata_to_xml()
    {
        require_once 'Temp/Common/Object.php';
        $expected = '<name>Test</name><message>Hello</message>';
        $o = new Object();
        $o->name = 'Test';
        $o->message = 'Hello';
        $result = data_to_xml($o);
        $this->assertEquals($expected, $result);

        $expected = '<item id="0">Test</item><item id="1">Hello</item>';
        $result = data_to_xml(array('Test', 'Hello'));
        $this->assertEquals($expected, $result);
    }
    /**
     * 测试递归创建目录函数
     */
    public function testmk_dir()
    {
        $dir = 'Temp/mk/dir';
        if (is_dir($dir)) {
            rmdir($dir);
            rmdir('Temp/mk');
        }
        $result = mk_dir($dir);
        $this->assertTrue($result);
        rmdir($dir);
        rmdir('Temp/mk');

        $result = mk_dir('Temp/file');
        $this->assertFalse($result);
    }
    /**
     * 测试清除缓存目录函数
     */
    public function testclearCache()
    {
    }
}
?>