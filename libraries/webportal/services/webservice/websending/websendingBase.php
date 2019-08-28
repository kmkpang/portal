<?php
/**
 * Created by JetBrains PhpStorm.
 * User: khan
 * Date: 7/2/13
 * Time: 11:52 PM
 * To change this template use File | Settings | File Templates.
 */

////var/www/eign_v2/libraries/propertyportal/fileManagement/propertyPortalFileManager.php
//require_once JPATH_ROOT . DS . "libraries" . DS . "propertyportal" . DS . "fileManagement" . DS . "propertyPortalFileManager.php";

define('OFFICE', 'OFFICE');
define('AGENT', 'AGENT');
define('PROPERTY', 'PROPERTY');
define('PROJECT', 'PROJECT');
define('PROJECT_UNIT_GALLERY', 'PROJECT_UNIT_GALLERY');
define('PROJECT_UNIT_FLOOR_PLAN', 'PROJECT_UNIT_FLOOR_PLAN');

class WebsendingBase
{


    public function __construct()
    {

    }


    /**
     * will update the geodata information ( names and stuff in the object itself )
     * @param $geodata
     * @param $object
     * @return bool
     */
    function validatePopulateAndUpdateGeoData($geodata, &$object)
    {
        $countryCode = $geodata["@attributes"]["Country"];
        $postalCodeId = $geodata["PostalCodeID"];
        $townId = $geodata["TownID"];
        $regionId = $geodata["RegionID"];

        $geoQuery = "SELECT #__geography_postal_codes.id AS __zip_code_id,
                           #__geography_postal_codes.name AS __zip_code_name,
                           #__geography_towns.id AS __city_town_id,
                           #__geography_towns.name AS __city_town_name,
                           #__geography_regions.id AS __region_id,
                           #__geography_regions.name AS __region_name
                      FROM    (   #__geography_towns #__geography_towns
                               INNER JOIN
                                  #__geography_regions #__geography_regions
                               ON (#__geography_towns.parent_id =
                                      #__geography_regions.id))
                           INNER JOIN
                              #__geography_postal_codes #__geography_postal_codes
                           ON (#__geography_postal_codes.parent_id =
                                  #__geography_towns.id)
                     WHERE (#__geography_postal_codes.id = $postalCodeId)";
        $result = WFactory::getServices()->getSqlService()->select($geoQuery);
        $result = $result[0];

        if (__NEWCOUNTRYGEODATAIMPLMENETEDBYSAGAYET == true) {

            if ($result["__city_town_id"] != $townId) return false;
            if ($result["__region_id"] != $regionId) return false;
        } else {

            WFactory::getLogger()->warn("ignoring geodata error: __NEWCOUNTRYGEODATAIMPLMENETEDBYSAGAYET : false");
        }

        foreach ($result as $key => $value) {
            if (property_exists($object, $key)) {
                $object->{$key} = $value;
            }
        }

        /*Dont enable zip code..or it will fail on insert*/
//        $zip = $this->splitZipCodeName($result["__zip_code_name"]);
//        $object->__zip_code_name = $zip["name"];
//        $object->__zip_code = $zip["code"];

        // adjust zip code name stuff

        return true;
    }

    function splitZipCodeName($zipCodeName)
    {
        $zipCodeName = explode("-", $zipCodeName);
        return array("code" => trim($zipCodeName[0]), "name" => trim($zipCodeName[1]));

    }

    function handleOfficeLogo($logoPath, $companyId = null, $companyName = null,
                              $officeId = null, $officeName = null)
    {
//        if (__ISUNITTEST) { // since saga keeps deleting its own images..i need to donwload a image from google in order to do this properly
//            $logoPath = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_GSEARCH)->searchImage("beautiful logo");
//        }

        if (WFactory::getHelper()->isUnitTest() || (defined('KHAN_HOME') && KHAN_HOME === true)) { // since saga keeps deleting its own images..i need to donwload a image from google in order to do this properly
            ///home/khan/www/softverk-webportal-generic/tests/portaltest/testImages/logo.jpg
            $logoPath = JPATH_ROOT . "/tests/portaltest/testImages/logo.jpg";//WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_GSEARCH)->searchImage("beautiful logo");
        }


        $officeImagePage = $this->buildOfficeImagePath($companyId, $companyName, $officeId, $officeName);
        $destinationPath = $officeImagePage . "/logo." . pathinfo($logoPath, PATHINFO_EXTENSION);
        $webPathURL = "";
        $fileManager = WFactory::getFileManager();
        $commonConfig = WFactory::getConfig()->getWebportalConfigurationArray();
        /**
         * @var iFileManager
         */
        $fileManager = $fileManager->getFileManager($commonConfig["filesystem"]);
        $fileManager->putFile($logoPath, $destinationPath, $webPathURL);

        return $webPathURL;

    }

    function handleVideo($videos, $propertyId)
    {
        //first delete the videos
        $query = "delete from #__portal_property_videos where property_id=$propertyId";

        WFactory::getSqlService()->delete($query);

        if (isset($videos['Video']['SequenceNumber'])){
            WFactory::getLogger()->info("Single video node");
            $videosArray = array($videos['Video']);
        }
        else {
            WFactory::getLogger()->info("Multiple video node");
            $videosArray = $videos['Video'];
        }

        WFactory::getLogger()->info("Attempting to insert videos : " . json_encode($videosArray), __LINE__, __FILE__);


        //now insert them

        foreach ($videosArray as $video) {
            WFactory::getLogger()->info("video data : " . json_encode($video), __LINE__, __FILE__);
            /**
             * @var $videoClass PortalPortalPropertyVideosSql
             */
            $videoClass = WFactory::getServices()->getSqlService()->getDbClass(__PORTAL_PORTAL_PROPERTY_VIDEOS_SQL);
            /*
             * <Video>
                    <SequenceNumber>1</SequenceNumber>
                    <Url>https://www.youtube.com/watch?v=jx3G0CoZjAc</Url>
                    <DescriptiveName>House</DescriptiveName>
                    <Alt/>
                </Video>
             * */
            $videoClass->__property_id = $propertyId;
            $videoClass->__description = $video['DescriptiveName'];
            $videoClass->__origin_url = $video['Url'];
            $videoClass->__sequence = $video['SequenceNumber'];
            $videoClass->__provider = parse_url($video['Url'], PHP_URL_HOST);
            $videoClass->__alt = $video['alt'];
            $videoClass->__timestamp = WFactory::getServices()->getSqlService()->getMySqlDateTime();
            //$videoURL = $video['PropertyPortalVideoURL'];

            $insertId = WFactory::getServices()->getSqlService()->insert($videoClass);
            WFactory::getLogger()->info("Inserted video $insertId with data : " . json_encode($videoClass), __LINE__, __FILE__);

        }

    }

    function handleImage(&$Images, $type,
                         $companyId = null, $companyName = null,
                         $officeId = null, $officeName = null,
                         $agentId = null, $agentName = null,
                         $propertyId = null, $propertyAddress = null,
                         $projectId = null, $projectUnitId = null)
    {
        $defaultImageSequence = $Images["DefaultImageSequenceNumber"];

        $takeFirstImageAsDefaultImage = false;
        if ($defaultImageSequence == "0") {
            WFactory::getLogger()->warn("Default Image Sequence is 0. Ask Gudni to fix..");
            WFactory::getLogger()->warn("Setting default image sequence number to 1 for now...");
            $takeFirstImageAsDefaultImage = true;
        }

        $localImages = &$Images["Image"];
        if ($localImages["FileName"] !== null) { //if its single dimentional, i want to make it multi dimentional
            $localImages = array($localImages);
        }

        $fileManager = WFactory::getFileManager();
        $commonConfig = WFactory::getConfig()->getWebportalConfigurationArray();
        /**
         * @var iFileManager
         */
        $fileManager = $fileManager->getFileManager($commonConfig["filesystem"]);
        $result = true;
        foreach ($localImages as &$image) {

//            if ((defined('__ISUNITTEST') && __ISUNITTEST === true) || (defined('KHAN_HOME') && KHAN_HOME === true)) { // since saga keeps deleting its own images..i need to donwload a image from google in order to do this properly
//                // $image["FileName"] = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_GSEARCH)->searchImage("beautiful sales $type");
//                $image["FileName"] = JPATH_ROOT . "/tests/portaltest/testImages/$type.jpg";
//            }


            if ($type == OFFICE) {
                $officeImagePath = $this->buildOfficeImagePath($companyId, $companyName, $officeId, $officeName);
                $sourceFilePath = $image["FileName"];
                $imgSequence = $image["SequenceNumber"];
                $destinationPath = $officeImagePath . "/image/{$officeId}_{$imgSequence}." . pathinfo($sourceFilePath, PATHINFO_EXTENSION);
                $webPathURL = "";
                $tmpResult = $fileManager->putFile($sourceFilePath, $destinationPath, $webPathURL);
                $image["PropertyPortalImageURL"] = $webPathURL;
                $result = $result && $tmpResult;
                if ($tmpResult == false || empty($webPathURL)) {
                    WFactory::getLogger()->warn("Failed to upload office image $sourceFilePath to S3");
                } else {
                    WFactory::getLogger()->debug("Uploaded office image $sourceFilePath --> $webPathURL");
                }

                if ($imgSequence == $defaultImageSequence) {
                    $Images["DefaultImagePath"] = $webPathURL;
                }
            }
            if ($type == AGENT) {
                $projectImagePath = $this->buildAgentImagePath($companyId, $companyName, $officeId, $officeName, $agentId, $agentName);
                $sourceFilePath = $image["FileName"];
                $imgSequence = $image["SequenceNumber"];
                $destinationPath = $projectImagePath . "/image/{$agentId}_{$imgSequence}." . pathinfo($sourceFilePath, PATHINFO_EXTENSION);
                $webPathURL = "";
                $tmpResult = $fileManager->putFile($sourceFilePath, $destinationPath, $webPathURL);
                $image["PropertyPortalImageURL"] = $webPathURL;
                $result = $result && $tmpResult;
                if ($tmpResult == false || empty($webPathURL)) {
                    WFactory::getLogger()->warn("Failed to upload agent image $sourceFilePath to S3");
                } else {
                    WFactory::getLogger()->debug("Uploaded agent image $sourceFilePath --> $webPathURL");
                }

                if ($imgSequence == $defaultImageSequence) {
                    $Images["DefaultImagePath"] = $webPathURL;
                }
            }
            if ($type == PROPERTY) {
                $propertyImagePath = $this->buildPropertyImagePath($companyId, $companyName, $officeId, $officeName, $agentId, $agentName, $propertyId, $propertyAddress);
                $sourceFilePath = $image["FileName"];
                $imgSequence = $image["SequenceNumber"];
                $destinationPath = $propertyImagePath . "/image/{$propertyId}_{$imgSequence}." . pathinfo($sourceFilePath, PATHINFO_EXTENSION);
                $webPathURL = "";
                $tmpResult = $fileManager->putFile($sourceFilePath, $destinationPath, $webPathURL);


                $image["PropertyPortalImageURL"] = $webPathURL;
                $result = $result && $tmpResult;
                if ($tmpResult == false || empty($webPathURL)) {
                    WFactory::getLogger()->warn("Failed to upload property image $sourceFilePath to S3");
                } else {
                    WFactory::getLogger()->debug("Uploaded property image $sourceFilePath --> $webPathURL");
                }

                if ($takeFirstImageAsDefaultImage || $imgSequence == $defaultImageSequence) {
                    $Images["DefaultImagePath"] = $webPathURL;

                    $thumbTypes = array("list", "map");

                    for ($i = 0; $i < count($thumbTypes); $i++) {
                        // get the original images height and width
                        list($originalWidth, $originalHeight) = GetimageSize($sourceFilePath);
                        $thumb = $thumbTypes[$i];
                        $thumbWidth = $commonConfig["websending"]["{$thumb}page_thumb_size"]["width"]; // get the values from configuration
                        $thumbHeight = $commonConfig["websending"]["{$thumb}page_thumb_size"]["height"];

                        if ($originalWidth > $originalHeight) { // landescape image , so keep the width
                            $aspectRatio = floatval($originalWidth / $originalHeight);
                            $thumbHeight = $originalHeight / $aspectRatio;

                        } else { // portrait image , keep the height
                            $aspectRatio = floatval($originalHeight / $originalWidth);
                            $thumbWidth = $originalWidth / $aspectRatio;
                        }

                        if ($thumbWidth == null || $thumbHeight == null) {
                            $thumbWidth = 221;
                            $thumbHeight = 147;
                            WFactory::getLogger()->warn("thumb width and height not defined, using default!");
                        }

                        $localThumbPath = $commonConfig["tempFolderPath"] . DS . uniqid() . ".jpg";

                        $this->createThumbs($sourceFilePath, $localThumbPath, intval($thumbWidth), intval($thumbHeight));
                        $destinationPath = $propertyImagePath . "/image/{$propertyId}_{$imgSequence}_{$thumb}thumb." . pathinfo($localThumbPath, PATHINFO_EXTENSION);
                        $webPathURL = "";
                        $tmpResult = $fileManager->putFile($localThumbPath, $destinationPath, $webPathURL);

                        $Images[ucfirst($thumb) . "ImagePath"] = $webPathURL;

                    }

                    $takeFirstImageAsDefaultImage = false;
                }
            }

        }


        if ($type == PROPERTY) {

            if (count($Images['Image']) > 0) {
                //delete old images first...

                $query = "delete from #__portal_property_images where property_id=$propertyId";

                WFactory::getSqlService()->delete($query);

            }


            foreach ($Images['Image'] as $img) {
                /**
                 * @var $imageClass PortalPortalPropertyImagesSql
                 */
                $imageClass = WFactory::getServices()->getSqlService()->getDbClass(__PORTAL_PORTAL_PROPERTY_IMAGES_SQL);

                $imageClass->__property_id = $propertyId;
                $imageClass->__origin_url = $img['FileName'];
                $imageClass->__description = $img['DescriptiveName'];
                $imageClass->__alt = $img['Alt'];
                $imageURL = $img['PropertyPortalImageURL'];

                $imageClass->__server_url = $imageURL;

                //pathinfo($imageData["DefaultImagePath"], PATHINFO_FILENAME) . "." . pathinfo($imageData["DefaultImagePath"], PATHINFO_EXTENSION)
                $imageClass->__image_file_name = basename($imageURL);

                $imageClass->__is_created = 1;

                WFactory::getServices()->getSqlService()->insert($imageClass);

            }

        }

        if ($type == PROJECT) {

            $query = "delete from jos_portal_project_image where project_id=$projectId";

            WFactory::getSqlService()->delete($query);


            foreach ($Images as $img) {
                $img = get_object_vars($img);

                if(WFactory::getHelper()->isNullOrEmptyString($img['FileName']))
                    continue;

                /**
                 * @var $imageClass PortalPortalProjectImageSql
                 */
                $imageClass = WFactory::getServices()->getSqlService()->getDbClass(__PORTAL_PORTAL_PROJECT_IMAGE_SQL);

                $imageClass->__project_id = $projectId;
                $imageClass->__origin_url = $img['FileName'];
                $imageClass->__description = $img['Title'];


                $projectImagePath = "$companyId/$officeId/proj_$projectId"; //$this->buildAgentImagePath($companyId, $companyName, $officeId, $officeName, $agentId, $agentName);
                $sourceFilePath = $img["FileName"];
                $imgSequence = $img["SequenceNumber"];
                $destinationPath = $projectImagePath . "/image/{$projectId}_{$imgSequence}." . pathinfo($sourceFilePath, PATHINFO_EXTENSION);
                $webPathURL = "";
                $fileManager->putFile($sourceFilePath, $destinationPath, $webPathURL);
                $imageClass->__server_url = $webPathURL;

                WFactory::getServices()->getSqlService()->insert($imageClass);

            }

        }

        if ($type == PROJECT_UNIT_GALLERY) {

            $query = "delete from jos_portal_project_unit_image where project_id=$projectId AND unit_id = $projectUnitId AND type='GALLERY'";

            WFactory::getSqlService()->delete($query);


            foreach ($Images as $img) {
                $img = get_object_vars($img);
                if(WFactory::getHelper()->isNullOrEmptyString($img['FileName']))
                    continue;
                /**
                 * @var $imageClass PortalPortalProjectUnitImageSql
                 */
                $imageClass = WFactory::getServices()->getSqlService()->getDbClass(__PORTAL_PORTAL_PROJECT_UNIT_IMAGE_SQL);

                $imageClass->__project_id = $projectId;
                $imageClass->__origin_url = $img['FileName'];
                $imageClass->__description = $img['Title'];
                $imageClass->__unit_id = $projectUnitId;
                $imageClass->__type = 'GALLERY';

                $projectImagePath = "$companyId/$officeId/proj_$projectId/unit_$projectUnitId/gallery"; //$this->buildAgentImagePath($companyId, $companyName, $officeId, $officeName, $agentId, $agentName);
                $sourceFilePath = $img["FileName"];
                $imgSequence = $img["SequenceNumber"];
                $destinationPath = $projectImagePath . "/image/{$projectId}_{$imgSequence}." . pathinfo($sourceFilePath, PATHINFO_EXTENSION);
                $webPathURL = "";
                $fileManager->putFile($sourceFilePath, $destinationPath, $webPathURL);
                $imageClass->__server_url = $webPathURL;

                WFactory::getServices()->getSqlService()->insert($imageClass);

            }

        }

        if ($type == PROJECT_UNIT_FLOOR_PLAN) {
            $query = "delete from jos_portal_project_unit_image where project_id=$projectId AND unit_id = $projectUnitId AND type='FLOORPLAN'";

            WFactory::getSqlService()->delete($query);


            foreach ($Images as $img) {
                $img = get_object_vars($img);
                if(WFactory::getHelper()->isNullOrEmptyString($img['FileName']))
                    continue;
                /**
                 * @var $imageClass PortalPortalProjectUnitImageSql
                 */
                $imageClass = WFactory::getServices()->getSqlService()->getDbClass(__PORTAL_PORTAL_PROJECT_UNIT_IMAGE_SQL);

                $imageClass->__project_id = $projectId;
                $imageClass->__origin_url = $img['FileName'];
                $imageClass->__description = $img['Title'];
                $imageClass->__unit_id = $projectUnitId;
                $imageClass->__type = 'FLOORPLAN';

                $projectImagePath = "$companyId/$officeId/proj_$projectId/unit_$projectUnitId/floorplan"; //$this->buildAgentImagePath($companyId, $companyName, $officeId, $officeName, $agentId, $agentName);
                $sourceFilePath = $img["FileName"];
                $imgSequence = $img["SequenceNumber"];
                $destinationPath = $projectImagePath . "/image/{$projectId}_{$imgSequence}." . pathinfo($sourceFilePath, PATHINFO_EXTENSION);
                $webPathURL = "";
                $fileManager->putFile($sourceFilePath, $destinationPath, $webPathURL);
                $imageClass->__server_url = $webPathURL;

                WFactory::getServices()->getSqlService()->insert($imageClass);

            }

        }


        return $result;


    }

    public function buildCompanyImagePath($companyId, $companyName)
    {
        $commonConfig = WFactory::getConfig()->getWebportalConfigurationArray();

        if ($commonConfig["filesystem"] == "gq") {
            $companyName = urlencode($companyName); // because this will be part of a url
            return "$companyId-$companyName";
        }
    }

    public function buildOfficeImagePath($companyId, $companyName, $officeId, $officeName)
    {
        $commonConfig = WFactory::getConfig()->getWebportalConfigurationArray();
        $companyName = str_replace("/", "", $companyName); // we cant hav / in the name!
        $officeName = str_replace("/", "", $officeName); // we cant hav / in the name!
        if ($commonConfig["filesystem"] == "s3") {
            return "$companyId/$officeId";
        }
    }

    public function buildAgentImagePath($companyId, $companyName, $officeId, $officeName, $agentId, $agentName)
    {
        $commonConfig = WFactory::getConfig()->getWebportalConfigurationArray();
        $companyName = str_replace("/", "", $companyName); // we cant hav / in the name!
        $officeName = str_replace("/", "", $officeName); // we cant hav / in the name!
        $agentName = str_replace("/", "", $agentName); // we cant hav / in the name!

        if ($commonConfig["filesystem"] == "s3") {

            return "$companyId/$officeId/$agentId";
        }
    }

    public static function buildPropertyImagePath($companyId, $companyName, $officeId, $officeName, $agentId, $agentName, $propertyId, $propertyAddress)
    {
        $commonConfig = WFactory::getConfig()->getWebportalConfigurationArray();
        $companyName = str_replace("/", "", $companyName); // we cant hav / in the name!
        $officeName = str_replace("/", "", $officeName); // we cant hav / in the name!
        $agentName = str_replace("/", "", $agentName); // we cant hav / in the name!
        $propertyAddress = str_replace("/", "_", $propertyAddress); // we cant hav / in the name!

        if ($commonConfig["filesystem"] == "s3") {
            return "$companyId/$officeId/$agentId/$propertyId";
        }
    }

    public static function createThumbs($imageFileUrl, $localFilesystemPathToThumb, $new_width, $new_height)
    {

        if (!JFile::exists($imageFileUrl)) {
            if (WFactory::getHelper()->checkIfFileAtURLExists($imageFileUrl)) {
                $imageFileUrl = WFactory::getHelper()->downloadFileToTmpFolder($imageFileUrl);
            } else {
                WFactory::getLogger()->warn("Create thumb FAILED to copy , file $imageFileUrl does not exist");
                return false;
            }
        }


        $localFilesystemPathToImage = $imageFileUrl;

        $info = pathinfo($localFilesystemPathToImage);
        if (strtolower($info['extension']) == 'jpg'
            || strtolower($info['extension']) == 'jpeg'
        ) {
            // load image and get image size
            $img = imagecreatefromjpeg($localFilesystemPathToImage);
            $width = imagesx($img);
            $height = imagesy($img);


            $tmp_img = imagecreatetruecolor($new_width, $new_height);

            // copy and resize old image into new image
            imagecopyresampled($tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

            // save thumbnail into a file
            imagejpeg($tmp_img, $localFilesystemPathToThumb);
        }

        if (strtolower($info['extension']) == 'png'
        ) {
            // load image and get image size
            $img = imagecreatefrompng($localFilesystemPathToImage);
            $width = imagesx($img);
            $height = imagesy($img);


            // create a new temporary image
            $tmp_img = imagecreatetruecolor($new_width, $new_height);

            // copy and resize old image into new image
            imagecopyresampled($tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

            imagejpeg($tmp_img, $localFilesystemPathToThumb);
        }


        return true;

    }

    function findSwappingExtraFlat($descriptionText, $entrance)
    {

        $swapText = " skipti ";
        $extraFlatText = "aukaíbúð";
        $exclusiveEntranceText = "sérinngangur";

        $dataStructure = Array();
        $dataStructure["swapping"] = 0;
        $dataStructure["extra_flat"] = 0;
        $dataStructure["exclusive_entrance"] = 0;

        $haystack = strtolower(utf8_decode(strip_tags(WFactory::getHelper()->escapePercentU(trim($descriptionText)))));
        $entrance = strtolower(utf8_decode(trim($entrance)));


        if (strpos($haystack, $swapText) != false)
            $dataStructure["swapping"] = 1;
        if (strpos($haystack, $extraFlatText) != false)
            $dataStructure["extra_flat"] = 1;
        if ($entrance == $exclusiveEntranceText)
            $dataStructure["exclusive_entrance"] = 1;
        // DebugBreak();
        return $dataStructure;

    }

    function getLastPriceUpdateDate($unique_id, $new_price)
    {
        $db =& JFactory::getDBO();

        $query = "SELECT current_listing_price FROM #__portal_properties WHERE unique_id ='$unique_id'";

        $db->setQuery($query);

        $current_price = $db->loadResult();


        $today = &JFactory::getDate();
        $today = $today->toMySQL();

        $dataStructure = Array();
        $dataStructure["last_price_update_date"] = false;
        $dataStructure["last_price_reduction_date"] = false;

        if ($current_price != $new_price) {
            $dataStructure["last_price_update_date"] = $today;
        }

        if ($new_price < $current_price) {
            $dataStructure["last_price_reduction_date"] = $today;
        }

        return $dataStructure;


    }


    function generateUniqueId($type, $indexId, $companyId = 1)
    {
        switch ($type) {
            case PROPERTY:
                $key = "PR";
                break;
            case AGENT:
                $key = "AG";
                break;
            case OFFICE:
                $key = "OF";
                break;
            default:
                die("Type isn't recognized.");
        }
        $today = date("YmdHis");
        $unique_id = "C" . $companyId . $key . $indexId . $today . strtoupper(uniqid());

        return $unique_id;
    }

    function generatePublicKey($officeId)
    {
        return strtoupper(uniqid($officeId));
    }

    function responseJson($associatedId, $incomingJson, &$outgoingJsonId, $code, $msg, $command)
    {
        $url = JUri::base() . "index.php?option=com_webportal&view=projectdetail&project-id=$associatedId";
        $response = array(
            "code" => $code,
            "msg" => $msg,
            "link" => $url
        );
        $fromip = $_SERVER['HTTP_HOST'];
        $toip = $_SERVER['REMOTE_ADDR'];
        $sent2webDbClass = $this->saveSentToWebToDatabase('OUTGOING',
            $fromip,
            $toip,
            $command,
            "Project",
            json_encode($response),
            $associatedId,
            $incomingJson->__id);


        $outgoingJsonId = $sent2webDbClass->__id;
        $incomingJson->__realted_senttoweb_id = $outgoingJsonId;
        WFactory::getSqlService()->update($incomingJson);


        WFactory::getLogger()->debug("WEBSENDING COMPLETE. RETURNING: \r\n" . json_encode($response, JSON_PRETTY_PRINT));


        ob_clean(); /* clean the buffer, otherwise apache adds random empty bytes to the ehader */

        $responseString = json_encode($response);
        echo trim($responseString);

        if (WFactory::getHelper()->isUnitTest() === true || php_sapi_name() === 'cli') {
            return $responseString;
        } else
            exit();
    }

    /**
     * @param $result
     * @param $associatedId
     * @param $incomingXml PortalPortalSenttowebLogSql
     * @param $outgoingXmlId
     * @param $type
     * @param $code
     * @param $msg
     * @param $dom
     * @param $command
     * @return string
     */
    function response($result, $associatedId, $incomingXml, &$outgoingXmlId, $type, $code, $msg, $dom, $command)
    {

        $url = "";
        if ($type == "Office") {////
            $url = JUri::base() . ("index.php?option=com_webportal&view=offices&office_id=$associatedId");
        }
        if ($type == "Property") {///
            $url = JUri::base() . ("index.php?option=com_webportal&view=property&property-id=$associatedId");
        }

        if ($type == "Agent") {///
            $url = JUri::base() . ("index.php?option=com_webportal&view=agents&agent_id=$associatedId");
        }


        $xml = '<?xml version ="1.0" encoding="UTF-8" ?>' .
            '<UploadXML>' .
            '<Response>' .
            '<Number>' . $code . '</Number>' .
            '<Message>' . $msg . '</Message>' .
            '<Link>' . htmlspecialchars($url, ENT_XML1, 'UTF-8') . '</Link>' .
            '</Response>' .
            '</UploadXML>';

        $fromip = $_SERVER['HTTP_HOST'];
        $toip = $_SERVER['REMOTE_ADDR'];


        //$type = "SEND to SAGA/HOMEBASE";

        $currentSiteConfig = WFactory::getConfig()->getWebportalConfigurationArray();

        if ($currentSiteConfig["logging"]["log_websending_to_database"]) {
            /*$sent2webDbClass = $this->websendingBase->saveSentToWebToDatabase('INCOMING',
                '127.0.0.1',
                WFactory::getPublicIp(),
                'Create',
                'Office',
                $this->xmlString,
                0, 0);
            */
            $sent2webDbClass = $this->saveSentToWebToDatabase('OUTGOING',
                $fromip,
                $toip,
                $command,
                $type,
                $xml,
                $associatedId,
                $incomingXml->__id);


            $outgoingXmlId = $sent2webDbClass->__id;
            $incomingXml->__realted_senttoweb_id = $outgoingXmlId;
            WFactory::getSqlService()->update($incomingXml);

        }


        WFactory::getLogger()->debug("WEBSENDING COMPLETE. RETURNING: \r\n" . WFactory::getHelper()->getFomattedXml($xml));


        ob_clean(); /* clean the buffer, otherwise apache adds random empty bytes to the ehader */

        echo trim($xml);

        if ($currentSiteConfig["logging"]["log_websending_to_filesystem"]) {
            //NOTE: Dont have this function yet!
            $this->writeSenttowebLog($xml, $dom, 'send');
        }


        if (WFactory::getHelper()->isUnitTest() === true || php_sapi_name() === 'cli') {
            return $xml;
        } else
            exit();


    }

    /**
     * @param $fromip
     * @param $toip
     * @param $command
     * @param $type
     * @param $xml
     * @param $associated_id
     * @param $related_senttoweb_id
     * @return PortalPortalSenttowebLogSql
     */
    public function saveSentToWebToDatabase($direction, $fromip, $toip, $command, $type, $xml, $associated_id, $related_senttoweb_id)
    {

        $sqlService = WFactory::getServices()->getSqlService();
        /**
         * @var $senToWebLog PortalPortalSenttowebLogSql
         */
        $senToWebLog = $sqlService->getDbClass(__PORTAL_PORTAL_SENTTOWEB_LOG_SQL);
        $senToWebLog->__command = $command;
        $senToWebLog->__data = $xml;
        $senToWebLog->__direction = $direction;
        $senToWebLog->__fromip = $fromip;
        $senToWebLog->__toip = $toip;
        $senToWebLog->__type = $type;
        $senToWebLog->__associated_id = $associated_id;
        $senToWebLog->__realted_senttoweb_id = $related_senttoweb_id;
        $senToWebLog->__date = $sqlService->getMySqlDateTime();


        $id = $sqlService->insert($senToWebLog);

        $senToWebLog->__id = $id;

        return $senToWebLog;
    }


    public function loadMarketingInfo($type, $referenceId)
    {

        $type = WFactory::getServices()->getSqlService()->getSqlServiceClass(__PROPPERTY_PORTAL_MARKETINGINFO)->getMarketingInfoTypeIdFromMarketingInfoType($type);

        /**
         * @var PropertyPortalLibraryCore $sqlService
         */
        $sqlService = WFactory::getServices()->getSqlService();
        $query = $sqlService->getQuery();
        $query->select('*')
            ->from('#__portal_marketing_info')
            ->where("marketing_info_type_id = '$type'")
            ->where("reference_id = $referenceId");

        return $sqlService->select((string)$query);
    }


    public function getWebsendingConfig()
    {

        $configArray = WFactory::getConfig()->getWebportalConfigurationArray();
        $websendingConfig = $configArray["websending"];
        return $websendingConfig;
    }

    public function checkIfAgentUniqueIdExists($uniqueId)
    {
        if (empty($uniqueId) || $uniqueId == null) {
            return false;
        }

        $sqlService = WFactory::getServices()->getSqlService();
        $query = $sqlService->getQuery();
        $query->select('id')
            ->from("#__portal_sales")
            ->where("unique_id = '$uniqueId'");
        $result = $sqlService->select($query);

        if ($result[0]["id"] == null)
            return false;
        return $result[0]["id"];
    }

    public function checkIfPropertyUniqueIdExists($uniqueId)
    {
        if (empty($uniqueId) || $uniqueId == null) {
            return false;
        }

        $sqlService = WFactory::getServices()->getSqlService();
        $query = $sqlService->getQuery();
        $query->select('id')
            ->from("#__portal_properties")
            ->where("unique_id = '$uniqueId'");
        $result = $sqlService->select($query);

        if ($result[0]["id"] == null)
            return false;
        return $result[0]["id"];
    }

    public function checkIfOfficeUniqueIdExists($uniqueId)
    {
        if (empty($uniqueId) || $uniqueId == null) {
            return false;
        }

        $sqlService = WFactory::getServices()->getSqlService();
        $query = $sqlService->getQuery();
        $query->select('id')
            ->from("#__portal_offices")
            ->where("unique_id = '$uniqueId'");
        $result = $sqlService->select($query);

        if ($result[0]["id"] == null)
            return false;
        return $result[0]["id"];

    }


}
