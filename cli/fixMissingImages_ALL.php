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
        /**
         * ---------------------------------------------------
         * ------------------- setup -------------------------
         * ---------------------------------------------------
         * @var iFileManager
         */

        $commonConfig = WFactory::getConfig()->getWebportalConfigurationArray();
        $fileManager = WFactory::getFileManager();
        $fileManager = $fileManager->getFileManager($commonConfig["filesystem"]);
        $websending = new WebsendingBase();


        /**
         * ---------------------------------------------------
         * ------------------- execute -----------------------
         * ---------------------------------------------------
         */

        $query = "select * from jos_portal_properties where is_deleted = 0 and id=6968";

        $result = WFactory::getSqlService()->select($query);


        foreach ($result as $r) {
            /**
             * @var $propertiesDbClass PortalPortalPropertiesSql
             */
            $propertiesDbClass = WFactory::getServices()->getSqlService()->getDbClass(__PORTAL_PORTAL_PROPERTIES_SQL);
            $propertiesDbClass->bind($r);

            $images = "SELECT jos_portal_property_images.*
                              FROM `remax-th2`.jos_portal_property_images jos_portal_property_images
                             WHERE (jos_portal_property_images.property_id = {$propertiesDbClass->__id}) order by id asc";
            $images = WFactory::getSqlService()->select($images);

            /**
             * ---------------------------------------------------
             * ---------build missing images array for insert-----
             * ---------------------------------------------------
             */

            $missingImages = array();
            $missingImages['DefaultImageSequenceNumber'] = 1;
            $missingImages['Image'] = array();

            $index = 1;
            foreach ($images as $image) {
                if (!WFactory::getHelper()->isNullOrEmptyString($image['origin_url']) &&  //origin exists
                    WFactory::getHelper()->isNullOrEmptyString($image['server_url'])      //but server url does not exist!
                ) {

                    WFactory::getLogger()->debug("Found property {$propertiesDbClass->__id} have no image at id {$image['id']} , but server url exists!");
                    $parsableImage = array(
                        'SequenceNumber' => $index,
                        'FileName' => $image['origin_url'],
                        'DescriptiveName' => '',
                        'Alt'=>''
                    );

                    $missingImages['Image'][] = $parsableImage;

                    $index++;
                }
            }


            /**
             * ---------------------------------------------------
             * --------- now pass it to xml handler --------------
             * ---------------------------------------------------
             */

            $result = $websending->handleImage($missingImages, PROPERTY,
                $this->companyId, "",
                $propertiesDbClass->__office_id, "",
                $propertiesDbClass->__sale_id,"",
                $propertiesDbClass->__id,
                $propertiesDbClass->__address
            );

        }
    }


}

JApplicationCli::getInstance('Fixmissingimage')->execute();
