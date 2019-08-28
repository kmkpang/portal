<?php

/**
 * Created by PhpStorm.
 * User: khan
 * Date: 5/19/14
 * Time: 1:21 PM
 */

require_once JPATH_LIBRARY_WEBPORTAL_SERVICES . DS . 'agent' . DS . 'agentModel.php';

class AgentService
{
    /**
     * Get agent details
     * @param $agentId
     * @return AgentModel
     * @throws PortalException
     */
    function getAgent($agentId, $getAsModel = false)
    {
        if (is_array($agentId))
            $agentId = $agentId['agentId'];

        $agentModel = new AgentModel();
        /**
         * @var $agentDbClass PortalPortalSalesSql
         */
        $agentDbClass = WFactory::getSqlService()->getDbClass(__PORTAL_PORTAL_SALES_SQL);
        $agentDbClass->__id = $agentId;
        $result = $agentDbClass->loadDataFromDatabase();

        if ($result === false) {
            WFactory::getLogger()->warn("Failed to find agent with id $agentId");
            return null;
        }

        if ($agentDbClass->__is_deleted == 1 && !WFactory::getSqlService()->returnDeletedRecord()) {
            WFactory::getLogger()->warn("Tried to retrive deleted agent, configuration does not allow it, please set returnDeletedRecord to true in webportalconfig.php file");

            WFactory::throwPortalException("Agent id $agentId is deleted and configuration does not allow retrieving deleted record");
        }

        $agentModel->bindToDb($agentDbClass);
        //WFactory::getHelper()->printArrayAsClassToGenerateModel($agent);

        //get his marketing info
        $marketingInfo = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_MARKETINGINFO)->getMarketingInfo('Agent', $agentId);
        $agentModel->marketing_info = $marketingInfo;
        //get his address
        $address = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_ADDRESS)->getAddress($agentModel->address_id);
        $agentModel->address = $address;


        //get properties by this agent first


        $properties = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_PROPERTIES)->getPropertiesByAgent($agentId);

        $agentModel->properties = $properties;

        //office

        $office = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_OFFICE)->getOffice($agentModel->office_id);
        $agentModel->office = $office;


        if ($getAsModel) {
            //addressModel

            $addressModel = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_ADDRESS)->getPropertyAddressModel();
            $addressModel->bindToDb($agentModel->address);
            $agentModel->address = $addressModel;

            //marketing model
            $marketingModel = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_MARKETINGINFO)->getMarketingInfoModel();
            $marketingModel->bindToDb($agentModel->marketing_info);
            $agentModel->marketing_info = $marketingModel;

            //office

            $office = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_OFFICE)->getOffice($agentModel->office_id, true);
            $agentModel->office = $office;
        }


        return $agentModel;
    }

    /**
     * Checks if the agent is deleted or not...
     * If the agent is DELETED, his account is blocked
     * If the agent is NOT DELETED , his account is UPDATED / CREATED ( if it does not exist )
     * @param $agentId
     * @return bool
     */
    function processAgentAccount($agentId)
    {

        $success = true;
        /**
         * @var $agentDbClass PortalPortalSalesSql
         */
        $agentDbClass = WFactory::getSqlService()->getDbClass(__PORTAL_PORTAL_SALES_SQL);
        $agentDbClass->__id = $agentId;
        $result = $agentDbClass->loadDataFromDatabase();

        if ($result === false) {
            WFactory::getLogger()->warn("Failed to find agent with id $agentId");
            return false;
        }

        $agentAccount = WFactory::getServices()
            ->getServiceClass(__PROPPERTY_PORTAL_USERS)
            ->getUserAccountByEmail($agentDbClass->__email);


        if ($agentAccount === false) {
            //agent account needs to be created now

            $userGroup = WFactory::getConfig()->getWebportalConfigurationArray();
            $userGroup = $userGroup['users']['agentsGroups'];
            $password1 = "AVeryRandomPassword-FORAAgents___DISABLEDFORNOW@__";
            $name = "{$agentDbClass->__first_name} {$agentDbClass->__middle_name} {$agentDbClass->__last_name}";

            $agentAccount = WFactory::getServices()
                ->getServiceClass(__PROPPERTY_PORTAL_USERS)
                ->createJoomlaUser($name, $agentDbClass->__email, $userGroup, 0, $agentDbClass->__email, $password1);

            if ($agentAccount['success']) {
                WFactory::getLogger()->debug("New agent account created for agent id $agentId / {$agentDbClass->__email}");
            }
            $agentAccount = $agentAccount['data'];


        }

        $success &= is_a($agentAccount, 'JUser');


        if ($agentDbClass->__is_deleted) {
            //delete / block that agent
            $blockResult = WFactory::getServices()
                ->getServiceClass(__PROPPERTY_PORTAL_USERS)
                ->blockJoomlaUser($agentDbClass->__email);

            if ($blockResult === true) {
                WFactory::getLogger()->debug("User {$agentDbClass->__email} has been disabled");
            } else
                $success &= false;

        } else {
            $unblockResult = WFactory::getServices()
                ->getServiceClass(__PROPPERTY_PORTAL_USERS)
                ->unblockJoomlaUser($agentDbClass->__email);

            if ($unblockResult === true) {
                WFactory::getLogger()->debug("User {$agentDbClass->__email} has been enabled");
            } else
                $success &= false;
        }


        return boolval($success);
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

    /**
     * @param $agentId
     * @return bool|mixed
     * @throws PortalException| when agent is already deleted
     */
    function deleteAgent($agentId)
    {
        $fromWeb = false;
        $resultArray = array(
            "success" => false,
            "message" => ""
        );

        if (is_array($agentId)) {
            $agentId = $agentId['agent-id'];
            if (intval($agentId) > 0)
                $fromWeb = true;
        }

        if (intval($agentId) > 0) {
            $agent = $this->getAgent($agentId);
            //just set it to is_deleted

            /**
             * @var $agentDbClass PortalPortalSalesSql
             */
            $agentDbClass = WFactory::getSqlService()->getDbClass(__PORTAL_PORTAL_SALES_SQL);
            $agentDbClass->bind($agent);
            $agentDbClass->__is_deleted = 1;
            $agentDbClass->__show_on_web = 0;
            $agentDbClass->__date_modified = WFactory::getSqlService()->getMySqlDateTime();
            $result = WFactory::getSqlService()->update($agentDbClass);

            $propertyService = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_PROPERTY);
            foreach ($agent->properties as $p) {
                /**
                 * @var $p PropertyListModel
                 */
                $propertyId = $p->property_id;
                $result = $result && $propertyService->deleteProperty($propertyId);
            }

            if ($result)
                WFactory::getLogger()->debug("Agent $agentId is deleted");


            if ($fromWeb) {
                $resultArray = array(
                    "success" => true,
                    "message" => "Agent {$agent->first_name} {$agent->middle_name} {$agent->last_name} is deleted !"
                );
                echo json_encode($resultArray);
                exit(0);
            }

            return $result;
        }
        WFactory::getLogger()->fatal("Delete agent called with agent id $agentId");

    }

    public function toggleAgentPublish()
    {

        $resultArray = array(
            "success" => false,
            "message" => ""
        );
        $input = JFactory::getApplication()->input;
        $publish = $input->getInt('publish', null);
        $agentId = $input->getInt('agent-id', 0);

        /**
         * @var $agentDb PortalPortalSalesSql
         */
        $agentDb = WFactory::getSqlService()->getDbClass(__PORTAL_PORTAL_SALES_SQL);
        $agentDb->__id = $agentId;
        $agentDb->loadDataFromDatabase();

        if ($publish === 0 || $publish === 1) {
            $agentDb->__show_on_web = $publish;
            $updateResult = WFactory::getSqlService()->update($agentDb);

            $resultArray['success'] = true;
            $resultArray['message'] = "$publish";


        } else {
            $resultArray['success'] = false;
            $resultArray['message'] = "Failed to read publish state from JInput";
        }

        echo json_encode($resultArray);
        exit(0);

    }

    public function updateAgentImage()
    {
        WFactory::getHelper()->isAdminOrExit();

        ///home/khan/www/softverk-webportal-generic/libraries/joomla/filesystem/file.php
        jimport('joomla.filesystem.file');
        $input = JFactory::getApplication()->input;
        $file = $input->files->get('agentImageFile');
        $agentId = $input->getInt('agent-id', 0);
        $filename = JFile::makeSafe($file['name']);
        $src = $file['tmp_name'];

        /**
         * @var $agent AgentModel
         */
        $agent = $this->getAgent($agentId, true);

        $imageFileName = $agent->image_file_name;
        $existingFileExtension = pathinfo($imageFileName, PATHINFO_EXTENSION);
        $fileNameWithoutExtension = str_replace(".$existingFileExtension", "", $imageFileName);
        if (WFactory::getHelper()->isNullOrEmptyString($fileNameWithoutExtension)) {
            $fileNameWithoutExtension = "{$agentId}_1";
        }

        $fileManager = WFactory::getFileManager();
        $commonConfig = WFactory::getConfig()->getWebportalConfigurationArray();
        /**
         * @var iFileManager
         */
        $fileManager = $fileManager->getFileManager($commonConfig["filesystem"]);


        $company = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_COMPANY)->getCompany();
        $agentImagePath = $this->getWebsendingBase()->buildAgentImagePath($company->id, '', $agent->office_id, '', $agentId, '');
        $sourceFilePath = $src;
        $destinationPath = $agentImagePath . "/image/$fileNameWithoutExtension." . pathinfo($filename, PATHINFO_EXTENSION);
        $webPathURL = "";
        $tmpResult = $fileManager->putFile($sourceFilePath, $destinationPath, $webPathURL);

        $resultArray = array(
            "success" => false,
            "message" => ""
        );


        if ($tmpResult == false || empty($webPathURL)) {
            WFactory::getLogger()->warn("Failed to upload agent image $sourceFilePath to S3");
            $resultArray['success'] = false;
            $resultArray['message'] = 'Failed to upload agent image $sourceFilePath to S3';
        } else {
            WFactory::getLogger()->debug("Uploaded agent image $sourceFilePath --> $webPathURL");

            //now update!!

            /**
             * @var $agentDb PortalPortalSalesSql
             */
            $agentDb = WFactory::getSqlService()->getDbClass(__PORTAL_PORTAL_SALES_SQL);
            $agentDb->__id = $agentId;
            $agentDb->loadDataFromDatabase();
            $agentDb->__image_file_path = $webPathURL;
            $updateResult = WFactory::getSqlService()->update($agentDb);

            $resultArray['success'] = true;
            $resultArray['message'] = $webPathURL;

        }

        echo json_encode($resultArray);
        exit(0);


    }

    /**
     * @return AgentModel
     */
    public function getAgentModel()
    {
        require_once "agentModel.php";
        return new AgentModel();
    }


    /**
     *
     * @param $agentModel AgentModel
     * @return mixed
     */
    public function updateAgent($agentModel)
    {

        if (get_class($agentModel) === 'stdClass') {
            $agentModelTemp = $this->getAgentModel();
            $addressModelTemp = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_ADDRESS)->getPropertyAddressModel();
            $marketingModelTemp = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_MARKETINGINFO)->getMarketingInfoModel();
            /**
             * @var $agentModel AgentModel
             */
            $agentModel = WFactory::getHelper()->castObject($agentModelTemp, $agentModel);

            if (get_class($agentModel->address) === 'stdClass') {
                $agentModel->address = WFactory::getHelper()->castObject($addressModelTemp, $agentModel->address);
            }
            if (get_class($agentModel->marketing_info) === 'stdClass') {
                $agentModel->marketing_info = WFactory::getHelper()->castObject($marketingModelTemp, $agentModel->marketing_info);
            }

        }

        /**
         * @var $agentDbClass PortalPortalSalesSql
         */
        $agentDbClass = WFactory::getSqlService()->getDbClass(__PORTAL_PORTAL_SALES_SQL);

        $agentDbClass = $agentModel->reverseBindToDb($agentDbClass);
        $updateResult = true;
