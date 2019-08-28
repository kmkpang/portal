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
class Importv2compatibilitytable extends JApplicationCli
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

        //C:\xampp\htdocs\softverk-webportal\cli\v2files\fasteigsnae
        $filePath = 'C:\xampp\htdocs\softverk-webportal\cli\v2files\fasteigsnae';
        $v2Data = file_get_contents($filePath);

        $v2Data = explode("\n", $v2Data);

        foreach ($v2Data as &$v2) {
            $v2 = trim(str_replace("\r", "", $v2));
            $v2 = explode(",", $v2);
        }


        for ($i = 1; $i < count($v2Data); $i++) {


            //find the id
            $query = "SELECT id FROM `mapportal_fasteignsnae_v2`.`jos_portal_properties` WHERE `unique_id` like '{$v2Data[$i][1]}'";
            $id = WFactory::getSqlService()->select($query);
            $id = $id[0]['id'];


            /**
             * @var $compatibility PortalPortalPropertiesV2CompatibilitySql
             */
            $compatibility = WFactory::getSqlService()->getDbClass(__PORTAL_PORTAL_PROPERTIES_V2_COMPATIBILITY_SQL);
            $compatibility->__v2_unique_id = $v2Data[$i][1];
            $compatibility->__v2_id = $id;
            $compatibility->__id = 0;
            $compatibility->__reg_id = $v2Data[$i][0];
            WFactory::getSqlService()->insert($compatibility);

        }

        $dbName = "saga_remax_th_2_5_101104";




    }




}


JApplicationCli::getInstance('Importv2compatibilitytable')->execute();
