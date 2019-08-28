<?php
/**
 * Created by PhpStorm.
 * User: khan
 * Date: 6/22/14
 * Time: 1:28 PM
 */


require_once JPATH_LIBRARY_WEBPORTAL_SERVICES . DS . "beanstalkd" . DS . "beanstalkdJobModel.php";


// badddddd idea..time to learn more about composer!
require_once JPATH_LIBRARY_WEBPORTAL_SERVICES . DS . "beanstalkd" . DS . "pheanstalk" . DS . "PheanstalkInterface.php";
require_once JPATH_LIBRARY_WEBPORTAL_SERVICES . DS . "beanstalkd" . DS . "pheanstalk" . DS . "Exception.php";
require_once JPATH_LIBRARY_WEBPORTAL_SERVICES . DS . "beanstalkd" . DS . "pheanstalk" . DS . "Exception" . DS . "ServerException.php";


foreach (glob(JPATH_LIBRARY_WEBPORTAL_SERVICES . DS . "beanstalkd" . DS . "pheanstalk" . DS . "*.php") as $filename) {
    require_once $filename;
}

foreach (glob(JPATH_LIBRARY_WEBPORTAL_SERVICES . DS . "beanstalkd" . DS . "pheanstalk" . DS . "Exception" . DS . "*.php") as $filename) {
    require_once $filename;
}

foreach (glob(JPATH_LIBRARY_WEBPORTAL_SERVICES . DS . "beanstalkd" . DS . "pheanstalk" . DS . "Socket" . DS . "*.php") as $filename) {
    require_once $filename;
}
foreach (glob(JPATH_LIBRARY_WEBPORTAL_SERVICES . DS . "beanstalkd" . DS . "pheanstalk" . DS . "Response" . DS . "*.php") as $filename) {
    require_once $filename;
}

foreach (glob(JPATH_LIBRARY_WEBPORTAL_SERVICES . DS . "beanstalkd" . DS . "pheanstalk" . DS . "Command" . DS . "*.php") as $filename) {
    require_once $filename;
}


define("__PORTAL_BEANSTALKD_SEARCHQUEUE", "searchService");
define("__PORTAL_BEANSTALKD_MAILQUEUE", "mailService");
define("__PORTAL_BEANSTALKD_GANALYTIC", "googleAnalytic");

class BeanstalkdService extends \Pheanstalk\Pheanstalk
{
    var $configArray;

    public function __construct()
    {


        $this->configArray = WFactory::getConfig()->getWebportalConfigurationArray();
        $this->configArray = $this->configArray["beanstalkd"];

        $host = $this->configArray["beanstalkdHost"];
        $port = $this->configArray["beanstalkdPort"];

        parent::__construct($host, $port);
    }


    public function isBeanstalkdEnabled()
    {
        return $this->configArray["enabled"];
    }

    /**
     * @param $serviceProviderName
     * @param $functionName
     * @param $payLoad
     * @return BeanstalkdJobModel
     */
    public function getBeanstalkdModel($serviceProviderName, $functionName, $payLoad)
    {
        return new BeanstalkdJobModel($serviceProviderName, $functionName, $payLoad);
    }

    /**
     * @param $payload BeanstalkdJobModel
     * @return int|null : null on error
     */
    public function putSearchQueue($payload)
    {
        return $this->putJob(__PORTAL_BEANSTALKD_SEARCHQUEUE, $payload->toString());
    }

    /**
     * @param $payload BeanstalkdJobModel
     * @return int|null : null on error
     */
    public function putMailQueue($payload)
    {
        return $this->putJob(__PORTAL_BEANSTALKD_MAILQUEUE, $payload->toString());
    }


