<?php

$jpath_base = (dirname(__FILE__)) . "/../../../";
$jpath_base = realpath($jpath_base);
require_once "$jpath_base/phpunitbootstrap.php";
//require_once 'PHPUnit/Framework/TestCase.php';

require_once JPATH_ROOT . "/libraries/webportal/services/webservice/websending/agent.php";
//require_once 'PHPUnit/Autoload.php';

/**
 * Created by PhpStorm.
 * User: khan
 * Date: 5/19/14
 * Time: 2:02 PM
 */
class AgentServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var AgentService
     */
    var $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {


        $this->object = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_AGENT);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }


    /**
     * @covers AgentService::getAgent
     * @covers AddressService::getAddress
     */
    public function testGetAgent()
    {
        logTestStart(__FUNCTION__);
        $query = 'SELECT id FROM `jos_portal_sales` where is_deleted = 0';
        $result = WFactory::getSqlService()->select($query);
        $agentId = $result[0]["id"];

        $agent = $this->object->getAgent($agentId);
        $this->assertNotEmpty($agent);
        logTestEnd(__FUNCTION__);
    }

    /**
     * @covers AgentService::getAgent
     * @covers AddressService::getAddress
     */
    public function testGetAgent2()
    {
        logTestStart(__FUNCTION__);
        $query = 'SELECT id FROM `jos_portal_sales` where is_deleted = 0';
        $result = WFactory::getSqlService()->select($query);
        $agentId = $result[0]["id"];

        $agent = $this->object->getAgent($agentId, true);
        $this->assertNotEmpty($agent);
        logTestEnd(__FUNCTION__);


        $agentId = JFactory::getApplication()->input->getInt("agent_id");
        $agentService = WFactory::getServices()->getSqlService()->getSqlServiceClass(__PROPPERTY_PORTAL_AGENT);
        $agent = $agentService->getAgent($agentId, true);
    }

    public function test__updateAgent()
    {
        logTestStart(__FUNCTION__);
        $query = 'SELECT id FROM `jos_portal_sales` where is_deleted = 0 ';
        $result = WFactory::getSqlService()->select($query);
        $agentId = $result[0]["id"];

        $agent = $this->object->getAgent($agentId, true);
        $this->assertNotEmpty($agent);


        $agent->marketing_info->slogan = "HELLO!";
        $agent->first_name = "HOLLA";

        $this->assertTrue($this->object->updateAgent($agent));

        logTestEnd(__FUNCTION__);
    }

    public function test__processAgentAccount()
    {

        logTestStart(__FUNCTION__);
        //$query = 'SELECT id FROM `jos_portal_sales` where is_deleted = 1 order by rand() ';
        $query = 'SELECT id FROM `jos_portal_sales`  order by rand() ';
        $result = WFactory::getSqlService()->select($query);
        $agentId = $result[0]["id"];

        $this->assertTrue($this->object->processAgentAccount($agentId));

    }

//    public function testGetAgentSimpleNULL()
//    {
//        logTestStart(__FUNCTION__);
//        $agent = $this->object->getAgent(1);
//        $this->assertNull($agent);
//        logTestEnd(__FUNCTION__);
//    }
//
//    public function testGetAgentSimple()
//    {
//        logTestStart(__FUNCTION__);
//        $agent = $this->object->getAgent(175);
//        $this->assertNotNull($agent);
//        logTestEnd(__FUNCTION__);
//    }

    /**
     * @covers AgentService::deleteAgent
     */
    public function testDeleteAgentThatIsAlreadyDeleted()
    {
        logTestStart(__FUNCTION__);
        $query = 'SELECT id FROM `jos_portal_sales`';
        $result = WFactory::getSqlService()->select($query);
        $agentId = $result[0]["id"];

        try {
            //delete twice to force exception
            $result = $this->object->deleteAgent($agentId);
            $result = $this->object->deleteAgent($agentId);

            $this->assertFalse(true);
        } catch (Exception $e) {

            $this->assertTrue(true);
        }

        logTestEnd(__FUNCTION__);

    }

    /**
     * @covers AgentService::deleteAgent
     */
    public function testDeleteAgent()
    {
        logTestStart(__FUNCTION__);
        $query = 'SELECT id FROM `jos_portal_sales`';
        $result = WFactory::getSqlService()->select($query);
        $agentId = $result[0]["id"];

        /**
         * @var $agentDbClass PortalPortalSalesSql
         */
        $agentDbClass = WFactory::getSqlService()->getDbClass(__PORTAL_PORTAL_SALES_SQL);
        $agentDbClass->__id = $agentId;
        $agentDbClass->__is_deleted = 0;
        $undeleteResult = WFactory::getSqlService()->update($agentDbClass);
        $this->assertTrue($undeleteResult);

        $result = $this->object->deleteAgent($agentId);
        $this->assertTrue($result);
        logTestEnd(__FUNCTION__);
    }


}
 