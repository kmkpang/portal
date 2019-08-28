<?php

$jpath_base = (dirname(__FILE__)) . "/../../";
$jpath_base = realpath($jpath_base);
require_once "$jpath_base/phpunitbootstrap.php";
//require_once 'PHPUnit/Framework/TestCase.php';
////require_once 'PHPUnit/Autoload.php';
///var/www/softverk-webportal/libraries/webportal/logger/logger.php
//require_once JPATH_ROOT . "/libraries/webportal/logger/logger.php";


/**
 * Created by PhpStorm.
 * User: khan
 * Date: 4/24/14
 * Time: 1:28 PM
 */
class LoggerTest extends PHPUnit_Framework_TestCase
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



    public function testInialize()
    {
        $result = WFactory::getLogger()->initialize();
        $this->assertTrue($result);
    }


    public function testLogging()
    {

        WFactory::getLogger()->debug("Hello");
        WFactory::getLogger()->info("Hello");
        WFactory::getLogger()->warn("Hello");
        WFactory::getLogger()->error("Hello");
        WFactory::getLogger()->fatal("Hello");
    }

    /**
     *
     */
    public function testFatalError()
    {

        $this->markTestSkipped(
            'Can not test fatal error in the jenkins . uncomment the following to test fatal error [ you should get mail notification of it though ]'
        );

        //WFactory::getLogger()->jumpOffACliff();


    }

    public function testCleanLogFiles()
    {
        $this->assertTrue(WFactory::getLogger()->cleanLogFiles());
    }

}
 