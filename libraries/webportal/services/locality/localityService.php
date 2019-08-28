<?php

/**
 * Created by PhpStorm.
 * User: khan
 * Date: 1/2/16
 * Time: 11:31 PM
 */
class LocalityService
{


    public function getLocalAttractions($propertyId)
    {

        if (is_array($propertyId))
            $propertyId = $propertyId['propertyId'];

        //first get property lat lang

        $address = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_ADDRESS)->getAddressByPropertyId($propertyId);

        $whereClause = "";
        $selectClause = "";
        WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_SEARCH)->createLatLangSearchCondition(
            $address['latitude'],
            $address['longitude'],
            1.2,
            $selectClause,
            $whereClause);


        $query = "select *, $selectClause from jos_portal_locality having $whereClause ";
       //   $query = "select *, $selectClause from jos_portal_locality";
        $result = WFactory::getSqlService()->select($query);


        //format result now

        $formattedResults = array();

        foreach ($result as $r) {
            $type = strtoupper($r['type']);
            if (!array_key_exists($type, $formattedResults)) {
                $formattedResults[$type] = array();
            }
            $formattedResults[$type][] = $r;
        }

        foreach ($formattedResults as $key=>$val) {

            $formattedResults[$key] = WFactory::getHelper()->sortArray($val, "distance", "asc");

        }

        return $formattedResults;
        //

    }


}