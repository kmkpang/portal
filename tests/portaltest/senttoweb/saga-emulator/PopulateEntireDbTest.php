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
//require_once 'PHPUnit/Framework/TestCase.php';
//require_once 'PHPUnit/Autoload.php';


require_once JPATH_ROOT . "/libraries/webportal/services/webservice/websending/property.php";
require_once JPATH_ROOT . "/libraries/webportal/services/webservice/websending/agent.php";

class PopulateEntireDbTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PropertySentToWeb
     */
    var $object;
    var $curl;
    var $xmlString;
    var $xml;


    var $officeKey;
    var $officeId;

    /**
     * Sets u the fixture, for example, opens a network connection.
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


    public function unzipAndRestore()
    {
        $sqlFolder = JPATH_BASE . DS . "sql";
        $config =& JFactory::getConfig();
        $login = $config->get('user');
        $pass = $config->get('password');
        $dbName = "senttowebhelper";

        $unzipCommand = "unzip -o $sqlFolder/websending.zip -d $sqlFolder/ ";
        $dropCommand = "mysql -u$login -p$pass -e \"DROP DATABASE IF EXISTS $dbName\"";
        $createDbCommand = "mysqladmin -u$login -p$pass create  $dbName";
        $restoreDbCommand = "mysql -u$login -p$pass $dbName < $sqlFolder/senttoweb.sql";


        WFactory::getLogger()->debug("Unzipping senttoweb: $unzipCommand");
        exec($unzipCommand);
        WFactory::getLogger()->debug("dropping existing senttoweb: $dropCommand");
        exec($dropCommand);
        WFactory::getLogger()->debug("Creating senttoweb db: $createDbCommand");
        exec($createDbCommand);
        WFactory::getLogger()->debug("Restoring senttoweb: $restoreDbCommand");
        exec($restoreDbCommand);

    }

    public function getAllAgentCreateSentToWeb()
    {
        $query = "SELECT jos_portal_senttoweb_log.data
                  FROM `softverk-webportal`.jos_portal_senttoweb_log jos_portal_senttoweb_log
                 WHERE     (jos_portal_senttoweb_log.command = 'Create')
                       AND (jos_portal_senttoweb_log.type = 'Agent')";

        return WFactory::getSqlService()->select($query);

    }


    public function testAllOfficesSentToWeb()
    {
        $this->markTestIncomplete("This test is not valid!");
        return;

        if (!__QUICKTEST)
            $this->unzipAndRestore();


        //there is NO office sent to web..!!
        // so we need to fake one and make it!!

        //select any random agent!!
        $query = "SELECT jos_portal_senttoweb_log.type,
                       jos_portal_senttoweb_log.command,
                       jos_portal_senttoweb_log.data
                  FROM senttowebhelper.jos_portal_senttoweb_log jos_portal_senttoweb_log
                 WHERE     (jos_portal_senttoweb_log.type = 'Agent')
                       AND (jos_portal_senttoweb_log.command = 'Create') LIMIT 1";

        $agent = WFactory::getSqlService()->select($query);
        $agentSentToWeb = $agent[0]["data"];

        $agentSentToWeb = simplexml_load_string($agentSentToWeb);

        //print_r($agentSentToWeb);

        $officeKey = $agentSentToWeb->System->PublicKey;
        $officeId = $agentSentToWeb->SalesAssociates->SalesAssociate->Information->OfficeID;


        $this->officeKey = $officeKey;
        $this->officeId = $officeId;

        $this->assertNotNull($this->officeKey);
        $this->assertNotNull($this->officeId);


        $this->xml = JPATH_ROOT . "/tests/portaltest/testXml/officeCreate.xml.xsl";
        $xml = trim(file_get_contents($this->xml));

        $this->xmlString = $xml;
        $this->object = new OfficeSentToWeb($xml);


        $result = $this->object->create();
        $this->assertNotEmpty($result);

        $xml = simplexml_load_string($result);

        $xmlData = json_decode(json_encode($xml), true);

        $this->assertTrue($xmlData["Response"]["Number"] == "02311");

    }

    public
    function testCreateAgents()
    {


        $this->markTestIncomplete("This test is not valid!");
        return;

        //  $agentSentToWebs = $this->getAllAgentCreateSentToWeb();

        $query = "SELECT jos_portal_senttoweb_log.type,
                       jos_portal_senttoweb_log.command,
                       jos_portal_senttoweb_log.data
                  FROM senttowebhelper.jos_portal_senttoweb_log jos_portal_senttoweb_log
                 WHERE     (jos_portal_senttoweb_log.type = 'Agent')
                       AND (jos_portal_senttoweb_log.command = 'Create')";


        $result = WFactory::getSqlService()->select($query);
        foreach ($result as $r) {
            $agentSentToWeb = $r["data"];

            $agentSentToWeb = simplexml_load_string($agentSentToWeb);

        }


    }


}
