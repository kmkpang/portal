<?php

$jpath_base = (dirname(__FILE__)) . "/../../";
$jpath_base = realpath($jpath_base);
require_once "$jpath_base/phpunitbootstrap.php";

/**
 * Created by PhpStorm.
 * User: KHAN
 * Date: 2/26/2016
 * Time: 7:40 PM
 */
class HelperTest extends PHPUnit_Framework_TestCase
{

    /**
     *
     *
     * /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {

    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    public function test__getVersionInfo()
    {
        $this->assertNotNull(WFactory::getHelper()->getVersionInfo());
    }
}
