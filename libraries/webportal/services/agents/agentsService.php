<?php

/**
 * Created by PhpStorm.
 * User: khan
 * Date: 5/19/14
 * Time: 1:21 PM
 */

require_once JPATH_LIBRARY_WEBPORTAL_SERVICES . DS . 'agents' . DS . 'agentsModel.php';

class AgentsService
{

    function getAgentsModel()
    {
        return new AgentsModel();
    }


    function getAgentsList($officeId = null)
    {
        if (is_array($officeId)) {
            $officeId = $officeId['officeId'];
        }


        $conditions = array();

        $query = "select #__portal_sales.* , #__portal_offices.office_name 
          FROM #__portal_sales
       INNER JOIN #__portal_offices
          ON (#__portal_sales.office_id = #__portal_offices.id)";

        if ($officeId !== null) {
            $conditions[] = "#__portal_sales.office_id=$officeId";
        }
        $conditions[] = "#__portal_sales.first_name NOT LIKE '%\\_%'";
        $conditions[] = "#__portal_sales.first_name NOT LIKE '%DATA%'";
        $conditions[] = "#__portal_sales.is_deleted=0";
        $conditions[] = "#__portal_sales.show_on_web=1";

        $conditions = implode(" AND ", $conditions);
        $query .= " where $conditions order by #__portal_sales.first_name";

        $result = WFactory::getSqlService()->select($query);

        if ($result === false) {
            WFactory::getLogger()->warn("Failed to find agents with office id $officeId");
            return null;
        }

        $returnResult = array();
        foreach ($result as $r) {
            /**
             * @var $agentDbClass PortalPortalSalesSql
             */
            $agentDbClass = WFactory::getSqlService()->getDbClass(__PORTAL_PORTAL_SALES_SQL);
            $agentDbClass->bind($r);
            $agentModel = new AgentsModel();
            $agentModel->bindToDb($agentDbClass);
            //WFactory::getHelper()->printArrayAsClassToGenerateModel($agent);

            //get his marketing info
            $marketingInfo = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_MARKETINGINFO)->getMarketingInfo('Agent', $agentModel->id);
            $agentModel->marketing_info = $marketingInfo;
            $agentModel->office_name = $r['office_name'];

            $returnResult[] = $agentModel;
        }

        return $returnResult;
    }

    /**
     * @param $officeId
     * @return array(AgentModel)
     */
    function getAgents($officeId = null, $getAsModel = false)
    {
        if (is_array($officeId)) {
            $officeId = $officeId['officeId'];
        }


        //marketing info
        $marketingInfoTypeId = WFactory::getServices()
            ->getSqlService()
            ->getSqlServiceClass(__PROPPERTY_PORTAL_MARKETINGINFO)
            ->getMarketingInfoTypeIdFromMarketingInfoType("Agent");

        $conditions = array();

        $query = "select * from #__portal_sales";

        if ($officeId !== null) {
            $conditions[] = "office_id=$officeId";
        }
        $conditions[] = "show_on_web=1";
        $conditions[] = "is_deleted=0";
        $conditions = implode(" AND ", $conditions);
        $query .= " where $conditions order by first_name";

        $result = WFactory::getSqlService()->select($query);

        if ($result === false) {
            WFactory::getLogger()->warn("Failed to find agents with office id $officeId");
            return null;
        }

        $returnResult = array();
        foreach ($result as $r) {
            /**
             * @var $agentDbClass PortalPortalSalesSql
             */
            $agentDbClass = WFactory::getSqlService()->getDbClass(__PORTAL_PORTAL_SALES_SQL);
            $agentDbClass->bind($r);
            $agentModel = new AgentsModel();
            $agentModel->bindToDb($agentDbClass);
            //WFactory::getHelper()->printArrayAsClassToGenerateModel($agent);

            //get his marketing info
            $marketingInfo = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_MARKETINGINFO)->getMarketingInfo('Agent', $agentModel->id);
            $agentModel->marketing_info = $marketingInfo;
            //get his address
            $address = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_ADDRESS)->getAddress($agentModel->address_id);
            $agentModel->address = $address;

            //and his office name!
            /**
             * @var $officeDbClass PortalPortalOfficesSql
             */
            $officeDbClass = WFactory::getSqlService()->getDbClass(__PORTAL_PORTAL_OFFICES_SQL);
            $officeDbClass->__id = $agentModel->office_id;
            $officeDbClass->loadDataFromDatabase();
            $agentModel->office_name = $officeDbClass->__office_name;


            if ($getAsModel) {
                //addressModel

                $addressModel = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_ADDRESS)->getPropertyAddressModel();
                $addressModel->bindToDb($agentModel->address);
                $agentModel->address = $addressModel;
                $agentModel->full_name = "{$agentModel->first_name} {$agentModel->last_name}";

