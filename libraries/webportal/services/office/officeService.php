<?php

/**
 * Created by JetBrains PhpStorm.
 * User: khan
 * Date: 7/2/13
 * Time: 7:51 PM
 * To change this template use File | Settings | File Templates.
 */
class OfficeService
{

    /**
     * @var PortalPortalOfficesSql
     */
    var $dbClass;

    public function __construct()
    {
        //Thou shalt not construct that which is unconstructable!
        $this->dbClass = WFactory::getServices()->getSqlService()->getDbClass(__PORTAL_PORTAL_OFFICES_SQL);

    }

    public function checkIfRouteIsOffice($name)
    {
        $name = str_replace(":", "-", $name);
        $name = strtolower(WFactory::getHelper()->sanitizeName($name));
        $officeNames = $this->getSanitizedOfficeNames();

        if (array_key_exists($name, $officeNames))
            return $officeNames[$name];

        return null;

    }

    /**
     * just a test function to check route online..
     * @param $id
     */
    public function getRoute($id)
    {
        echo JRoute::_("index.php?option=com_webportal&view=offices&office_id=$id");
        exit();
    }


    public function getJRouteFormattedOfficeName($officeId)
    {
        $officeNames = $this->getSanitizedOfficeNames();
        return array_search($officeId, $officeNames);
    }


    private function getSanitizedOfficeNames()
    {

        /** @var JCacheController $cache */
        $cache = JFactory::getCache('OfficeService', '');


        $cache_id = 'sanitized_office_names';

        if (!$names = $cache->get($cache_id)) {
            WFactory::getLogger()->warn("Cache miss on $cache_id");

            $query = "SELECT id,office_name
                  FROM #__portal_offices where show_on_web=1";

            $offices = WFactory::getSqlService()->select($query);
            $names = array();
            foreach ($offices as $office) {
                $officeName = strtolower(WFactory::getHelper()->sanitizeName($office['office_name']));
                $names[$officeName] = $office['id'];
            }

            $result = $cache->store($names, $cache_id);
            if (!$result)
                WFactory::getLogger()->warn("Failed to save cache for $cache_id ; Cache NOT ENABLED?");
        }

        return $names;

    }

    /**
     * @var WebsendingBase
     */
    private $__websendingBase;

    /**
     * @return WebsendingBase
     */
    public function getWebsendingBase()
    {
        if (!$this->__websendingBase) {
            require_once JPATH_ROOT . '/libraries/webportal/services/webservice/websending/websendingBase.php';
            $this->__websendingBase = new WebsendingBase();
        }
        return $this->__websendingBase;
    }


    public function updateOfficeImage()
    {
        WFactory::getHelper()->isAdminOrExit();

        ///home/khan/www/softverk-webportal-generic/libraries/joomla/filesystem/file.php
        jimport('joomla.filesystem.file');
        $input = JFactory::getApplication()->input;
        $file = $input->files->get('officeImageFile');
        $officeId = $input->getInt('office-id', 0);
        $filename = JFile::makeSafe($file['name']);
        $src = $file['tmp_name'];

        /**
         * @var $office OfficeModel
         */
        $office = $this->getOffice($officeId, true);

        $imageFileName = $office->image_file_name;
        $existingFileExtension = pathinfo($imageFileName, PATHINFO_EXTENSION);
        $fileNameWithoutExtension = str_replace(".$existingFileExtension", "", $imageFileName);
        if (WFactory::getHelper()->isNullOrEmptyString($fileNameWithoutExtension)) {
            $fileNameWithoutExtension = "{$officeId}_1";
        }


        $fileManager = WFactory::getFileManager();
        $commonConfig = WFactory::getConfig()->getWebportalConfigurationArray();
        /**
         * @var iFileManager
         */
        $fileManager = $fileManager->getFileManager($commonConfig["filesystem"]);


        $company = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_COMPANY)->getCompany();
        $officeImagePath = $this->getWebsendingBase()->buildOfficeImagePath($company->id, '', $officeId, '');
        $sourceFilePath = $src;
        $destinationPath = $officeImagePath . "/image/$fileNameWithoutExtension." . pathinfo($filename, PATHINFO_EXTENSION);
        $webPathURL = "";
        $tmpResult = $fileManager->putFile($sourceFilePath, $destinationPath, $webPathURL);

