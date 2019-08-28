<?php
/**
 * Created by JetBrains PhpStorm.
 * User: khan
 * Date: 7/2/13
 * Time: 11:09 PM
 * To change this template use File | Settings | File Templates.
 */

$jpath_base = (dirname(__FILE__)) . "/../../../../";
$jpath_base = realpath($jpath_base);
require_once "$jpath_base/phpunitbootstrap.php";
////require_once 'PHPUnit/Framework/TestCase.php';


//require_once 'PHPUnit/Autoload.php';


class UpdatePropertyTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PropertySentToWeb
     */
    var $object;
    var $curl;
    var $xmlString;
    var $xml;

    var $siteAddress = "localhost/softverk-webportal-remaxth/";


    /**
     * Sets u the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $xml = file_get_contents(JPATH_BASE . DS . "tests/portaltest/testXml/propertyUpdate.xml.xsl");
        $this->xmlString = $xml;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {

    }

    public function testUpdatePropertyOverWebService()
    {

        $this->curl = getRemoteCurl("senttoweb", "service", "", $this->siteAddress);
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $this->xmlString);
        $result = curl_exec($this->curl);

        WFactory::getLogger()->debug("Gotten : $result");

        if (!$result) {
            $msg = curl_error($this->curl);
            WFactory::getLogger()->error("$msg");
        }

        $xml = simplexml_load_string($result);
        $xmlData = json_decode(json_encode($xml), true);

        $this->assertTrue($xmlData["Response"]["Number"] == "02211");

    }


}
