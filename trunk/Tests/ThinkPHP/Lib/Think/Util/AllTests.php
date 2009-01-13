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

// Require test suites
require_once 'ThinkPHP/Lib/Think/Util/Cache/AllTests.php';
require_once 'ThinkPHP/Lib/Think/Util/Filter/AllTests.php';
require_once 'ThinkPHP/Lib/Think/Util/CacheTest.php';
require_once 'ThinkPHP/Lib/Think/Util/CookieTest.php';
require_once 'ThinkPHP/Lib/Think/Util/DebugTest.php';
require_once 'ThinkPHP/Lib/Think/Util/DispatcherTest.php';
require_once 'ThinkPHP/Lib/Think/Util/FilterTest.php';
require_once 'ThinkPHP/Lib/Think/Util/HtmlCacheTest.php';
require_once 'ThinkPHP/Lib/Think/Util/InputTest.php';
require_once 'ThinkPHP/Lib/Think/Util/LogTest.php';
require_once 'ThinkPHP/Lib/Think/Util/SessionTest.php';

class Lib_Think_Util_AllTests
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('ThinkPHP_Lib_Think_Util');

        $suite->addTest(Lib_Think_Util_Cache_AllTests::suite());
        $suite->addTest(Lib_Think_Util_Filter_AllTests::suite());
        $suite->addTestsuite('Lib_Think_Util_CacheTest');
        $suite->addTestsuite('Lib_Think_Util_CookieTest');
        $suite->addTestsuite('Lib_Think_Util_DebugTest');
        $suite->addTestsuite('Lib_Think_Util_DispatcherTest');
        $suite->addTestsuite('Lib_Think_Util_FilterTest');
        $suite->addTestsuite('Lib_Think_Util_HtmlCacheTest');
        $suite->addTestsuite('Lib_Think_Util_InputTest');
        $suite->addTestsuite('Lib_Think_Util_LogTest');
        $suite->addTestsuite('Lib_Think_Util_SessionTest');

        return $suite;
    }
}
?>