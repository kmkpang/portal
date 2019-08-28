<?php
/**
 * Created by JetBrains PhpStorm.
 * User: khan
 * Date: 7/2/13
 * Time: 10:46 PM
 * To change this template use File | Settings | File Templates.
 */

require_once JPATH_ROOT . DS . "libraries" . DS . "webportal" . DS . "services" . DS . "webservice" . DS . "websending" . DS . "websendingBase.php";

define('ERROR_RESPONSE', 400);
define('SUCCESS_RESPONSE', 200);

class ProjectSentToWeb extends WebsendingBase
{


    /**
     * @var Object
     */
    var $json;
    /**
     * @var PortalPortalProjectsSql
     */
    var $dbClass;

    /**
     * @var PortalPortalProjectImageSql
     */
    var $projectImage;
    /**
     * @var PortalPortalProjectImageTypeSql
     */
    var $projectImageType;

    /**
     * @var PortalPortalProjectPlanSql
     */
    var $projectPlan;
    /**
     * @var PortalPortalProjectPlanTypeSql
     */
    var $projectPlanType;

    /**
     * @var PortalPortalProjectUnitSql
     */
    var $projectUnit;
    /**
     * @var PortalPortalProjectFeaturesSql
     */
    var $projectFeatures;
    /**
     * @var PortalPortalProjectUnitTypeSql
     */
    var $projectUnitType;


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


    public function __construct($json)
    {

        $this->dbClass = WFactory::getServices()->getSqlService()->getDbClass(__PORTAL_PORTAL_PROJECTS_SQL);
        $this->projectImage = WFactory::getServices()->getSqlService()->getDbClass(__PORTAL_PORTAL_PROJECT_IMAGE_SQL);
        $this->projectImageType = WFactory::getServices()->getSqlService()->getDbClass(__PORTAL_PORTAL_PROJECT_IMAGE_TYPE_SQL);
        $this->projectPlan = WFactory::getServices()->getSqlService()->getDbClass(__PORTAL_PORTAL_PROJECT_PLAN_SQL);
        $this->projectFeatures = WFactory::getServices()->getSqlService()->getDbClass(__PORTAL_PORTAL_PROJECT_FEATURES_SQL);
        $this->projectPlanType = WFactory::getServices()->getSqlService()->getDbClass(__PORTAL_PORTAL_PROJECT_PLAN_TYPE_SQL);
        $this->projectUnit = WFactory::getServices()->getSqlService()->getDbClass(__PORTAL_PORTAL_PROJECT_UNIT_SQL);
        $this->projectUnitType = WFactory::getServices()->getSqlService()->getDbClass(__PORTAL_PORTAL_PROJECT_UNIT_TYPE_SQL);


        $this->region = WFactory::getServices()->getSqlService()->getDbClass(__PORTAL_GEOGRAPHY_REGIONS_SQL);
        $this->towns = WFactory::getServices()->getSqlService()->getDbClass(__PORTAL_GEOGRAPHY_TOWNS_SQL);
        $this->postalCodes = WFactory::getServices()->getSqlService()->getDbClass(__PORTAL_GEOGRAPHY_POSTAL_CODES_SQL);
        $this->address = WFactory::getServices()->getSqlService()->getDbClass(__PORTAL_PORTAL_PROPERTY_ADDRESSES_SQL);

        $this->loadJSON($json);

        $websendingConfig = parent::getWebsendingConfig();
        $this->companyId = $websendingConfig["companyId"];
        $this->companyName = $websendingConfig["companyName"];
    }

