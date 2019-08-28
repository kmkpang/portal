<?php

$jpath_base = (dirname(__FILE__)) . "/../../../";
$jpath_base = realpath($jpath_base);
require_once "$jpath_base/phpunitbootstrap.php";
//require_once 'PHPUnit/Framework/TestCase.php';


//require_once 'PHPUnit/Autoload.php';

/**
 * Created by PhpStorm.
 * User: khan
 * Date: 5/19/14
 * Time: 2:02 PM
 */
class AgentsServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var AgentsService
     */
    var $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {


        $this->object = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_AGENTS);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }


    /**
     * @covers AgentsService::getAgents
     * @covers AddressService::getAddress
     */
    public function testGetAgents()
    {
        logTestStart(__FUNCTION__);

        $query = 'SELECT id FROM `jos_portal_offices`';
        $result = WFactory::getSqlService()->select($query);
        $officeId = $result[0]["id"];

        $agents = $this->object->getAgents($officeId);

        $json = json_encode($agents);

        print_r($json);


        $this->assertNotEmpty($agents);


        logTestEnd(__FUNCTION__);
    }


    public function testGetAllAgents()
    {
        logTestStart(__FUNCTION__);


        $agents = $this->object->getAgentsAll();
        $this->assertNotEmpty($agents);


        logTestEnd(__FUNCTION__);
    }


    public function testGetAllAgents2()
    {
        logTestStart(__FUNCTION__);


        $agents = $this->object->getAgents(null,true);
        $this->assertNotEmpty($agents);


        logTestEnd(__FUNCTION__);
    }


}
 