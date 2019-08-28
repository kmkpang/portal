<?php
/**
 * Created by JetBrains PhpStorm.
 * User: khan
 * Date: 7/2/13
 * Time: 10:46 PM
 * To change this template use File | Settings | File Templates.
 */

require_once JPATH_ROOT . DS . "libraries" . DS . "webportal" . DS . "services" . DS . "webservice" . DS . "websending" . DS . "websendingBase.php";
if (!defined('RENT')) {
    define('BUY', "SALE");
    define('RENT', "RENT");
}

class PropertySentToWeb extends WebsendingBase
{


    /**
     * @var string
     */
    var $xml;
    /**
     * @var PortalPortalPropertiesSql
     */
    var $dbClass;
    /**
     * @var PortalPortalPropertiesSql
     */
    var $property;
    /**
     * @var PortalPortalSalesSql
     */
    var $agent;

    /**
     * @var PortalPortalOfficesSql
     */
    var $office;
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

        $this->dbClass = WFactory::getServices()->getSqlService()->getDbClass(__PORTAL_PORTAL_PROPERTIES_SQL);
        $this->marketingInfo = WFactory::getServices()->getSqlService()->getDbClass(__PORTAL_PORTAL_MARKETING_INFO_SQL);
        $this->region = WFactory::getServices()->getSqlService()->getDbClass(__PORTAL_GEOGRAPHY_REGIONS_SQL);
        $this->towns = WFactory::getServices()->getSqlService()->getDbClass(__PORTAL_GEOGRAPHY_TOWNS_SQL);
        $this->postalCodes = WFactory::getServices()->getSqlService()->getDbClass(__PORTAL_GEOGRAPHY_POSTAL_CODES_SQL);
        $this->address = WFactory::getServices()->getSqlService()->getDbClass(__PORTAL_PORTAL_PROPERTY_ADDRESSES_SQL);
        $this->office = WFactory::getServices()->getSqlService()->getDbClass(__PORTAL_PORTAL_OFFICES_SQL);
        $this->property = WFactory::getServices()->getSqlService()->getDbClass(__PORTAL_PORTAL_PROPERTIES_SQL);
        $this->agent = WFactory::getServices()->getSqlService()->getDbClass(__PORTAL_PORTAL_SALES_SQL);

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
            $msg = 'XML Validation Failed,Property ID, Agent ID or Office ID incorrect';
            $xmlReply = parent::response(false, $this->dbClass->__id, $sent2webDbClass,
                $outgoingXmlId, "Property", "01210", $msg,
                $this->xml, "CREATE_FAILED");
            $sent2webDbClass->__realted_senttoweb_id = $outgoingXmlId;
            WFactory::getSqlService()->update($sent2webDbClass);
            return $xmlReply;
            //return parent::response(false, "01210", "XML Validation Failed,Property ID, Agent ID or Office ID incorrect", $this->xml);
        }

        $geodata = $this->dbClass->xmlAddress;
        if (!parent::validatePopulateAndUpdateGeoData($geodata, $this->dbClass)) {

            WFactory::getLogger()->warn("Invalid geodata for property create
            (
                PostalCodeID : {$geodata["PostalCodeID"]},
                TownID       : {$geodata["TownID"]},
                RegionID     : {$geodata["RegionID"]}
            )");
            //return parent::response(false, "01210", "Incorrected geodata relation", $this->xml);

            if (__NEWCOUNTRYGEODATAIMPLMENETEDBYSAGAYET === true) {
                $msg = 'Incorrected geodata relation';
                $xmlReply = parent::response(false, $this->dbClass->__id, $sent2webDbClass,
                    $outgoingXmlId, "Property", "01210", $msg,
                    $this->xml, "CREATE_FAILED");
                $sent2webDbClass->__realted_senttoweb_id = $outgoingXmlId;
                WFactory::getSqlService()->update($sent2webDbClass);
                return $xmlReply;
                //return parent::response(false, "01210", "Incorrected geodata relation", $this->xml);
            } else {
                WFactory::getLogger()->warn("ignoring geodata error: __NEWCOUNTRYGEODATAIMPLMENETEDBYSAGAYET : false");
            }
        }

        $this->dbClass->__created_date = WFactory::getServices()->getSqlService()->getMySqlDateTime();
        $this->dbClass->__company_id = $this->companyId;

        //check if officeID is exists [ existed ? dieded ? dyied ? go 9gag ]
        $officeId = parent::checkIfOfficeUniqueIdExists($this->dbClass->__office_id);
        if ($officeId === false) {
            WFactory::getLogger()->warn("Invalid office unique Id
            (
                __office_id : {$this->dbClass->__office_id},
            )");

            $msg = 'Office unique id does not exists';
            $xmlReply = parent::response(false, $this->dbClass->__id, $sent2webDbClass,
                $outgoingXmlId, "Property", "01210", $msg,
                $this->xml, "CREATE_FAILED");
            $sent2webDbClass->__realted_senttoweb_id = $outgoingXmlId;
            WFactory::getSqlService()->update($sent2webDbClass);
            return $xmlReply;

            //return parent::response(false, "01210", "Office unique id does not exists", $this->xml);
        }
        //move the office_id of xml [ which is actually uniqueID to office_uniqueid ]
        $this->dbClass->___office_unique_id = $this->dbClass->__office_id;
        //set the result of checking to __office_id variable, as this will be pushed to db later
        $this->dbClass->__office_id = $officeId;
        $this->dbClass->__unique_id = parent::generateUniqueId(PROPERTY, $this->dbClass->__id, $this->companyId);;
        $this->office->__id = $officeId;
        $this->office->loadDataFromDatabase();

        $agentId = parent::checkIfAgentUniqueIdExists($this->dbClass->__sale_id);
        if ($agentId === false) {
            WFactory::getLogger()->warn("Invalid agent unique Id
            (
                __unique_id : {$this->dbClass->__unique_id},
            )");
            $msg = 'Sale unique id does not exists';
            $xmlReply = parent::response(false, $this->dbClass->__id, $sent2webDbClass,
                $outgoingXmlId, "Property", "01210", $msg,
                $this->xml, "CREATE_FAILED");
            $sent2webDbClass->__realted_senttoweb_id = $outgoingXmlId;
            WFactory::getSqlService()->update($sent2webDbClass);
            return $xmlReply;
            // return parent::response(false, "01210", "Sale unique id does not exists", $this->xml);
        }

        $this->dbClass->__sale_unique_id = $this->dbClass->__sale_id;
        $this->dbClass->__sale_id = $agentId;
        $this->agent->__id = $agentId;
        $this->agent->loadDataFromDatabase();

        //now do an insert
        $propertyId = WFactory::getServices()->getSqlService()->insert($this->dbClass);
        if (!is_numeric($propertyId)) {
            $msg = 'Property insert failed';
            $xmlReply = parent::response(false, $this->dbClass->__id, $sent2webDbClass,
                $outgoingXmlId, "Property", "02110", $msg,
                $this->xml, "CREATE_FAILED");
            $sent2webDbClass->__realted_senttoweb_id = $outgoingXmlId;
            WFactory::getSqlService()->update($sent2webDbClass);
            return $xmlReply;
            //return parent::response(false, "02110", "Property insert failed", $this->xml);
        }
        $this->dbClass->__id = $propertyId;

        //handle the address now
        $this->address->__type_id = WFactory::getServices()
            ->getSqlService()
            ->getSqlServiceClass(__PROPPERTY_PORTAL_ADDRESS)
            ->getAddressTypeIdFromAddressType('Property address');
        $this->address->__region_id = $geodata['RegionID'];
        $this->address->__town_id = $geodata['TownID'];
        $this->address->__postal_code_id = $geodata['PostalCodeID'];
        $this->address->__address = $geodata['HouseAddress'];
        $this->address->__street_name = $geodata['Street'];
        $this->address->__house_number = $geodata['HouseNumber'];
        $this->address->__latitude = $geodata['Latitude'];
        $this->address->__longitude = $geodata['Longitude'];


        $addressId = WFactory::getServices()->getSqlService()->insert($this->address);
        $this->dbClass->__address_id = $addressId;

        $addressDetails = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_ADDRESS)->getAddress($addressId, true);

        parent::handleVideo($this->dbClass->xmlVideos,$this->dbClass->__id);

        //now handle images
        /*
         *  function handleImage(&$Images, $type,
                         $companyId = null, $companyName = null,
                         $officeId = null, $officeName = null,
                         $propertyId = null, $propertyName = null,
                         $propertyId = null, $propertyAddress = null)
         * */
        $result = parent::handleImage($this->dbClass->xmlImages, PROPERTY,
            $this->companyId, $this->companyName,
            $this->dbClass->__office_id, $this->office->__office_name,
            $this->dbClass->__sale_id,
            "{$this->agent->__first_name} {$this->agent->__last_name}",
            $this->dbClass->__id,
            $this->dbClass->__address
        );
        if (!$result) {
            WFactory::getLogger()->warn("One or more image insert had failed for Property");
        }

        $imageData = $this->dbClass->xmlImages;
        $this->dbClass->__image_file_path = $imageData["DefaultImagePath"];
        $this->dbClass->__list_page_thumb_path = $imageData["ListImagePath"];
        $this->dbClass->__map_page_thumb_path = $imageData["MapImagePath"];
        $this->dbClass->__image_file_name = pathinfo($imageData["DefaultImagePath"], PATHINFO_FILENAME) . "." . pathinfo($imageData["DefaultImagePath"], PATHINFO_EXTENSION);
        $this->dbClass->__date_entered = WFactory::getServices()->getSqlService()->getMySqlDateTime();


        //do some setup stuff

        $this->dbClass->__reg_id = $this->dbClass->xmlPropertyID;
        if (WFactory::getHelper()->isNullOrEmptyString($this->agent->__middle_name)) {
            $this->agent->__middle_name = "";
        }
        if (WFactory::getHelper()->isNullOrEmptyString($this->agent->__last_name)) {
            $this->agent->__last_name = "";
        }
        $this->dbClass->__sales_agent_full_name = "{$this->agent->__first_name} {$this->agent->__middle_name} {$this->agent->__last_name}";
        $this->dbClass->__sales_agent_office_phone = $this->agent->__phone;
        $this->dbClass->__sales_agent_mobile_phone = $this->agent->__mobile;
        $this->dbClass->__sales_agent_email = $this->agent->__email;
        $this->dbClass->__sales_agent_image = $this->agent->__image_file_path;
        //-------------------------------------------------------------
        $this->dbClass->__office_name = $this->office->__office_name;
        $this->dbClass->__office_phone = $this->office->__phone;
        $this->dbClass->__office_email = $this->office->__email;
        $this->dbClass->__office_logo = $this->office->__logo;
        //--------------------------------------------------------------
        $this->dbClass->__address_id = $addressDetails["id"];
        $this->dbClass->__country_id = $addressDetails["country_id"];
        $this->dbClass->__country_code = $addressDetails["country_code"];
        $this->dbClass->__country_name = $addressDetails["country_name"];
        $this->dbClass->__region_id = $addressDetails["region_id"];
        $this->dbClass->__region_name = $addressDetails["region_name"];
        $this->dbClass->__state_province_id = $addressDetails["state_province_id"];
        $this->dbClass->__sate_province_name = $addressDetails["sate_province_name"];
        $this->dbClass->__district_id = $addressDetails["district_id"];
        $this->dbClass->__district_name = $addressDetails["district_name"];
        $this->dbClass->__subdistrict_id = $addressDetails["subdistrict_id"];
        $this->dbClass->__subdistrict_name = $addressDetails["subdistrict_name"];
        $this->dbClass->__city_town_id = $addressDetails["city_town_id"];
        $this->dbClass->__city_town_name = $addressDetails["city_town_name"];
        $this->dbClass->__zip_code_id = $addressDetails["postal_code_id"];
        $this->dbClass->__zip_code_name = $addressDetails["postal_code_name"];
        $this->dbClass->__zip_code = $addressDetails["postal_code"];
        $this->dbClass->__street_name = $addressDetails["street"];
        $this->dbClass->__house_number = $addressDetails["house_number"];
        $this->dbClass->__address = $addressDetails["address"];
        $this->dbClass->__latitude = $addressDetails["latitude"];
        $this->dbClass->__longitude = $addressDetails["longitude"];
        //-------------------------------------
        $propertyDetailInformationXml = $this->dbClass->xmlInformation;
        $this->dbClass->__residential_commercial = $propertyDetailInformationXml['Mode']['@attributes']['Mode'] == 3 ? "COMMERCIAL" : "RESIDENTIAL";

        $this->dbClass->__type_id = $propertyDetailInformationXml['PropertyType']['@attributes']['PropertyType'];
        $this->dbClass->__buy_rent = $this->dbClass->__type_id == 2 ? BUY : RENT;
        $this->dbClass->__is_featured = $propertyDetailInformationXml['Featured'] === 'true' ? 1 : 0;
        $this->dbClass->__sale_percent = floatval($propertyDetailInformationXml['SalePercent']);
        $this->dbClass->__sale_commission = floatval($propertyDetailInformationXml['SaleComm']);

        if (__COUNTRY == "IS") {
            $this->dbClass->__description_text_th = $this->dbClass->xmlDescription['DescriptionText'];
            $this->dbClass->__description_text_en = $this->dbClass->xmlDescription['DescriptionText'];
        }
        if (__COUNTRY == "TH") {
            $this->dbClass->__description_text_th = $this->dbClass->xmlDescription['DescriptionText'];
            $this->dbClass->__description_text_en = $this->dbClass->xmlEnglishDescription['DescriptionText'];
        } else {
            $this->dbClass->__description_text_th = $this->dbClass->xmlDescription['DescriptionText'];
            $this->dbClass->__description_text_en = $this->dbClass->xmlDescription['DescriptionText'];
        }

        $this->dbClass->__title = $this->dbClass->xmlAddress['Title'];
        $this->dbClass->__title_en = $this->dbClass->xmlAddress['Title'];
        $this->dbClass->__title_th = $this->dbClass->xmlAddress['Title'];

        $swappingExclusiveEntrance = parent::findSwappingExtraFlat($this->dbClass->__description_text_en, $propertyDetailInformationXml['Entrance']);
        $this->dbClass->__swapping = $swappingExclusiveEntrance['swapping'];
        $this->dbClass->__exclusive_entrance = $swappingExclusiveEntrance['exclusive_entrance'];
        $this->dbClass->__extra_flat = $swappingExclusiveEntrance['extra_flat'];
        $this->dbClass->__category_id = $propertyDetailInformationXml['PropertyCategory']['@attributes']['PropertyCategory'];
        $this->dbClass->__garage = $propertyDetailInformationXml['Garage'] === 'true' ? 1 : 0;
        $this->dbClass->__garage_area = $propertyDetailInformationXml['GarageArea'];
        $this->dbClass->__elevator = $propertyDetailInformationXml['Elevator'] === 'true' ? 1 : 0;

        /**
         * @var $categoryClass PortalPortalPropertyCategoriesSql
         */
        $categoryClass = WFactory::getSqlService()->getDbClass(__PORTAL_PORTAL_PROPERTY_CATEGORIES_SQL);
        $categoryClass->__id = $this->dbClass->__category_id;
        $categoryClass->loadDataFromDatabase();
        $this->dbClass->__category_name = $categoryClass->__description;
        $this->dbClass->__current_listing_currency = $this->dbClass->__current_listing_currency['@attributes']['CurrentListingCurrency'];
        $this->dbClass->__rental_price_granularity = $this->dbClass->__rental_price_granularity['@attributes']['RentalPriceGranularity'];
        $this->dbClass->__property_status = $this->dbClass->__property_status['@attributes']['PropertyStatus'];
        $this->dbClass->__floor_level = $this->dbClass->__rental_price_granularity['@attributes']['FloorLevel'];
        $this->dbClass->__unit_of_measuer = $propertyDetailInformationXml['UnitOfMeasue']['@attributes']['UnitOfMeasue'];

        //2015-07-07T00:00:00
        $this->dbClass->__original_listing_date = $propertyDetailInformationXml['OrigListingDate'];
        $this->dbClass->__alternate_url = $propertyDetailInformationXml['AlternateURL']['@attributes']['AlternateURL'];
        $this->dbClass->__virtual_tour = $propertyDetailInformationXml['VirtualTour']['@attributes']['VirtualTour'];

        $openHouseXml = $this->dbClass->xmlRegistration;
        $this->dbClass->__open_house_start = $openHouseXml['OpenHouseStart'];
        $this->dbClass->__open_house_end = $openHouseXml['OpenHouseEnd'];
        $this->dbClass->__register_date_requested = $openHouseXml['RegDateRequested'];

        $this->dbClass->__notes = $this->dbClass->xmlNote;
        $this->dbClass->__zone_id = $geodata['ZoneId'];

        /*
         * ----- commercial
         <Commercial>
				<CommNumberOfOffices>0</CommNumberOfOffices>
				<CommNumberOfFloors>0</CommNumberOfFloors>
				<CommTotalNumberOfRooms>6</CommTotalNumberOfRooms>
				<CommBuildingFrontage/>
				<CommClearanceHeight/>
				<CommElevators>false</CommElevators>
				<CommLeaseArea>0</CommLeaseArea>
				<CommManufacturingSpace/>
				<CommOfficeSpace/>
				<CommParking/>
				<CommPossessionDate/>
				<CommPricePerArea>0</CommPricePerArea>
				<CommRetailSpace/>
				<CommTotalArea>0</CommTotalArea>
				<CommUseableArea>0</CommUseableArea>
				<CommWarehouseSpace/>
				<CommYearBuilt>0000</CommYearBuilt>
				<CommZoning/>
			</Commercial>
         * */
        $commercialXml = $this->dbClass->xmlCommercial;
        $this->dbClass->bind($commercialXml, null, true);

        $this->dbClass->__sent_to_web = 1;
        $this->dbClass->__is_deleted = 0;

        $this->dbClass->__last_update =
        $this->dbClass->__last_price_update_date =
        $this->dbClass->__last_price_reduction_date = $this->dbClass->__created_date;

        /* add all the  extended info aid text search */
        $extended_description = $this->dbClass->__category_name . ' ' . $this->dbClass->__office_name
            . ' ' . $this->dbClass->__sales_agent_full_name . ' ' . $this->dbClass->__country_name
            . ' ' . $this->dbClass->__region_name . ' ' . $this->dbClass->__city_town_name
            . ' ' . $this->dbClass->__zip_code
            . ' ' . $this->dbClass->__zip_code . ' ' . $this->dbClass->__zip_code_name
            . ' ' . $this->dbClass->__street_name
            . ' ' . $this->dbClass->__house_number
            . ' ' . $this->dbClass->__address;
        //   DebugBreak();
        $this->dbClass->__full_text_search_helper = strip_tags(WFactory::getHelper()->escapePercentU(
            $this->dbClass->__description_text_en .
            ' |||| ' . $this->dbClass->__description_text_th .
            ' |||| ' . $this->dbClass->__title .
            ' |||| ' . $this->dbClass->__title_en .
            ' |||| ' . $this->dbClass->__title_th .
            ' |||| ' . $this->dbClass->__project_name .
            ' |||| ' . $this->dbClass->__unit_code .
            ' |||| ' . $this->dbClass->__unit_type .
            ' |||| ' . $extended_description)
        );

        $this->dbClass->__initial_picture_path = $imageData['DefaultImagePath'];
        $this->dbClass->__picture_count = count($imageData['Image']);
        $this->dbClass->__office_logo_path = $this->office->__image_file_path;
        $this->dbClass->__google_viewcount = 0;
        $result = WFactory::getServices()->getSqlService()->update($this->dbClass);
        $result &= $this->updatePropertyFeatures($this->dbClass->__id);

        $sent2webDbClass->__associated_id = $this->dbClass->__id;
        $outgoingXmlId = 0;
        if ($result == true) {
            //$msg = '<UniqueID>' . $this->dbClass->__unique_id . '</UniqueID>';
            $msg = $this->dbClass->__unique_id;
            $xmlReply = parent::response(true, $this->dbClass->__id, $sent2webDbClass,
                $outgoingXmlId, "Property", "02111", $msg, "", "CREATE");
            $sent2webDbClass->__realted_senttoweb_id = $outgoingXmlId;
            WFactory::getSqlService()->update($sent2webDbClass);
            return $xmlReply;
            //return parent::response(true, "02111", $msg, "", "CREATE");
        } else {
            $msg = 'Property insert failed';
            $xmlReply = parent::response(false, $this->dbClass->__id, $sent2webDbClass,
                $outgoingXmlId, "Property", "02110", $msg,
                $this->xml, "CREATE_FAILED");
            $sent2webDbClass->__realted_senttoweb_id = $outgoingXmlId;
            WFactory::getSqlService()->update($sent2webDbClass);
            return $xmlReply;
            //return parent::response(false, "02110", "Property insert failed", $this->xml);
        }

        return false;
    }

    function updatenp()
    {
        return $this->update(true);
    }

    /**
     * @param $sent2webDbClass PortalPortalSenttowebLogSql
     * @param bool $np = handle pictures or NOT??
     * @return bool|string
     */
    function update($sent2webDbClass, $np = false)
    {
        if (!$this->validate()) {
            WFactory::getLogger()->warn("Xml validation failed for property update");

            $msg = 'XML Validation Failed,Property ID, Agent ID or Office ID incorrect';
            $xmlReply = parent::response(false, $this->dbClass->__id, $sent2webDbClass,
                $outgoingXmlId, "Property", "01210", $msg,
                $this->xml, "UPDATE_FAILED");
            $sent2webDbClass->__realted_senttoweb_id = $outgoingXmlId;
            WFactory::getSqlService()->update($sent2webDbClass);
            return $xmlReply;
            // return parent::response(false, "01210", "XML Validation Failed,Property ID, Agent ID or Office ID incorrect", $this->xml);
        }

        $geodata = $this->dbClass->xmlAddress;
        if (!parent::validatePopulateAndUpdateGeoData($geodata, $this->dbClass)) {

            WFactory::getLogger()->warn("Invalid geodata for property create
            (
                PostalCodeID : {$geodata["PostalCodeID"]},
                TownID       : {$geodata["TownID"]},
                RegionID     : {$geodata["RegionID"]}
            )");
            // return parent::response(false, "01210", "Incorrected geodata relation", $this->xml);

            if (__NEWCOUNTRYGEODATAIMPLMENETEDBYSAGAYET === true) {
                $msg = 'Incorrected geodata relation';
                $xmlReply = parent::response(false, $this->dbClass->__id, $sent2webDbClass,
                    $outgoingXmlId, "Property", "01210", $msg,
                    $this->xml, "UPDATE_FAILED");
                $sent2webDbClass->__realted_senttoweb_id = $outgoingXmlId;
                WFactory::getSqlService()->update($sent2webDbClass);
                return $xmlReply;
                // return parent::response(false, "01210", "Incorrected geodata relation", $this->xml);
            } else {
                WFactory::getLogger()->warn("ignoring geodata error: __NEWCOUNTRYGEODATAIMPLMENETEDBYSAGAYET : false");
            }
        }

        $this->dbClass->__company_id = $this->companyId;

        //check if officeID is exists [ existed ? dieded ? dyied ? go 9gag ]
        $officeId = parent::checkIfOfficeUniqueIdExists($this->dbClass->__office_id);
        if ($officeId === false) {
            WFactory::getLogger()->warn("Invalid office unique Id
            (
                __office_id : {$this->dbClass->__office_id},
            )");
            $msg = 'Office unique id does not exists';
            $xmlReply = parent::response(false, $this->dbClass->__id, $sent2webDbClass,
                $outgoingXmlId, "Property", "01210", $msg,
                $this->xml, "UPDATE_FAILED");
            $sent2webDbClass->__realted_senttoweb_id = $outgoingXmlId;
            WFactory::getSqlService()->update($sent2webDbClass);
            return $xmlReply;
            // return parent::response(false, "01210", "Office unique id does not exists", $this->xml);
        }
        //move the office_id of xml [ which is actually uniqueID to office_uniqueid ]
        $this->dbClass->___office_unique_id = $this->dbClass->__office_id;
        //set the result of checking to __office_id variable, as this will be pushed to db later
        $this->dbClass->__office_id = $officeId;
        $this->dbClass->__unique_id = parent::generateUniqueId(PROPERTY, $this->dbClass->__id, $this->companyId);;
        $this->office->__id = $officeId;
        $this->office->loadDataFromDatabase();

        $agentId = parent::checkIfAgentUniqueIdExists($this->dbClass->__sale_id);
        if ($agentId === false) {
            WFactory::getLogger()->warn("Invalid agent unique Id
            (
                __unique_id : {$this->dbClass->__unique_id},
            )");
            $msg = 'Sale unique id does not exists';
            $xmlReply = parent::response(false, $this->dbClass->__id, $sent2webDbClass,
                $outgoingXmlId, "Property", "01210", $msg,
                $this->xml, "UPDATE_FAILED");
            $sent2webDbClass->__realted_senttoweb_id = $outgoingXmlId;
            WFactory::getSqlService()->update($sent2webDbClass);
            return $xmlReply;
            //return parent::response(false, "01210", "Sale unique id does not exists", $this->xml);
        }

        $this->dbClass->__sale_unique_id = $this->dbClass->__sale_id;
        $this->dbClass->__sale_id = $agentId;
        $this->agent->__id = $agentId;
        $this->agent->loadDataFromDatabase();

        //now check if property unique id exists
        $propertyId = parent::checkIfPropertyUniqueIdExists($this->dbClass->xmlPropertyID);
        if ($propertyId === false) {
            WFactory::getLogger()->warn("Invalid property unique Id
            (
                __unique_id : {$this->dbClass->xmlPropertyID},
            )");

            $msg = 'Property unique id does not exists';
            $xmlReply = parent::response(false, $this->dbClass->__id, $sent2webDbClass,
                $outgoingXmlId, "Property", "01210", $msg,
                $this->xml, "UPDATE_FAILED");
            $sent2webDbClass->__realted_senttoweb_id = $outgoingXmlId;
            WFactory::getSqlService()->update($sent2webDbClass);
            return $xmlReply;

            //return parent::response(false, "01210", "Property unique id does not exists", $this->xml);
        }

        $this->dbClass->__unique_id = $this->dbClass->xmlPropertyID;
        $this->dbClass->__id = $propertyId;
        $this->property->__id = $propertyId;
        $this->property->loadDataFromDatabase();

        //handle the address now
        $this->address->__type_id = WFactory::getServices()
            ->getSqlService()
            ->getSqlServiceClass(__PROPPERTY_PORTAL_ADDRESS)
            ->getAddressTypeIdFromAddressType('Property address');
        $this->address->__region_id = $geodata['RegionID'];
        $this->address->__town_id = $geodata['TownID'];
        $this->address->__postal_code_id = $geodata['PostalCodeID'];
        $this->address->__address = !WFactory::getHelper()->isNullOrEmptyString($geodata['HouseAddress']) ? $geodata['HouseAddress'] : "";
        $this->address->__street_name = !WFactory::getHelper()->isNullOrEmptyString($geodata['Street']) ? $geodata['Street'] : "";
        $this->address->__house_number = !WFactory::getHelper()->isNullOrEmptyString($geodata['HouseNumber']) ? $geodata['HouseNumber'] : "";
        $this->address->__latitude = !WFactory::getHelper()->isNullOrEmptyString($geodata['Latitude']) ? $geodata['Latitude'] : "0";
        $this->address->__longitude = !WFactory::getHelper()->isNullOrEmptyString($geodata['Longitude']) ? $geodata['Longitude'] : "0";
        $this->address->__id = $this->property->__address_id;

        if ($this->address->__id == null || $this->address->__id == 0) { //if address it not already inserted..insert it now!
            $addressId = WFactory::getServices()->getSqlService()->insert($this->address);
            $this->property->__address_id = $addressId;
        } else
            $addressUpdateResult = WFactory::getServices()->getSqlService()->update($this->address);

        if ($addressUpdateResult === false) {
            WFactory::getLogger()->warn("Failed to update property address
            (
                __address_id : {$this->property->__address_id},

            )");
            $msg = 'Failed to update property address';
            $xmlReply = parent::response(false, $this->dbClass->__id, $sent2webDbClass,
                $outgoingXmlId, "Property", "02120", $msg,
                $this->xml, "UPDATE_FAILED");
            $sent2webDbClass->__realted_senttoweb_id = $outgoingXmlId;
            WFactory::getSqlService()->update($sent2webDbClass);
            return $xmlReply;
            //return parent::response(false, "02120", "Failed to update property address", $this->xml);
        }
        $this->dbClass->__address_id = $this->property->__address_id;

        $addressDetails = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_ADDRESS)->getAddress($this->property->__address_id, true);


        //-----------------------------handle images------------------------------------------
        if ($np) {
            WFactory::getLogger()->warn("NO IMAGE IS HANDLED!", __LINE__, __FILE__);
        } else {

            // delete old images, they are always re-uploaded!
            $oldImageDeleteResult = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_PROPERTY)->deletePropertyImages($this->property->__id, true);
            if (!$oldImageDeleteResult) {
                WFactory::getLogger()->warn("Failed to delete from images for property id {$this->property->__id}");
            }

            parent::handleVideo($this->dbClass->xmlVideos,$this->dbClass->__id);
            /*
             *  function handleImage(&$Images, $type,
                             $companyId = null, $companyName = null,
                             $officeId = null, $officeName = null,
                             $propertyId = null, $propertyName = null,
                             $propertyId = null, $propertyAddress = null)
             * */


            $result = parent::handleImage($this->dbClass->xmlImages, PROPERTY,
                $this->companyId, $this->companyName,
                $this->dbClass->__office_id, $this->office->__office_name,
                $this->dbClass->__sale_id,
                "{$this->agent->__first_name} {$this->agent->__last_name}",
                $this->dbClass->__id,
                $this->dbClass->__address
            );
            if (!$result) {
                WFactory::getLogger()->warn("One or more image insert had failed for Property");
            }

            $imageData = $this->dbClass->xmlImages;
            $this->dbClass->__image_file_path = $imageData["DefaultImagePath"];
            $this->dbClass->__list_page_thumb_path = $imageData["ListImagePath"];
            $this->dbClass->__map_page_thumb_path = $imageData["MapImagePath"];
            $this->dbClass->__image_file_name = pathinfo($imageData["DefaultImagePath"], PATHINFO_FILENAME) . "." . pathinfo($imageData["DefaultImagePath"], PATHINFO_EXTENSION);
            $this->dbClass->__date_entered = WFactory::getServices()->getSqlService()->getMySqlDateTime();
        }

        //-------------------------------------------------------------------------------------------------------------

        //do some setup stuff

        //$this->dbClass->__reg_id = $this->dbClass->xmlPropertyID; [] this is because during update this xml property id is ..well not good
        if (WFactory::getHelper()->isNullOrEmptyString($this->agent->__middle_name)) {
            $this->agent->__middle_name = "";
        }
        if (WFactory::getHelper()->isNullOrEmptyString($this->agent->__last_name)) {
            $this->agent->__last_name = "";
        }
        $this->dbClass->__sales_agent_full_name = "{$this->agent->__first_name} {$this->agent->__middle_name} {$this->agent->__last_name}";
        $this->dbClass->__sales_agent_office_phone = $this->agent->__phone;
        $this->dbClass->__sales_agent_mobile_phone = $this->agent->__mobile;
        $this->dbClass->__sales_agent_email = $this->agent->__email;
        $this->dbClass->__sales_agent_image = $this->agent->__image_file_path;
        //-------------------------------------------------------------
        $this->dbClass->__office_name = $this->office->__office_name;
        $this->dbClass->__office_phone = $this->office->__phone;
        $this->dbClass->__office_email = $this->office->__email;
        //--------------------------------------------------------------
        $this->dbClass->__address_id = $addressDetails["id"];
        $this->dbClass->__country_id = $addressDetails["country_id"];
        $this->dbClass->__country_code = $addressDetails["country_code"];
        $this->dbClass->__country_name = $addressDetails["country_name"];
        $this->dbClass->__region_id = $addressDetails["region_id"];
        $this->dbClass->__region_name = $addressDetails["region_name"];
        $this->dbClass->__state_province_id = $addressDetails["state_province_id"];
        $this->dbClass->__sate_province_name = $addressDetails["sate_province_name"];
        $this->dbClass->__district_id = $addressDetails["district_id"];
        $this->dbClass->__district_name = $addressDetails["district_name"];
        $this->dbClass->__subdistrict_id = $addressDetails["subdistrict_id"];
        $this->dbClass->__subdistrict_name = $addressDetails["subdistrict_name"];
        $this->dbClass->__city_town_id = $addressDetails["city_town_id"];
        $this->dbClass->__city_town_name = $addressDetails["city_town_name"];
        $this->dbClass->__zip_code_id = $addressDetails["postal_code_id"];
        $this->dbClass->__zip_code_name = $addressDetails["postal_code_name"];
        $this->dbClass->__zip_code = $addressDetails["postal_code"];
        $this->dbClass->__street_name = $addressDetails["street"];
        $this->dbClass->__house_number = $addressDetails["house_number"];
        $this->dbClass->__address = $addressDetails["address"];
        $this->dbClass->__latitude = $addressDetails["latitude"];
        $this->dbClass->__longitude = $addressDetails["longitude"];
        //-------------------------------------
        $propertyDetailInformationXml = $this->dbClass->xmlInformation;
        $this->dbClass->__residential_commercial = $propertyDetailInformationXml['Mode']['@attributes']['Mode'] == 3 ? "COMMERCIAL" : "RESIDENTIAL";

        $this->dbClass->__type_id = $propertyDetailInformationXml['PropertyType']['@attributes']['PropertyType'];
        $this->dbClass->__buy_rent = $this->dbClass->__type_id == 2 ? BUY : RENT;
        $this->dbClass->__is_featured = $propertyDetailInformationXml['Featured'] === 'true' ? 1 : 0;
        $this->dbClass->__sale_percent = floatval($propertyDetailInformationXml['SalePercent']);
        $this->dbClass->__sale_commission = floatval($propertyDetailInformationXml['SaleComm']);


        if (__COUNTRY == "IS") {
            $this->dbClass->__description_text_th = !WFactory::getHelper()->isNullOrEmptyString($this->dbClass->xmlDescription['DescriptionText']) ? $this->dbClass->xmlDescription['DescriptionText'] : "";
            $this->dbClass->__description_text_en = !WFactory::getHelper()->isNullOrEmptyString($this->dbClass->xmlDescription['DescriptionText']) ? $this->dbClass->xmlDescription['DescriptionText'] : "";
        }
        if (__COUNTRY == "TH") {
            $this->dbClass->__description_text_th = !WFactory::getHelper()->isNullOrEmptyString($this->dbClass->xmlDescription['DescriptionText']) ? $this->dbClass->xmlDescription['DescriptionText'] : "";
            $this->dbClass->__description_text_en = !WFactory::getHelper()->isNullOrEmptyString($this->dbClass->xmlEnglishDescription['DescriptionText']) ? $this->dbClass->xmlEnglishDescription['DescriptionText'] : "";
        } else {
            $this->dbClass->__description_text_th = $this->dbClass->xmlDescription['DescriptionText'];
            $this->dbClass->__description_text_en = $this->dbClass->xmlDescription['DescriptionText'];
        }

        $this->dbClass->__title = $this->dbClass->xmlAddress['Title'];
        $this->dbClass->__title_en = $this->dbClass->xmlAddress['Title'];
        $this->dbClass->__title_th = $this->dbClass->xmlAddress['Title'];

        $swappingExclusiveEntrance = parent::findSwappingExtraFlat($this->dbClass->__description_text_en, $propertyDetailInformationXml['Entrance']);
        $this->dbClass->__swapping = $swappingExclusiveEntrance['swapping'];
        $this->dbClass->__exclusive_entrance = $swappingExclusiveEntrance['exclusive_entrance'];
        $this->dbClass->__extra_flat = $swappingExclusiveEntrance['extra_flat'];
        $this->dbClass->__category_id = $propertyDetailInformationXml['PropertyCategory']['@attributes']['PropertyCategory'];
        $this->dbClass->__garage = $propertyDetailInformationXml['Garage'] === 'true' ? 1 : 0;
        $this->dbClass->__garage_area = $propertyDetailInformationXml['GarageArea'];
        $this->dbClass->__elevator = $propertyDetailInformationXml['Elevator'] === 'true' ? 1 : 0;
        /**
         * @var $categoryClass PortalPortalPropertyCategoriesSql
         */
        $categoryClass = WFactory::getSqlService()->getDbClass(__PORTAL_PORTAL_PROPERTY_CATEGORIES_SQL);
        $categoryClass->__id = $this->dbClass->__category_id;
        $categoryClass->loadDataFromDatabase();
        $this->dbClass->__category_name = $categoryClass->__description;
        $this->dbClass->__current_listing_currency = $this->dbClass->__current_listing_currency['@attributes']['CurrentListingCurrency'];
        $this->dbClass->__rental_price_granularity = $this->dbClass->__rental_price_granularity['@attributes']['RentalPriceGranularity'];
        $this->dbClass->__property_status = $this->dbClass->__property_status['@attributes']['PropertyStatus'];
        $this->dbClass->__floor_level = $this->dbClass->__rental_price_granularity['@attributes']['FloorLevel'];
        $this->dbClass->__unit_of_measuer = $propertyDetailInformationXml['UnitOfMeasue']['@attributes']['UnitOfMeasue'];


        //$listingDate = date_parse($propertyDetailInformationXml['OrigListingDate']);
        $this->dbClass->__original_listing_date = $propertyDetailInformationXml['OrigListingDate'];//$propertyDetailInformationXml['OrigListingDate']['@attributes']['OrigListingDate'];

        $this->dbClass->__alternate_url = $propertyDetailInformationXml['AlternateURL']['@attributes']['AlternateURL'];
        $this->dbClass->__virtual_tour = $propertyDetailInformationXml['VirtualTour']['@attributes']['VirtualTour'];

        $openHouseXml = $this->dbClass->xmlRegistration;
        $this->dbClass->__open_house_start = $openHouseXml['OpenHouseStart'];
        $this->dbClass->__open_house_end = $openHouseXml['OpenHouseEnd'];
        $this->dbClass->__register_date_requested = $openHouseXml['RegDateRequested'];

        $this->dbClass->__notes = $this->dbClass->xmlNote;
        $this->dbClass->__zone_id = $geodata['ZoneId'];
        /*
         * ----- commercial
         <Commercial>
				<CommNumberOfOffices>0</CommNumberOfOffices>
				<CommNumberOfFloors>0</CommNumberOfFloors>
				<CommTotalNumberOfRooms>6</CommTotalNumberOfRooms>
				<CommBuildingFrontage/>
				<CommClearanceHeight/>
				<CommElevators>false</CommElevators>
				<CommLeaseArea>0</CommLeaseArea>
				<CommManufacturingSpace/>
				<CommOfficeSpace/>
				<CommParking/>
				<CommPossessionDate/>
				<CommPricePerArea>0</CommPricePerArea>
				<CommRetailSpace/>
				<CommTotalArea>0</CommTotalArea>
				<CommUseableArea>0</CommUseableArea>
				<CommWarehouseSpace/>
				<CommYearBuilt>0000</CommYearBuilt>
				<CommZoning/>
			</Commercial>
         * */
        $commercialXml = $this->dbClass->xmlCommercial;
        $this->dbClass->bind($commercialXml, null, true);

        $this->dbClass->__sent_to_web = 1;
        $this->dbClass->__is_deleted = 0;
        $this->dbClass->__last_update = WFactory::getSqlService()->getMySqlDateTime();

        if ($this->property->__current_listing_price != $this->dbClass->__current_listing_price) {
            WFactory::getLogger()->debug("Previous listing price {$this->property->__current_listing_price}
                                          is different from currently sent price {$this->dbClass->__current_listing_price}");

            $this->dbClass->__last_price_update_date = $this->dbClass->__last_update;
        }
        if ($this->property->__current_listing_price > $this->dbClass->__current_listing_price) {

            WFactory::getLogger()->debug("Previous listing price {$this->property->__current_listing_price}
                                          is MORE THAN currently sent price {$this->dbClass->__current_listing_price}");

            $this->dbClass->__last_price_reduction_date = $this->dbClass->__last_update;
        }


        /* add all the  extended info aid text search */
        /* add all the  extended info aid text search */
        $extended_description = $this->dbClass->__category_name . ' ' . $this->dbClass->__office_name
            . ' ' . $this->dbClass->__sales_agent_full_name . ' ' . $this->dbClass->__country_name
            . ' ' . $this->dbClass->__region_name . ' ' . $this->dbClass->__city_town_name
            . ' ' . $this->dbClass->__zip_code
            . ' ' . $this->dbClass->__zip_code . ' ' . $this->dbClass->__zip_code_name
            . ' ' . $this->dbClass->__street_name
            . ' ' . $this->dbClass->__house_number
            . ' ' . $this->dbClass->__address;
        //   DebugBreak();
        $this->dbClass->__full_text_search_helper = strip_tags(WFactory::getHelper()->escapePercentU(
            $this->dbClass->__description_text_en .
            ' |||| ' . $this->dbClass->__description_text_th .
            ' |||| ' . $this->dbClass->__title .
            ' |||| ' . $this->dbClass->__title_en .
            ' |||| ' . $this->dbClass->__title_th .
            ' |||| ' . $this->dbClass->__project_name .
            ' |||| ' . $this->dbClass->__unit_code .
            ' |||| ' . $this->dbClass->__unit_type .
            ' |||| ' . $extended_description));

        $this->dbClass->__initial_picture_path = $imageData['DefaultImagePath'];
        $this->dbClass->__picture_count = count($imageData['Image']);
        $this->dbClass->__office_logo_path = $this->office->__image_file_path;

        $result = WFactory::getServices()->getSqlService()->update($this->dbClass);
        $result &= $this->updatePropertyFeatures($this->dbClass->__id);

        $sent2webDbClass->__associated_id = $this->dbClass->__id;
        $outgoingXmlId = 0;
        if ($result == true) {
            //$msg = '<UniqueID>' . $this->dbClass->__unique_id . '</UniqueID>';
            $msg = $this->dbClass->__unique_id;
            $xmlReply = parent::response(true, $this->dbClass->__id, $sent2webDbClass,
                $outgoingXmlId, "Property", "02121", $msg, "", "UPDATE");
            $sent2webDbClass->__realted_senttoweb_id = $outgoingXmlId;
            WFactory::getSqlService()->update($sent2webDbClass);
            return $xmlReply;
            //return parent::response(true, "02121", $msg, "", "UPDATE");
        } else {
            $msg = 'Property update failed';
            $xmlReply = parent::response(false, $this->dbClass->__id, $sent2webDbClass,
                $outgoingXmlId, "Property", "02120", $msg,
                $this->xml, "UPDATE_FAILED");
            $sent2webDbClass->__realted_senttoweb_id = $outgoingXmlId;
            WFactory::getSqlService()->update($sent2webDbClass);
            return $xmlReply;
            //return parent::response(false, "02120", "Property update failed", $this->xml);
        }

        return false;


    }

    /**
     *
     * @param $sent2webDbClass PortalPortalSenttowebLogSql
     * @return bool|string
     */
    public function delete($sent2webDbClass)
    {
//        $agentId = parent::checkIfAgentUniqueIdExists($this->dbClass->__sale_id);
//        if ($agentId === false) {
//            WFactory::getLogger()->warn("Invalid agent unique Id
//            (
//                __unique_id : {$this->dbClass->__unique_id},
//            )");
//            return parent::response(false, "01210", "Sale unique id does not exists", $this->xml);
//        }
        //now check if property unique id exists
        $propertyId = parent::checkIfPropertyUniqueIdExists($this->dbClass->xmlPropertyID);
        if ($propertyId === false) {
            WFactory::getLogger()->warn("Invalid property unique Id
            (
                __unique_id : {$this->dbClass->xmlPropertyID},
            )");
            $msg = "Property unique id does not exists";
            $xmlReply = parent::response(false, $propertyId, $sent2webDbClass,
                $outgoingXmlId, "Property", "01210", $msg, $this->xml, "DELETE_FAILED");
            $sent2webDbClass->__realted_senttoweb_id = $outgoingXmlId;
            WFactory::getSqlService()->update($sent2webDbClass);
            return $xmlReply;
            //return parent::response(false, "01210", "Property unique id does not exists", $this->xml);
        }


        $result = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_PROPERTY)->deleteProperty($propertyId);

        $sent2webDbClass->__associated_id = $propertyId;
        $outgoingXmlId = 0;
        if ($result == true) {
            //$msg = '<UniqueID>' . $this->dbClass->__unique_id . '</UniqueID>';
            $msg = $this->dbClass->xmlPropertyID;
            $xmlReply = parent::response(true, $propertyId, $sent2webDbClass,
                $outgoingXmlId, "Property", "02131", $msg, "", "DELETE");
            $sent2webDbClass->__realted_senttoweb_id = $outgoingXmlId;
            WFactory::getSqlService()->update($sent2webDbClass);
            return $xmlReply;
            //return parent::response(true, "02131", $msg, "", "DELETE");
        } else {
            $msg = "Property delete failed";
            $xmlReply = parent::response(false, $propertyId, $sent2webDbClass,
                $outgoingXmlId, "Property", "02130", $msg, $this->xml, "DELETE_FAILED");
            $sent2webDbClass->__realted_senttoweb_id = $outgoingXmlId;
            WFactory::getSqlService()->update($sent2webDbClass);
            return $xmlReply;
            //return parent::response(false, "02130", "Property update failed", $this->xml);
        }

        return false;


    }


    function updatePropertyFeatures($propertyId)
    {
        $query = "DELETE FROM #__portal_property_features
                  WHERE (#__portal_property_features.property_id = $propertyId);";

        $result = WFactory::getSqlService()->delete($query);
        $features = $this->dbClass->xmlFeatures;

        $dbFeatures = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_PROPERTIES)->getAllFeatures();

        foreach ($features['FeatureID'] as $f) {
            /**
             * @var $propertyFeatureTable PortalPortalPropertyFeaturesSql
             */
            $propertyFeatureTable = WFactory::getSqlService()->getDbClass(__PORTAL_PORTAL_PROPERTY_FEATURES_SQL);
            if (is_array($f))
                $propertyFeatureTable->__feature_id = $f['@attributes']['FeatureID'];
            else
                $propertyFeatureTable->__feature_id = $f;
            $propertyFeatureTable->__name_en = $dbFeatures[$propertyFeatureTable->__feature_id];
            $propertyFeatureTable->__name_th = $dbFeatures[$propertyFeatureTable->__feature_id];
            $propertyFeatureTable->__property_id = $propertyId;

            WFactory::getSqlService()->insert($propertyFeatureTable);

        }

        return true;
    }

    function loadXML($xml)
    {
        $this->dbClass->loadDataFromXml($xml);

        // now load marketing info
        $this->marketingInfo->loadClassVariableFromXmlData($this->dbClass->xmlInformation["MarketingInfo"]);
        $this->marketingInfo->__description = $this->dbClass->xmlInformation["MarketingInfo"]["OfficeDescription"];
        $this->marketingInfo->__marketing_info_type = "SALE";

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


        $officeUniqueId = str_replace(" ", "", str_replace("\n", "", $this->dbClass->__office_id));
        $agentUniqueId = str_replace(" ", "", str_replace("\n", "", $this->dbClass->__sale_id));
        $propertyUniqueId = str_replace(" ", "", str_replace("\n", "", $this->dbClass->xmlPropertyID));

        WFactory::getLogger()->warn("Public Key checking has been disabled !!!");

        $queryString = "SELECT jos_portal_properties.id
                          FROM    (  jos_portal_properties jos_portal_properties
                                   INNER JOIN
                                     jos_portal_offices jos_portal_offices
                                   ON (jos_portal_properties.office_id = jos_portal_offices.id))
                               INNER JOIN
                                 jos_portal_sales jos_portal_sales
                               ON (jos_portal_properties.sale_id = jos_portal_sales.id)
                         WHERE     (jos_portal_offices.unique_id = '$officeUniqueId')
                               /* AND (jos_portal_sales.unique_id = '$agentUniqueId')*/ /* THIS is disabled so that agent id can be updated for a property*/
                               /* AND (jos_portal_properties.unique_id = '$propertyUniqueId')*/ /* THIS is disabled so that office id can be updated for a property*/
                               /* AND (jos_portal_offices.public_key = 'zz') */";


        $result = WFactory::getServices()->getSqlService()->select($queryString);

        if ($result[0]["id"] == null) {
            WFactory::getLogger()->warn("Property ID and Office ID does not match(property does nto seem to belong to this office)");
            return false;
        }
        return $result[0];


    }


}
