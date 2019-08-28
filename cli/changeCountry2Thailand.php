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
class Changecountry2thailand extends JApplicationCli
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

        //jos_portal_properties
        //jos_portal_property_addresses
        //jos_portal_property_images
        //jos_portal_property_viewings_users
        //jos_portal_property_features


        $this->sleepWait(10, "This will delete your existing geodata tables and restore thai info ! press ctrl+c to stop...");

        $config = JFactory::getConfig();
        ///home/khan/www/softverk-webportal-generic-b/cli/th-data/geodata.sql
        $command = "mysql -u" . $config->get("user") . " -p" . $config->get("password") . " " . $config->get("db") . " < " . JPATH_BASE . "/cli/th-data/geodata.sql";
        $this->simpleEcho("Going to execute : $command");
        $command = "mysql -u" . $config->get("user") . " -p" . $config->get("password") . " " . $config->get("db") . " < " . JPATH_BASE . "/cli/th-data/jos_portal_property_categories.sql";
        $this->simpleEcho("Going to execute : $command");



        shell_exec($command);


    }

    function simpleEcho($msg)
    {
        echo "$msg\r\n";
    }

    private function sleepWait($waitTime, $msg)
    {
        WFactory::getLogger()->warn($msg);
        for ($i = 0; $i < $waitTime; $i++) {
            sleep(1);
            echo " . ";
        }
        echo "\n";
    }
}


JApplicationCli::getInstance('Changecountry2thailand')->execute();
