<?php

$jpath_base = (dirname(__FILE__)) . "/../../../";
$jpath_base = realpath($jpath_base);
require_once "$jpath_base/phpunitbootstrap.php";
//require_once 'PHPUnit/Framework/TestCase.php';
//require_once 'PHPUnit/Autoload.php';

/**
 * Created by PhpStorm.
 * User: khan
 * Date: 6/22/14
 * Time: 1:37 PM
 */
class BeanstalkdServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var BeanstalkdService
     */
    var $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_BEANSTALKD);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    public function testCreation()
    {
        logTestStart(__FUNCTION__);
        $this->assertNotNull($this->object);
    }

    public function testPuttingJob()
    {
        logTestStart(__FUNCTION__);
        $value = $this->object->putSearchQueue($this->object->getBeanstalkdModel("", "", array()));

        $this->assertNotNull($value);

    }

    public function testGettingJob()
    {
        logTestStart(__FUNCTION__);
        $value = $this->object->putSearchQueue($this->object->getBeanstalkdModel("", "", array()));

        $this->assertNotNull($value);

        $jobValue = $this->object->getFromSearchQueue();

        WFactory::getLogger()->debug("Got job: \r\n" . $jobValue->getData());

        $this->assertNotNull($jobValue);
    }

    public function testProcessAllSearchQueue()
    {
        logTestStart(__FUNCTION__);
        $this->assertTrue($this->object->processAllSearchQueue());
    }

    public function testProcessPlacesSearchQueue()
    {
        logTestStart(__FUNCTION__);
        $this->assertTrue($this->object->processAllSearchQueue(true,__PROPPERTY_PORTAL_PLACES,'searchPlaces'));
    }
}
 