    /**
     *
     * @param $sent2webDbClass PortalPortalSenttowebLogSql
     * @return bool|string
     */
    function update($sent2webDbClass)
    {

        $outgoingJsonId = 0;
        $error = null;
        if (!$this->validate("update", $error)) {

            //  function responseJson($associatedId, $incomingJson, &$outgoingJsonId, $code, $msg, $command)
            $xmlReply = parent::responseJson(
                $this->dbClass->__id,
                $sent2webDbClass,
                $outgoingJsonId,
                ERROR_RESPONSE,
                $error,
                "UPDATE_FAILED");
            $sent2webDbClass->__realted_senttoweb_id = $outgoingJsonId;
            WFactory::getSqlService()->update($sent2webDbClass);
            return $xmlReply;
        }
        $existingProject = WFactory::getSqlService()->select("SELECT * FROM `jos_portal_projects` where id = " . $this->json->Id);
        $this->dbClass->bind($existingProject[0]);

        $geodata = array(
            "PostalCodeID" => $this->json->AreaCode,
            "TownID" => $this->json->DistrictCode,
            "RegionID" => $this->json->ProvinceCode,
            "Latitude" => $this->json->Latitude,
            "Longitude" => $this->json->Longitude,
            "ZoneId" => $this->json->ZoneCode,
            "Address" => json_encode($this->json->Address, JSON_UNESCAPED_UNICODE)
        );

        $this->dbClass->__updated_date = WFactory::getServices()->getSqlService()->getMySqlDateTime();
        $this->dbClass->__company_id = $this->companyId;


        //handle inserting into the jos_portal_projects now
        $this->dbClass->__name = json_encode($this->json->Name, JSON_UNESCAPED_UNICODE);
        $this->dbClass->__main_reg_id = $this->json->MainRegId;
        $this->dbClass->__developer_name = json_encode($this->json->Developer, JSON_UNESCAPED_UNICODE);
        $this->dbClass->__year_built = $this->json->YearBuilt;
        $this->dbClass->__year_complete = $this->json->YearComplete;
        $this->dbClass->__land_size = $this->json->LandSize;
        $this->dbClass->__remark = json_encode($this->json->Remark, JSON_UNESCAPED_UNICODE);
        $this->dbClass->__id = $this->json->Id;
        WFactory::getServices()->getSqlService()->update($this->dbClass);

        $projectId = $this->json->Id;

        //handle the address now

        $existingAddress = WFactory::getSqlService()->select("SELECT * FROM `jos_portal_property_addresses` where id = " . $this->dbClass->__address_id);
        $this->address->bind($existingAddress[0]);
        $this->address->__region_id = $geodata['RegionID'];
        $this->address->__town_id = $geodata['TownID'];
        $this->address->__postal_code_id = $geodata['PostalCodeID'];
        $this->address->__address = $geodata['Address'];
        $this->address->__latitude = $geodata['Latitude'];
        $this->address->__longitude = $geodata['Longitude'];
        $addressId = WFactory::getServices()->getSqlService()->update($this->address);

        // handle features
        WFactory::getSqlService()->update("Delete from  `jos_portal_project_features` where project_id = {$projectId} ");
        foreach ($this->json->Features as $feature) {
            /** @var PortalPortalProjectFeaturesSql $f */
            $f = WFactory::getServices()->getSqlService()->getDbClass(__PORTAL_PORTAL_PROJECT_FEATURES_SQL);
            $f->__project_id = $projectId;
            $f->__feature_id = $feature;
            WFactory::getSqlService()->insert($f);
        }

        $officeId = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_OFFICE)->getDefaultOfficeId();


        //handle units
        // delete all units and images
        WFactory::getSqlService()->update("Delete from  `jos_portal_project_unit` where project_id = {$projectId} ");
        WFactory::getSqlService()->update("Delete from  `jos_portal_project_unit_image` where project_id = {$projectId} ");
        foreach ($this->json->ProjectUnits as $projectUnit) {
            /** @var PortalPortalProjectUnitSql $u */
            $u = WFactory::getServices()->getSqlService()->getDbClass(__PORTAL_PORTAL_PROJECT_UNIT_SQL);
            $u->__project_id = $projectId;
            $u->__units_name = $projectUnit->Name;
            $u->__saga_type_id = $projectUnit->Type;
            $u->__unit_code = $projectUnit->Code;
            $u->__saga_unit_id = $projectUnit->Id;
            $u->__description = $projectUnit->Remark;
            $u->__unit_size = $projectUnit->Size;
            $unitId = WFactory::getSqlService()->insert($u);

            // now handle the gallery
            $result = parent::handleImage($projectUnit->Galleries, PROJECT_UNIT_GALLERY,
                $this->companyId, $this->companyName,
                $officeId, null,
                null, null,
                null, null,
                $projectId, $unitId
            );
            // now handle the floorplan
            $result = parent::handleImage($projectUnit->FloorPlans, PROJECT_UNIT_FLOOR_PLAN,
                $this->companyId, $this->companyName,
                $officeId, null,
                null, null,
                null, null,
                $projectId, $unitId
            );

        }


