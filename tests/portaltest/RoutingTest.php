<?php

$jpath_base = (dirname(__FILE__)) . "/../../";
$jpath_base = realpath($jpath_base);
require_once "$jpath_base/phpunitbootstrap.php";
//require_once 'PHPUnit/Framework/TestCase.php';
////require_once 'PHPUnit/Autoload.php';
///var/www/softverk-webportal/libraries/webportal/routing/routing.php
//require_once JPATH_ROOT . "/libraries/webportal/routing/routing.php";


/**
 * Created by PhpStorm.
 * User: khan
 * Date: 4/24/14
 * Time: 1:28 PM
 */
class RoutingTest extends PHPUnit_Framework_TestCase
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

    public function testPropertyDetailRoute()
    {
        $query = "select id from #__portal_properties LIMIT 1";
        $property = WFactory::getSqlService()->select($query);
        $id = $property[0]["id"];

        if ($id == null) {
            $this->markTestIncomplete("Failed to retrive property id to test : " . __FUNCTION__);
        } else {

            $route = JRoute::_("index.php?option=com_webportal&view=property&property-id=$id");


            WFactory::getLogger()->debug("Got route: $route");

        }

    }

    public function testOfficeDetailRoute()
    {
        $query = "select id from #__portal_offices LIMIT 1";
        $office = WFactory::getSqlService()->select($query);
        $id = $office[0]["id"];

        if ($id == null) {
            $this->markTestIncomplete("Failed to retrive office id to test : " . __FUNCTION__);
        } else {

            $route = JRoute::_("index.php?option=com_webportal&view=offices&office_id=$id");

            WFactory::getLogger()->debug("Got route: $route");

            $this->assertNotEmpty($route);

            $curl = getLocalApiCurl("api/v1/office/getRoute/");
            curl_setopt($curl, CURLOPT_POSTFIELDS, $id);

            $result = curl_exec($curl);

            WFactory::getLogger()->debug("Got route over API: $result");

            $this->assertNotEmpty($result);

        }

    }

    public function testAgentDetailRoute()
    {
        $query = "select id from #__portal_sales LIMIT 1";
        $agent = WFactory::getSqlService()->select($query);
        $id = $agent[0]["id"];

        if ($id == null) {
            $this->markTestIncomplete("Failed to retrive agent id to test : " . __FUNCTION__);
        } else {

            $route = JRoute::_("index.php?option=com_webportal&view=agents&agent_id=$id");

            $this->assertNotEmpty($route);

            WFactory::getLogger()->debug("Got route: $route");

            $curl = getLocalApiCurl("api/v1/agents/getRoute/");
            curl_setopt($curl, CURLOPT_POSTFIELDS, $id);

            $result = curl_exec($curl);

            WFactory::getLogger()->debug("Got route over API: $result");

            $this->assertNotEmpty($result);

        }

    }


}
 