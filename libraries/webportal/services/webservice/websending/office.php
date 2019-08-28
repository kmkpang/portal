<?php
/**
 * Created by JetBrains PhpStorm.
 * User: khan
 * Date: 7/2/13
 * Time: 10:46 PM
 * To change this template use File | Settings | File Templates.
 */
/*///var/www/softverk-webportal/libraries/webportal/services/office/propertyPortalOffice.php
require_once JPATH_ROOT . DS . "libraries" . DS . "webportal" . DS . "services" . DS . "office" . DS . "propertyPortalOffice.php";
///var/www/eign_v2/libraries/propertyportal/dbclasses/class.offices.php
require_once JPATH_ROOT . DS . "libraries" . DS . "webportal" . DS . "services" . DS . "dbclasses" . DS . "class.offices.php";
require_once JPATH_ROOT . DS . "libraries" . DS . "webportal" . DS . "services" . DS . "dbclasses" . DS . "class.geographyRegions.php";
require_once JPATH_ROOT . DS . "libraries" . DS . "webportal" . DS . "services" . DS . "dbclasses" . DS . "class.geographyTowns.php";
require_once JPATH_ROOT . DS . "libraries" . DS . "webportal" . DS . "services" . DS . "dbclasses" . DS . "class.geographyPostalCodes.php";
require_once JPATH_ROOT . DS . "libraries" . DS . "webportal" . DS . "services" . DS . "dbclasses" . DS . "class.marketingInfo.php";

///var/www/eign_v2/libraries/propertyportal/office/propertyPortalOffice.php
require_once JPATH_ROOT . DS . "libraries" . DS . "webportal" . DS . "services" . DS . "office" . DS . "propertyPortalOffice.php";
require_once JPATH_ROOT . DS . "libraries" . DS . "webportal" . DS . "services" . DS . "libraryCore.php";
*/
////var/www/softverk-webportal/libraries/webportal/services/webservice/websending/websendingBase.php
require_once JPATH_ROOT . DS . "libraries" . DS . "webportal" . DS . "services" . DS . "webservice" . DS . "websending" . DS . "websendingBase.php";


class OfficeSentToWeb extends WebsendingBase
{


    /**
     * @var string
     */
    var $xml;
    /**
     * @var PortalPortalOfficesSql
     */
    var $dbClass;
    /**
     * @var PortalPortalMarketingInfoSql
     */
    var $marketingInfo;
    /**
     * @var PortalGeographyRegionsSql
     */
    var $region;
    /**
     * @var PortalGeographyTownsSql
     */
    var $towns;
    /**
     * @var  PortalGeographyPostalCodesSql
     */
    var $postalCodes;

    /**
     * @var PortalPortalPropertyAddressesSql
     */
    var $address;


    var $companyId;
    var $companyName;

    // used only when this is a unittest
    var $uniqueId;
    var $publicKey;

    public function __construct($xml)
    {

        $this->dbClass = WFactory::getServices()->getSqlService()->getDbClass(__PORTAL_PORTAL_OFFICES_SQL);
        $this->marketingInfo = WFactory::getServices()->getSqlService()->getDbClass(__PORTAL_PORTAL_MARKETING_INFO_SQL);
        //$this->marketingInfo->__
        $this->region = WFactory::getServices()->getSqlService()->getDbClass(__PORTAL_GEOGRAPHY_REGIONS_SQL);
        $this->towns = WFactory::getServices()->getSqlService()->getDbClass(__PORTAL_GEOGRAPHY_TOWNS_SQL);
        $this->postalCodes = WFactory::getServices()->getSqlService()->getDbClass(__PORTAL_GEOGRAPHY_POSTAL_CODES_SQL);
        $this->address = WFactory::getServices()->getSqlService()->getDbClass(__PORTAL_PORTAL_PROPERTY_ADDRESSES_SQL);

        $this->loadXML($xml);

        $websendingConfig = parent::getWebsendingConfig();
        $this->companyId = $websendingConfig["companyId"];
        $this->companyName = $websendingConfig["companyName"];
    }

