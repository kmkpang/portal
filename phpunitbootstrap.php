<?php
/**
 * @package        Joomla.Site
 * @copyright    Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */
//DebugBreak();
/**
 * security..if jexe is not defined..none of the php classes will execute
 */
if (!defined('_JEXEC'))
    define('_JEXEC', 1);

error_reporting(1);


//define('_TESTSMSINCOMING',1);


define('DS', DIRECTORY_SEPARATOR);
define('__ISUNITTEST', true);
define('__QUICKTEST', true);
define('__NEWCOUNTRYGEODATAIMPLMENETEDBYSAGAYET', false);


$jpath_base = (dirname(__FILE__));
$jpath_base = realpath($jpath_base);
ini_set('display_errors', '1'); // only for xampp , because it screws up the display
ini_set('max_execution_time', '0');
ini_set('memory_limit', '2048M'); // required only on linux and ubuntu !

// for debugging..
//if ($_REQUEST['show_errors'] == '1')
//ini_set('display_errors', '1');


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

require_once JPATH_BASE . '/includes/framework.php';

// Mark afterLoad in the profiler.
JDEBUG ? $_PROFILER->mark('afterLoad') : null;

// Instantiate the application.
$app = JFactory::getApplication('site');


ob_start(); // Start output buffering

try {
    // Execute the application.
    $app->execute();
} catch (Exception $e) {
    WFactory::getLogger()->debug("Ignoring : " . $e->getMessage());
}

$list = ob_get_contents(); // Store buffer in variable

ob_end_clean(); // End buffering and clean up


if (__QUICKTEST)
    WFactory::getLogger()->warn("-------------------------Quicktest is ENABLED!!!!!!-------------------------");

function loginToJoomla($username = "admin", $password = "hungur76")
{
    $app = JFactory::getApplication();

    $credentials = array();
    $credentials['username'] = $username;
    $credentials['password'] = $password;

    $options = array();

    // Perform the log in.
    if (true === $app->login($credentials, $options)) {
        // Success
        $app->setUserState('users.login.form.data', array());
    }

    $user = JFactory::getUser();
}


function logTestStart($testName)
{
    echo "\r\n";
    WFactory::getLogger()->debug("***********************************************************");
    WFactory::getLogger()->debug("***** Starting test $testName ");
    WFactory::getLogger()->debug("***********************************************************");
}

function logTestEnd($testName)
{
    WFactory::getLogger()->debug("***********************************************************");
    WFactory::getLogger()->debug("***** Ending test $testName ");
    WFactory::getLogger()->debug("***********************************************************");
    echo "\r\n";
}

