<?php

/**
 * Created by JetBrains PhpStorm.
 * User: khan
 * Date: 7/2/13
 * Time: 7:51 PM
 * To change this template use File | Settings | File Templates.
 */
class CompanyService
{

    /**
     * @var PortalPortalCompaniesSql
     */
    var $dbClass;

    public function __construct()
    {
        //Thou shalt not construct that which is unconstructable!
        $this->dbClass = WFactory::getServices()->getSqlService()->getDbClass(__PORTAL_PORTAL_COMPANIES_SQL);

    }

    /**
     * @return CompanyModel
     */
    public function getCompanyModel()
    {
        require_once JPATH_LIBRARY_WEBPORTAL_SERVICES . DS . 'company' . DS . 'companyModel.php';

        return new CompanyModel();
    }

    /**
     * if $companyId is NULL, it will return default company defined in ['websending']['companyId'] configuration
     * @param null $companyId
     * @return CompanyModel
     */
    public function getCompany($companyId = null)
    {
        if ($companyId == null) {
            $companyId = WFactory::getConfig()->getWebportalConfigurationArray()['websending']['companyId'];
        }


        $this->dbClass->__id = $companyId;
        $this->dbClass->loadDataFromDatabase();

        $company = $this->dbClass->unbind();

        $companyModel = $this->getCompanyModel();
        $companyModel->bindToDb($company);

        return $companyModel;
    }

    /**
     * @param null $companyId
     * @return string
     */
    public function getCompanyEmail($companyId = null)
    {
        return $this->getCompany($companyId)->email;
    }

    public function updateCompany($companyModel)
    {
        if (get_class($companyModel) === 'stdClass') {
            $companyModelTemp = $this->getCompanyModel();
            $addressModelTemp = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_ADDRESS)->getPropertyAddressModel();
            $marketingModelTemp = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_MARKETINGINFO)->getMarketingInfoModel();
            /**
             * @var $companyModel CompanyModel
             */
            $companyModel = WFactory::getHelper()->castObject($companyModelTemp, $companyModel);

            if (get_class($companyModel->address) === 'stdClass') {
                $companyModel->address = WFactory::getHelper()->castObject($addressModelTemp, $companyModel->address);
            }
            if (get_class($companyModel->marketingInfo) === 'stdClass') {
                $companyModel->marketingInfo = WFactory::getHelper()->castObject($marketingModelTemp, $companyModel->marketingInfo);
            }

        }
        /**
         * @var $companyDbClass PortalPortalCompaniesSql
         */
        $companyDbClass = WFactory::getSqlService()->getDbClass(__PORTAL_PORTAL_COMPANIES_SQL);

        $companyDbClass = $companyModel->reverseBindToDb($companyDbClass);
        $updateResult = true;
        if ($companyModel->address !== null) {
            //update address
            /**
             * @var $addressDbClass PortalPortalPropertyAddressesSql
             */
            $addressDbClass = WFactory::getServices()->getSqlService()->getDbClass(__PORTAL_PORTAL_PROPERTY_ADDRESSES_SQL);
            $addressDbClass = $companyModel->address->reverseBindToDb($addressDbClass);
            $updateResult &= WFactory::getSqlService()->update($addressDbClass);
        }
        if ($companyModel->marketingInfo !== null) {
            //update address
            /**
             * @var $markeingInfoDbClass PortalPortalMarketingInfoSql
             */
            $markeingInfoDbClass = WFactory::getServices()->getSqlService()->getDbClass(__PORTAL_PORTAL_MARKETING_INFO_SQL);
            $markeingInfoDbClass = $companyModel->marketingInfo->reverseBindToDb($markeingInfoDbClass);
            $updateResult &= WFactory::getSqlService()->update($markeingInfoDbClass);
        }

        $companyDbClass->__date_modified = WFactory::getSqlService()->getMySqlDateTime();
        $updateResult &= WFactory::getSqlService()->update($companyDbClass);

        //update ALL company related shit in properties table

        WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_PROPERTY)->updatePropertyCompanyInformation($companyDbClass);

        $resultArray = array(
            "success" => $updateResult ? true : false,
            "message" => "Update complete.."
        );

        echo json_encode($resultArray);
        exit(0);

    }


}