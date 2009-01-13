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
require_once 'ThinkPHP/Lib/ORG/Date/AllTests.php';
require_once 'ThinkPHP/Lib/ORG/Io/AllTests.php';
require_once 'ThinkPHP/Lib/ORG/Net/AllTests.php';
require_once 'ThinkPHP/Lib/ORG/RBAC/AllTests.php';
require_once 'ThinkPHP/Lib/ORG/Text/AllTests.php';
require_once 'ThinkPHP/Lib/ORG/Util/AllTests.php';

class Lib_ORG_AllTests
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('ThinkPHP_Lib_ORG');

        $suite->addTest(Lib_ORG_Date_AllTests::suite());
        $suite->addTest(Lib_ORG_Io_AllTests::suite());
        $suite->addTest(Lib_ORG_Net_AllTests::suite());
        $suite->addTest(Lib_ORG_RBAC_AllTests::suite());
        $suite->addTest(Lib_ORG_Text_AllTests::suite());
        $suite->addTest(Lib_ORG_Util_AllTests::suite());

        return $suite;
    }
}
?>