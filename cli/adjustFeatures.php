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
class Adjustfeatures extends JApplicationCli
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

        $featureFile = JPATH_BASE . DS . "cli" . DS . "features.txt";

        WFactory::getLogger()->debug("Reading feature from : $featureFile");
        $text = shell_exec("cat $featureFile");
        $features = explode("\n", $text);

        $command = "delete from jos_portal_features";
        $result = WFactory::getSqlService()->delete($command);

        $index = 0;
        foreach ($features as $f) {
            $this->simpleEcho("Got : $f");
            if ($index > 0) {

                $f = explode(",", $f);

                if (!WFactory::getHelper()->isNullOrEmptyString($f[0])) {
                    /**
                     * @var $featuresTable PortalPortalFeaturesSql
                     */
                    $featuresTable = WFactory::getSqlService()->getDbClass(__PORTAL_PORTAL_FEATURES_SQL);
                    $featuresTable->__id = trim($f[0]);
                    $featuresTable->__name = trim($f[1]);

                    $this->simpleEcho("Inserting --> {$featuresTable->__id}");
                    WFactory::getSqlService()->insert($featuresTable);
                }

            }
            $index++;

        }

    }

    function simpleEcho($msg)
    {
        echo "$msg\r\n";
    }


}


JApplicationCli::getInstance('Adjustfeatures')->execute();