function getLocalApiCurl($curlURL, $keepUrlIntact = false)
{
    if (!__QUICKTEST)
        $publicIp = WFactory::getPublicIp();
    $pathbase = JPATH_BASE;
    $rootFolderName = explode(DS, $pathbase);
    $rootFolderName = $rootFolderName[count($rootFolderName) - 1];

    //$urlpart = array("option=com_webportal");
    $urlpart[] = $curlURL;


    $urlpart = implode("&", $urlpart);

    $isGq = false;
    if (strpos($publicIp, "46") === 0) {
        $isGq = true;
    }

    if (!$keepUrlIntact)
        $urlpart = JRoute::_($urlpart);

    if ($isGq) {
        $siteAddress = "http://staging-workspace.softverk.is/$rootFolderName/$urlpart";
    } else { //localhost

        $siteAddress = "http://localhost/$rootFolderName/$urlpart";
    }

    //$siteAddress = "http://staging-workspace.softverk.is/softverk-webportal-khan-dev/api/v1/properties/search/";
    WFactory::getLogger()->debug("Executing curl to : $siteAddress");

    $url = $siteAddress;
    $curl_connection = curl_init($url);
    curl_setopt($curl_connection, CURLOPT_HTTPHEADER, array('text/html; charset=utf-8', ""));
    curl_setopt($curl_connection, CURLOPT_HEADER, false);
    curl_setopt($curl_connection, CURLOPT_TIMEOUT, 86400); //one day!
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

function getProperties($count)
{
    $query = "Select * from jos_portal_properties where is_deleted=0 ORDER BY RAND() LIMIT $count";
    $result = WFactory::getSqlService()->select($query);

    return $result;
}

/**
 * This is a helper function for other tests
 * Actuall just return 20...!otherwise too slow
 */
function get20Properties()
{
    return getProperties(20);
}


function getLocalCurl($controller, $task, $optherOptions)
{
    if (!__QUICKTEST)
        $publicIp = WFactory::getPublicIp();
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

    $isGq = false;
    if (strpos($publicIp, "46") === 0) {
        $isGq = true;
    }

    if ($isGq) {
        $siteAddress = "http://staging-workspace.softverk.is/$rootFolderName/index.php?$urlpart";
    } else { //localhost

        $siteAddress = "http://localhost/$rootFolderName/index.php?$urlpart";
    }


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

function getRemoteCurl($controller, $task, $optherOptions, $baseAddress)
{

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


    $siteAddress = "http://$baseAddress/index.php?$urlpart";


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


function setUpWebsendingDatabase($downloadEntireDb = false)
{

    //remax live server is needed for me to do this test:
    $remote_db_login = "root";
    $remote_db_pass = "softverk8";
    $remote_db_name = "mapportal_remax_v2";
    $remote_ssh_login = "root";
    $remote_ssh_pass = "onodire@123";
    $remote_server_address = "157.157.173.23";

    $localdb_name = JFactory::getApplication()->get('db');
    $localdb_login = JFactory::getApplication()->get('user');
    $localdb_password = JFactory::getApplication()->get('password');

    $unittest_name = uniqid();
    $office_db_file = "/tmp/jos_portal_office_remax.sql";
    $senttoweb_file = "/tmp/senttoweb.sql";


    $officeDumpingCommand = "mysqldump -u$remote_db_login -p$remote_db_pass $remote_db_name jos_portal_offices >  $office_db_file";
    $officeDownloadCommand = "$office_db_file $office_db_file";

    $sshpassCommand = "sshpass -p $remote_ssh_pass ssh $remote_ssh_login@$remote_server_address ";
    $scpCommand = "sshpass -p $remote_ssh_pass scp $remote_ssh_login@$remote_server_address:";

    WFactory::getLogger()->debug("Dumping office data on remote server");
    $command = $sshpassCommand . "'$officeDumpingCommand'";
    WFactory::getLogger()->debug("Executing : $command");
    shell_exec($command);

    WFactory::getLogger()->debug("Downloading office data to local server");
    $command = $scpCommand . $officeDownloadCommand;
    WFactory::getLogger()->debug("Executing : $command");
    shell_exec($command);

    //dump and restore mysql
    WFactory::getLogger()->debug("Restoring jos_portal_offices");
    $query = "delete from jos_portal_offices";
    WFactory::getSqlService()->update($query);
    shell_exec("mysql -u$localdb_login -p$localdb_password $localdb_name < $office_db_file");

    WFactory::getLogger()->debug("Done with offices");

    //--------------------------------------------------------------------------------

    if ($downloadEntireDb) {

        $condition = $downloadEntireDb ? "" : "--where=\\\"command LIKE \\'Update\\' or command LIKE \\'create\\' or command like \\'Delete\\' group by command,type ORDER BY id desc LIMIT 20\\\"";
        $dumpCommand = "mysqldump -u$remote_db_login -p$remote_db_pass $remote_db_name jos_portal_senttoweb_log $condition > $senttoweb_file";
        WFactory::getLogger()->debug("Dumping senttoweb data on remote server");
        $command = $sshpassCommand . $dumpCommand;
        WFactory::getLogger()->debug("Executing : $command");
        shell_exec($command);

    } else {
        //upload shit to remote machine
        ///var/www/softverk-webportal/tests/portaltest/senttoweb/helper.sh
        $localHelpFile = JPATH_BASE . DS . "tests" . DS . "portaltest" . DS . "senttoweb" . DS . "helper.sh";
        $uploadCommand = "sshpass -p $remote_ssh_pass  scp $localHelpFile $remote_ssh_login@$remote_server_address:/tmp/helper.sh";
        $command = $uploadCommand;
        WFactory::getLogger()->debug("Executing : $command");
        shell_exec($command);

        $executeCommand = "chmod +x /tmp/helper.sh";
        $command = $sshpassCommand . $executeCommand;
        WFactory::getLogger()->debug("Executing : $command");
        shell_exec($command);

        $executeCommand = "/tmp/helper.sh $remote_db_login $remote_db_pass $remote_db_name";
        $command = $sshpassCommand . $executeCommand;
        WFactory::getLogger()->debug("Executing : $command");
        shell_exec($command);
    }
    WFactory::getLogger()->debug("Downloading senttoweb data to local server");
    $sentToWebDownloadCommand = "$senttoweb_file $senttoweb_file";
    $command = $scpCommand . $sentToWebDownloadCommand;
    WFactory::getLogger()->debug("Executing : $command");
    shell_exec($command);

    //dump and restore mysql
    WFactory::getLogger()->debug("Restoring setnttoweb");
    $query = "delete from jos_portal_senttoweb_log";
    WFactory::getSqlService()->update($query);
    shell_exec("mysql -u$localdb_login -p$localdb_password $localdb_name < $senttoweb_file");

    WFactory::getLogger()->debug("Done with senttoweb");

    fixWebsendingAddress();

}

function fixWebsendingAddress()
{
    $query = "SELECT * FROM `jos_portal_senttoweb_log` ";

    $result = WFactory::getSqlService()->select($query);

    foreach ($result as $r) {
        /**
         * @var $dbClass PortalPortalSenttowebLogSql
         */
        $dbClass = WFactory::getSqlService()->getDbClass(__PORTAL_PORTAL_SENTTOWEB_LOG_SQL);
        $dbClass->bind($r);


        if (strtoupper($dbClass->__command) == "UPDATE" || strtoupper($dbClass->__command) == "CREATE") {

            $xml = new SimpleXMLElement($dbClass->__data);
            $address = null;
            if ($dbClass->__type == "Property") {
                $address = $xml->Properties->Property->Address;
            }
            if ($dbClass->__type == "Agent") {
                $address = $xml->SalesAssociates->SalesAssociate->Address;
            }

            $ranndomAddress = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_ADDRESS)->getRandomAddress();

            $address->PostalCodeID = $ranndomAddress["postal"];
            $address->RegionID = $ranndomAddress["region"];
            $address->TownID = $ranndomAddress["town"];

            $dbClass->__data = $xml->asXML();

            WFactory::getSqlService()->update($dbClass);


        }


    }


}
