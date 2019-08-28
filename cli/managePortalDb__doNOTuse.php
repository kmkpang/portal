<?php
/**
 * @package        Joomla.Site
 * @copyright    Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */
if (!defined('_JEXEC'))
    define('_JEXEC', 1);

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
class Manageportaldb extends JApplicationCli
{

    var $password;
    var $user;
    var $database;
    var $output;
    var $input;
    var $tarfile;
    var $keepOffice;

    /**
     * Entry point for the script
     *
     * @return  void
     *
     * @since   2.5
     */
    public function doExecute()
    {
        WFactory::getLogger()->warn("****************************************************************");
        WFactory::getLogger()->warn("*****************Not tested on windows!!!!**********************");
        WFactory::getLogger()->warn("*****if windows,make sure mysql command available from path*****");
        require_once JPATH_LIBRARY_WEBPORTAL_SERVICES . DS . "sqlclassgenerator/generator.php";

        $arguments = parseArguments();
        $functionName = $arguments["function"];

        if (WFactory::getHelper()->isNullOrEmptyString($functionName)) {
            WFactory::getLogger()->error("Please provide function name like this : php managePortalDb.php --function=FUNCTION_NAME");
            exit(1);
        }


        $config = JFactory::getConfig();
        $this->password = $config->get('password');; //          <<---------
        $this->user = $config->get('user');; //          <<---------
        $this->database = $config->get('db');; //          <<---------

        WFactory::getLogger()->debug("Executing function : " . $functionName);

        $this->{$functionName}($arguments);

    }

