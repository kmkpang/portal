<?php
/**
 * Created by JetBrains PhpStorm.
 * User: khan
 * Date: 7/2/13
 * Time: 5:27 PM
 * To change this template use File | Settings | File Templates.
 */

JLoader::import("joomla.filesystem.file");


require_once JPATH_ROOT . DS . "libraries" . DS . "webportal" . DS . "fileManagement" . DS . "iFileManager.php";
require_once JPATH_ROOT . DS . "libraries" . DS . "webportal" . DS . "fileManagement" . DS . "s3" . DS . "S3.php";


class WebportalS3FileManager implements iFileManager
{
    private $s3;
    private $baseBucketName;
    private $s3config;
    private $dummyFilePath;
    private $dummyFileName;

    /**
     * @var PropertyPortalLogger
     */
    private $logger;

    public function __construct()
    {

        $this->dummyFileName = "dummy.txt";
        ///var/www/softverk-webportal/libraries/webportal/fileManagement/dummy.txt
        $this->dummyFilePath = JPATH_BASE . DS . "libraries" . DS . "webportal" . DS . "fileManagement" . DS . $this->dummyFileName;
        $this->logger = WFactory::getLogger();
    }


    public function createFolder($folderPath)
    {
        $folderPath = $folderPath . "/" . $this->dummyFileName;
        $webURL = "";
        return $this->putFile($this->dummyFilePath, $folderPath, $webURL);


    }

    /**
     * @param $filePaths
     * @return bool
     */
    public function deleteFolder($filePaths)
    {
        $result = true;
        foreach ($filePaths as $f) {
            $result = $result && $this->deleteFile($f);
        }
        return $result;
    }

    public function putFile($sourcePath, $destinationPath, &$webPathURL)
    {
        ///var/www/softverk-webportal/libraries/joomla/filesystem/file.php
        WFactory::getLogger()->info("Attempting to put file from $sourcePath to $destinationPath");
        if (!JFile::exists($sourcePath)) {
            if (WFactory::getHelper()->checkIfFileAtURLExists($sourcePath)) { //why does this not commit???
                $sourcePath = WFactory::getHelper()->downloadFileToTmpFolder($sourcePath);
            } else {
                $this->logger->warn("[S3]: FAILED to copy , file $sourcePath does not exist");
                return false;
            }
        }


        $bucketName = $this->getBaseBucketName();
        $destinationPath = $this->checkAndRemoveStartingSlash($destinationPath);
        WFactory::getLogger()->debug("[S3]: attempting to put to bucket $bucketName and destination $destinationPath, from $sourcePath");
        try {
            $result = $this->getS3()->putObjectFile($sourcePath, $bucketName, $destinationPath, S3::ACL_PUBLIC_READ);
            if ($result) {
                $this->logger->debug("[S3]: File copied to BUCKET: {$bucketName}/" . $destinationPath);
                $webPathURL = $this->buildS3WebURL($destinationPath);
                WFactory::getLogger()->debug("[S3] WebURL: $webPathURL");
                return true;
            }
            WFactory::getLogger()->debug("[S3]: Put result is : " . $result);
        } catch (Exception $e) {
            $this->logger->warn("[S3]: FAILED to copy to BUCKET: {$bucketName}/" . $destinationPath . " error was: " . $e->getMessage());
            return false;
        }


    }

    function getUrl($filePath)
    {
        $commonConfig = WFactory::getConfig()->getWebportalConfigurationArray();
        $timeStamp = time() + 3600;
        $url = "https://s.qstack.advania.com/{$commonConfig['s3']['s3BucketName']}/$filePath?response-content-disposition=attachment&Expires=$timeStamp&AWSAccessKeyId={$commonConfig['s3']['awsAccessKey']}";

        $signature = $this->getS3()->getSignedCannedURL($url, 3600);
        $url = $url . "&Signature=$signature";

    }

    public function getFile($filePath, &$stream)
    {
        $bucketName = $this->getBaseBucketName();

        $result = $this->getS3()->getObject($bucketName, $filePath, $stream);

        return $result;
    }

    public function deleteFile($filePath)
    {

        // test/testImage1.png
        if (WFactory::getHelper()->checkIfUrl($filePath)) {
            $filePath = $this->removeS3BasePath($filePath);
        }

        $bucketName = $this->getBaseBucketName();
        $filePath = $this->checkAndRemoveStartingSlash($filePath);
        $this->logger->warn("[S3]: Request to delete picture at $bucketName/$filePath");
        try {
            if ($this->getS3()->deleteObject($bucketName, $filePath)) {
                $this->logger->debug("[S3]: File deleted from {$bucketName}/" . $filePath);
                return true;
            }
        } catch (Exception $e) {
            $this->logger->warn("[S3]: FAILED to delete {$bucketName}/" . $filePath . " error was: " . $e->getMessage());
            return false;
        }
    }


    /*----------------------------------------------------------------------------------------------------------------------------*/
    public function getS3()
    {
        if ($this->s3 === null) {
//            if (!extension_loaded('curl') && !@dl(PHP_SHLIB_SUFFIX == 'so' ? 'curl.so' : 'php_curl.dll')) {
//                $this->logger->error(" CURL extension not loaded FOR s3");
//            }
            $config = $this->getS3Config();
            $this->s3 = new S3($config["awsAccessKey"],
                $config["awsSecretKey"],
                $config["awsUseSSL"],
                $config["awsEndpoint"]);

            $this->logger->info("[S3]: S3 initialized with
                awsAccessKey: {$config["awsAccessKey"]},
                awsSecretKey: {$config["awsSecretKey"]},
                awsUseSSL:    {$config["awsUseSSL"]},
                awsEndpoint:  {$config["awsEndpoint"]} ");

            //$this->s3->setExceptions(true);

        }
        return $this->s3;

    }

    private function checkAndRemoveStartingSlash($path)
    {
        if (0 === strpos($path, "/") || 0 === strpos($path, "\\")) {
            $path = substr($path, 1);
        }
        return $path;
    }


    public function useProxy()
    {
        $config = $this->getS3Config();
        return $config['useproxy'] === true;
    }

    public function getBaseBucketName()
    {
        if ($this->baseBucketName == null) {
            $config = $this->getS3Config();
            $this->baseBucketName = $config["s3BucketName"];
        }
        return $this->baseBucketName;
    }

    public function getS3Config()
    {
        if ($this->s3config == null) {
            $config = WFactory::getConfig()->getWebportalConfigurationArray();
            $this->s3config = $config["s3"];
        }

        return $this->s3config;
    }


    public function s3ListBuckets()
    {
        $buckets = $this->getS3()->listBuckets(true);


        return $buckets;
    }

    public function buildS3WebURL($filePath)
    {
        $commonConfig = $this->getS3Config();
        $useProxy = $this->useProxy();

        if ($useProxy === true) {
            $url = JUri::root() . "api/v1/image/get?path=$filePath";
        } else
            $url = "http://" . $this->getBaseBucketName() . "." . $commonConfig["s3BaseFileURL"] . "/" . $filePath;

        return $url;
    }

    public function removeS3BasePath($s3Url)
    {
        $basepath = $this->buildS3WebURL("");
        $path = str_replace($basepath, "", $s3Url);
        return $path;
    }

    public function buildS3PathFromWebURL($webURL)
    {
        $path = parse_url($webURL, PHP_URL_PATH);
        return $path;

    }


}
