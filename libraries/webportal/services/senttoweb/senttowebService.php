<?php
/**
 * Created by PhpStorm.
 * User: khan
 * Date: 8/20/15
 * Time: 10:04 PM
 */

require_once 'senttowebModel.php';


class SenttowebService
{

    /**
     * @return SenttowebModel
     */
    public function getSenttowebModel()
    {
        return new SenttowebModel();
    }

    public function getXml($id)
    {
        WFactory::getHelper()->isAdminOrExit();

        /**
         * @var $sent2webModel PortalPortalSenttowebLogSql
         */
        $sent2webModel = WFactory::getSqlService()->getDbClass(__PORTAL_PORTAL_SENTTOWEB_LOG_SQL);
        $sent2webModel->__id = $id;
        $sent2webModel->loadDataFromDatabase();
        $data = trim($sent2webModel->__data);

        try {
            $xml = new SimpleXMLElement($data);
            $domxml = new DOMDocument('1.0');
            $domxml->preserveWhiteSpace = false;
            $domxml->formatOutput = true;
            /* @var $xml SimpleXMLElement */
            $domxml->loadXML($xml->asXML());

            return $domxml->saveXML();
        } catch (Exception $e) {
              return $data;
//            ob_clean();
//            //do a direct download instead
//            $filename = JPATH_ROOT . "/tmp/" . uniqid() . ".txt";
//            file_put_contents($filename, $data);
//            header('Content-Description: File Transfer');
//            header('Content-Type: application/octet-stream');
//            header('Content-Disposition: attachment; filename="' . basename("invalidxml-{$id}.txt") . '"');
//            header('Expires: 0');
//            header('Cache-Control: must-revalidate');
//            header('Pragma: public');
//            header('Content-Length: ' . filesize($filename));
//
//
//            readfile($filename);
//
//            exit(0);


        }

    }

    public function resendSentToWebXml($xmlId)
    {

        WFactory::getHelper()->isAdminOrExit();

        $query = "SELECT * FROM `jos_portal_senttoweb_log` where id=$xmlId";
        $xml = WFactory::getSqlService()->select($query);
        $xml = $xml[0];

        $xmlData = $xml['data'];
        $postUrl = JUri::root() . "index.php?option=com_webportal&controller=senttoweb&task=service";
        $curl = WFactory::getHelper()->getCurl($postUrl, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $xmlData);
        $result = curl_exec($curl);

        WFactory::getLogger()->debug("Gotten : $result");

        if (!$result) {
            $msg = curl_error($curl);
            WFactory::getLogger()->error("$msg");
        }

        echo $result;
        exit;

    }

