<?php
/**
 * Created by JetBrains PhpStorm.
 * User: khan
 * Date: 7/2/13
 * Time: 10:46 PM
 * To change this template use File | Settings | File Templates.
 */

require_once JPATH_ROOT . DS . "libraries" . DS . "webportal" . DS . "services" . DS . "webservice" . DS . "websending" . DS . "websendingBase.php";


class AgentSentToWeb extends WebsendingBase
{


    /**
     * @var string
     */
    var $xml;
    /**
     * @var PortalPortalSalesSql
     */
    var $dbClass;
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

        $this->dbClass = WFactory::getServices()->getSqlService()->getDbClass(__PORTAL_PORTAL_SALES_SQL);
        $this->marketingInfo = WFactory::getServices()->getSqlService()->getDbClass(__PORTAL_PORTAL_MARKETING_INFO_SQL);
        $this->region = WFactory::getServices()->getSqlService()->getDbClass(__PORTAL_GEOGRAPHY_REGIONS_SQL);
        $this->towns = WFactory::getServices()->getSqlService()->getDbClass(__PORTAL_GEOGRAPHY_TOWNS_SQL);
        $this->postalCodes = WFactory::getServices()->getSqlService()->getDbClass(__PORTAL_GEOGRAPHY_POSTAL_CODES_SQL);
        $this->address = WFactory::getServices()->getSqlService()->getDbClass(__PORTAL_PORTAL_PROPERTY_ADDRESSES_SQL);
        $this->office = WFactory::getServices()->getSqlService()->getDbClass(__PORTAL_PORTAL_OFFICES_SQL);
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
            $xmlReply = parent::response(false, $this->dbClass->__id, $sent2webDbClass, $outgoingXmlId, "Agent", "", "", $this->xml, "CREATE_FAILED");
            $sent2webDbClass->__realted_senttoweb_id = $outgoingXmlId;
            WFactory::getSqlService()->update($sent2webDbClass);
            return $xmlReply;
            //return parent::response(false);
        }

        $geodata = $this->dbClass->xmlAddress;
        if (!parent::validatePopulateAndUpdateGeoData($geodata, $this->dbClass)) {

            WFactory::getLogger()->warn("Invalid geodata for agent create
            (
                PostalCodeID : {$geodata["PostalCodeID"]},
                TownID       : {$geodata["TownID"]},
                RegionID     : {$geodata["RegionID"]}
            )");
            WFactory::getLogger()->warn("However, saga ALWAYS sends wrong data for agents!");
            //return parent::response(false, "01210", "Incorrected geodata relation", $this->xml);
        }

        $this->dbClass->__date_entered = WFactory::getServices()->getSqlService()->getMySqlDateTime();
        $this->dbClass->__company_id = $this->companyId;

        //check if officeID is exists [ existed ? dieded ? dyied ? go 9gag ]
        $officeId = parent::checkIfOfficeUniqueIdExists($this->dbClass->__office_id);
        if ($officeId === false) {
            WFactory::getLogger()->warn("Invalid office unique Id
            (
                __office_id : {$this->dbClass->__office_id},
            )");

            $msg = "Office unique id does not exists";
            $xmlReply = parent::response(false, $this->dbClass->__id, $sent2webDbClass, $outgoingXmlId, "Agent", "01210", $msg, $this->xml, "CREATE_FAILED");
            $sent2webDbClass->__realted_senttoweb_id = $outgoingXmlId;
            WFactory::getSqlService()->update($sent2webDbClass);
            return $xmlReply;


            //return parent::response(false, "01210", "Office unique id does not exists", $this->xml);
        }
        //move the office_id of xml [ which is actually uniqueID to office_uniqueid ]
        $this->dbClass->___office_unique_id = $this->dbClass->__office_id;
        //set the result of checking to __office_id variable, as this will be pushed to db later
        $this->dbClass->__office_id = $officeId;
        $this->dbClass->__unique_id = parent::generateUniqueId(AGENT, $this->dbClass->__id, $this->companyId);;
        $this->office->__id = $officeId;
        $this->office->loadDataFromDatabase();


        //now do an insert
        $agentId = WFactory::getServices()->getSqlService()->insert($this->dbClass);
        if (!is_numeric($agentId)) {

            $msg = "Agent insert failed";
            $xmlReply = parent::response(false, $this->dbClass->__id, $sent2webDbClass, $outgoingXmlId, "Agent", "01210", $msg, $this->xml, "CREATE_FAILED");
            $sent2webDbClass->__realted_senttoweb_id = $outgoingXmlId;
            WFactory::getSqlService()->update($sent2webDbClass);
            return $xmlReply;
            //return parent::response(false, "01210", "Agent insert failed", $this->xml);
        }
        $this->dbClass->__id = $agentId;

        //handle the address now
        $this->address->__type_id = WFactory::getServices()
            ->getSqlService()
            ->getSqlServiceClass(__PROPPERTY_PORTAL_ADDRESS)
            ->getAddressTypeIdFromAddressType('Sale address');
        $this->address->__region_id = $geodata['RegionID'];
        $this->address->__town_id = $geodata['TownID'];
        $this->address->__postal_code_id = $geodata['PostalCodeID'];
        $this->address->__address = $geodata['HouseAddress'];
        $this->address->__latitude = $geodata['Latitude'];
        $this->address->__longitude = $geodata['Longitude'];


        $addressId = WFactory::getServices()->getSqlService()->insert($this->address);
        $this->dbClass->__address_id = $addressId;

        //handle marketing info now
        $marketingData = $this->dbClass->xmlInformation['MarketingInfo'];
        $this->marketingInfo->__marketing_info_type_id = WFactory::getServices()->getSqlService()
            ->getSqlServiceClass(__PROPPERTY_PORTAL_MARKETINGINFO)
            ->getMarketingInfoTypeIdFromMarketingInfoType($this->marketingInfo->__marketing_info_type);
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
        /*
         *  function handleImage(&$Images, $type,
                         $companyId = null, $companyName = null,
                         $officeId = null, $officeName = null,
                         $agentId = null, $agentName = null,
                         $propertyId = null, $propertyAddress = null)
         * */
        $result = parent::handleImage($this->dbClass->xmlImages, AGENT,
            $this->companyId, $this->companyName,
            $this->dbClass->__office_id, $this->office->__office_name,
            $this->dbClass->__id,
            "{$this->dbClass->__first_name} {$this->dbClass->__last_name}"
        );
        if (!$result) {
            WFactory::getLogger()->warn("One or more image insert had failed for Agent");
        }

        $imageData = $this->dbClass->xmlImages;
        $this->dbClass->__image_file_path = $imageData["DefaultImagePath"];
        $this->dbClass->__image_file_name = pathinfo($imageData["DefaultImagePath"], PATHINFO_FILENAME) . "." . pathinfo($imageData["DefaultImagePath"], PATHINFO_EXTENSION);
        $this->dbClass->__date_entered = WFactory::getServices()->getSqlService()->getMySqlDateTime();
        //TODO: talk to them about fixing the gender tag ! this is silly!a person can not have multiple gender!..
        $this->dbClass->__gender = $this->dbClass->__gender["@attributes"]["Gender"];
        $this->dbClass->__show_on_web = $this->dbClass->__show_on_web === "false" ? 0 : 1;

        $result = WFactory::getServices()->getSqlService()->update($this->dbClass);

        $sent2webDbClass->__associated_id = $this->dbClass->__id;
        $outgoingXmlId = 0;
        if ($result == true) {

            //process agent account now
            WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_AGENT)->processAgentAccount($this->dbClass->__id);

            //$msg = '<UniqueID>' . $this->dbClass->__unique_id . '</UniqueID>';
            $msg = $this->dbClass->__unique_id;
            $xmlReply = parent::response(true, $this->dbClass->__id, $sent2webDbClass, $outgoingXmlId, "Agent", "02211", $msg, "", "CREATE");
            $sent2webDbClass->__realted_senttoweb_id = $outgoingXmlId;
            WFactory::getSqlService()->update($sent2webDbClass);
            return $xmlReply;
            //return parent::response(true, "02211", $msg, "", "CREATE");
        } else {
            $msg = "Agent insert failed";
            $xmlReply = parent::response(false, $this->dbClass->__id, $sent2webDbClass, $outgoingXmlId, "Agent", "02210", $msg, $this->xml, "CREATE_FAILED");
            $sent2webDbClass->__realted_senttoweb_id = $outgoingXmlId;
            WFactory::getSqlService()->update($sent2webDbClass);
            return $xmlReply;
            // return parent::response(false, "02210", "Office insert failed", $this->xml);
        }

        return false;
    }

    /**
     *
     * @param $sent2webDbClass PortalPortalSenttowebLogSql
     * @return bool|string
     */
    function update($sent2webDbClass)
    {
        if (!$this->validate()) {
            WFactory::getLogger()->warn("XML Validation failed");
            $xmlReply = parent::response(false,
                $this->dbClass->__id, $sent2webDbClass,
                $outgoingXmlId, "Agent", "", "", $this->xml, "UPDATE_FAILED");

            $sent2webDbClass->__realted_senttoweb_id = $outgoingXmlId;
            WFactory::getSqlService()->update($sent2webDbClass);
            return $xmlReply;
            // return parent::response(false);
        }

        $geodata = $this->dbClass->xmlAddress;
        if (!parent::validatePopulateAndUpdateGeoData($geodata, $this->dbClass)) {

            WFactory::getLogger()->warn("Invalid geodata for agent update
            (
                PostalCodeID : {$geodata["PostalCodeID"]},
                TownID       : {$geodata["TownID"]},
                RegionID     : {$geodata["RegionID"]}
            )");
            WFactory::getLogger()->warn("However, saga ALWAYS sends wrong data for agents!");
            //return parent::response(false, "01220", "Incorrected geodata relation", $this->xml);
        }

        $this->dbClass->__date_modified = WFactory::getServices()->getSqlService()->getMySqlDateTime();
        $this->dbClass->__company_id = $this->companyId;

        //check if officeID is exists [ existed ? dieded ? dyied ? go 9gag ]
        $officeId = parent::checkIfOfficeUniqueIdExists($this->dbClass->__office_id);
        if ($officeId === false) {
            WFactory::getLogger()->warn("Invalid office unique Id
            (
                __office_id : {$this->dbClass->__office_id},
            )");
            $msg = "Office unique id does not exists";
            $xmlReply = parent::response(false, $this->dbClass->__id, $sent2webDbClass,
                $outgoingXmlId, "Agent", "01220", $msg, $this->xml, "UPDATE_FAILED");
            $sent2webDbClass->__realted_senttoweb_id = $outgoingXmlId;
            WFactory::getSqlService()->update($sent2webDbClass);
            return $xmlReply;
            //return parent::response(false, "01220", "Office unique id does not exists", $this->xml);
        }

        $agentId = parent::checkIfAgentUniqueIdExists($this->dbClass->xmlSaleID);
        if ($agentId === false) {
            WFactory::getLogger()->warn("Invalid agent unique Id
            (
                __unique_id : {$this->dbClass->__unique_id},
            )");
            $msg = "Sale unique id does not exists";
            $xmlReply = parent::response(false, $this->dbClass->__id, $sent2webDbClass,
                $outgoingXmlId, "Agent", "02220", $msg, $this->xml, "UPDATE_FAILED");
            $sent2webDbClass->__realted_senttoweb_id = $outgoingXmlId;
            WFactory::getSqlService()->update($sent2webDbClass);
            return $xmlReply;
            //return parent::response(false, "02220", "Sale unique id does not exists", $this->xml);
        }


        //move the office_id of xml [ which is actually uniqueID to office_uniqueid ]
        $this->dbClass->___office_unique_id = $this->dbClass->__office_id;
        //set the result of checking to __office_id variable, as this will be pushed to db later
        $this->dbClass->__office_id = $officeId;
        $this->office->__id = $officeId;
        $this->office->loadDataFromDatabase();

        //move the xmlSaleID to the uniqueID
        $this->dbClass->__unique_id = $this->dbClass->xmlSaleID;
        $this->dbClass->__id = $agentId;
        $this->agent->__id = $agentId;
        $this->agent->loadDataFromDatabase();


        //now do an update
        $updateResult = WFactory::getServices()->getSqlService()->update($this->dbClass);
        if (!$updateResult) {
            $msg = "Agent update failed";
            $xmlReply = parent::response(false, $this->dbClass->__id, $sent2webDbClass,
                $outgoingXmlId, "Agent", "02220", $msg, $this->xml, "UPDATE_FAILED");
            $sent2webDbClass->__realted_senttoweb_id = $outgoingXmlId;
            WFactory::getSqlService()->update($sent2webDbClass);
            return $xmlReply;
            //  return parent::response(false, "02220", "Agent update failed", $this->xml);
        }


        //handle the address now
        $this->address->__type_id = WFactory::getServices()
            ->getSqlService()
            ->getSqlServiceClass(__PROPPERTY_PORTAL_ADDRESS)
            ->getAddressTypeIdFromAddressType('Sale address');
        $this->address->__region_id = !WFactory::getHelper()->isNullOrEmptyString($geodata['RegionID']) ? $geodata['RegionID'] : "";
        $this->address->__town_id = !WFactory::getHelper()->isNullOrEmptyString($geodata['TownID']) ? $geodata['TownID'] : "";
        $this->address->__postal_code_id = !WFactory::getHelper()->isNullOrEmptyString($geodata['PostalCodeID']) ? $geodata['PostalCodeID'] : "";
        $this->address->__address = !WFactory::getHelper()->isNullOrEmptyString($geodata['HouseAddress']) ? $geodata['HouseAddress'] : "";
        $this->address->__latitude = !WFactory::getHelper()->isNullOrEmptyString($geodata['Latitude']) ? $geodata['Latitude'] : "";
        $this->address->__longitude = !WFactory::getHelper()->isNullOrEmptyString($geodata['Longitude']) ? $geodata['Longitude'] : "";
        $this->address->__id = $this->agent->__address_id;


        $updateResult = WFactory::getServices()->getSqlService()->update($this->address);
        if (!$updateResult) {
            $msg = "Agent address update failed";
            $xmlReply = parent::response(false, $this->dbClass->__id, $sent2webDbClass,
                $outgoingXmlId, "Agent", "02220", $msg, $this->xml, "UPDATE_FAILED");
            $sent2webDbClass->__realted_senttoweb_id = $outgoingXmlId;
            WFactory::getSqlService()->update($sent2webDbClass);
            return $xmlReply;
            // return parent::response(false, "02220", "Agent address update failed", $this->xml);
        }
        $this->dbClass->__address_id = $this->address->__id;

        //handle marketing info now
        $marketingData = $this->dbClass->xmlInformation['MarketingInfo'];
        $this->marketingInfo->__reference_id = $this->dbClass->__id;
        $marketingInfoFromDatabase = parent::loadMarketingInfo("SALE", $this->dbClass->__id);
        $this->marketingInfo->__id = $marketingInfoFromDatabase[0]["id"];

//        $marketingInfoCount = count($marketingData['LanguageID']);
//        if ($marketingInfoCount == 0)
//            $marketingInfoCount = 1; // want to run atleast once..even with wrong data!so it can be updated later
//        for ($i = 0; $i < $marketingInfoCount; $i++) {

        $this->marketingInfo->__marketing_info_type_id = WFactory::getServices()->getSqlService()->getSqlServiceClass(__PROPPERTY_PORTAL_MARKETINGINFO)->getMarketingInfoTypeIdFromMarketingInfoType($this->marketingInfo->__marketing_info_type);
        $this->marketingInfo->__slogan = !WFactory::getHelper()->isNullOrEmptyString($marketingData['Slogan']) ? $marketingData['Slogan'] : "";
        $this->marketingInfo->__description = !WFactory::getHelper()->isNullOrEmptyString($marketingData['AgentDescription']) ? $marketingData['AgentDescription'] : "";
        $this->marketingInfo->__closer = !WFactory::getHelper()->isNullOrEmptyString($marketingData['Closer']) ? $marketingData['Closer'] : "";
        $this->marketingInfo->__bullet_point1 = !WFactory::getHelper()->isNullOrEmptyString($marketingData['BulletPoint1']) ? $marketingData['BulletPoint1'] : "";
        $this->marketingInfo->__bullet_point2 = !WFactory::getHelper()->isNullOrEmptyString($marketingData['BulletPoint2']) ? $marketingData['BulletPoint2'] : "";
        $this->marketingInfo->__bullet_point3 = !WFactory::getHelper()->isNullOrEmptyString($marketingData['BulletPoint3']) ? $marketingData['BulletPoint3'] : "";

        if ($this->marketingInfo->__id !== null)
            $updateResult = WFactory::getServices()->getSqlService()->update($this->marketingInfo);
        else
            $updateResult = WFactory::getServices()->getSqlService()->insert($this->marketingInfo);
        if (!$updateResult) {
            WFactory::getLogger()->warn("Failed to update Marketing info of Agent");
        }
        //    }


        //now handle images
        /*
         *  function handleImage(&$Images, $type,
                         $companyId = null, $companyName = null,
                         $officeId = null, $officeName = null,
                         $agentId = null, $agentName = null,
                         $propertyId = null, $propertyAddress = null)
         * */
        $result = parent::handleImage($this->dbClass->xmlImages, AGENT,
            $this->companyId, $this->companyName,
            $this->dbClass->__office_id, $this->office->__office_name,
            $this->dbClass->__id,
            "{$this->dbClass->__first_name} {$this->dbClass->__last_name}"
        );
        if (!$result) {
            WFactory::getLogger()->warn("One or more image insert had failed for Agent");
        }

        $imageData = $this->dbClass->xmlImages;
        $this->dbClass->__image_file_path = $imageData["DefaultImagePath"];
        $this->dbClass->__image_file_name = pathinfo($imageData["DefaultImagePath"], PATHINFO_FILENAME) . "." . pathinfo($imageData["DefaultImagePath"], PATHINFO_EXTENSION);
        $this->dbClass->__date_entered = WFactory::getServices()->getSqlService()->getMySqlDateTime();
        //TODO: talk to them about fixing the gender tag ! this is silly!a person can not have multiple gender!..
        $this->dbClass->__gender = $this->dbClass->__gender["@attributes"]["Gender"];
        $this->dbClass->__show_on_web = $this->dbClass->__show_on_web === "false" ? 0 : 1;

        //Main params
        $this->dbClass->__first_name = !WFactory::getHelper()->isNullOrEmptyString($this->dbClass->__first_name) ? $this->dbClass->__first_name : "";
        $this->dbClass->__middle_name = !WFactory::getHelper()->isNullOrEmptyString($this->dbClass->__middle_name) ? $this->dbClass->__middle_name : "";
        $this->dbClass->__last_name = !WFactory::getHelper()->isNullOrEmptyString($this->dbClass->__last_name) ? $this->dbClass->__last_name : "";
        $this->dbClass->__email = !WFactory::getHelper()->isNullOrEmptyString($this->dbClass->__email) ? $this->dbClass->__email : "";
        $this->dbClass->__fax = !WFactory::getHelper()->isNullOrEmptyString($this->dbClass->__fax) ? $this->dbClass->__fax : "";
        $this->dbClass->__phone = !WFactory::getHelper()->isNullOrEmptyString($this->dbClass->__phone) ? $this->dbClass->__phone : "";
        $this->dbClass->__mobile = !WFactory::getHelper()->isNullOrEmptyString($this->dbClass->__mobile) ? $this->dbClass->__mobile : "";
        $this->dbClass->__url_to_private_page = !WFactory::getHelper()->isNullOrEmptyString($this->dbClass->__fax) ? $this->dbClass->__url_to_private_page : "";
        $this->dbClass->__SIN = !WFactory::getHelper()->isNullOrEmptyString($this->dbClass->__SIN) ? $this->dbClass->__SIN : "";

        $this->dbClass->__language_spoken1 = !WFactory::getHelper()->isNullOrEmptyString($this->dbClass->__language_spoken1) ? $this->dbClass->__language_spoken1 : "";
        $this->dbClass->__language_spoken2 = !WFactory::getHelper()->isNullOrEmptyString($this->dbClass->__language_spoken2) ? $this->dbClass->__language_spoken2 : "";
        $this->dbClass->__language_spoken3 = !WFactory::getHelper()->isNullOrEmptyString($this->dbClass->__language_spoken3) ? $this->dbClass->__language_spoken3 : "";
        $this->dbClass->__language_spoken4 = !WFactory::getHelper()->isNullOrEmptyString($this->dbClass->__language_spoken4) ? $this->dbClass->__language_spoken4 : "";
        $this->dbClass->__language_spoken5 = !WFactory::getHelper()->isNullOrEmptyString($this->dbClass->__language_spoken5) ? $this->dbClass->__language_spoken5 : "";

        $this->dbClass->__title = !WFactory::getHelper()->isNullOrEmptyString($this->dbClass->__title) ? $this->dbClass->__title : "";

        $result = WFactory::getServices()->getSqlService()->update($this->dbClass);


        $result = $result && WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_PROPERTY)->updatePropertyAgentInformation($this->dbClass);

        $sent2webDbClass->__associated_id = $this->dbClass->__id;
        $outgoingXmlId = 0;
        if ($result == true) {
            //$msg = '<UniqueID>' . $this->dbClass->__unique_id . '</UniqueID>';
            WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_AGENT)->processAgentAccount($this->dbClass->__id);
            $msg = $this->dbClass->__unique_id;
            $xmlReply = parent::response(true, $this->dbClass->__id, $sent2webDbClass,
                $outgoingXmlId, "Agent", "02221", $msg, "", "UPDATE");
            $sent2webDbClass->__realted_senttoweb_id = $outgoingXmlId;
            WFactory::getSqlService()->update($sent2webDbClass);
            return $xmlReply;

            //return parent::response(true, "02221", $msg, "", "UPDATE");
        } else {
            $msg = "Agent update failed";
            $xmlReply = parent::response(false, $this->dbClass->__id, $sent2webDbClass,
                $outgoingXmlId, "Agent", "02220", $msg, $this->xml, "UPDATE_FAILED");
            $sent2webDbClass->__realted_senttoweb_id = $outgoingXmlId;
            WFactory::getSqlService()->update($sent2webDbClass);
            return $xmlReply;

            // return parent::response(false, "02220", "Agent update failed", $this->xml);
        }

        return false;
    }

    /**
     *
     * @param $sent2webDbClass PortalPortalSenttowebLogSql
     * @return bool|string
     */
    function delete($sent2webDbClass)
    {
        $agentId = parent::checkIfAgentUniqueIdExists($this->dbClass->xmlSaleID);
        if ($agentId === false) {
            WFactory::getLogger()->warn("Invalid agent unique Id
            (
                __unique_id : {$this->dbClass->__unique_id},
            )");
            $msg = "Sale unique id does not exists";
            $xmlReply = parent::response(false, $agentId, $sent2webDbClass,
                $outgoingXmlId, "Agent", "01230", $msg, $this->xml, "DELETE_FAILED");
            $sent2webDbClass->__realted_senttoweb_id = $outgoingXmlId;
            WFactory::getSqlService()->update($sent2webDbClass);
            return $xmlReply;
            //return parent::response(false, "01230", "Sale unique id does not exists", $this->xml);
        }
        try {
            $agentDeleteResult = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_AGENT)->deleteAgent($agentId);
        } catch (Exception $e) {
            WFactory::getLogger()->warn("Agent $agentId was alredy deleted!");
            $agentDeleteResult = true;
        }

        $sent2webDbClass->__associated_id = $agentId;
        $outgoingXmlId = 0;
        WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_AGENT)->processAgentAccount($agentId);
        if ($agentDeleteResult == true) {
            //$msg = '<UniqueID>' . $this->dbClass->__unique_id . '</UniqueID>';
            $msg = ""; //EMPTY
            $xmlReply = parent::response(true, $agentId, $sent2webDbClass,
                $outgoingXmlId, "Agent", "02231", $msg, "", "DELETE");
            $sent2webDbClass->__realted_senttoweb_id = $outgoingXmlId;
            WFactory::getSqlService()->update($sent2webDbClass);
            return $xmlReply;
            // return parent::response(true, "02231", $msg, "", "DELETE");
        } else {
            $msg = "Agent delete failed";
            $xmlReply = parent::response(false, $agentId, $sent2webDbClass,
                $outgoingXmlId, "Agent", "02230", $msg, $this->xml, "UPDATE_FAILED");
            $sent2webDbClass->__realted_senttoweb_id = $outgoingXmlId;
            WFactory::getSqlService()->update($sent2webDbClass);
            return $xmlReply;
            // return parent::response(false, "02230", "Agent delete failed", $this->xml);
        }

        return false;


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


        $officeUniqueID = str_replace(" ", "", str_replace("\n", "", $this->dbClass->__office_id));

        WFactory::getLogger()->warn("Public Key checking has been disabled !!!");

        $queryString = "SELECT #__portal_sales.id
                          FROM    #__portal_sales #__portal_sales
                               INNER JOIN
                                  #__portal_offices #__portal_offices
                               ON (#__portal_sales.office_id = #__portal_offices.id)
                         WHERE     /* (#__portal_offices.public_key = '$publicKey')
                               AND */ (#__portal_offices.unique_id = '$officeUniqueID')";


        $result = WFactory::getServices()->getSqlService()->select($queryString);

        if ($result[0]["id"] == null) {
            WFactory::getLogger()->warn("Agent ID and Office ID does not match(agent does nto seem to belong to this office)");
            return false;
        }
        return $result[0];


    }


}