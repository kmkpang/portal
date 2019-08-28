<?php
/**
 * Created by PhpStorm.
 * User: khan
 * Date: 5/10/14
 * Time: 12:43 PM
 */

require_once JPATH_LIBRARY_WEBPORTAL_SERVICES . DS . "webservice" . DS . "websending" . DS . "websendingBase.php";

class WebportalWebsendingFactory
{

    protected static $instance = null;

    /**
     * @var WebsendingBase
     */
    private $websendingBase;

    protected function __construct()
    {
        //Thou shalt not construct that which is unconstructable!

        $this->websendingBase = new WebsendingBase();

    }

    protected function __clone()
    {
        //Me not like clones! Me smash clones!
    }

    /**
     * @return WebportalWebsendingFactory
     */
    public static function getInstance()
    {
        if (!isset(static::$instance)) {
            static::$instance = new static;

        }
        return static::$instance;
    }

    public function detectType($xmlObject, &$type, &$command)
    {

        $sentData = $xmlObject->System->SentData;
        $sentData = get_object_vars($sentData);
        $sentData = $sentData['@attributes'];

        $type = $sentData['Type'];
        $command = $sentData['Order'];

    }

    public function detectJsonType($jsonObject, &$type, &$command)
    {
        $type = $jsonObject->Type;
        $command = $jsonObject->Order;

    }

    public function execute($task)
    {

        if ($task == 'service') {
            $xml = file_get_contents("php://input");
            $xml = stripslashes($xml);


            $source = $_SERVER['REMOTE_ADDR'];


            if ($xml == "") {
                WFactory::getLogger()->error("Empty xml received from remote location $source");
                $outgoing = 0;
                return $this->websendingBase->response(false, 0, 0, $outgoing, "", "01000", "Empty String", "", "EMPTY_STRING");
            } else {

                $xmlObject = simplexml_load_string($xml);
                if ($xmlObject) { // existing crappy xml sending
                    $type = "";
                    $command = "";
                    $this->detectType($xmlObject, $type, $command);
                    ///////incoming!!!!!!!

                    //Remove and Hard fix amazon ip --> WFactory::getPublicIp()
                    if (__COUNTRY == "IS") {
                        $toip = '82.221.95.13';
                    } else {
                        $toip = '54.255.167.23';
                    }
                    $sent2webDbClass = $this->websendingBase->saveSentToWebToDatabase('INCOMING', $source, $toip, $command, $type, $xml, 0, 0);


                    JLoader::import("webportal.services.webservice.websending." . strtolower($type));
                    $className = ucfirst($type) . "SentToWeb";

                    $sentToWebClass = new $className($xml);
                    $command = strtolower($command);
                    return $sentToWebClass->$command($sent2webDbClass);

                } else {
                    $jsonObject = json_decode($xml);
                    $type = "";
                    $command = "";
                    $this->detectJsonType($jsonObject, $type, $command);
                    ///////incoming!!!!!!!

                    //Remove and Hard fix amazon ip --> WFactory::getPublicIp()
                    if (__COUNTRY == "IS") {
                        $toip = '82.221.95.13';
                    } else {
                        $toip = '54.255.167.23';
                    }
                    $sent2webDbClass = $this->websendingBase->saveSentToWebToDatabase('INCOMING', $source, $toip, $command, $type, $xml, 0, 0);


                    JLoader::import("webportal.services.webservice.websending." . strtolower($type));
                    $className = ucfirst($type) . "SentToWeb";

                    $sentToWebClass = new $className($xml);
                    $command = strtolower($command);
                    return $sentToWebClass->$command($sent2webDbClass);

                }
            }


        }

    }
}