    /**
     * @var $searchModel SenttowebModel
     * @return array
     */
    public function searchSendToWebXml($searchModel)
    {

        WFactory::getHelper()->isAdminOrExit();


        $sendXmlClass = false;
        $selectStatement = "SELECT * FROM  #__portal_senttoweb_log";
        $andConditions = array();

        //actually turn it into an actual ID!


        if (!WFactory::getHelper()->isNullOrEmptyString($searchModel->propertyUniqueId)) {

            if (is_numeric($searchModel->propertyUniqueId) && intval($searchModel->propertyUniqueId) > 0) {
                $andConditions[] = " associated_id = {$searchModel->propertyUniqueId}  ";
            } else {
                $q = "select id from jos_portal_properties where unique_id='{$searchModel->propertyUniqueId}'";
                $r = WFactory::getSqlService()->select($q);
                if (!empty($r)) {
                    $associated_id = $r[0]['id'];
                    $andConditions[] = " ( data LIKE  '%{$searchModel->propertyUniqueId}%' OR associated_id = $associated_id ) ";
                } else
                    $andConditions[] = "data LIKE  '%{$searchModel->propertyUniqueId}%'";
            }
        }
        if (!WFactory::getHelper()->isNullOrEmptyString($searchModel->agentUniqueId)) {
            if (is_numeric($searchModel->agentUniqueId) && intval($searchModel->agentUniqueId) > 0) {
                $andConditions[] = " associated_id = {$searchModel->agentUniqueId}  ";
            } else {
                $q = "select id from jos_portal_sales where unique_id='{$searchModel->agentUniqueId}'";
                $r = WFactory::getSqlService()->select($q);
                if (!empty($r)) {
                    $associated_id = $r[0]['id'];
                    $andConditions[] = " ( data LIKE  '%{$searchModel->agentUniqueId}%' OR associated_id = $associated_id ) ";
                } else
                    $andConditions[] = "data LIKE  '%{$searchModel->agentUniqueId}%'";
            }
        }
        if (!WFactory::getHelper()->isNullOrEmptyString($searchModel->officeUniqueId)) {
            if (is_numeric($searchModel->officeUniqueId) && intval($searchModel->officeUniqueId) > 0) {
                $andConditions[] = " associated_id = {$searchModel->officeUniqueId} ";
            } else {
                $q = "select id from jos_portal_offices where unique_id='{$searchModel->officeUniqueId}'";
                $r = WFactory::getSqlService()->select($q);
                if (!empty($r)) {
                    $associated_id = $r[0]['id'];
                    $andConditions[] = " ( data LIKE  '%{$searchModel->officeUniqueId}%' OR associated_id = $associated_id ) ";
                } else
                    $andConditions[] = "data LIKE  '%{$searchModel->officeUniqueId}%'";
            }
        }
        if (!WFactory::getHelper()->isNullOrEmptyString($searchModel->type)) {
            $andConditions[] = "type LIKE  '{$searchModel->type}'";
        }
        if (!WFactory::getHelper()->isNullOrEmptyString($searchModel->command)) {
            $andConditions[] = "command LIKE  '{$searchModel->command}'";
        }
        if (!WFactory::getHelper()->isNullOrEmptyString($searchModel->date)) {
            $andConditions[] = "date >  '{$searchModel->date}'";
        }
        if (!WFactory::getHelper()->isNullOrEmptyString($searchModel->direction)) {
            $myself = $this->__getMyself();

            if ($searchModel->direction == DIRECTION_OUTGOING) {

//                foreach ($myself as &$m) {
//                    $m = " fromip like '$m'";
//                }
//                $c = implode(' OR ', $myself);
//                $andConditions[] = " ( $c )";

                $andConditions[] = "direction LIKE 'OUTGOING' ";
            }

            if ($searchModel->direction == DIRECTION_INCOMING) {

//                foreach ($myself as &$m) {
//                    $m = " fromip NOT like '$m'";
//                }
//                $c = implode(' AND ', $myself);
//                $andConditions[] = " ( $c )";
                $andConditions[] = "direction LIKE 'INCOMING' ";
            }


        }
        $where = '';
        if (!empty($andConditions))
            $where = ' WHERE ' . implode(' AND ', $andConditions);


        $query = "$selectStatement  $where  ORDER BY date DESC LIMIT 50";

        $sent2webLogs = WFactory::getSqlService()->select($query);

        //now format the result

        $returnArray = array();

        foreach ($sent2webLogs as $s) {
            /**
             * @var $sent2webModel PortalPortalSenttowebLogSql
             */
            $sent2webModel = WFactory::getSqlService()->getDbClass(__PORTAL_PORTAL_SENTTOWEB_LOG_SQL);
            $sent2webModel->bind($s);

            $direction = $this->__getDirection($sent2webModel);

            $m = $this->getSenttowebModel();

            $m->id = $sent2webModel->__id;


            $m->date = $sent2webModel->__date;
            if ($sendXmlClass) {
                $m->data = base64_encode($sent2webModel->__data);
            }
            $m->rawSqlClass = $sent2webModel;
            $m->type = $this->__getType($sent2webModel);
            $m->direction = $direction;
            $m->command = $this->__getCommand($sent2webModel);
            if (intval($sent2webModel->__associated_id) > 0) {
                $m->officeAgentPropertyId = $sent2webModel->__associated_id;
                $m->officeAgentPropertyLink = $this->__getLinkToAgentOfficeProperty($sent2webModel);
            } else
                $m->officeAgentPropertyId = 0;

            if ($searchModel->getAssociated) {
                if ($direction == DIRECTION_OUTGOING) {
                    $m->rawAssociatedSqlClass = $this->__getIncomingMsg($sent2webModel);
                } else {
                    $m->rawAssociatedSqlClass = $this->__getOutgoingMsg($sent2webModel);
                }

                $m->associatedSentToWeb = $this->getSenttowebModel();
                $m->associatedSentToWeb->id = $m->rawAssociatedSqlClass->__id;
                $m->associatedSentToWeb->date = $m->rawAssociatedSqlClass->__date;
                if ($sendXmlClass) {
                    $m->associatedSentToWeb->data = base64_encode($m->rawAssociatedSqlClass->__data);
                }
                $m->associatedSentToWeb->rawSqlClass = $m->rawAssociatedSqlClass;
                $m->associatedSentToWeb->type = $this->__getType($m->rawAssociatedSqlClass);
                $m->associatedSentToWeb->direction = $this->__getDirection($m->rawAssociatedSqlClass);
                $m->associatedSentToWeb->command = $this->__getCommand($m->rawAssociatedSqlClass);

                if (intval($m->rawAssociatedSqlClass->__associated_id) > 0) {
                    $m->associatedSentToWeb->officeAgentPropertyId = $m->rawAssociatedSqlClass->__associated_id;
                    $m->associatedSentToWeb->officeAgentPropertyLink = $this->__getLinkToAgentOfficeProperty($m->rawAssociatedSqlClass);
                } else
                    $m->associatedSentToWeb->officeAgentPropertyId = 0;


                if (!WFactory::getHelper()->isNullOrEmptyString($m->associatedSentToWeb->date))
                    $returnArray[intval($m->associatedSentToWeb->id)] = $m->associatedSentToWeb;
            }

            if (WFactory::getHelper()->isNullOrEmptyString($m->type) || WFactory::getHelper()->isNullOrEmptyString($m->command)) {
                $m->type = "XML PARSE ERROR";
                $m->command = "XML PARSE ERROR";
            }
            if ($m->id == 88645) {
                $break = 1;
            }
            if (!array_key_exists(intval($m->id), $returnArray))
                $returnArray[intval($m->id)] = $m;


        }

        ksort($returnArray, SORT_NUMERIC);

        $returnArray = array_reverse($returnArray);

        return $returnArray;
    }