    /**
     *
     * @param $sent2webDbClass PortalPortalSenttowebLogSql
     * @return bool|string
     */
    function create($sent2webDbClass)
    {
        $outgoingXmlId = 0;
        if (!$this->validate()) {
            $xmlReply = parent::response(false, $this->dbClass->__id, $sent2webDbClass, $outgoingXmlId, "Office", "", "", $this->xml, "CREATE_FAILED");
            $sent2webDbClass->__realted_senttoweb_id = $outgoingXmlId;
            WFactory::getSqlService()->update($sent2webDbClass);
            return $xmlReply;
            // return parent::response(false);
        }

        $geodata = $this->dbClass->xmlAddress;
        if (!parent::validatePopulateAndUpdateGeoData($geodata, $this->dbClass)) {
            WFactory::getLogger()->warn("Invalid geodata for office create
            (
                PostalCodeID : {$geodata["PostalCodeID"]},
                TownID       : {$geodata["TownID"]},
                RegionID     : {$geodata["RegionID"]}
            )");

            if (__NEWCOUNTRYGEODATAIMPLMENETEDBYSAGAYET === true) {
                $msg = "Incorrected geodata relation";
                $xmlReply = parent::response(false, $this->dbClass->__id, $sent2webDbClass, $outgoingXmlId, "Office", "01210", $msg, $this->xml, "CREATE_FAILED");
                $sent2webDbClass->__realted_senttoweb_id = $outgoingXmlId;
                WFactory::getSqlService()->update($sent2webDbClass);
                return $xmlReply;
                //return parent::response(false, "01210", "Incorrected geodata relation", $this->xml);
            } else {
                WFactory::getLogger()->warn("ignoring geodata error: __NEWCOUNTRYGEODATAIMPLMENETEDBYSAGAYET : false");
            }
        }

        $this->dbClass->__date_entered = WFactory::getServices()->getSqlService()->getMySqlDateTime();
        $this->dbClass->__company_id = $this->companyId;

        //now do an insert
        $officeId = WFactory::getServices()->getSqlService()->insert($this->dbClass);
        if (!is_numeric($officeId)) {
            $msg = "Office insert failed";
            $xmlReply = parent::response(false, $this->dbClass->__id, $sent2webDbClass, $outgoingXmlId, "Office", "01210", $msg, $this->xml, "CREATE_FAILED");
            $sent2webDbClass->__realted_senttoweb_id = $outgoingXmlId;
            WFactory::getSqlService()->update($sent2webDbClass);
            return $xmlReply;
            //return parent::response(false, "01210", "Office insert failed", $this->xml);
        }
        $this->dbClass->__id = $officeId;

        //handle the address now
        $this->address->__type_id = WFactory::getServices()->getSqlService()->getSqlServiceClass(__PROPPERTY_PORTAL_ADDRESS)->getAddressTypeIdFromAddressType('Office Address');
        $this->address->__region_id = $geodata['RegionID'];
        $this->address->__town_id = $geodata['TownID'];
        $this->address->__postal_code_id = $geodata['PostalCodeID'];
        $this->address->__address = WFactory::getHelper()->isNullOrEmptyString($geodata['HouseAddress']) ? "" : $geodata['HouseAddress'];
        $this->address->__street = WFactory::getHelper()->isNullOrEmptyString($geodata['Street']) ? "" : $geodata['Street'];
        $this->address->__latitude = $geodata['Latitude'];
        $this->address->__longitude = $geodata['Longitude'];


        $addressId = WFactory::getServices()->getSqlService()->insert($this->address);
        $this->dbClass->__address_id = $addressId;

        //handle marketing info now
        $marketingData = $this->dbClass->xmlInformation['MarketingInfo'];
        $this->marketingInfo->__marketing_info_type_id = WFactory::getServices()->getSqlService()->getSqlServiceClass(__PROPPERTY_PORTAL_MARKETINGINFO)->getMarketingInfoTypeIdFromMarketingInfoType($this->marketingInfo->__marketing_info_type);
        $this->marketingInfo->__reference_id = $this->dbClass->__id;

        $marketingInfoCount = count($marketingData['LanguageID']);
        if ($marketingInfoCount == 0)
            $marketingInfoCount = 1; // want to run atleast once..even with wrong data!so it can be updated later
        for ($i = 0; $i < $marketingInfoCount; $i++) {
            $this->marketingInfo->__slogan = count($marketingData['Slogan'][$i]) == 1 ? $marketingData['Slogan'] : $marketingData['Slogan'][$i];
            $this->marketingInfo->__closer = count($marketingData['Closer'][$i]) == 1 ? $marketingData['Closer'] : $marketingData['Closer'][$i];
            $this->marketingInfo->__bullet_point1 = count($marketingData['BulletPoint1'][$i]) == 1 ? $marketingData['BulletPoint1'] : $marketingData['BulletPoint1'][$i];
            $this->marketingInfo->__bullet_point2 = count($marketingData['BulletPoint2'][$i]) == 1 ? $marketingData['BulletPoint2'] : $marketingData['BulletPoint2'][$i];
            $this->marketingInfo->__bullet_point3 = count($marketingData['BulletPoint3'][$i]) == 1 ? $marketingData['BulletPoint3'] : $marketingData['BulletPoint3'][$i];

            WFactory::getServices()->getSqlService()->insert($this->marketingInfo);
            $this->marketingInfo->__id = NULL; //unset for next round
        }


        //now handle images
        $logoPath = parent::handleOfficeLogo($this->dbClass->__logo, $this->companyId, $this->companyName, $this->dbClass->__id, $this->dbClass->__office_name);
        if (empty($logoPath)) {
            WFactory::getLogger()->warn("Inserting logo failed for office {$this->dbClass->__id} - {$this->dbClass->__office_name}");
        } else
            $this->dbClass->__logo = $logoPath;

        $result = parent::handleImage($this->dbClass->xmlImages, OFFICE, $this->companyId, $this->companyName, $this->dbClass->__id, $this->dbClass->__office_name);
        if (!$result) {
            WFactory::getLogger()->warn("One or more image insert had failed for Office");
        }


        $this->dbClass->__unique_id = parent::generateUniqueId(OFFICE, $this->dbClass->__id, $this->companyId);
        $this->dbClass->__public_key = parent::generatePublicKey($this->dbClass->__unique_id);

        $imageData = $this->dbClass->xmlImages;
        $this->dbClass->__image_file_path = $imageData["DefaultImagePath"];
        $this->dbClass->__image_file_name = pathinfo($imageData["DefaultImagePath"], PATHINFO_FILENAME) . "." . pathinfo($imageData["DefaultImagePath"], PATHINFO_EXTENSION);
        $this->dbClass->__date_entered = WFactory::getServices()->getSqlService()->getMySqlDateTime();
        $this->dbClass->__url_to_private_page = $this->dbClass->xmlURLToPrivatePage;
        $this->dbClass->__show_on_web = $this->dbClass->__show_on_web === "false" ? 0 : 1;

        $result = WFactory::getServices()->getSqlService()->update($this->dbClass);
        $result &= WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_PROPERTIES)->updatePropertyTableWithOfficeInformation($this->dbClass);

        $sent2webDbClass->__associated_id = $this->dbClass->__id;
        $outgoingXmlId = 0;

        /**
         * If this is NOT the first office, it will simply return!
         */
        WFactory::getServices()->getSqlService()->getSqlServiceClass(__PROPPERTY_PORTAL_OFFICE)->deployFirstOffice();

        if ($result == true) {
            $msg = '<UniqueID>' . $this->dbClass->__unique_id . '</UniqueID><PublicKey>' . $this->dbClass->__public_key . '</PublicKey>';
            $xmlReply = parent::response(true, $this->dbClass->__id, $sent2webDbClass, $outgoingXmlId, "Office", "02311", $msg, "", "CREATE");
            $sent2webDbClass->__realted_senttoweb_id = $outgoingXmlId;
            WFactory::getSqlService()->update($sent2webDbClass);
            return $xmlReply;
        } else {
            $msg = "Office insert failed";
            $xmlReply = parent::response(false, $this->dbClass->__id, $sent2webDbClass, $outgoingXmlId, "Office", "02310", $msg, $this->xml, "CREATE_FAILED");
            $sent2webDbClass->__realted_senttoweb_id = $outgoingXmlId;
            WFactory::getSqlService()->update($sent2webDbClass);
            return $xmlReply;
        }

        return false;
    }