    private function restoredb($arguments)
    {
        $this->input = $arguments['input'];
        $this->tarfile = $arguments['tarfile'];
        if (WFactory::getHelper()->isNullOrEmptyString($this->input) && WFactory::getHelper()->isNullOrEmptyString($this->tarfile)) {
            WFactory::getLogger()
                ->error("Please provide input file name like this: php managePortalDb.php --function=restoredb --input=/path/to/output/file.sql
                                                                or php managePortalDb.php --function=restoredb --tarfile=/var/www/softverk-webportal/sql/jos_portal.sql.tar.gz ");
            exit(1);
        }

        if (!WFactory::getHelper()->isNullOrEmptyString($this->tarfile)) {
            $output = JPATH_BASE . DS . "sql" . DS;
            $commaand = "tar -xzvf {$this->tarfile} -C $output --strip-components=1";
            exec($commaand);
            $this->input = $output . "jos_portal.sql";
        }

        $this->sleepWait(10, "This will drop all your jos_portal_* and jos_geography_* tables. press ctrl+c now to exit..");

        $tables = getTableList();

        foreach ($tables as $t) {
            $query = "drop table `$t`";
            WFactory::getLogger()->warn("executing: $query");
            $result = WFactory::getServices()->getSqlService()->update($query);
            WFactory::getLogger()->debug("result of dropping table $t : " . $result);
        }

        WFactory::getLogger()->debug("Restoring db from {$this->input}");

        $command = "mysql -u{$this->user} -p{$this->password} {$this->database} < {$this->input}";

        WFactory::getLogger()->debug("Executing: $command");

        exec($command);

        WFactory::getLogger()->debug("Your database has been restored..");

        $query = "drop table `jos_portal_property_information`";
        WFactory::getLogger()->warn("executing: $query");
        $result = WFactory::getServices()->getSqlService()->update($query);

    }

    private function prepareNewSite($arguments)
    {

        $this->keepOffice = $arguments['keepOffice'];

        if ($this->keepOffice !== null)
            $this->keepOffice = true;
        else
            $this->keepOffice = false;

        //  $this->sleepWait(10, "This will delete your existing properties,sales etc , sales , offices! press ctrl+c to stop...");

        $tables = getTableList();

        $skippable = array(
            'jos_portal_address_types',
            'jos_portal_companies',

            'jos_portal_contacts',
            'jos_portal_property_modes',
            'jos_portal_property_types',
            'jos_portal_setting',
            'jos_portal_property_categories',
            'jos_portal_features'

        );

        if ($this->keepOffice)
            $skippable[] = 'jos_portal_offices';

        foreach ($tables as $t) {
            if (strpos($t, 'jos_geography') !== false || in_array($t, $skippable))
                continue;


            WFactory::getLogger()->warn("Emptying table $t");
            $query = 'delete from ' . $t;
            WFactory::getSqlService()->update($query);
        }

        /*
               $query = 'delete from jos_portal_properties';
               WFactory::getSqlService()->update($query);
               $query = 'delete from jos_portal_sales';
               WFactory::getSqlService()->update($query);
               $query = 'delete from jos_portal_sales';
               WFactory::getSqlService()->update($query);
               $query = 'delete from jos_portal_properties';
               WFactory::getSqlService()->update($query);
               $query = 'delete from jos_portal_property_addresses';
               WFactory::getSqlService()->update($query);
               $query = 'delete from jos_portal_property_images';
               WFactory::getSqlService()->update($query);
           */

//        $this->restoreOffices();
//        $this->restoreAgents();
//
//        $this->restoreProperties();
    }


    private function dumpdb($arguments)
    {
        $this->output = $arguments['output'];
        if (WFactory::getHelper()->isNullOrEmptyString($this->output)) {
            WFactory::getLogger()->error("Please provide output file name like this: php managePortalDb.php --function=dumpdb --output=/path/to/output/file.sql");
            exit(1);
        }

        WFactory::getLogger()->debug("Starting ManagePortalDb");

        $query = "truncate table jos_portal_senttoweb_log";
        WFactory::getServices()->getSqlService()->update($query);


        $tables = getTableList();

        $tables = implode(" ", $tables);

        $command = "mysqldump -u{$this->user} -p{$this->password} {$this->database} $tables > {$this->output}";

        WFactory::getLogger()->debug("Executing: $command");

        exec($command);

        WFactory::getLogger()->debug("Your database is located at : {$this->output}");

        $tarfile = $arguments["tarfile"];

        if (!WFactory::getHelper()->isNullOrEmptyString($tarfile)) {
            WFactory::getLogger()->debug("Tar gz-ing your file");
            $command = "tar -czf $tarfile {$this->output}";
            exec($command);

            WFactory::getLogger()->debug("Your tar is located at : {$tarfile}");
        }


    }

    private function populatePropertyTable()
    {
        $this->sleepWait(10, "This will delete your existing properties , sales , offices! press ctrl+c to stop...");
        $this->unzipAndRestoreSentToWeb();

        $query = 'delete from jos_portal_offices';
        WFactory::getSqlService()->update($query);
        $query = 'delete from jos_portal_sales';
        WFactory::getSqlService()->update($query);
        $query = 'delete from jos_portal_sales';
        WFactory::getSqlService()->update($query);
        $query = 'delete from jos_portal_properties';
        WFactory::getSqlService()->update($query);
        $query = 'delete from jos_portal_property_addresses';
        WFactory::getSqlService()->update($query);
        $query = 'delete from jos_portal_property_images';
        WFactory::getSqlService()->update($query);

        define("__ISUNITTEST", true); // use this in order to make sure random images are indeed created!

        $this->restoreOffices();
        $this->restoreAgents();

        $this->restoreProperties();

    }


//===============================================================================================================================================
//===============================================================================================================================================
//===============================================================================================================================================
//===============================================================================================================================================
//===============================================================================================================================================
//===============================================================================================================================================
//===============================================================================================================================================
//===============================================================================================================================================
//===============================================================================================================================================
//===============================================================================================================================================


    private function restoreAgents()
    {
        require_once JPATH_ROOT . "/libraries/webportal/services/webservice/websending/agent.php";

        $query = "SELECT jos_portal_senttoweb_log.command,
                       jos_portal_senttoweb_log.type,
                       jos_portal_senttoweb_log.data
                  FROM senttowebhelper.jos_portal_senttoweb_log jos_portal_senttoweb_log
                 WHERE     (jos_portal_senttoweb_log.command = 'Create')
                       AND (jos_portal_senttoweb_log.type = 'Agent')";

        $createAgents = WFactory::getSqlService()->select($query);

        foreach ($createAgents as $agent) {
            $xml = $agent['data'];
            $this->restoreSingleAgent($xml);
        }

    }

    private function restoreSingleAgent($xml)
    {
        $xml = new SimpleXMLElement($xml);
        $xml->System->PublicKey = $this->officeKey;
        $xml->SalesAssociates->SalesAssociate->Information->OfficeID = $this->officeId;

        $xml = $xml->asXML();
        $this->object = new AgentSentToWeb($xml);

        $result = $this->object->create();

        $xml = simplexml_load_string($result);

        $xmlData = json_decode(json_encode($xml), true);

        $agentId = $xmlData['Response']['Message'];

        WFactory::getLogger()->debug("\n****************** agent created with $agentId **************************\n");

    }

    private function restoreProperties()
    {

        require_once JPATH_ROOT . "/libraries/webportal/services/webservice/websending/property.php";

        $query = "SELECT jos_portal_senttoweb_log.command,
                       jos_portal_senttoweb_log.type,
                       jos_portal_senttoweb_log.data
                  FROM senttowebhelper.jos_portal_senttoweb_log jos_portal_senttoweb_log
                 WHERE     (jos_portal_senttoweb_log.command = 'Create')
                       AND (jos_portal_senttoweb_log.type = 'Property')";

        $createProperties = WFactory::getSqlService()->select($query);


        $query = "SELECT jos_portal_sales.unique_id AS sales_uniqueid,
                       jos_portal_offices.public_key AS office_key,
                       jos_portal_offices.unique_id AS office_uniqueid
                  FROM    `softverk-webportal`.jos_portal_sales jos_portal_sales
                       INNER JOIN
                          `softverk-webportal`.jos_portal_offices jos_portal_offices
                       ON (jos_portal_sales.office_id = jos_portal_offices.id)";

        $agents = WFactory::getSqlService()->select($query);


        foreach ($createProperties as $property) {

            $agent = $agents[array_rand($agents)];

            $xml = $property['data'];
            $xml = new SimpleXMLElement($xml);
            $xml->System->PublicKey = $agent["office_key"];
            $xml->Properties->Property->Information->SaleID = $agent["sales_uniqueid"];
            $xml->Properties->Property->Information->OfficeID = $agent["office_uniqueid"];

            $xml = $xml->asXML();

            $this->restoreSingleProperty($xml);


        }
    }

    private function restoreSingleProperty($xml)
    {
        $this->object = new PropertySentToWeb($xml);
        $result = $this->object->create();


        //WFactory::getLogger()->debug($result);

        $xml = simplexml_load_string($result);

        $xmlData = json_decode(json_encode($xml), true);

        $propertyId = $xmlData['Response']['Message'];

        WFactory::getLogger()->debug("\n****************** property created with $propertyId **************************\n");

    }

    var $officeKey;
    var $officeId;
    var $object;
    var $xml;


    private function restoreOffices()
    {
        require_once JPATH_ROOT . "/libraries/webportal/services/webservice/websending/office.php";
        $this->xml = JPATH_ROOT . "/tests/portaltest/testXml/officeCreate.xml.xsl";
        $xml = trim(file_get_contents($this->xml));

        $this->xmlString = $xml;
        $this->object = new OfficeSentToWeb($xml);

        $result = $this->object->create();

        $xml = simplexml_load_string($result);

        $xmlData = json_decode(json_encode($xml), true);

        $this->officeKey = $xmlData['Response']['Message']['PublicKey'];
        $this->officeId = $xmlData['Response']['Message']['UniqueID'];

        WFactory::getLogger()->debug("Created office with {$this->officeKey} <-- Key , {$this->officeId} <-- uniqueId");


    }

    private function sleepWait($waitTime, $msg)
    {
        WFactory::getLogger()->warn($msg);
        for ($i = 0; $i < $waitTime; $i++) {
            sleep(1);
            echo ".";
        }
        echo "\n";
    }

    private function unzipAndRestoreSentToWeb()
    {
        $sqlFolder = JPATH_BASE . DS . "sql";
        $login = $this->user;
        $pass = $this->password;
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

        $this->fixWebsendingAddress();

    }

    private function fixWebsendingAddress()
    {
        $query = "SELECT * FROM `senttowebhelper`.`jos_portal_senttoweb_log` ";

        $result = WFactory::getSqlService()->select($query);

        foreach ($result as $r) {
            /**
             * @var $dbClass PortalPortalSenttowebLogSql
             */
            $dbClass = WFactory::getSqlService()->getDbClass(__PORTAL_PORTAL_SENTTOWEB_LOG_SQL);
            $dbClass->bind($r);


            if ((strtoupper($dbClass->__type) == "PROPERTY" || strtoupper($dbClass->__type) == "AGENT") &&
                (strtoupper($dbClass->__command) == "UPDATE" || strtoupper($dbClass->__command) == "CREATE")
            ) {

                $xml = new SimpleXMLElement($dbClass->__data);
                $address = null;
                if ($dbClass->__type == "Property") {
                    $address = $xml->Properties->Property->Address;
                }
                if ($dbClass->__type == "Agent") {
                    $address = $xml->SalesAssociates->SalesAssociate->Address;
                }

                //  var_dump($address);

                $ranndomAddress = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_ADDRESS)->getRandomAddress();

                $address->PostalCodeID = $ranndomAddress["postal"];
                $address->RegionID = $ranndomAddress["region"];
                $address->TownID = $ranndomAddress["town"];

                $dbClass->__data = $xml->asXML();

                //confirm it:
//                $xml = new SimpleXMLElement($dbClass->__data);
//                $address = null;
//                if ($dbClass->__type == "Property") {
//                    $address = $xml->Properties->Property->Address;
//                }
//                if ($dbClass->__type == "Agent") {
//                    $address = $xml->SalesAssociates->SalesAssociate->Address;
//                }
//
//                var_dump($address);

                $data = mysql_real_escape_string($dbClass->__data);

                $updateCommand = "UPDATE senttowebhelper.jos_portal_senttoweb_log
                               SET data = '$data'
                             WHERE (jos_portal_senttoweb_log.id = {$dbClass->__id});";

                WFactory::getLogger()->debug("Updating {$dbClass->__id}");

                WFactory::getSqlService()->update($updateCommand);


            }


        }


    }

}

JApplicationCli::getInstance('Manageportaldb')->execute();
