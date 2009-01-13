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
require_once 'ThinkPHP/Lib/Think/Util/Cache/CacheApachenoteTest.php';
require_once 'ThinkPHP/Lib/Think/Util/Cache/CacheApcTest.php';
require_once 'ThinkPHP/Lib/Think/Util/Cache/CacheDbTest.php';
require_once 'ThinkPHP/Lib/Think/Util/Cache/CacheEacceleratorTest.php';
require_once 'ThinkPHP/Lib/Think/Util/Cache/CacheFileTest.php';
require_once 'ThinkPHP/Lib/Think/Util/Cache/CacheMemcacheTest.php';
require_once 'ThinkPHP/Lib/Think/Util/Cache/CacheShmopTest.php';
require_once 'ThinkPHP/Lib/Think/Util/Cache/CacheSqliteTest.php';
require_once 'ThinkPHP/Lib/Think/Util/Cache/CacheXcacheTest.php';

class Lib_Think_Util_Cache_AllTests
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('ThinkPHP_Lib_Think_Util_Cache');

        $suite->addTestsuite('Lib_Think_Util_Cache_CacheApachenoteTest');
        $suite->addTestsuite('Lib_Think_Util_Cache_CacheApcTest');
        $suite->addTestsuite('Lib_Think_Util_Cache_CacheDbTest');
        $suite->addTestsuite('Lib_Think_Util_Cache_CacheEacceleratorTest');
        $suite->addTestsuite('Lib_Think_Util_Cache_CacheFileTest');
        $suite->addTestsuite('Lib_Think_Util_Cache_CacheMemcacheTest');
        $suite->addTestsuite('Lib_Think_Util_Cache_CacheShmopTest');
        $suite->addTestsuite('Lib_Think_Util_Cache_CacheSqliteTest');
        $suite->addTestsuite('Lib_Think_Util_Cache_CacheXcacheTest');

        return $suite;
    }
}
?>