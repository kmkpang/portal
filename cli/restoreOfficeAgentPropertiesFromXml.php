<?php
/**
 * @package        Joomla.Site
 * @copyright    Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */
if (!defined('_JEXEC'))
    define('_JEXEC', 1);

define('__ISUNITTEST', false);

error_reporting(1);


define('DS', DIRECTORY_SEPARATOR);

ini_set('display_errors', '1'); // only for xampp , because it screws up the display
ini_set('max_execution_time', '0');
ini_set('memory_limit', '2048M'); // required only on linux and ubuntu !

// We are a valid entry point.
const _JEXEC = 1;

// Load system defines
if (file_exists(dirname(__DIR__) . '/defines.php')) {
    require_once dirname(__DIR__) . '/defines.php';
}

if (!defined('_JDEFINES')) {
    define('JPATH_BASE', dirname(__DIR__));
    require_once JPATH_BASE . '/includes/defines.php';
}

/**
 * Constant that is checked in included files to prevent direct access.
 * define() is used in the installation folder rather than "const" to not error for PHP 5.2 and lower
 */
define('_JEXEC', 1);

if (file_exists(__DIR__ . '/defines.php')) {
    include_once __DIR__ . '/defines.php';
}

if (!defined('_JDEFINES')) {
    define('JPATH_BASE', __DIR__);
    require_once JPATH_BASE . '/includes/defines.php';
}
// Get the framework.
require_once JPATH_LIBRARIES . '/import.legacy.php';

// Bootstrap the CMS libraries.
require_once JPATH_LIBRARIES . '/cms.php';
// Framework import
require_once JPATH_BASE . '/includes/framework.php';
require_once JPATH_BASE . '/cliParser.php';

///var/www/softverk-webportal-remaxth/libraries/webportal/services/webservice/websending/websendingBase.php

require_once JPATH_BASE . '/libraries/webportal/services/webservice/websending/websendingBase.php';

require_once JPATH_ROOT . "/libraries/webportal/services/webservice/websending/agent.php";

// Instantiate the application.
$app = JFactory::getApplication('site');

ob_start(); // Start output buffering

// Execute the application.
$app->execute();

$list = ob_get_contents(); // Store buffer in variable

ob_end_clean(); // End buffering and clean up

/**
 * This script will fetch the update information for all extensions and store
 * them in the database, speeding up your administrator.
 *
 * @package  Joomla.Cli
 * @since    2.5
 */
class Restoreofficeagentpropertiesfromxml extends JApplicationCli
{
    var $officeDb = "mapportalv2_atv";
    var $agentDb = "mapportal_remax_v2";
    var $curl = null;

    /**
     * Entry point for the script
     *
     * @return  void
     *
     * @since   2.5
     */
    public function doExecute()
    {
//        $this->__handleOffice();
//        $this->__handleAgents();
//        $this->__handleProperties();
        $this->__fixAgentImages();
    }


    private function __fixAgentImages()
    {

        $query = "select * from jos_portal_sales";
        $existingAgents = WFactory::getSqlService()->select($query);
        $fileManager = WFactory::getFileManager();
        $commonConfig = WFactory::getConfig()->getWebportalConfigurationArray();
        /**
         * @var iFileManager
         */
        $fileManager = $fileManager->getFileManager($commonConfig["filesystem"]);
        $sourceFilePath = "/home/khan/www/softverk-webportal-generic/tests/portaltest/testImages/AGENT.jpg";
        foreach ($existingAgents as $agent) {

            $agentImagePath = "1/{$agent['office_id']}/{$agent['id']}";
            $destinationPath = $agentImagePath . "/image/{$agent['id']}_1." . pathinfo($sourceFilePath, PATHINFO_EXTENSION);
            $webPathURL = "";
            $tmpResult = $fileManager->putFile($sourceFilePath, $destinationPath, $webPathURL);

        }


    }

    private function __handleProperties()
    {
        $query = "  SELECT *
                    FROM  {$this->agentDb}.`jos_portal_senttoweb_log`
                    WHERE  `command` LIKE  'create'
                    AND  `type` LIKE  'property' order by date desc LIMIT 150";

        $propertiesXml = WFactory::getSqlService()->select($query);

        foreach ($propertiesXml as $xml) {
            $xml = $xml['data'];

            $this->curl = $this->getLocalCurl("senttoweb", "service", "");;
            curl_setopt($this->curl, CURLOPT_POSTFIELDS, $xml);
            $result = curl_exec($this->curl);

            WFactory::getLogger()->debug("Gotten : $result");

            if (!$result) {
                $msg = curl_error($this->curl);
                WFactory::getLogger()->error("$msg");
            }

            $xml = simplexml_load_string($result);
            $xmlData = json_decode(json_encode($xml), true);

            WFactory::getLogger()->info("Xml response is : {$xmlData["Response"]["Number"]}");
        }
    }

