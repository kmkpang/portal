<?php

/**
 * Created by JetBrains PhpStorm.
 * User: khan
 * Date: 3/12/13
 * Time: 2:42 PM
 * To change this template use File | Settings | File Templates.
 */
class MarketinginfoService
{

    function getMarketingInfoModel()
    {
        require_once "marketingInfoModel.php";
        return new MarketingInfoModel();
    }

    function getMarketingInfoTypeIdFromMarketingInfoType($marketingInfoType)
    {

        if (strtoupper($marketingInfoType) === "AGENT")
            $marketingInfoType = "SALE";

        $query = "SELECT jos_portal_marketing_info_type.id
                      FROM jos_portal_marketing_info_type jos_portal_marketing_info_type
                     WHERE (jos_portal_marketing_info_type.description = '$marketingInfoType')";


        $result = WFactory::getServices()->getSqlService()->select($query);
        $result = $result[0]['id'];

        return $result;
    }

    /**
     * @param $infoType | Int or String
     * @param $referenceId
     * @return array
     */
    function getMarketingInfo($infoType, $referenceId)
    {

        if (!is_numeric($infoType)) {
            $infoType = $this->getMarketingInfoTypeIdFromMarketingInfoType($infoType);
        }

        /**
         * @var $marketingInfoDbClass PortalPortalMarketingInfoSql
         */
        $marketingInfoDbClass = WFactory::getSqlService()->getDbClass(__PORTAL_PORTAL_MARKETING_INFO_SQL);
        $marketingInfoDbClass->__reference_id = $referenceId;
        $marketingInfoDbClass->__marketing_info_type_id = $infoType;

        $marketingInfoDbClass->loadDataFromDatabase();

        return $marketingInfoDbClass->unbind();
    }

}
