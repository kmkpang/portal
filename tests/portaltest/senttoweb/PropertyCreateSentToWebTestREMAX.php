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


require_once JPATH_ROOT . "/libraries/webportal/services/webservice/websending/property.php";
require_once JPATH_ROOT . "/libraries/webportal/services/webservice/websending/agent.php";

class PropertyCreateSentToWebTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PropertySentToWeb
     */
    var $object;
    var $curl;
    var $xmlString;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {


//        $query = "SELECT data  FROM `jos_portal_senttoweb_log` WHERE `command` LIKE 'Create' AND `type` LIKE 'Property' order by id desc LIMIT 1";
//        $result = WFactory::getServices()->getSqlService()->select($query);
//        $xml = $result[0]['data'];
//        $xml = new SimpleXMLElement($xml);


        ///var/www/softverk-webportal-remaxth/tests/portaltest/testXml/propertyCreate.xml.xsl.xml
        $xml = file_get_contents(JPATH_BASE . DS . "tests/portaltest/testXml/propertyCreate.xml.xsl.xml");
        $xml = new SimpleXMLElement($xml);

//        $xml->System->PublicKey = $publicKey;
//        $xml->Properties->Property->Information->SaleID = $agentId;


        $xml = $xml->asXML();
        $this->xmlString = $xml;
        $this->object = new PropertySentToWeb($xml);

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
            WFactory::getPublicIp(),
            'Create',
            'Property',
            $this->xmlString,
            0, 0);

        $result = ($this->object->create($sent2webDbClass));
        $this->assertNotEmpty($result);

        //WFactory::getLogger()->debug($result);

        $xml = simplexml_load_string($result);

        $xmlData = json_decode(json_encode($xml), true);

        $this->assertTrue($xmlData["Response"]["Number"] == "02111");
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

        $this->assertTrue($xmlData["Response"]["Number"] == "02111");


    }
}
