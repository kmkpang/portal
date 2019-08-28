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
class Emulatesagatransportandrestoreofficeagentpropertiesfromxml extends JApplicationCli
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

        $query = "select data from jos_portal_senttoweb_log where direction='INCOMING' order by date asc";
        $result = WFactory::getSqlService()->select($query);
        $this->curl = $this->getLocalCurl("senttoweb", "service", "");;
        foreach ($result as $r) {

            $xml = ($r['data']);

            curl_setopt($this->curl, CURLOPT_POSTFIELDS, $xml);
            $result = curl_exec($this->curl);


        }

        curl_close($this->curl);


//        $this->__handleOffice();
//        $this->__handleAgents();
//        $this->__handleProperties();

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


    function simpleEcho($msg)
    {
        echo "$msg\r\n";
    }

    function getLocalCurl($controller, $task, $optherOptions)
    {

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


JApplicationCli::getInstance('Emulatesagatransportandrestoreofficeagentpropertiesfromxml')->execute();
