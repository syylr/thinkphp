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
require_once 'ThinkPHP/Lib/Think/Db/Driver/AllTests.php';
require_once 'ThinkPHP/Lib/Think/Db/DbTest.php';
require_once 'ThinkPHP/Lib/Think/Db/ResultIteratorTest.php';

class Lib_Think_Db_AllTests
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('ThinkPHP_Lib_Think_Db');

        $suite->addTest(Lib_Think_Db_Driver_AllTests::suite());
        $suite->addTestsuite('Lib_Think_Db_DbTest');
        $suite->addTestsuite('Lib_Think_Db_ResultIteratorTest');

        return $suite;
    }
}
?>