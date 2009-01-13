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
require_once 'ThinkPHP/Lib/Think/Util/Filter/FilterDbSessionTest.php';

class Lib_Think_Util_Filter_AllTests
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('ThinkPHP_Lib_Think_Util_Filter');

        $suite->addTestSuite('Lib_Think_Util_Filter_FilterDbSessionTest');

        return $suite;
    }
}
?>