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
require_once 'ThinkPHP/Lib/Think/Db/Driver/DbIbaseTest.php';
require_once 'ThinkPHP/Lib/Think/Db/Driver/DbMssqlTest.php';
require_once 'ThinkPHP/Lib/Think/Db/Driver/DbMysqliTest.php';
require_once 'ThinkPHP/Lib/Think/Db/Driver/DbMysqlTest.php';
require_once 'ThinkPHP/Lib/Think/Db/Driver/DbOracleTest.php';
require_once 'ThinkPHP/Lib/Think/Db/Driver/DbPdoTest.php';
require_once 'ThinkPHP/Lib/Think/Db/Driver/DbPgsqlTest.php';
require_once 'ThinkPHP/Lib/Think/Db/Driver/DbSqliteTest.php';

class Lib_Think_Db_Driver_AllTests
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('ThinkPHP_Lib_Think_Db_Driver');

        $suite->addTestsuite('Lib_Think_Db_Driver_DbIbaseTest');
        $suite->addTestsuite('Lib_Think_Db_Driver_DbMssqlTest');
        $suite->addTestsuite('Lib_Think_Db_Driver_DbMysqliTest');
        $suite->addTestsuite('Lib_Think_Db_Driver_DbMysqlTest');
        $suite->addTestsuite('Lib_Think_Db_Driver_DbOracleTest');
        $suite->addTestsuite('Lib_Think_Db_Driver_DbPdoTest');
        $suite->addTestsuite('Lib_Think_Db_Driver_DbPgsqlTest');
        $suite->addTestsuite('Lib_Think_Db_Driver_DbSqliteTest');

        return $suite;
    }
}
?>