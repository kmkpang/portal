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
class Fixmissingimage extends JApplicationCli
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

        $query = "select * from jos_portal_properties where is_deleted = 0";

        $result = WFactory::getSqlService()->select($query);

        $commonConfig = WFactory::getConfig()->getWebportalConfigurationArray();
        $fileManager = WFactory::getFileManager();
        /**
         * @var iFileManager
         */
        $fileManager = $fileManager->getFileManager($commonConfig["filesystem"]);


        foreach ($result as $r) {
            /**
             * @var $propertiesDbClass PortalPortalPropertiesSql
             */
            $propertiesDbClass = WFactory::getServices()->getSqlService()->getDbClass(__PORTAL_PORTAL_PROPERTIES_SQL);
            $propertiesDbClass->bind($r);

            if (WFactory::getHelper()->isNullOrEmptyString($propertiesDbClass->__list_page_thumb_path)) {

                $images = "SELECT jos_portal_property_images.*
                              FROM `remax-th2`.jos_portal_property_images jos_portal_property_images
                             WHERE (jos_portal_property_images.property_id = {$propertiesDbClass->__id}) order by id asc";
                $images = WFactory::getSqlService()->select($images);

                $firstImage = $images[0];
                $sourceFilePath = $firstImage['server_url'];

                $propertiesDbClass->__initial_picture_path = $sourceFilePath;

                $thumbTypes = array("list", "map");
                for ($i = 0; $i < count($thumbTypes); $i++) {
                    $thumb = $thumbTypes[$i];

                    $thumbWidth = $commonConfig["websending"]["{$thumb}page_thumb_size"]["width"];
                    $thumbHeight = $commonConfig["websending"]["{$thumb}page_thumb_size"]["height"];

                    if ($thumbWidth == null || $thumbHeight == null) {
                        $thumbWidth = 221;
                        $thumbHeight = 147;

                        WFactory::getLogger()->warn("thumb width and height not defined, using default!");
                    }

                    $localThumbPath = $commonConfig["tempFolderPath"] . DS . uniqid() . ".jpg";

                    WebsendingBase::createThumbs($sourceFilePath, $localThumbPath, $thumbWidth, $thumbHeight);

                    //buildPropertyImagePath($companyId, $companyName, $officeId, $officeName, $agentId, $agentName, $propertyId, $propertyAddress)

                    $propertyImagePath = WebsendingBase::buildPropertyImagePath(
                        "1",
                        "",
                        $propertiesDbClass->__office_id,
                        "",
                        $propertiesDbClass->__sale_id,
                        "",
                        $propertiesDbClass->__id,
                        ""
                    );
                    $destinationPath = $propertyImagePath . "/image/{$propertiesDbClass->__id}_1_{$thumb}thumb." . pathinfo($localThumbPath, PATHINFO_EXTENSION);
                    $webPathURL = "";
                    $tmpResult = $fileManager->putFile($localThumbPath, $destinationPath, $webPathURL);

                    if ($thumb == "list")
                        $propertiesDbClass->__list_page_thumb_path = $webPathURL;
                    if ($thumb == "map")
                        $propertiesDbClass->__map_page_thumb_path = $webPathURL;

                }

                WFactory::getSqlService()->update($propertiesDbClass);


            }

        }
    }


}

JApplicationCli::getInstance('Fixmissingimage')->execute();
