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
class Deploynewsite extends JApplicationCli
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


        $this->sleepWait(10, "This will delete your existing properties,agents,offices ! press ctrl+c to stop...");


        WFactory::getLogger()->info("deleting jos_portal_properties");
        $query = 'delete from jos_portal_properties';
        WFactory::getSqlService()->update($query);
        WFactory::getLogger()->info("deleting jos_portal_property_images");
        $query = 'delete from jos_portal_property_images';
        WFactory::getSqlService()->update($query);
        WFactory::getLogger()->info("deleting jos_portal_property_viewings_users");
        $query = 'delete from jos_portal_property_viewings_users';
        WFactory::getSqlService()->update($query);
        WFactory::getLogger()->info("deleting jos_portal_property_features");
        $query = 'delete from jos_portal_property_features';
        WFactory::getSqlService()->update($query);
        WFactory::getLogger()->info("deleting jos_portal_property_addresses");
        $query = 'delete from jos_portal_property_addresses';
        WFactory::getSqlService()->update($query);

        WFactory::getLogger()->debug("Properties cleaned...please resume websending..");


        $query = 'delete from jos_portal_sales';
        WFactory::getSqlService()->update($query);
        $query = 'delete from jos_portal_marketing_info';
        WFactory::getSqlService()->update($query);

        WFactory::getLogger()->debug("Agents cleaned...please resume websending..");

        $query = 'delete from jos_portal_offices';
        WFactory::getSqlService()->update($query);

        WFactory::getLogger()->debug("Offices cleaned...please resume websending..");

        $query = 'delete from jos_portal_property_email_log';
        WFactory::getSqlService()->update($query);
        $query = 'delete from jos_portal_requestinfo';
        WFactory::getSqlService()->update($query);
        $query = 'delete from jos_portal_senttoweb_log';
        WFactory::getSqlService()->update($query);

        $query = 'delete from jos_portal_users_profile';
        WFactory::getSqlService()->update($query);

        WFactory::getLogger()->debug("Others cleaned...please resume websending..");


        WFactory::getLogger()->debug("Creating folders and stuffs...");

        $path = JPATH_ROOT . "/tmp/";
        if (!file_exists($path)) {
            mkdir($path);
            if (!file_exists($path)) {
                WFactory::getLogger()->warn("Failed to created $path");
            } else
                WFactory::getLogger()->info("Temp file : $path created");
        } else
            WFactory::getLogger()->info("Temp file : $path exists");


        $path = JPATH_ROOT . "/cache/";
        if (!file_exists($path)) {
            mkdir($path);
            if (!file_exists($path)) {
                WFactory::getLogger()->warn("Failed to created $path");
            } else
                WFactory::getLogger()->info("Cache file : $path created");
        } else
            WFactory::getLogger()->info("Cache file : $path exists");

        $path = JPATH_ROOT . "/logs/";
        if (!file_exists($path)) {
            mkdir($path);
            if (!file_exists($path)) {
                WFactory::getLogger()->warn("Failed to created $path");
            } else
                WFactory::getLogger()->info("Local Logs file : $path created");
        } else
            WFactory::getLogger()->info("Local Logs file : $path exists");


        $path = JPATH_ROOT . "/robots.txt";
        if (!file_exists($path)) {
            $content = file_get_contents(JPATH_ROOT . "/robots.txt.dist");
            file_put_contents($path, $content);
            if (!file_exists($path)) {
                WFactory::getLogger()->warn("Failed to created $path");
            } else
                WFactory::getLogger()->info("Robot : $path created");
        } else
            WFactory::getLogger()->info("Robot : $path exists");


        WFactory::getLogger()->info("------- Applying permissions ------------");

        $commands = array(
            "sudo chown -R www-data:www-data *",
            "sudo find " . JPATH_ROOT . " -type f -exec chmod 644 {} \\;",
            "sudo find " . JPATH_ROOT . " -type d -exec chmod 755 {} \\;"

        );

        foreach ($commands as $c) {
            WFactory::getLogger()->debug("Executing : $c");
            $result = shell_exec($c);
            WFactory::getLogger()->debug("Result : $c  => $result");
        }

        WFactory::getLogger()->info("------- == DONE == ------------");


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


JApplicationCli::getInstance('Deploynewsite')->execute();