    /**
     * @param $sent2webDbClass PortalPortalSenttowebLogSql
     * @return bool|string
     */
    function update($sent2webDbClass)
    {
        $data = &$this->dbClass;
        $originalOfficeFromDatabase = $this->validate();
        $outgoingXmlId = 0;

        if (!$originalOfficeFromDatabase) {
            $msg = "Office uniqe id or publickey does not exist";
            $xmlReply = parent::response(false, $this->dbClass->__id, $sent2webDbClass, $outgoingXmlId, "Office", "01220", $msg, $this->xml, "UPDATE_FAILED");
            $sent2webDbClass->__realted_senttoweb_id = $outgoingXmlId;
            WFactory::getSqlService()->update($sent2webDbClass);
            return $xmlReply;

            //return parent::response(false, "01220", "Office uniqe id or publickey does not exist", $this->xml);
        }

        $this->dbClass->__id = $originalOfficeFromDatabase["id"];
        $geodata = $this->dbClass->xmlAddress;

        if (!parent::validatePopulateAndUpdateGeoData($geodata, $this->dbClass)) {
            WFactory::getLogger()->warn("Invalid geodata for office create
            (
                PostalCodeID : {$geodata["PostalCodeID"]},
                TownID       : {$geodata["TownID"]},
                RegionID     : {$geodata["RegionID"]}
            )");

            if (__NEWCOUNTRYGEODATAIMPLMENETEDBYSAGAYET === true) {
                $msg = "Incorrected geodata relation";
                $xmlReply = parent::response(false, $this->dbClass->__id, $sent2webDbClass, $outgoingXmlId, "Office", "01220", $msg, $this->xml, "UPDATE_FAILED");
                $sent2webDbClass->__realted_senttoweb_id = $outgoingXmlId;
                WFactory::getSqlService()->update($sent2webDbClass);
                return $xmlReply;
                //return parent::response(false, "01220", "Incorrected geodata relation", $this->xml);
            } else {
                WFactory::getLogger()->warn("ignoring geodata error: __NEWCOUNTRYGEODATAIMPLMENETEDBYSAGAYET : false");
            }
        }


        //handle the address now
        $this->address->__type_id = WFactory::getServices()->getSqlService()->getSqlServiceClass(__PROPPERTY_PORTAL_ADDRESS)->getAddressTypeIdFromAddressType('Office Address');

        $addressId = $originalOfficeFromDatabase['address_id'];

        $this->address->__id = $addressId;
        $addressFromDb = $this->address->loadDataFromDatabase();

        $this->address = $addressFromDb->bind($this->address, $addressFromDb, true);

        $this->address->__region_id = $geodata['RegionID'];
        $this->address->__town_id = $geodata['TownID'];
        $this->address->__postal_code_id = $geodata['PostalCodeID'];

        $this->address->__address = WFactory::getHelper()->isNullOrEmptyString($geodata['HouseAddress']) ? "" : trim($geodata['HouseAddress']);
        $this->address->__street = WFactory::getHelper()->isNullOrEmptyString($geodata['Street']) ? "" : trim($geodata['Street']);

        $this->address->__latitude = $geodata['Latitude'] == "0" ? '0.0000' : $geodata['Latitude'];
        $this->address->__longitude = $geodata['Longitude'] == "0" ? '0.0000' : $geodata['Longitude'];

        if ($this->address->__id !== null)
            WFactory::getServices()->getSqlService()->update($this->address);
        else
            WFactory::getServices()->getSqlService()->insert($this->address);


        //update marketing info
        $marketingData = $this->dbClass->xmlInformation['MarketingInfo'];
        $this->marketingInfo->__reference_id = $this->dbClass->__id;
        $marketingInfoFromDatabase = parent::loadMarketingInfo("OFFICE", $this->dbClass->__id);
        $this->marketingInfo->__id = $marketingInfoFromDatabase[0]["id"];


        $marketingInfoCount = count($marketingData['LanguageID']);
//        if ($marketingInfoCount == 0)
//            $marketingInfoCount = 1; // want to run atleast once..even with wrong data!so it can be updated later
//        for ($i = 0; $i < $marketingInfoCount; $i++) {
        $this->marketingInfo->__marketing_info_type_id = WFactory::getServices()->getSqlService()->getSqlServiceClass(__PROPPERTY_PORTAL_MARKETINGINFO)->getMarketingInfoTypeIdFromMarketingInfoType($this->marketingInfo->__marketing_info_type);
        $this->marketingInfo->__slogan = !WFactory::getHelper()->isNullOrEmptyString($marketingData['Slogan']) ? $marketingData['Slogan'] : "";
        $this->marketingInfo->__closer = !WFactory::getHelper()->isNullOrEmptyString($marketingData['Closer']) ? $marketingData['Closer'] : "";
        $this->marketingInfo->__bullet_point1 = !WFactory::getHelper()->isNullOrEmptyString($marketingData['BulletPoint1']) ? $marketingData['BulletPoint1'] : "";
        $this->marketingInfo->__bullet_point2 = !WFactory::getHelper()->isNullOrEmptyString($marketingData['BulletPoint2']) ? $marketingData['BulletPoint2'] : "";
        $this->marketingInfo->__bullet_point3 = !WFactory::getHelper()->isNullOrEmptyString($marketingData['BulletPoint3']) ? $marketingData['BulletPoint3'] : "";
        if ($this->marketingInfo->__id !== null) {
            $result = WFactory::getServices()->getSqlService()->update($this->marketingInfo);
        } else {
            $result = WFactory::getServices()->getSqlService()->insert($this->marketingInfo);
        }
        if (!$result) {
            WFactory::getLogger()->warn("Failed to update Marketing info of Office");
        }
        //}


        //now do an update
        $officeUpdateResult = WFactory::getServices()->getSqlService()->update($this->dbClass);
        if (!$officeUpdateResult) {
            $msg = "Update Office Error";
            $xmlReply = parent::response(false, $this->dbClass->__id, $sent2webDbClass, $outgoingXmlId, "Office", "01220", $msg, $this->xml, "UPDATE_FAILED");
            $sent2webDbClass->__realted_senttoweb_id = $outgoingXmlId;
            WFactory::getSqlService()->update($sent2webDbClass);
            return $xmlReply;
            //return parent::response(false, "01220", "Update Office Error", $this->xml);
        }

        //TODO: if office name is changed, this will create a new folder ( virtual folder ) on amazon.
        //TODO: make sure to delete older files and reupload all..argh...
        $logoPath = parent::handleOfficeLogo($this->dbClass->__logo, $this->companyId, $this->companyName, $this->dbClass->__id, $this->dbClass->__office_name);
        if (empty($logoPath)) {
            WFactory::getLogger()->warn("Inserting logo failed for office {$this->dbClass->__id} - {$this->dbClass->__office_name}");
        } else
            $this->dbClass->__logo = $logoPath;

        $handleImageResult = parent::handleImage($this->dbClass->xmlImages, OFFICE, $this->companyId, $this->companyName, $this->dbClass->__id, $this->dbClass->__office_name);

        if (!$handleImageResult) {
            WFactory::getLogger()->warn("One or more image insert had failed for Office");
        }

        $imageData = $this->dbClass->xmlImages;
        $this->dbClass->__image_file_path = $imageData["DefaultImagePath"];
        $this->dbClass->__image_file_name = pathinfo($imageData["DefaultImagePath"], PATHINFO_FILENAME) . "." . pathinfo($imageData["DefaultImagePath"], PATHINFO_EXTENSION);
        $this->dbClass->__date_modified = WFactory::getServices()->getSqlService()->getMySqlDateTime();
        $this->dbClass->__url_to_private_page = $this->dbClass->xmlURLToPrivatePage;
        $this->dbClass->__show_on_web = $this->dbClass->__show_on_web === "false" ? 0 : 1;


        $result = WFactory::getServices()->getSqlService()->update($this->dbClass);
        $result = $result && WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_PROPERTY)->updatePropertyOfficeInformation($this->dbClass);

        if (!$result) {
            WFactory::getLogger()->warn("Failed to update Office");
        }

        $sent2webDbClass->__associated_id = $this->dbClass->__id;

        /**
         * If this is NOT the first office, it will simply return!
         */
        WFactory::getServices()->getSqlService()->getSqlServiceClass(__PROPPERTY_PORTAL_OFFICE)->deployFirstOffice();

        $outgoingXmlId = 0;
        if ($result == true) {
            $msg = '<UniqueID>' . $originalOfficeFromDatabase['unique_id'] . '</UniqueID>';
            $xmlReply = parent::response(true, $this->dbClass->__id, $sent2webDbClass, $outgoingXmlId, "Office", "02311", $msg, "", "UPDATE");
            $sent2webDbClass->__realted_senttoweb_id = $outgoingXmlId;
            WFactory::getSqlService()->update($sent2webDbClass);
            return $xmlReply;
            //return parent::response(true, "02321", $msg, $this->xml, "", "UPDATE");
        } else {
            $msg = "Office Update failed";
            $xmlReply = parent::response(false, $this->dbClass->__id, $sent2webDbClass, $outgoingXmlId, "Office", "02320", $msg, $this->xml, "UPDATE_FAILED");
            $sent2webDbClass->__realted_senttoweb_id = $outgoingXmlId;
            WFactory::getSqlService()->update($sent2webDbClass);
            return $xmlReply;
            //return parent::response(false, "02320", "Office Update failed", $this->xml);
        }

        return false;
    }

