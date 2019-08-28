<?php
/**
 * @package        Joomla.Site
 * @copyright    Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */
if (!defined('_JEXEC'))
    define('_JEXEC', 1);

define('__ISUNITTEST', false);
define('__KHANHOME', true);

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
class Updageagentsfromlastrecievedxml extends JApplicationCli
{
    /**
     * Entry point for the script
     *
     * @return  void
     *
     * @since   2.5
     */
    public function doExecute()
    {
        $query = "SELECT jos_portal_sales.is_deleted, jos_portal_sales.*
                      FROM `remax-th2`.jos_portal_sales jos_portal_sales
                     WHERE (jos_portal_sales.is_deleted = 0)";


        $agents = WFactory::getSqlService()->select($query);

        foreach ($agents as $a) {
            $query = "SELECT jos_portal_senttoweb_log.*, jos_portal_senttoweb_log.command
                          FROM `remax-th2`.jos_portal_senttoweb_log jos_portal_senttoweb_log
                         WHERE     (jos_portal_senttoweb_log.type = 'Agent')
                               AND (jos_portal_senttoweb_log.data LIKE '%{$a["unique_id"]}%')
                               AND (jos_portal_senttoweb_log.command = 'Update')
                        ORDER BY jos_portal_senttoweb_log.`date` DESC
                        LIMIT 1";

            $senttoweb = WFactory::getSqlService()->select($query);
            $senttoweb = $senttoweb[0];

            if ($senttoweb !== null) {

                $data = $senttoweb['data'];

                $xml = simplexml_load_string($data);
                $xmlData = json_decode(json_encode($xml), true);

                $marketingInfo = $xmlData['SalesAssociates']['SalesAssociate']['Information']['MarketingInfo'];

                $break = false;
                foreach ($marketingInfo as $m)
                    if (!empty($m))
                        $break = true;

                if ($break == true) {
                    $x = $marketingInfo;
                }


                if ($break === true) {

                    $senttoWeb = new AgentSentToWeb($data);

                    $result = $senttoWeb->update();

                    $xml = simplexml_load_string($result);
                    $xmlData = json_decode(json_encode($xml), true);

                    if ($xmlData["Response"]["Number"] == "02221")
                        WFactory::getLogger()->debug("UPDATED agent id {$a["id"]} / {$a["unique_id"]}");
                }

            } else {
                WFactory::getLogger()->warn("NO Update msg for agent id {$a["id"]} / {$a["unique_id"]}");
            }

        }


    }

    function simpleEcho($msg)
    {
        echo "$msg\r\n";
    }


}


JApplicationCli::getInstance('Updageagentsfromlastrecievedxml')->execute();
