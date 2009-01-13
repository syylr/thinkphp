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

error_reporting(E_ALL | E_STRICT);

define('THINK_PATH', '../ThinkPHP');
define('APP_NAME', 'UnitTest');
define('APP_PATH', 'Temp/App');

require THINK_PATH . '/Common/defines.php';
require 'Configure.php';

// Require test suites
require_once 'ThinkPHP/Common/AllTests.php';
require_once 'ThinkPHP/Lib/ORG/AllTests.php';
require_once 'ThinkPHP/Lib/Think/AllTests.php';

class AllTests
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('ThinkPHP');

        $suite->addTest(Common_AllTests::suite());
        $suite->addTest(Lib_ORG_AllTests::suite());
        $suite->addTest(Lib_Think_AllTests::suite());

        return $suite;
    }
}

/**
 * 解决require_cache和include引起的类名重定义
 * 使用require_once和include替换require和include
 *
 * @param  string  $path  The file path to fix for unit test
 */
function unittest_require($path)
{
    $data = file_get_contents($path);
    $data = preg_replace('/^[^<]*<\?(php)?/', '', $data);
    $data = preg_replace('/\?>\s*$/', '', $data);
    $data = str_replace('require ', 'require_once ', $data);
    $data = str_replace('include ', 'include_once ', $data);
    eval($data);
}
?>