    function delete($sent2webDbClass)
    {

    }

    function loadXML($xml)
    {
        $this->dbClass->loadDataFromXml($xml);

        // now load marketing info
        $this->marketingInfo->loadClassVariableFromXmlData($this->dbClass->xmlInformation["MarketingInfo"]);
        $this->marketingInfo->__description = $this->dbClass->xmlInformation["MarketingInfo"]["OfficeDescription"];
        $this->marketingInfo->__marketing_info_type = "OFFICE";

    }

    /**
     * @return mixed
     */
    function validate()
    {

        if ($this->dbClass->xmlOrder == "Create")
            return true;

        // remove spaces and line breaks
        $publicKey = str_replace(" ", "", str_replace("\n", "", $this->dbClass->xmlPublicKey));

//        if (!empty($publicKey) && __ISUNITTEST) {
//            $publicKey = $this->publicKey;
//        }

        $uniqueId = str_replace(" ", "", str_replace("\n", "", $this->dbClass->xmlOfficeID));

//        if (!empty($uniqueId) && __ISUNITTEST) {
//            $uniqueId = $this->uniqueId;
//        }


        /**
         * @var $query JDatabaseQuery
         */
        $query = WFactory::getServices()->getSqlService()->getQuery();
        $query->select('*')
            ->from("#__portal_offices")
            ->where("public_key LIKE '$publicKey'")
            ->where("unique_id LIKE '$uniqueId'");

        $queryString = (string)$query;

        $result = WFactory::getServices()->getSqlService()->select($queryString);

        if ($result[0]["id"] == null)
            return false;
        return $result[0];


    }


}