    /**
     * @param $send2webModel PortalPortalSenttowebLogSql
     * @return string
     */
    private function __getLinkToAgentOfficeProperty($send2webModel)
    {
        $type = strtoupper($send2webModel->__type);
        if ($type === SENTTOWEB_PROPERTY) {
            $link = JRoute::_("index.php?option=com_webportal&view=property&property-id={$send2webModel->__associated_id}");
        }
        if ($type === SENTTOWEB_AGENT) {
            $link = JRoute::_("index.php?option=com_webportal&view=agent&agent_id={$send2webModel->__associated_id}");
        }
        if ($type === SENTTOWEB_OFFICE) {
            $link = JRoute::_("index.php?option=com_webportal&view=office&office_id={$send2webModel->__associated_id}");
        }

        $link = explode('/', $link);
        return JUri::root() . $link[count($link) - 1];
    }

    /**
     * @param $s PortalPortalSenttowebLogSql
     * @return string
     */
    private function __getDirection($s)
    {

        if (!WFactory::getHelper()->isNullOrEmptyString($s->__direction)) {
            return strtoupper($s->__direction);
        }

        $myself = $this->__getMyself();

        if (in_array($s->__fromip, $myself))
            return DIRECTION_OUTGOING;
        else
            return DIRECTION_INCOMING;

    }

    /**
     * @return array
     */
    private function __getMyself()
    {
        $configuration = WFactory::getConfig()->getWebportalConfigurationArray();
        $myself = $configuration['websending']['myself'];
        if ($myself == null) { // incase i forget to put that in !
            $myself = array( // used in searching xml in backend
                "127.0.0.1",
                "www.remax.co.th",
                "54.255.167.23"
            );
        }
        return $myself;
    }

    /**
     * @param $s PortalPortalSenttowebLogSql
     * @return string
     */
    private function __getType($s)
    {
        if (strtoupper(trim($s->__type)) === 'OFFICE') {
            return SENTTOWEB_OFFICE;
        }
        if (strtoupper(trim($s->__type)) === 'AGENT') {
            return SENTTOWEB_AGENT;
        }
        if (strtoupper(trim($s->__type)) === 'PROPERTY') {
            return SENTTOWEB_PROPERTY;
        }
        if (strtoupper(trim($s->__type)) === 'PROJECT') {
            return SENTTOWEB_PROJECT;
        }

        return '';

    }


    /**
     * @param $s PortalPortalSenttowebLogSql
     * @return string
     */
    private function __getCommand($s)
    {
        if (strtoupper(trim($s->__command)) === 'CREATE') {
            return COMMAND_CREATE;
        }
        if (strtoupper(trim($s->__command)) === 'UPDATE') {
            return COMMAND_UPDATE;
        }
        if (strtoupper(trim($s->__command)) === 'DELETE') {
            return COMMAND_DELETE;
        }

        return strtoupper($s->__command);

    }

    /**
     * Find the msg from saga that caused this outoging msg
     * @param $outgoingMsg PortalPortalSenttowebLogSql
     * @return PortalPortalSenttowebLogSql
     */
    private function __getIncomingMsg($outgoingMsg)
    {
        //that means incoming has id lower than it .....
        /**
         * @var $incomingMsg PortalPortalSenttowebLogSql
         */
        $incomingMsg = WFactory::getSqlService()->getDbClass(__PORTAL_PORTAL_SENTTOWEB_LOG_SQL);
        $incomingMsg->__command = $outgoingMsg->__command;
        $incomingMsg->__id = $outgoingMsg->__realted_senttoweb_id;
        $incomingMsg->loadDataFromDatabase();

        return $incomingMsg;


    }


    /**
     * Find the msg from saga that caused this outoging msg
     * @param $incomingMsg PortalPortalSenttowebLogSql
     * @return PortalPortalSenttowebLogSql
     */
    private function __getOutgoingMsg($incomingMsg)
    {
        /**
         * @var $outgoingMsg PortalPortalSenttowebLogSql
         */
        $outgoingMsg = WFactory::getSqlService()->getDbClass(__PORTAL_PORTAL_SENTTOWEB_LOG_SQL);
        $outgoingMsg->__command = $incomingMsg->__command;
        $outgoingMsg->__id = $incomingMsg->__realted_senttoweb_id;
        $outgoingMsg->loadDataFromDatabase();

        return $outgoingMsg;
    }

}