        $resultArray = array(
            "success" => false,
            "message" => ""
        );


        if ($tmpResult == false || empty($webPathURL)) {
            WFactory::getLogger()->warn("Failed to upload office image $sourceFilePath to S3");
            $resultArray['success'] = false;
            $resultArray['message'] = 'Failed to upload office image $sourceFilePath to S3';
        } else {
            WFactory::getLogger()->debug("Uploaded office image $sourceFilePath --> $webPathURL");

            //now update!!

            /**
             * @var $officeDb PortalPortalOfficesSql
             */
            $officeDb = WFactory::getSqlService()->getDbClass(__PORTAL_PORTAL_OFFICES_SQL);
            $officeDb->__id = $officeId;
            $officeDb->loadDataFromDatabase();
            $officeDb->__image_file_path = $webPathURL;
            $updateResult = WFactory::getSqlService()->update($officeDb);

            $resultArray['success'] = true;
            $resultArray['message'] = $webPathURL;

        }

        echo json_encode($resultArray);
        exit(0);


    }

    public function updateOfficeLogo()
    {

        WFactory::getHelper()->isAdminOrExit();

        ///home/khan/www/softverk-webportal-generic/libraries/joomla/filesystem/file.php
        jimport('joomla.filesystem.file');
        $input = JFactory::getApplication()->input;
        $file = $input->files->get('officeImageFile');
        $officeId = $input->getInt('office-id', 0);
        $filename = JFile::makeSafe($file['name']);
        $src = $file['tmp_name'];

        /**
         * @var $office OfficeModel
         */
        $office = $this->getOffice($officeId, true);

        $imageFileName = basename($office->logo);
        $existingFileExtension = pathinfo($imageFileName, PATHINFO_EXTENSION);
        $fileNameWithoutExtension = str_replace(".$existingFileExtension", "", $imageFileName);


        $fileManager = WFactory::getFileManager();
        $commonConfig = WFactory::getConfig()->getWebportalConfigurationArray();
        /**
         * @var iFileManager
         */
        $fileManager = $fileManager->getFileManager($commonConfig["filesystem"]);


        $company = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_COMPANY)->getCompany();
        $officeImagePath = $this->getWebsendingBase()->buildOfficeImagePath($company->id, '', $officeId, '');
        $sourceFilePath = $src;
        $destinationPath = $officeImagePath . "/$fileNameWithoutExtension." . pathinfo($filename, PATHINFO_EXTENSION);
        $webPathURL = "";
        $tmpResult = $fileManager->putFile($sourceFilePath, $destinationPath, $webPathURL);

        $resultArray = array(
            "success" => false,
            "message" => ""
        );


        if ($tmpResult == false || empty($webPathURL)) {
            WFactory::getLogger()->warn("Failed to upload office image $sourceFilePath to S3");
            $resultArray['success'] = false;
            $resultArray['message'] = 'Failed to upload office image $sourceFilePath to S3';
        } else {
            WFactory::getLogger()->debug("Uploaded office image $sourceFilePath --> $webPathURL");

            //now update!!

            /**
             * @var $officeDb PortalPortalOfficesSql
             */
            $officeDb = WFactory::getSqlService()->getDbClass(__PORTAL_PORTAL_OFFICES_SQL);
            $officeDb->__id = $officeId;
            $officeDb->loadDataFromDatabase();
            $officeDb->__logo = $webPathURL;
            $updateResult = WFactory::getSqlService()->update($officeDb);

            $resultArray['success'] = true;
            $resultArray['message'] = $webPathURL;

        }

        echo json_encode($resultArray);
        exit(0);


    }

    public function toggleOfficePublish()
    {
        $resultArray = array(
            "success" => false,
            "message" => ""
        );
        $input = JFactory::getApplication()->input;
        $publish = $input->getInt('publish', null);
        $officeId = $input->getInt('office-id', 0);

        /**
         * @var $officeDb PortalPortalOfficesSql
         */
        $officeDb = WFactory::getSqlService()->getDbClass(__PORTAL_PORTAL_OFFICES_SQL);
        $officeDb->__id = $officeId;
        $officeDb->loadDataFromDatabase();

        if ($publish === 0 || $publish === 1) {
            $officeDb->__show_on_web = $publish;
            $updateResult = WFactory::getSqlService()->update($officeDb);

            $resultArray['success'] = true;
            $resultArray['message'] = "$publish";


        } else {
            $resultArray['success'] = false;
            $resultArray['message'] = "Failed to read publish state from JInput";
        }

        echo json_encode($resultArray);
        exit(0);

    }


    /**
     * @return OfficeModel
     */
    public function getOfficeModel()
    {
        require_once "officeModel.php";
        return new OfficeModel();
    }


    private $__officeCache = array();

    private function __isFirstOffice()
    {

        $query = "SELECT id  FROM `jos_portal_offices` WHERE `show_on_web` = 1";
        $result = WFactory::getSqlService()->select($query);
        if (count($result) === 1)
            return $result[0]['id'];
        return false;
    }

    public function getDefaultOfficeId()
    {
        $query = "SELECT id  FROM `jos_portal_offices` WHERE `show_on_web` = 1 limit 1";
        $result = WFactory::getSqlService()->select($query);
        return $result[0]['id'];

    }

    public function deployFirstOffice()
    {
        if (defined('__SUPPORT_MULTI_OFFICE') && __SUPPORT_MULTI_OFFICE === true) {
            WFactory::getLogger()->info("This is a multi office portal , NOT updating office detail links");
            return;
        }

        $firstOfficeId = $this->__isFirstOffice();
        if ($firstOfficeId === false)
            return;

        WFactory::getLogger()->debug("FirstOfficeReceived..");
        /**
         * @var $office OfficeModel
         */
        $office = $this->getOffice($firstOfficeId, true);

        //update company
        /**
         * @var $company PortalPortalCompaniesSql
         */
        $company = WFactory::getSqlService()->getDbClass(__PORTAL_PORTAL_COMPANIES_SQL);
        $company->__id = $office->company_id;
        $company = $company->loadDataFromDatabase();
        $company->__company_name = $office->office_name;
        $company->__email = $office->email;
        WFactory::getSqlService()->update($company);

        //update menus
        $menus2Update = array(
            143, //English
            531, //Is
            178,//Thai //TODO: Add chinese menu !
            586, // Tegalog ( ph )
        );

        foreach ($menus2Update as &$m) {
            $m = "id=$m";
        }
        $where = implode(' or ', $menus2Update);

        $url = $this->getJRouteFormattedOfficeName($firstOfficeId);

        $query = "UPDATE `jos_menu` SET  `link` =  '/$url' , type ='url' where $where";
        $result = WFactory::getSqlService()->update($query);


        //update modules
        $modules2Update = array(
            187, //Front Page - Office Address
            188, //Front Page - Office Description
            189, //Front Page - Office Email
            190, //Front Page - Agents
            198  //Front Page - Office Map
        );

        $modules2Update = implode(',', $modules2Update);

        $query = "select * from jos_modules where id in ($modules2Update)";
        $modules = WFactory::getSqlService()->select($query);


        foreach ($modules as $m) {

            $param = json_decode($m['params']);
            $param->office_id = $firstOfficeId;
            $param = json_encode($param);

            $update = "UPDATE  `jos_modules` SET  `params` ='$param' where id ={$m['id']}";
            $result = WFactory::getSqlService()->update($update);
        }

        return true;


    }

    public function getDefaultOffice()
    {
        return $this->getOffice($this->getDefaultOfficeId(), true);
    }

    /**
     * @param $officeId
     * @return mixed
     */
    public function getOffice($officeId, $asOfficeModel = false)
    {
        if(is_array($officeId))
        {
            $officeId=$officeId['officeId'];
        }
        $type = $asOfficeModel ? "model" : "array";
        if (intval($officeId) <= 0) {
            WFactory::getLogger()->warn("Office ID  [ $officeId ] is null or 0 ! ", __LINE__, __FILE__);
            return array();
        }

        if ($this->__officeCache["{$officeId}_{$type}"] !== null) {
            return $this->__officeCache["{$officeId}_{$type}"];
        }


        $this->dbClass->__id = $officeId;

        if (!WFactory::getSqlService()->returnDeletedRecord()) {
            $this->dbClass->__show_on_web = 1;
        }
        $this->dbClass->loadDataFromDatabase();

        $office = $this->dbClass->unbind();

        if (empty($office)) {
            WFactory::getLogger()->warn("Office NOT FOUND with office ID $officeId", __LINE__, __FILE__);
            return null;
        }

        //marketing info
        $marketingInfoTypeId = WFactory::getServices()
            ->getSqlService()
            ->getSqlServiceClass(__PROPPERTY_PORTAL_MARKETINGINFO)
            ->getMarketingInfoTypeIdFromMarketingInfoType("Office");

        /**
         * @var $markeingInfoDbClass PortalPortalMarketingInfoSql
         */
        $markeingInfoDbClass = WFactory::getServices()->getSqlService()->getDbClass(__PORTAL_PORTAL_MARKETING_INFO_SQL);
        $markeingInfoDbClass->__reference_id = $this->dbClass->__id;
        $markeingInfoDbClass->__marketing_info_type_id = $marketingInfoTypeId;
        $markeingInfoDbClass->loadDataFromDatabase();


        $marketingInfo = $markeingInfoDbClass->unbind();
        $office["marketingInfo"] = $marketingInfo;


        //load address now

//        /**
//         * @var $addressDbClass PortalPortalPropertyAddressesSql
//         */
//        $addressDbClass = WFactory::getServices()->getSqlService()->getDbClass(__PORTAL_PORTAL_PROPERTY_ADDRESSES_SQL);
//        $addressDbClass->__id = $this->dbClass->__address_id;
//        $addressDbClass->loadDataFromDatabase();
//
//        $address = $addressDbClass->unbind();
        $office["address"] = WFactory::getServices()
            ->getServiceClass(__PROPPERTY_PORTAL_ADDRESS)
            ->getAddress($this->dbClass->__address_id);


        if ($asOfficeModel) {

            $officeModel = $this->getOfficeModel();
            $officeModel->bindToDb($office);

            //addressModel

//            $addressModel = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_ADDRESS)->getPropertyAddressModel();
//            $addressModel->bindToDb($office['address']);
//            $officeModel->address = WFactory::getServices()
//                ->getServiceClass(__PROPPERTY_PORTAL_ADDRESS)
//                ->getAddress($office['address']);

            //marketing model
            $marketingModel = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_MARKETINGINFO)->getMarketingInfoModel();
            $marketingModel->bindToDb($office['marketingInfo']);


            $officeModel->marketingInfo = $marketingModel;


            $office = $officeModel;


        }

        if ($asOfficeModel) {
            $office->marketingInfo->slogan_plain = strip_tags($markeingInfoDbClass->__slogan);
            $office->marketingInfo->description_plain = strip_tags($markeingInfoDbClass->__description);
            $office->marketingInfo->bullet_point1_plain = strip_tags($markeingInfoDbClass->__bullet_point1);
            $office->marketingInfo->bullet_point2_plain = strip_tags($markeingInfoDbClass->__bullet_point2);
            $office->marketingInfo->bullet_point3_plain = strip_tags($markeingInfoDbClass->__bullet_point3);


        } else {
            $office['marketingInfo']["slogan_plain"] = strip_tags($markeingInfoDbClass->__slogan);
            $office['marketingInfo']["description_plain"] = strip_tags($markeingInfoDbClass->__description);
            $office['marketingInfo']["bullet_point1_plain"] = strip_tags($markeingInfoDbClass->__bullet_point1);
            $office['marketingInfo']["bullet_point2_plain"] = strip_tags($markeingInfoDbClass->__bullet_point2);
            $office['marketingInfo']["bullet_point3_plain"] = strip_tags($markeingInfoDbClass->__bullet_point3);

        }

        $this->__officeCache["{$officeId}_{$type}"] = $office;
        return $office;
    }

    public function getOfficePhone($officeId = null)
    {
        $where = "";
        if ($officeId) {
            $where = " where id=$officeId ";
        }
        $query = "SELECT phone FROM `jos_portal_offices` $where limit 1";
        $phone = WFactory::getSqlService()->select($query);
        return $phone[0]['phone'];
    }

    public function createNewOffice($data)
    {
        $officeId = $this->insert($data);
        return $officeId;
    }

    /**
     * @param $officeId
     * @return bool|mixed
     */
    public function deleteOffice($officeId)
    {
        $resultArray = array(
            "success" => false,
            "message" => ""
        );

        if (is_array($officeId)) {
            $officeId = $officeId['office-id'];
        }

        if (intval($officeId) > 0) {

            /**
             * @var $officeDb PortalPortalOfficesSql
             */
            $officeDb = WFactory::getSqlService()->getDbClass(__PORTAL_PORTAL_OFFICES_SQL);
            $officeDb->__id = $officeId;
            $officeDb->loadDataFromDatabase();

            $officeDb->__show_on_web = 0;

            $updateResult = WFactory::getSqlService()->update($officeDb);

            //agents
            $query = "update jos_portal_sales set is_deleted = 1 , show_on_web = 0 where office_id = $officeId";
            $agentsUpdated = WFactory::getSqlService()->update($query);

            //properties
            $query = "update jos_portal_properties set is_deleted = 1 where office_id = $officeId";
            $propertiesUpdated = WFactory::getSqlService()->update($query);

            $resultArray = array(
                "success" => true,
                "message" => "Office {$officeDb->__office_name} is deleted !"
            );

        }
        $resultArray['message'] = 'Failed to parse office id ';

        echo json_encode($resultArray);
        exit(0);
    }


    /**
     *
     * @param $officeModel OfficeModel
     * @return mixed
     */
    public function updateOffice($officeModel)
    {
        if (get_class($officeModel) === 'stdClass') {
            $officeModelTemp = $this->getOfficeModel();
            $addressModelTemp = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_ADDRESS)->getPropertyAddressModel();
            $marketingModelTemp = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_MARKETINGINFO)->getMarketingInfoModel();
            /**
             * @var $officeModel OfficeModel
             */
            $officeModel = WFactory::getHelper()->castObject($officeModelTemp, $officeModel);

            if (get_class($officeModel->address) === 'stdClass') {
                $officeModel->address = WFactory::getHelper()->castObject($addressModelTemp, $officeModel->address);
            }
            if (get_class($officeModel->marketingInfo) === 'stdClass') {
                $officeModel->marketingInfo = WFactory::getHelper()->castObject($marketingModelTemp, $officeModel->marketingInfo);
            }

        }
        /**
         * @var $officeDbClass PortalPortalOfficesSql
         */
        $officeDbClass = WFactory::getSqlService()->getDbClass(__PORTAL_PORTAL_OFFICES_SQL);

        $officeDbClass = $officeModel->reverseBindToDb($officeDbClass);
        $updateResult = true;
        if ($officeModel->address !== null) {
            //update address
            /**
             * @var $addressDbClass PortalPortalPropertyAddressesSql
             */
            $addressDbClass = WFactory::getServices()->getSqlService()->getDbClass(__PORTAL_PORTAL_PROPERTY_ADDRESSES_SQL);
            $addressDbClass = $officeModel->address->reverseBindToDb($addressDbClass);
            $updateResult &= WFactory::getSqlService()->update($addressDbClass);
        }
        if ($officeModel->marketingInfo !== null) {
            //update address
            /**
             * @var $markeingInfoDbClass PortalPortalMarketingInfoSql
             */
            $markeingInfoDbClass = WFactory::getServices()->getSqlService()->getDbClass(__PORTAL_PORTAL_MARKETING_INFO_SQL);
            $markeingInfoDbClass = $officeModel->marketingInfo->reverseBindToDb($markeingInfoDbClass);
            $updateResult &= WFactory::getSqlService()->update($markeingInfoDbClass);
        }

        $officeDbClass->__date_modified = WFactory::getSqlService()->getMySqlDateTime();
        $updateResult &= WFactory::getSqlService()->update($officeDbClass);

        //update ALL office related shit in properties table

        WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_PROPERTY)->updatePropertyOfficeInformation($officeDbClass);

        $resultArray = array(
            "success" => $updateResult ? true : false,
            "message" => "Update complete.."
        );

        echo json_encode($resultArray);
        exit(0);

    }

    /**
     * @return OfficeModel[]
     */
    public function getOffices()
    {
//        if (is_array($asModel)) {
//            $asModel = $asModel['asModel']==='true'?true:false;
//        }
        /**
         * @var $officeDb PortalPortalOfficesSql
         */
        $officeDb = WFactory::getSqlService()->getDbClass(__PORTAL_PORTAL_OFFICES_SQL);
        $officeDb->__show_on_web = 1;

        $offices = $officeDb->loadDataFromDatabase(false);
        foreach ($offices as &$office) {
            $office->address = WFactory::getServices()
                ->getServiceClass(__PROPPERTY_PORTAL_ADDRESS)
                ->getAddress($office->__address_id);
        }


        $result = array();

        /**
         * @var $o PortalPortalOfficesSql
         */
        foreach ($offices as $i => $o) {
            $__office = $this->getOfficeModel();
            $__office->bindToDb($o);
            $__office->address = $o->address;
            $result[] = $__office;
        }

        return $result;

    }

    /**
     * @param SearchModel $officeSearchModel
     * @return mixed
     */
    public function searchOfficeByLocation($officeSearchModel = null)
    {
        $addressType = WFactory::getServices()
            ->getSqlService()
            ->getSqlServiceClass(__PROPPERTY_PORTAL_ADDRESS)
            ->getAddressTypeIdFromAddressType('Office Address');

        $query = WFactory::getSqlService()->getQuery();
        $query->select('#__portal_offices.id,#__portal_offices.office_name')
            ->from('#__portal_offices')
            ->innerJoin('#__portal_property_addresses on #__portal_offices.address_id=#__portal_property_addresses.id')
            ->where('#__portal_offices.show_on_web=1')
            ->where('#__portal_property_addresses.type_id=' . $addressType);


        if (!empty($officeSearchModel->region_id)) {
            $query->where('#__portal_property_addresses.region_id=' . $officeSearchModel->region_id);
        }

        //NOTE:  9. add property:
        //       --- filter office by ONLY province ( not district )
//        if (!empty($officeSearchModel->city_town_id)) {
//            $query->where('#__portal_property_addresses.town_id=' . $officeSearchModel->city_town_id);
//        }
//        if (!empty($officeSearchModel->zip_code_id)) {
//            $query->where('#__portal_property_addresses.postal_code_id=' . $officeSearchModel->zip_code_id);
//        }
        //--


        $result = WFactory::getSqlService()->select($query);

        if ($officeSearchModel === null)
            array_unshift($result, "EMPTY_OFFICE_RETURN_ALL");


        if (empty($result))//if nothing found..return ALL
            return $this->searchOfficeByLocation();


        return $result;


    }

    /**
     * Used in setting default latitude and longitude of google map
     * @return mixed
     */
    public function getFirstOfficeLatitudeLongitudeZoomLevel()
    {
        $query = "SELECT #__portal_property_addresses.latitude,
                       #__portal_property_addresses.longitude
                  FROM    #__portal_offices #__portal_offices
                       INNER JOIN
                          #__portal_property_addresses #__portal_property_addresses
                       ON (#__portal_offices.address_id = #__portal_property_addresses.id)
                 WHERE (#__portal_offices.show_on_web = 1)
                ORDER BY #__portal_offices.id ASC
                LIMIT 1";

        $result = WFactory::getSqlService()->select($query);
        $result = $result[0];
        if ($result !== null) {
            $result['zoom'] = '14';
        }
        return $result;
    }


    /**
     * @deprecated use getOffices
     * @param null $officeId
     * @return mixed
     */
    public function getOfficeAll($officeId = null)
    {

        if ($officeId) {
            $officeId = "id = $officeId AND";
        }

        $query = "
			SELECT *
			FROM jos_portal_offices
			where $officeId show_on_web = 1
			ORDER BY id asc
		";

        $result = WFactory::getSqlService()->select($query);

        foreach ($result as &$r) {
            $query = "SELECT COUNT(jos_portal_properties.id) as properties
                      FROM jos_portal_properties jos_portal_properties
                     WHERE     (jos_portal_properties.office_id = {$r["id"]})
                           AND (jos_portal_properties.is_deleted = 0)";
            $properties = WFactory::getSqlService()->select($query);
            $properties = $properties[0]['properties'];
            $r['properties'] = $properties;

        }

        return $result;
    }


}