    private function __handleAgents()
    {

        $query = "  SELECT *
                    FROM  {$this->agentDb}.`jos_portal_senttoweb_log`
                    WHERE  `command` LIKE  'create'
                    AND  `type` LIKE  'agent'";

        $officeXmls = WFactory::getSqlService()->select($query);

        //this is because we need to match office ID with agents
        $query = "select * from {$this->agentDb}.jos_portal_sales";
        $existingAgents = WFactory::getSqlService()->select($query);

        //now insert the office....

        //select random first office
        $query = "select * from {$this->agentDb}.jos_portal_offices";
        $existingOffices = WFactory::getSqlService()->select($query);

        $uniqueId = $existingOffices[0]['unique_id'];
        $key = $existingOffices[0]['public_key'];


        foreach ($existingAgents as $agent) {
            $xml = $officeXmls[0]['data'];
            $xml = new SimpleXMLElement($xml);
            $xml->System->PublicKey = $key;
            $xml->SalesAssociates->SalesAssociate->Information->OfficeID = $uniqueId;

            $xml = $xml->asXML();

            $this->curl = $this->getLocalCurl("senttoweb", "service", "");;
            curl_setopt($this->curl, CURLOPT_POSTFIELDS, $xml);
            $result = curl_exec($this->curl);

            WFactory::getLogger()->debug("Gotten : $result");

            if (!$result) {
                $msg = curl_error($this->curl);
                WFactory::getLogger()->error("$msg");
            }

            $xml = simplexml_load_string($result);
            $xmlData = json_decode(json_encode($xml), true);

            WFactory::getLogger()->info("Xml response is : {$xmlData["Response"]["Number"]}");


            //find the latest id of office

            $query = "SELECT *
                        FROM  `jos_portal_sales`
                        ORDER BY  `jos_portal_sales`.`id` DESC LIMIT 1";
            $createdAgent = WFactory::getSqlService()->select($query);
            $createdAgentId = $createdAgent[0]['id'];
            /**
             * @var $agentDetails AgentModel
             */
            $agentDetails = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_AGENT)->getAgent($createdAgentId, true);
            $agentDetails->first_name = $agent['first_name'];
            $agentDetails->last_name = $agent['last_name'];
            $agentDetails->unique_id = $agent['unique_id'];
            $agentDetails->mobile = $agent['mobile'];
            $agentDetails->email = $agent['email'];
            $agentDetails->phone = $agent['phone'];
            $agentDetails->marketing_info->description = "<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cum audissem Antiochum, Brute, ut solebam, cum M. Tibi hoc incredibile, quod beatissimum. Qui ita affectus, beatum esse numquam probabis; Duo Reges: constructio interrete. Odium autem et invidiam facile vitabis. Sed venio ad inconstantiae crimen, ne saepius dicas me aberrare; </p>

                                                        <p>Quam ob rem tandem, inquit, non satisfacit? Quod idem cum vestri faciant, non satis magnam tribuunt inventoribus gratiam. </p>

                                                        <p>Graecum enim hunc versum nostis omnes-: Suavis laborum est praeteritorum memoria. Utinam quidem dicerent alium alio beatiorem! Iam ruinas videres. </p>

                                                        <p>Non autem hoc: igitur ne illud quidem. Non minor, inquit, voluptas percipitur ex vilissimis rebus quam ex pretiosissimis. Quae cum magnifice primo dici viderentur, considerata minus probabantur. Nonne videmus quanta perturbatio rerum omnium consequatur, quanta confusio? Nosti, credo, illud: Nemo pius est, qui pietatem-; </p>
                                                        ";
            $agentDetails->marketing_info->bullet_point1 = "1 Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cum audissem Antiochum, Brute, ut solebam";
            $agentDetails->marketing_info->bullet_point2 = "2 Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cum audissem Antiochum, Brute, ut solebam";
            $agentDetails->marketing_info->bullet_point3 = "3 Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cum audissem Antiochum, Brute, ut solebam";

            $updateResult = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_AGENT)->updateAgent($agentDetails);
        }

    }

    private function __handleOffice()
    {
        // get the office create xml

        $query = "  SELECT *
                    FROM  {$this->officeDb}.`jos_portal_senttoweb_log`
                    WHERE  `command` LIKE  'create'
                    AND  `type` LIKE  'office'";

        $officeXmls = WFactory::getSqlService()->select($query);

        //this is because we need to match office ID with agents
        $query = "select * from {$this->agentDb}.jos_portal_offices";
        $existingOffices = WFactory::getSqlService()->select($query);

        //now insert the office....


        foreach ($existingOffices as $office) {
            $xml = $officeXmls[0]['data'];

            $this->curl = $this->getLocalCurl("senttoweb", "service", "");;
            curl_setopt($this->curl, CURLOPT_POSTFIELDS, $xml);
            $result = curl_exec($this->curl);

            WFactory::getLogger()->debug("Gotten : $result");

            if (!$result) {
                $msg = curl_error($this->curl);
                WFactory::getLogger()->error("$msg");
            }

            $xml = simplexml_load_string($result);
            $xmlData = json_decode(json_encode($xml), true);

            WFactory::getLogger()->info("Xml response is : {$xmlData["Response"]["Number"]}");


            //find the latest id of office

            $query = "SELECT *
                        FROM  `jos_portal_offices`
                        ORDER BY  `jos_portal_offices`.`id` DESC LIMIT 1";
            $createdOffice = WFactory::getSqlService()->select($query);
            $createdOfficeId = $createdOffice[0]['id'];
            /**
             * @var $officeDetails OfficeModel
             */
            $officeDetails = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_OFFICE)->getOffice($createdOfficeId, true);
            $officeDetails->office_name = strip_tags($office['office_name']);
            $officeDetails->unique_id = $office['unique_id'];
            $officeDetails->public_key = $office['public_key'];
            $officeDetails->fax = $office['fax'];
            $officeDetails->phone = $office['phone'];
            $officeDetails->marketingInfo->description = "<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cum audissem Antiochum, Brute, ut solebam, cum M. Tibi hoc incredibile, quod beatissimum. Qui ita affectus, beatum esse numquam probabis; Duo Reges: constructio interrete. Odium autem et invidiam facile vitabis. Sed venio ad inconstantiae crimen, ne saepius dicas me aberrare; </p>

                                                        <p>Quam ob rem tandem, inquit, non satisfacit? Quod idem cum vestri faciant, non satis magnam tribuunt inventoribus gratiam. </p>

                                                        <p>Graecum enim hunc versum nostis omnes-: Suavis laborum est praeteritorum memoria. Utinam quidem dicerent alium alio beatiorem! Iam ruinas videres. </p>

                                                        <p>Non autem hoc: igitur ne illud quidem. Non minor, inquit, voluptas percipitur ex vilissimis rebus quam ex pretiosissimis. Quae cum magnifice primo dici viderentur, considerata minus probabantur. Nonne videmus quanta perturbatio rerum omnium consequatur, quanta confusio? Nosti, credo, illud: Nemo pius est, qui pietatem-; </p>
                                                        ";
            $officeDetails->marketingInfo->bullet_point1 = "1 Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cum audissem Antiochum, Brute, ut solebam";
            $officeDetails->marketingInfo->bullet_point2 = "2 Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cum audissem Antiochum, Brute, ut solebam";
            $officeDetails->marketingInfo->bullet_point3 = "3 Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cum audissem Antiochum, Brute, ut solebam";

            $updateResult = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_OFFICE)->updateOffice($officeDetails);
        }


    }

    function simpleEcho($msg)
    {
        echo "$msg\r\n";
    }

    function getLocalCurl($controller, $task, $optherOptions)
    {

        $publicIp = "1.2.180.42";//WFactory::getPublicIp();
        $pathbase = JPATH_BASE;
        $rootFolderName = explode(DS, $pathbase);
        $rootFolderName = $rootFolderName[count($rootFolderName) - 1];

        $urlpart = array("option=com_webportal");
        if (!empty($controller) && $controller !== null) {
            $urlpart[] = "controller=$controller";
        }
        if (!empty($task) && $task !== null) {
            $urlpart[] = "task=$task";
        }
        if (!empty($optherOptions) && $optherOptions !== null) {
            $urlpart[] = $optherOptions;
        }

        $urlpart = implode("&", $urlpart);
        $siteAddress = "http://localhost/$rootFolderName/index.php?$urlpart";

        WFactory::getLogger()->debug("Executing curl to : $siteAddress");

        $url = $siteAddress;
        $curl_connection = curl_init($url);
        curl_setopt($curl_connection, CURLOPT_HTTPHEADER, array('text/html; charset=utf-8', ""));
        curl_setopt($curl_connection, CURLOPT_HEADER, false);
        curl_setopt($curl_connection, CURLOPT_TIMEOUT, 300);
        curl_setopt($curl_connection, CURLOPT_POST, 1);
        curl_setopt($curl_connection, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl_connection, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl_connection, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl_connection, CURLOPT_COOKIESESSION, TRUE);
        // stolen from firefox bookmarL
        // javascript:(/**%20@version%200.5.2%20*/function()%20{document.cookie='XDEBUG_SESSION='+'PHPSTORM'+';path=/;';})()
        curl_setopt($curl_connection, CURLOPT_COOKIE, 'XDEBUG_SESSION=PHPSTORM;path=/;');

        return $curl_connection;
    }

}


JApplicationCli::getInstance('Restoreofficeagentpropertiesfromxml')->execute();
