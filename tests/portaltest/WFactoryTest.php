<?php

$jpath_base = (dirname(__FILE__)) . "/../../";
$jpath_base = realpath($jpath_base);
require_once "$jpath_base/phpunitbootstrap.php";
//require_once 'PHPUnit/Framework/TestCase.php';
//require_once 'PHPUnit/Autoload.php';


require_once JPATH_ROOT . "/libraries/webportal/factory.php";


/**
 * @backupGlobals disabled
 */
class WFactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var WFactoryTest
     */
    var $object;


    /**
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

    /**
     * @covers WFactory::getLogger
     */
    public function testGetLogger()
    {
        $result = WFactory::getLogger();
        $this->assertNotEmpty($result);
    }



    /**
     * @covers WFactory::getConfig
     */
    public function testGetConfig()
    {
        $result = WFactory::getConfig();
        $this->assertNotEmpty($result);
    }


}