        //now handle images
        /*
         *   function handleImage(&$Images, $type,
                         $companyId = null, $companyName = null,
                         $officeId = null, $officeName = null,
                         $agentId = null, $agentName = null,
                         $propertyId = null, $propertyAddress = null,
                         $projectId = null)

         * */

        $result = parent::handleImage($this->json->Galleries, PROJECT,
            $this->companyId, $this->companyName,
            $officeId, null,
            null, null,
            null, null,
            $projectId
        );
        if (!$result) {
            WFactory::getLogger()->warn("One or more image insert had failed for Project");
        }


        $sent2webDbClass->__associated_id = $this->dbClass->__id;
        $outgoingJsonId = 0;
        if ($result == true) {

            $xmlReply = parent::responseJson(
                $this->dbClass->__id,
                $sent2webDbClass,
                $outgoingJsonId,
                SUCCESS_RESPONSE,
                null,
                "DELETE"
            );
            $sent2webDbClass->__realted_senttoweb_id = $outgoingJsonId;


            // function responseJson($associatedId, $incomingJson, &$outgoingJsonId, $code, $msg, $command)


            WFactory::getSqlService()->update($sent2webDbClass);
            return $xmlReply;
            //return parent::response(true, "02111", $msg, "", "CREATE");
        } else {
            $msg = 'Project update failed';
            $xmlReply = parent::response(false, $this->dbClass->__id, $sent2webDbClass,
                $outgoingJsonId, "Project", "02110", $msg,
                $this->json, "CREATE_FAILED");
            $sent2webDbClass->__realted_senttoweb_id = $outgoingJsonId;
            WFactory::getSqlService()->update($sent2webDbClass);
            return $xmlReply;
            //return parent::response(false, "02110", "Project insert failed", $this->xml);
        }

