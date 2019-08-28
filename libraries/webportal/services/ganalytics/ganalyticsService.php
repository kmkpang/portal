<?php

/**
 * Created by PhpStorm.
 * User: khan
 * Date: 8/16/14
 * Time: 6:38 PM
 */
class GanalyticsService
{

    private $analyticPage;

    public function setAnalyticPage($page)
    {
        $this->analyticPage = $page;
    }

    public function getAnalyticPage()
    {
        if ($this->analyticPage === null)
            return false;
        return $this->analyticPage;
    }

    public function getDefaultAnalyticId()
    {
        $analytics = WFactory::getConfig()->getWebportalConfigurationArray()['analytics'];
        foreach ($analytics as $a) {
            if ($a['name'] == 'DEFAULT')
                return $a['code'];
        }
    }


    function updateAllPropertyViewCount()
    {

        WFactory::getLogger()->debug("updateAllPropertyViewCount Called");
        $viewCount = $this->getPropertyViewCount();

        /**
         * @var $propertySql PortalPortalPropertiesSql
         */
        $propertySql = WFactory::getSqlService()->getDbClass(__PORTAL_PORTAL_PROPERTIES_SQL);


        $result = true;
        $resultcount = 0;
        foreach ($viewCount as $id => $view) {
            $propertySql->__id = $id;
            $propertySql->__google_viewcount = $view;

            $resulttemp = WFactory::getSqlService()->update($propertySql);
            if ($resulttemp) {
                $resultcount++;
                WFactory::getLogger()->debug("updated propertyid $id , current view : $view");
            }
            $result &= $resulttemp;
        }

        WFactory::getLogger()->debug("Updated $resultcount properties..");

        return $result;
    }

    function getPropertyViewCount($propertyId = null)
    {
        $propertyViews = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_GAPI)->getPageViewCount('/property/\d*');
        $propertyViewCount = array();
        foreach ($propertyViews as $i) {
            $tempPropertyId = $i[0];
            preg_match_all('/\/\d+/', $tempPropertyId, $matches);
            $tempPropertyId = str_replace("/", "", $matches[0][0]);

            $view = $i[1];

            if (array_key_exists($tempPropertyId, $propertyViewCount)) {
                $propertyViewCount[$tempPropertyId] = $propertyViewCount[$tempPropertyId] + $view;
            } else
                $propertyViewCount[$tempPropertyId] = intval($view);

        }

        if ($propertyId == null)
            return $propertyViewCount;
        return array_key_exists($propertyId, $propertyViewCount) ? $propertyViewCount[$propertyId] : 0;

    }


}