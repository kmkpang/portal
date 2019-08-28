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


///var/www/softverk-webportal/libraries/webportal/services/webservice/websending/office.php
///var/www/softverk-webportal/libraries/webportal/services/webservice/websending/office.php
require_once JPATH_ROOT . "/libraries/webportal/services/webservice/websending/office.php";

class OfficeCreateToWebTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var OfficeSentToWeb
     */
    var $object;
    /**
     * @var OfficeSentToWeb
     */
    var $createClass;

    var $xmlCreate = "";
    var $xml = "";
    var $curl;
    var $xmlString;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $xml = file_get_contents(JPATH_BASE . DS . "tests/portaltest/testXml/officeCreate.xml.xsl");
        $xml = new SimpleXMLElement($xml);

//        $query = "SELECT data  FROM `jos_portal_senttoweb_log` WHERE `command` LIKE 'Create' AND `type` LIKE 'Agent'";
//        $result = WFactory::getServices()->getSqlService()->select($query);
//        $xml = $result[0]['data'];
//        $this->object = new AgentSentToWeb($xml);

        $this->xmlString = $xml;
        //WFactory::getLogger()->debug("Public key {$this->object->dbClass->xmlPublicKey} and Office Id {$this->object->dbClass->__office_id}");

        $xml = $xml->asXML();
        $this->xmlString = $xml;
        $this->object = new OfficeSentToWeb($xml);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }


    public function testCreate()
    {

        require_once JPATH_BASE . '/libraries/webportal/services/webservice/websending/websendingBase.php';
        $websendingBase = new WebsendingBase();
        $sent2webDbClass = $websendingBase->saveSentToWebToDatabase('INCOMING',
            '127.0.0.1',
            'bleh',
            'Create',
            'Office',
            $this->xmlString,
            0, 0);


        $result = ($this->object->create($sent2webDbClass));
        $this->assertNotEmpty($result);

        $xml = simplexml_load_string($result);

        $xmlData = json_decode(json_encode($xml), true);

        //   $this->assertTrue($xmlData["Response"]["Number"] == "02221");


        $this->assertTrue($xmlData["Response"]["Number"] == "02321");
    }


}
