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
require_once 'ThinkPHP/Lib/ORG/Net/HttpTest.php';
require_once 'ThinkPHP/Lib/ORG/Net/IpLocationTest.php';
require_once 'ThinkPHP/Lib/ORG/Net/UploadFileTest.php';

class Lib_ORG_Net_AllTests
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('ThinkPHP_Lib_ORG_Net');

        $suite->addTestSuite('Lib_ORG_Net_HttpTest');
        $suite->addTestSuite('Lib_ORG_Net_IpLocationTest');
        $suite->addTestSuite('Lib_ORG_Net_UploadFileTest');

        return $suite;
    }
}
?>