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

require_once THINK_PATH . '/Lib/Think/Core/Base.class.php';
require_once THINK_PATH . '/Lib/Think/Util/Cache.class.php';
require_once THINK_PATH . '/Lib/Think/Util/Cache/CacheApachenote.class.php';

class Lib_Think_Util_Cache_CacheApachenoteTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var    CacheApachenote
     * @access protected
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp()
    {
        if (!function_exists('apache_note')) {
            $this->markTestSkipped('Your server is not Apache or function apache_note not found');
        } else {
            $this->object = new CacheApachenote();
        }
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     *
     * @access protected
     */
    protected function tearDown()
    {
    }

    /**
     * @todo Implement testIsConnected().
     */
    public function testIsConnected() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testGet().
     */
    public function testGet() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testSet().
     */
    public function testSet() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testRm().
     */
    public function testRm() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }
}
?>