        return false;
    }

    /**
     *
     * @param $sent2webDbClass PortalPortalSenttowebLogSql
     * @return bool|string
     */
    function create($sent2webDbClass)
    {

        $outgoingJsonId = 0;
        $error = null;
        if (!$this->validate("create", $error)) {

            //  function responseJson($associatedId, $incomingJson, &$outgoingJsonId, $code, $msg, $command)
            $xmlReply = parent::responseJson(
                $this->dbClass->__id,
                $sent2webDbClass,
                $outgoingJsonId,
                ERROR_RESPONSE,
                $error,
                "CREATE_FAILED");
            $sent2webDbClass->__realted_senttoweb_id = $outgoingJsonId;
            WFactory::getSqlService()->update($sent2webDbClass);
            return $xmlReply;
        }

        $geodata = array(
            "PostalCodeID" => $this->json->AreaCode,
            "TownID" => $this->json->DistrictCode,
            "RegionID" => $this->json->ProvinceCode,
            "Latitude" => $this->json->Latitude,
            "Longitude" => $this->json->Longitude,
            "ZoneId" => $this->json->ZoneCode,
            "Address" => json_encode($this->json->Address, JSON_UNESCAPED_UNICODE)
        );

        $this->dbClass->__created_date = WFactory::getServices()->getSqlService()->getMySqlDateTime();
        $this->dbClass->__updated_date = WFactory::getServices()->getSqlService()->getMySqlDateTime();
        $this->dbClass->__company_id = $this->companyId;


        //handle inserting into the jos_portal_projects now
        $this->dbClass->__name = json_encode($this->json->Name, JSON_UNESCAPED_UNICODE);
        $this->dbClass->__main_reg_id = $this->json->MainRegId;
        $this->dbClass->__developer_name = json_encode($this->json->Developer, JSON_UNESCAPED_UNICODE);
        $this->dbClass->__year_built = $this->json->YearBuilt;
        $this->dbClass->__year_complete = $this->json->YearComplete;
        $this->dbClass->__land_size = $this->json->LandSize;
        $this->dbClass->__remark = json_encode($this->json->Remark, JSON_UNESCAPED_UNICODE);
        $this->dbClass->__id = $this->json->Id;
        $this->dbClass->__is_deleted=0;

        // it might have been a deletd project being re-created. we should handle it..
        $existingProject = WFactory::getSqlService()->select("SELECT * FROM `jos_portal_projects` where id = " . $this->json->Id);
        if (count($existingProject) > 0) {
            WFactory::getServices()->getSqlService()->update($this->dbClass);
        } else
            WFactory::getServices()->getSqlService()->insert($this->dbClass);

        $projectId = $this->json->Id;

        $this->dbClass->__id = $this->json->Id;

        //handle the address now
        $this->address->__type_id = WFactory::getServices()
            ->getSqlService()
            ->getSqlServiceClass(__PROPPERTY_PORTAL_ADDRESS)
            ->getAddressTypeIdFromAddressType('Project address');
        $this->address->__region_id = $geodata['RegionID'];
        $this->address->__town_id = $geodata['TownID'];
        $this->address->__postal_code_id = $geodata['PostalCodeID'];
        $this->address->__address = $geodata['Address'];
        $this->address->__latitude = $geodata['Latitude'];
        $this->address->__longitude = $geodata['Longitude'];
        $addressId = WFactory::getServices()->getSqlService()->insert($this->address);
        $this->dbClass->__address_id = $addressId;

        WFactory::getSqlService()->update($this->dbClass);

        $addressDetails = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_ADDRESS)->getAddress($addressId, true);

        // handle features
        WFactory::getSqlService()->update("Delete from  `jos_portal_project_features` where project_id = {$projectId} ");
        foreach ($this->json->Features as $feature) {
            /** @var PortalPortalProjectFeaturesSql $f */
            $f = WFactory::getServices()->getSqlService()->getDbClass(__PORTAL_PORTAL_PROJECT_FEATURES_SQL);
            $f->__project_id = $projectId;
            $f->__feature_id = $feature;
            WFactory::getSqlService()->insert($f);
        }

        $officeId = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_OFFICE)->getDefaultOfficeId();


        //handle units
        // delete all units and images
        WFactory::getSqlService()->update("Delete from  `jos_portal_project_unit` where project_id = {$projectId} ");
        WFactory::getSqlService()->update("Delete from  `jos_portal_project_unit_image` where project_id = {$projectId} ");
        foreach ($this->json->ProjectUnits as $projectUnit) {
            /** @var PortalPortalProjectUnitSql $u */
            $u = WFactory::getServices()->getSqlService()->getDbClass(__PORTAL_PORTAL_PROJECT_UNIT_SQL);
            $u->__project_id = $projectId;
            $u->__units_name = $projectUnit->Name;
            $u->__saga_type_id = $projectUnit->Type;
            $u->__unit_code = $projectUnit->Code;
            $u->__saga_unit_id = $projectUnit->Id;
            $u->__description = $projectUnit->Remark;
            $u->__unit_size = $projectUnit->Size;
            $unitId = WFactory::getSqlService()->insert($u);

            // now handle the gallery
            $result = parent::handleImage($projectUnit->Galleries, PROJECT_UNIT_GALLERY,
                $this->companyId, $this->companyName,
                $officeId, null,
                null, null,
                null, null,
                $projectId, $unitId
            );
            // now handle the floorplan
            $result = parent::handleImage($projectUnit->FloorPlans, PROJECT_UNIT_FLOOR_PLAN,
                $this->companyId, $this->companyName,
                $officeId, null,
                null, null,
                null, null,
                $projectId, $unitId
            );

        }


        //now handle images
        /*
         *   function handleImage(&$Images, $type,
                         $companyId = null, $companyName = null,
                         $officeId = null, $officeName = null,
                         $agentId = null, $agentName = null,
                         $propertyId = null, $propertyAddress = null,
                         $projectId = null)

         * */

        $result = parent::handleImage($this->json->Galleries, PROJECT,
            $this->companyId, $this->companyName,
            $officeId, null,
            null, null,
            null, null,
            $projectId
        );
        if (!$result) {
            WFactory::getLogger()->warn("One or more image insert had failed for Project");
        }


        $sent2webDbClass->__associated_id = $this->dbClass->__id;
        $outgoingJsonId = 0;
        if ($result == true) {

            $xmlReply = parent::responseJson(
                $this->dbClass->__id,
                $sent2webDbClass,
                $outgoingJsonId,
                SUCCESS_RESPONSE,
                null,
                "CREATE"
            );
            $sent2webDbClass->__realted_senttoweb_id = $outgoingJsonId;


            // function responseJson($associatedId, $incomingJson, &$outgoingJsonId, $code, $msg, $command)


            WFactory::getSqlService()->update($sent2webDbClass);
            return $xmlReply;
            //return parent::response(true, "02111", $msg, "", "CREATE");
        } else {
            $msg = 'Project insert failed';
            $xmlReply = parent::response(false, $this->dbClass->__id, $sent2webDbClass,
                $outgoingJsonId, "Project", "02110", $msg,
                $this->json, "CREATE_FAILED");
            $sent2webDbClass->__realted_senttoweb_id = $outgoingJsonId;
            WFactory::getSqlService()->update($sent2webDbClass);
            return $xmlReply;
            //return parent::response(false, "02110", "Project insert failed", $this->xml);
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

        $outgoingJsonId = 0;
        $error = null;
        if (!$this->validate("delete", $error)) {

            //  function responseJson($associatedId, $incomingJson, &$outgoingJsonId, $code, $msg, $command)
            $xmlReply = parent::responseJson(
                $this->dbClass->__id,
                $sent2webDbClass,
                $outgoingJsonId,
                ERROR_RESPONSE,
                $error,
                "DELETE_FAILED");
            $sent2webDbClass->__realted_senttoweb_id = $outgoingJsonId;
            WFactory::getSqlService()->update($sent2webDbClass);
            return $xmlReply;
        }
        $existingProject = WFactory::getSqlService()->select("SELECT * FROM `jos_portal_projects` where id = " . $this->json->Id);
        $this->dbClass->bind($existingProject[0]);
        $this->dbClass->__is_deleted = 1;
        $result = WFactory::getSqlService()->update($this->dbClass);


        if ($result) {

            $xmlReply = parent::responseJson(
                $this->dbClass->__id,
                $sent2webDbClass,
                $outgoingJsonId,
                SUCCESS_RESPONSE,
                null,
                "DELETE"
            );
            $sent2webDbClass->__realted_senttoweb_id = $outgoingJsonId;


            // function responseJson($associatedId, $incomingJson, &$outgoingJsonId, $code, $msg, $command)


            WFactory::getSqlService()->update($sent2webDbClass);
            return $xmlReply;
            //return parent::response(true, "02111", $msg, "", "CREATE");
        } else {
            $msg = 'Project insert failed';
            $xmlReply = parent::response(false, $this->dbClass->__id, $sent2webDbClass,
                $outgoingJsonId, "Project", "02110", $msg,
                $this->json, "DELETE_FAILED");
            $sent2webDbClass->__realted_senttoweb_id = $outgoingJsonId;
            WFactory::getSqlService()->update($sent2webDbClass);
            return $xmlReply;
            //return parent::response(false, "02110", "Project insert failed", $this->xml);
        }

        return false;
    }


    function updateProjectFeatures($projectId)
    {
        $query = "DELETE FROM #__portal_project_features
                  WHERE (#__portal_project_features.project_id = $projectId);";

        $result = WFactory::getSqlService()->delete($query);
        $features = $this->dbClass->xmlFeatures;

        $dbFeatures = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_PROJECTS)->getAllFeatures();

        foreach ($features['FeatureID'] as $f) {
            /**
             * @var $projectFeatureTable PortalPortalProjectFeaturesSql
             */
            $projectFeatureTable = WFactory::getSqlService()->getDbClass(__PORTAL_PORTAL_PROJECT_FEATURES_SQL);
            if (is_array($f))
                $projectFeatureTable->__feature_id = $f['@attributes']['FeatureID'];
            else
                $projectFeatureTable->__feature_id = $f;
            $projectFeatureTable->__name_en = $dbFeatures[$projectFeatureTable->__feature_id];
            $projectFeatureTable->__name_th = $dbFeatures[$projectFeatureTable->__feature_id];
            $projectFeatureTable->__project_id = $projectId;

            WFactory::getSqlService()->insert($projectFeatureTable);

        }

        return true;
    }

    function loadJSON($json)
    {
        $this->json = json_decode($json);
    }

    /**
     * @return mixed
     */
    function validate($type, &$error)
    {
        $query = "SELECT * FROM `jos_portal_projects` where id = " . $this->json->Id . " and is_deleted = 0";
        $existingProject = WFactory::getSqlService()->select($query);
        if ($type === "create") {
            if (count($existingProject) > 0) {
                $error = "Project with id {$this->json->Id} already exists";
                WFactory::getLogger()->error($error);
                return false;
            }
        } else if ($type === "update" || $type === "delete") {
            if (count($existingProject) === 0) {
                $error = "Project with id {$this->json->Id} not found";
                WFactory::getLogger()->error($error);
                return false;
            }
        }

        return true;

    }


}