                //marketing model
                $marketingModel = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_MARKETINGINFO)->getMarketingInfoModel();
                $marketingModel->bindToDb($agentModel->marketing_info);
                $agentModel->marketing_info = $marketingModel;
            }


            $returnResult[] = $agentModel;
        }

        return $returnResult;
    }

    function getSearchAgents($first_last_phone = null, $officeId = null, $regional_id = null, $getAsModel = false)
    {

        if (is_array($officeId)) {
            $officeId = $officeId['officeId'];
        }


        //marketing info
        $marketingInfoTypeId = WFactory::getServices()
            ->getSqlService()
            ->getSqlServiceClass(__PROPPERTY_PORTAL_MARKETINGINFO)
            ->getMarketingInfoTypeIdFromMarketingInfoType("Agent");

        $conditions = array();

        $query = "select * from #__portal_sales";


        /* if (!empty($first_name)) {
             $conditions[] = "first_name like '%$first_name%'";
         }
         if (!empty($last_name)) {
             $conditions[] = "last_name like '%$last_name%'";
         }*/
        if (!empty($first_last_phone)) {
            $search_text = str_replace('-', '', $first_last_phone);
            if (!is_numeric($search_text)) {
                $conditions[] = "(first_name like '%$first_last_phone%' or last_name like '%$first_last_phone%')";
            } elseif (is_numeric($search_text)) {
                $conditions[] = "(REPLACE(phone,'-','') like '%$search_text%' or  REPLACE(mobile,'-','') like '%$search_text%')";
            }
        }
        if (!empty($officeId)) {
            $conditions[] = "office_id=$officeId";
        }
        if (!empty($regional_id)) {
            $conditions[] = "office_id=$regional_id";
        }
        $conditions[] = "show_on_web=1";
        $conditions[] = "is_deleted=0";
        $conditions = implode(" AND ", $conditions);
        $query .= " where $conditions order by first_name";

        $result = WFactory::getSqlService()->select($query);
        if ($result === false) {
            WFactory::getLogger()->warn("Failed to find agents with office id $officeId");
            return null;
        }

        $returnResult = array();
        foreach ($result as $r) {
            /**
             * @var $agentDbClass PortalPortalSalesSql
             */
            $agentDbClass = WFactory::getSqlService()->getDbClass(__PORTAL_PORTAL_SALES_SQL);
            $agentDbClass->bind($r);
            $agentModel = new AgentsModel();
            $agentModel->bindToDb($agentDbClass);
            //WFactory::getHelper()->printArrayAsClassToGenerateModel($agent);

            //get his marketing info
            $marketingInfo = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_MARKETINGINFO)->getMarketingInfo('Agent', $agentModel->id);
            $agentModel->marketing_info = $marketingInfo;
            //get his address
            $address = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_ADDRESS)->getAddress($agentModel->address_id);
            $agentModel->address = $address;

            //and his office name!
            /**
             * @var $officeDbClass PortalPortalOfficesSql
             */
            $officeDbClass = WFactory::getSqlService()->getDbClass(__PORTAL_PORTAL_OFFICES_SQL);
            $officeDbClass->__id = $agentModel->office_id;
            $officeDbClass->loadDataFromDatabase();
            $agentModel->office_name = $officeDbClass->__office_name;

            if ($getAsModel) {
                //addressModel

                $addressModel = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_ADDRESS)->getPropertyAddressModel();
                $addressModel->bindToDb($agentModel->address);
                $agentModel->address = $addressModel;
                $agentModel->full_name = "{$agentModel->first_name} {$agentModel->last_name}";

                //marketing model
                $marketingModel = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_MARKETINGINFO)->getMarketingInfoModel();
                $marketingModel->bindToDb($agentModel->marketing_info);
                $agentModel->marketing_info = $marketingModel;
            }


            $returnResult[] = $agentModel;
        }

        return $returnResult;
    }

    /**
     * @Deprecated
     * @return array
     */
    public function getAgentsAll()
    {
//        /**
//         * @var $agentDbClass PortalPortalSalesSql
//         */
//        $agentDbClass = WFactory::getSqlService()->getDbClass(__PORTAL_PORTAL_SALES_SQL);
//        $agentDbClass->__is_deleted = 0;
//        $agentDbClass->__show_on_web = 1;
//        $agents = $agentDbClass->loadDataFromDatabase(false);
        $query = "select * from #__portal_sales where is_deleted = 0 and show_on_web = 1 order by first_name asc";
        $agents = WFactory::getSqlService()->select($query);

        /**
         * @var $officeDbClass PortalPortalOfficesSql
         */
        $officeDbClass = WFactory::getSqlService()->getDbClass(__PORTAL_PORTAL_OFFICES_SQL);
        $allOffices = $officeDbClass->loadDataFromDatabase(false);
        $offices = array();
        foreach ($allOffices as $o) {
            /**
             * @var $o PortalPortalOfficesSql
             */
            $offices[$o->__id] = $o->__office_name;
        }

        foreach ($agents as &$a) {
            $agentsModel = $this->getAgentsModel();
            $agentsModel->bindToDb($a);

            //get his marketing info
            $marketingInfo = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_MARKETINGINFO)->getMarketingInfo('Agent', $agentsModel->id);
            $agentsModel->marketing_info = $marketingInfo;
            //get his address
            $address = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_ADDRESS)->getAddress($agentsModel->address_id);
            $agentsModel->address = $address;

            $agentsModel->office_name = $offices[$agentsModel->office_id];

            $a = $agentsModel;
        }

        //var_dump($agents);
        return $agents;
    }


    /**
     * just a test function to check route online..
     * @param $id
     */
    public function getRoute($id)
    {
        echo JRoute::_("index.php?option=com_webportal&view=agents&agent_id=$id");
        exit();
    }


    public function getJRouteFormattedAgentName($agentId)
    {
        //echo $agentId;
        $agentNames = $this->getSanitizedSalesNames();
        //print_r($agentNames);
        $search_array = array_search($agentId, $agentNames);
        return $search_array;
    }

    public function checkIfRouteIsAgent($name)
    {
        $name = str_replace(":", "-", $name);
        $name = strtolower(WFactory::getHelper()->sanitizeName($name));
        $salesNames = $this->getSanitizedSalesNames();
        //print_r($salesNames);
        if (array_key_exists($name, $salesNames))
            $result = $salesNames[$name];
        //may be there is a number at the end???


        //$result = null;
        return $result;
    }


    private function getSanitizedSalesNames()
    {

        /** @var JCacheController $cache */
        $cache = JFactory::getCache('AgentsService', '');


        $cache_id = 'sanitized_sales_names';
        //print_r($cache_id);

        if (!$names = $cache->get($cache_id)) {

            WFactory::getLogger()->warn("Cache miss on $cache_id");

            $query = "SELECT #__portal_sales.first_name,
                       #__portal_sales.middle_name,
                       #__portal_sales.last_name,
                       #__portal_sales.id
                  FROM #__portal_sales
                  WHERE #__portal_sales.is_deleted = 0  ";

            $saless = WFactory::getSqlService()->select($query);
            $names = array();
            foreach ($saless as $sales) {
                $name = $sales['first_name'] . ' ' . $sales['middle_name'] . ' ' . $sales['last_name'];
                $salesName = strtolower(WFactory::getHelper()->sanitizeName($name));
                if (array_key_exists($salesName, $names)) {
                    $salesName = "$salesName{$sales['id']}"; // conflicting names !
                }
                $names[$salesName] = $sales['id'];
            }

            $result = $cache->store($names, $cache_id);
            if (!$result)
                WFactory::getLogger()->warn("Failed to save cache for $cache_id ; Cache NOT ENABLED?");
        }
        ksort($names);
        //print_r($names);
        return $names;

    }

    function getAgentsLocation($officeId = null)
    {
        /** @var  $agentsSagaService SagaService */
        $agentsSagaService = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_SAGA);

        $agentsFromSaga = $agentsSagaService->getSagaAgentsCheckinList();

        $agentIdCondition = [];
        $agentsOrdered=[];
        foreach ($agentsFromSaga as $anAgent) {
            $agentIdCondition[] = "#__portal_sales.unique_id = '{$anAgent->user_id}'";
            $agentsOrdered[$anAgent->user_id]=$anAgent;
        }
        $agentIdCondition = "( " . implode(" OR ", $agentIdCondition) . ")";

        $query = "SELECT 
                        #__portal_sales.first_name,
                       #__portal_sales.middle_name,
                       #__portal_sales.last_name,
                       #__portal_sales.email,
                       #__portal_sales.mobile,
                       #__portal_offices.office_name,
                       #__portal_offices.phone,
                       #__portal_sales.id,
                        #__portal_sales.unique_id,
                       #__portal_sales.image_file_path              
                  FROM #__portal_sales 
                  JOIN #__portal_offices ON #__portal_sales.office_id = #__portal_offices.id
                  WHERE ( $agentIdCondition )
                  ";
//                  AND #__portal_sales.office_id = $officeId  ";

        $agents = WFactory::getSqlService()->select($query);

        // ===== start hard code
        foreach ($agents as $key => $value) {
           /* $random1 = rand(13568, 13747) / 1000;
            $random2 = rand(1004404, 1007504) / 10000;
            $agents[$key]['latitude'] = $random1;
            $agents[$key]['longitude'] = $random2;*/
                $uniqueId = $value["unique_id"];
            $agents[$key]['latitude'] = $agentsOrdered[$uniqueId]->latitude;
            $agents[$key]['longitude'] = $agentsOrdered[$uniqueId]->longitude;
            $agents[$key]['timestamp'] = str_replace("T"," ", $agentsOrdered[$uniqueId]->checkin_time);
            $agents[$key]['note'] = $agentsOrdered[$uniqueId]->note;

        }
        // ===== end hard code

        return $agents;

    }


}