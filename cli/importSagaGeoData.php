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
class Importsagageodata extends JApplicationCli
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
        $importRegion = true;

        $dbName = "saga_remax_th_2_5_101104";

        /**
         * @var $regionDb PortalGeographyRegionsSql
         */
        $regionDb = WFactory::getSqlService()->getDbClass(__PORTAL_GEOGRAPHY_REGIONS_SQL);
        /**
         * @var $postalDb PortalGeographyPostalCodesSql
         */
        $postalDb = WFactory::getSqlService()->getDbClass(__PORTAL_GEOGRAPHY_POSTAL_CODES_SQL);
        /**
         * @var $cityTownDb PortalGeographyTownsSql
         */
        $cityTownDb = WFactory::getSqlService()->getDbClass(__PORTAL_GEOGRAPHY_TOWNS_SQL);

        if ($importRegion) {
            $query = "DELETE FROM  `{$regionDb->getTableName()}`";
            $clearResult = WFactory::getSqlService()->update($query);
        }
        $query = "DELETE FROM  `{$postalDb->getTableName()}`";
        $clearResult = WFactory::getSqlService()->update($query);
        $query = "DELETE FROM  `{$cityTownDb->getTableName()}`";
        $clearResult = WFactory::getSqlService()->update($query);


        if ($importRegion) {
            $query = "SELECT postal_codes_regions.*
                  FROM saga_remax_th_2_5_101104.postal_codes_regions postal_codes_regions";

            $regions = WFactory::getSqlService()->select($query);
        }

        $query = "SELECT postal_codes.*
                  FROM saga_remax_th_2_5_101104.postal_codes postal_codes";

        $postalCodes = WFactory::getSqlService()->select($query);

        $query = "SELECT postal_codes_towns.*
                  FROM saga_remax_th_2_5_101104.postal_codes_towns postal_codes_towns";

        $towns = WFactory::getSqlService()->select($query);

        $insertedTowns = array();
        $insertedRegions = array();


        foreach ($postalCodes as $pCode) {

            $postalCodeId = $this->insertPostalCode($postalDb, $pCode);

            $townCode = $pCode['postalcode_town_id'];
            $regionCode = $pCode['postalcode_region_id'];


            if ($importRegion) {
                if (!array_key_exists($regionCode, $insertedRegions)) {
                    foreach ($regions as $r) {
                        if ($r['code'] == $regionCode) {
                            $this->insertRegion($regionDb, $r);
                            break;
                        }
                    }


                    $insertedRegions[$regionCode] = $regionCode;
                }
            }


            if (!array_key_exists($townCode, $insertedTowns)) {

                foreach ($towns as $t) {
                    if ($t['code'] == $townCode) {
                        $this->insertTown($cityTownDb, $t, $regionCode);
                        break;
                    }
                }


                $insertedTowns[$townCode] = $townCode;
            }


        }

    }

    /**
     * @param $postalCodeDb
     * @param $r
     * @return bool|mixed
     */
    function insertPostalCode($postalCodeDb, $r)
    {
        $postalCodeDb->__id = $r['id'];
        $postalCodeDb->__name_en = trim($r['code']) . ' - ' . trim($r['en_name']);
        $postalCodeDb->__name_th = trim($r['code']) . ' - ' . trim($r['name']);
        $postalCodeDb->__parent_id = $r['postalcode_town_id'];

        return WFactory::getSqlService()->insert($postalCodeDb);
    }

    /**
     * @param $regionDb PortalGeographyRegionsSql
     * @param $r
     * @return bool|mixed
     */
    function insertRegion($regionDb, $r)
    {
        $regionDb->__id = $r['code'];
        $regionDb->__name_en = $r['en_regionName'];
        $regionDb->__name_th = $r['regionName'];
        $regionDb->__parent_id = '1';

        return WFactory::getSqlService()->insert($regionDb);
    }

    /**
     * @param $townDb PortalGeographyTownsSql
     * @param $r
     * @param $parentId
     * @return bool|mixed
     */
    function insertTown($townDb, $r, $parentId)
    {
        $townDb->__id = $r['code'];
        $townDb->__name_en = $r['en_townName'];
        $townDb->__name_th = $r['townName'];
        $townDb->__parent_id = $parentId;

        return WFactory::getSqlService()->insert($townDb);
    }


}


JApplicationCli::getInstance('Importsagageodata')->execute();
