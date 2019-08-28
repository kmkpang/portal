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
class UpdateUserTrigger extends JApplicationCli
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

        $this->sleepWait(5, "This will update your portal with Users and Trigger ! press ctrl+c to stop...");

        WFactory::getLogger()->info("inserting columns and trigger in jos_portal_users_profile and jos_users");
        $query = 'ALTER TABLE `jos_portal_users_profile` ADD `name` VARCHAR(400) NOT NULL AFTER `id`';
        WFactory::getSqlService()->update($query);
        WFactory::getLogger()->debug("Successful insert name in jos_portal_users_profile..");
        $query = 'ALTER TABLE `jos_portal_users_profile` ADD `username` VARCHAR(150) NOT NULL AFTER `name`';
        WFactory::getSqlService()->update($query);
        WFactory::getLogger()->debug("Successful insert username in jos_portal_users_profile..");
        $query = 'ALTER TABLE `jos_portal_users_profile` ADD `email` VARCHAR(100) NOT NULL AFTER `username`';
        WFactory::getSqlService()->update($query);
        WFactory::getLogger()->debug("Successful insert email in jos_portal_users_profile..");

        $query = 'CREATE TRIGGER `insert_users_trigger` AFTER INSERT ON `jos_users` FOR EACH ROW INSERT INTO jos_portal_users_profile (name,username,email,joomla_user_id,created,updated)
VALUES (NEW.name,NEW.username,NEW.email,NEW.id,CURRENT_DATE,CURRENT_TIMESTAMP)';
        WFactory::getSqlService()->update($query);
        WFactory::getLogger()->debug("Successful insert Users Trigger..");

        $query = 'CREATE TRIGGER `update_users_trigger` AFTER UPDATE ON `jos_users` FOR EACH ROW UPDATE jos_portal_users_profile
                    SET name=NEW.name,
                    username=NEW.username,
                    email=NEW.email,
                    updated=CURRENT_TIMESTAMP
                    WHERE joomla_user_id=NEW.id';
        WFactory::getSqlService()->update($query);
        WFactory::getLogger()->debug("Successful updated Users Trigger..");
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
            echo ".";
        }
        echo "\n";
    }
}


JApplicationCli::getInstance('UpdateUserTrigger')->execute();