    public function processAllSearchQueue($filter = false, $serviceClass = null, $filterFunction = null)
    {
        $job = $this->getFromSearchQueue();

        while ($job !== null) {
            $filePath = $job->getData();
            /**
             * @var $data BeanstalkdJobModel
             */
            $data = $this->deserializeData($filePath);
            if ($data !== null) {

                if ($filter &&
                    $serviceClass !== $data->serviceProviderName &&
                    $filterFunction !== $data->functionName
                ) {
                    WFactory::getLogger()->warn("Filter is enabled and current job does not match filter, continueing!");


                    $this->release($job, 2048, 30); //put it back and make it less importan
                    $job = $this->getFromSearchQueue();


                    continue;
                }

                WFactory::getLogger()->debug("[Beanstalkd] Executing beanstalkd job --> {$data->serviceProviderName}::{$data->functionName}()");

                if (!WFactory::getHelper()->isNullOrEmptyString($data->serviceProviderName) && !WFactory::getHelper()->isNullOrEmptyString($data->functionName)) {

                    try {

                        $result = WFactory::getServices()->getServiceClass($data->serviceProviderName)->{$data->functionName}($data->payLoad);

                        if ($result) {
                            WFactory::getLogger()->debug("{$data->serviceProviderName}::{$data->functionName}() returned $result");

                            WFactory::getLogger()->debug("[Beanstalkd] Success --> {$data->serviceProviderName}::{$data->functionName}()");
                            WFactory::getLogger()->debug("[Beanstalkd] Deleting $filePath");
                            $deleteResult = unlink($filePath);
                            if (!$deleteResult) {
                                WFactory::getLogger()->warn("[Beanstalkd] Failed to delete $filePath! disk will fill up...!!!");
                            }
                            WFactory::getLogger()->debug("[Beanstalkd] Deleting successful beanstalkd job id : " . $job->getId());
                            $this->delete($job);
                            WFactory::getLogger()->info("[Beanstalkd] Deleted job , after executing properly, result was: \r\n : " . $result);

                        }
                    } catch (Exception $e) {
                        WFactory::getLogger()->fatal("Beantalkd job error : " . $e->getMessage());
                    }
                } else {
                    $this->delete($job);
                    WFactory::getLogger()->warn("[Beanstalkd] Delete job , as serviceProviderName or functionName was invalid \r\n job --> \r\n" . json_encode(get_object_vars($job)));
                }

            } else {
                $this->delete($job);
                WFactory::getLogger()->warn("[Beanstalkd] Delete job , as data was invalid \r\n job --> \r\n" . json_encode(get_object_vars($job)));

            }
            $job = $this->getFromSearchQueue();
        }

        return true;
    }

    public function getFromSearchQueue()
    {
        return $this->getJob(__PORTAL_BEANSTALKD_SEARCHQUEUE);

    }

//    private function buryJob($job)
//    {
//        $this->bury($job);
//        WFactory::getLogger()->warn("[Beanstalkd] Buried job , as data was invalid \r\n job --> \r\n" . json_encode(get_object_vars($job)));
//
//    }

    /**
     * @param $tubeName
     * @param $payload
     * @return int|null
     */
    private function putJob($tubeName, $payload)
    {
        if ($this->isBeanstalkdEnabled()) {


            if (!is_string($payload)) {

                $payload = $this->serializeData($payload, $tubeName);
            }
            try {
                return $this->useTube($tubeName)->put($payload);
            } catch (\Pheanstalk\Exception $ex) {
                WFactory::getLogger()->error("[Beanstalkd] Put error : " . $ex->getMessage());
            }


            return null;
        } else {
            WFactory::getLogger()->warn("Beanstalk not enabled in webportal.configuration.php file", __LINE__, __FILE__);
        }
    }


    private function serializeData($payload, $pipeName)
    {
        $payload = serialize($payload);
        $payloadDirectory = $this->configArray["jobPayloadFolder"];
        $payloadFile = $payloadDirectory . DS . uniqid("beanstalkd_{$pipeName}_");
        file_put_contents($payloadFile, $payload);

        return $payloadFile;

    }

    /**
     * @param $payload | path to payload file
     * @return BeanstalkdJobModel
     */
    private function deserializeData($payload)
    {
        $data = file_get_contents($payload);
        $data = unserialize($data);
        $model = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_BEANSTALKD)->getBeanstalkdModel(null, null, null);
        $model->bindToDb($data);

        return $model;
    }

    /**
     * @param $tubeName
     * @return bool|null|object|\Pheanstalk\Job
     */
    private function getJob($tubeName)
    {

        if ($this->isBeanstalkdEnabled()) {

            try {

                if (WFactory::getHelper()->isUnitTest()) {
                    $job = $this->peekReady($tubeName);
                    //if there is a job in ready state
                    if ($job !== null) {
                        $job = $this
                            ->watch($tubeName)
                            ->ignore('default')
                            ->reserve();
                    }

                } else {
                    WFactory::getLogger()->info("Starting watch on tube: $tubeName");
                    $job = $this
                        ->watch($tubeName)
                        ->ignore('default')
                        ->reserve();
                }
                return $job;
            } catch (\Pheanstalk\Exception $ex) {
                WFactory::getLogger()->error("[Beanstalkd] Get error : " . $ex->getMessage());
            }


            return null;
        } else {
            WFactory::getLogger()->warn("Beanstalk not enabled in webportal.configuration.php file", __LINE__, __FILE__);
        }

    }


}