<?php
/**
 * Created by JetBrains PhpStorm.
 * User: khan
 * Date: 7/2/13
 * Time: 12:12 PM
 * To change this template use File | Settings | File Templates.
 */

$jpath_base = (dirname(__FILE__)) . "/../../";
$jpath_base = realpath($jpath_base);
require_once "$jpath_base/phpunitbootstrap.php";
//require_once 'PHPUnit/Framework/TestCase.php';

//require_once 'PHPUnit/Autoload.php';


class ImportThailandDatabase extends PHPUnit_Framework_TestCase
{


    /**
     * @var PropertyPortalLibraryCore
     */
    var $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }


    public function testImport()
    {


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

        $query = "delete  from  `{$regionDb->getTableName()}`";
        $clearResult = WFactory::getSqlService()->update($query);
        $query = "DELETE FROM  `{$postalDb->getTableName()}`";
        $clearResult = WFactory::getSqlService()->update($query);
        $query = "DELETE FROM  `{$cityTownDb->getTableName()}`";
        $clearResult = WFactory::getSqlService()->update($query);


        $query = "SELECT postal_codes_regions.*
                  FROM saga_remax_th_2_5_101104.postal_codes_regions postal_codes_regions";

        $regions = WFactory::getSqlService()->select($query);

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



            if (!array_key_exists($regionCode, $insertedRegions)) {
                foreach ($regions as $r) {
                    if ($r['code'] == $regionCode)
                    {
                        $this->insertRegion($regionDb, $r);
                        break;
                    }
                }


                $insertedRegions[$regionCode] = $regionCode;
            }


            if (!array_key_exists($townCode, $insertedTowns)) {

                foreach ($towns as $t) {
                    if ($t['code'] == $townCode)
                    {
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