//        if ($agentModel->address !== null) { // address is never shown..so why bother !
//            //update address
//            /**
//             * @var $addressDbClass PortalPortalPropertyAddressesSql
//             */
//            $addressDbClass = WFactory::getServices()->getSqlService()->getDbClass(__PORTAL_PORTAL_PROPERTY_ADDRESSES_SQL);
//            $addressDbClass = $agentModel->address->reverseBindToDb($addressDbClass);
//            $updateResult &= WFactory::getSqlService()->update($addressDbClass);
//        }
        if ($agentModel->marketing_info !== null) {
            //update address
            /**
             * @var $markeingInfoDbClass PortalPortalMarketingInfoSql
             */
            $markeingInfoDbClass = WFactory::getServices()->getSqlService()->getDbClass(__PORTAL_PORTAL_MARKETING_INFO_SQL);
            $markeingInfoDbClass = $agentModel->marketing_info->reverseBindToDb($markeingInfoDbClass);
            $updateResult &= WFactory::getSqlService()->update($markeingInfoDbClass);
        }


        $updateResult &= WFactory::getSqlService()->update($agentDbClass);

        //now update ALL properties of that agent

        WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_PROPERTY)->updatePropertyAgentInformation($agentDbClass);


        $resultArray = array(
            "success" => $updateResult ? true : false,
            "message" => "Update complete.."
        );

        echo json_encode($resultArray);
        exit(0);

    }

}