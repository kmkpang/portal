<?php
/**
 * Created by JetBrains PhpStorm.
 * User: khan
 * Date: 7/2/13
 * Time: 11:09 PM
 * To change this template use File | Settings | File Templates.
 */

$jpath_base = (dirname(__FILE__)) . "/../../../";
$jpath_base = realpath($jpath_base);
require_once "$jpath_base/phpunitbootstrap.php";
//require_once 'PHPUnit/Framework/TestCase.php';
//require_once 'PHPUnit/Autoload.php';


///var/www/softverk-webportal/libraries/webportal/services/webservice/websending/agent.php
///var/www/softverk-webportal/libraries/webportal/services/webservice/websending/agent.php
require_once JPATH_ROOT . "/libraries/webportal/services/webservice/websending/agent.php";

class AgentUpdateToWebTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var AgentSentToWeb
     */
    var $object;

    var $curl;
    var $xmlString;
    var $xml = "";

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {

        setUpWebsendingDatabase();

        $query = 'delete from jos_portal_sales';
        WFactory::getSqlService()->update($query);


        $query = "SELECT data  FROM `jos_portal_senttoweb_log` WHERE `command` LIKE 'Create' AND `type` LIKE 'Agent'";
        $result = WFactory::getServices()->getSqlService()->select($query);
        $xml = $result[0]['data'];
        $this->object = new AgentSentToWeb($xml);
        $result = ($this->object->create());

        $xml = simplexml_load_string($result);

        $xmlData = json_decode(json_encode($xml), true);

        $this->assertTrue($xmlData["Response"]["Number"] == "02211");

        $agentId = $xmlData["Response"]["Message"];
        $officeId = $this->object->dbClass->___office_unique_id;
        $publicKey = $this->object->dbClass->xmlPublicKey;

        $query = "SELECT data  FROM `jos_portal_senttoweb_log` WHERE `command` LIKE 'Update' AND `type` LIKE 'Agent'";
        $result = WFactory::getServices()->getSqlService()->select($query);
        $xml = $result[0]['data'];
        $xml = new SimpleXMLElement($xml);


        $xml->System->PublicKey = $publicKey;
        $xml->SalesAssociates->SalesAssociate->Information->SaleID = $agentId;
        $xml->SalesAssociates->SalesAssociate->Information->OfficeID = $officeId;

        $xml = $xml->asXML();
        $this->xmlString = $xml;
        $this->object = new AgentSentToWeb($xml);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }


    public function testUpdate()
    {

        $result = ($this->object->update());
        $this->assertNotEmpty($result);

        $xml = simplexml_load_string($result);

        $xmlData = json_decode(json_encode($xml), true);

        $this->assertTrue($xmlData["Response"]["Number"] == "02221");
    }

    public function testUpdateWebService()
    {
        $this->curl = getLocalCurl("senttoweb", "service", "");
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $this->xmlString);
        $result = curl_exec($this->curl);

        WFactory::getLogger()->debug("Gotten : $result");

        if (!$result) {
            $msg = curl_error($this->curl);
            WFactory::getLogger()->error("$msg");
        }

        $xml = simplexml_load_string($result);
        $xmlData = json_decode(json_encode($xml), true);

        $this->assertTrue($xmlData["Response"]["Number"] == "02221");


    }
}
