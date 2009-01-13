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
require_once 'ThinkPHP/Lib/ORG/Date/DateTest.php';

class Lib_ORG_Date_AllTests
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('ThinkPHP_Lib_ORG_Date');

        $suite->addTestSuite('Lib_ORG_Date_DateTest');

        return $suite;
    }
}
?>