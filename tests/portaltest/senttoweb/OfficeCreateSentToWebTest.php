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

//blah..more push!
require_once JPATH_ROOT . "/libraries/webportal/services/webservice/websending/office.php";

class OfficeCreateSentToWebTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var OfficeSentToWeb
     */
    var $object;
    ///var/www/softverk-webportal/tests/portaltest/testXml/officeCreate.xml.xsl
    var $xml = "";
    var $curl;
    var $xmlString;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->xml = JPATH_ROOT . "/tests/portaltest/testXml/officeCreate.xml.xsl";
        $xml = trim(file_get_contents($this->xml));

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


    public function testImages()
    {
        $dbClass = $this->object->dbClass;

        $this->assertTrue($this->object->handleImage($this->object->dbClass->xmlImages, OFFICE, 1, "test company", 1, "test office"));

        $imges = $this->object->dbClass->xmlImages;
        $x = 1;

    }


    public function testCreate()
    {
        require_once JPATH_BASE . '/libraries/webportal/services/webservice/websending/websendingBase.php';
        $websendingBase = new WebsendingBase();
        $sent2webDbClass = $websendingBase->saveSentToWebToDatabase('INCOMING',
            '127.0.0.1',
            WFactory::getPublicIp(),
            'Create',
            'Office',
            $this->xmlString,
            0, 0);


        $result = $this->object->create($sent2webDbClass);
        $this->assertNotEmpty($result);

        $xml = simplexml_load_string($result);

        $xmlData = json_decode(json_encode($xml), true);

        $this->assertTrue($xmlData["Response"]["Number"] == "02311");
    }

    public function testCreateWebService()
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

        $this->assertTrue($xmlData["Response"]["Number"] == "02311");


    }

    public function testCreateWebServiceToRemax()
    {
        // $this->markTestSkipped("NOT TOUCHING REMAX");


        $this->curl = getRemoteCurl("senttoweb", "service", "", "remax.softverk.co.th");
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $this->xmlString);
        $result = curl_exec($this->curl);

        WFactory::getLogger()->debug("Gotten : $result");

        if (!$result) {
            $msg = curl_error($this->curl);
            WFactory::getLogger()->error("$msg");
        }

        $xml = simplexml_load_string($result);
        $xmlData = json_decode(json_encode($xml), true);

        $this->assertTrue($xmlData["Response"]["Number"] == "02311");


    }
}
