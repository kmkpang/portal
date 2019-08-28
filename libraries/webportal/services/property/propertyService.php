<?php

/**
 * Created by JetBrains PhpStorm.
 * User: khan
 * Date: 3/12/13
 * Time: 2:42 PM
 * To change this template use File | Settings | File Templates.
 */

require_once JPATH_LIBRARY_WEBPORTAL_SERVICES . DS . 'property' . DS . 'imageUploadModel.php';

class PropertyService
{
    /**
     * @param bool $asJson
     * @return ImageUploadModel|mixed|string
     */
    function getImageUploadModel($asJson = false)
    {
        $model = new ImageUploadModel();

        if ($asJson) {
            $model = json_encode(get_object_vars($model));
        }

        return $model;
    }

    function getDetailForSaga($propertyUniqueId)
    {
        if (is_object($propertyUniqueId))
            $propertyUniqueId = get_object_vars($propertyUniqueId);
        if (is_array($propertyUniqueId))
            $propertyUniqueId = $propertyUniqueId['propertyId'];

        $query = "  SELECT #__portal_properties.unique_id AS propertyId,
                           #__portal_offices.unique_id AS officeId,
                           #__portal_sales.unique_id AS agentId,
                           #__portal_properties.id AS id,
                           #__portal_properties.viewcount AS viewcount,
                           #__portal_properties.viewcount AS propertyViewed
                    FROM  `#__portal_properties`
                    INNER JOIN #__portal_offices ON ( #__portal_properties.office_id = #__portal_offices.id )
                    INNER JOIN #__portal_sales ON ( #__portal_properties.sale_id = #__portal_sales.id ) where #__portal_properties.unique_id='$propertyUniqueId' and #__portal_properties.is_deleted = 0  LIMIT 1";

        $result = WFactory::getSqlService()->select($query);


        if ($result == null || count($result) == 0) {
            return "Property Unique ID does not exists";
        }
        $result = $result[0];

        return $result;
    }


    function getPortalV3IdFromV2Id($v2Id)
    {
        $query = "SELECT jos_portal_properties_v2_compatibility.v2_id,
                       jos_portal_properties.id AS v3_id
                  FROM jos_portal_properties jos_portal_properties
                       INNER JOIN
                       jos_portal_properties_v2_compatibility
                       jos_portal_properties_v2_compatibility
                          ON (jos_portal_properties.reg_id =
                                 jos_portal_properties_v2_compatibility.reg_id)
                 WHERE (jos_portal_properties_v2_compatibility.v2_id = $v2Id)";

        $result = WFactory::getSqlService()->select($query);


        return $result[0]['v3_id'];
    }


    function getCategoryName($categoryId)
    {
        if (WFactory::getHelper()->isNullOrEmptyString($categoryId))
            return "";
        $query = "SELECT jos_portal_property_categories.description,
                       jos_portal_property_categories.id
                  FROM jos_portal_property_categories jos_portal_property_categories
                 WHERE (jos_portal_property_categories.id = $categoryId)";

        $result = WFactory::getSqlService()->select($query);
        return $result[0]['description'];
    }

    function getFeatureNames($features)
    {
        if (empty($features))
            return "";

        $queryCondition = array();

        foreach ($features as $i => $e) {
            if ($e === true) {
                $queryCondition[] = "id=$i";
            }
        }

        $queryCondition = implode(" or ", $queryCondition);


        $query = "SELECT jos_portal_features.id, jos_portal_features.*
                  FROM jos_portal_features jos_portal_features
                 WHERE ($queryCondition)";

        $result = WFactory::getSqlService()->select($query);
        return $result;
    }

    function incrementViewCount($id)
    {
        /**
         * @var $propertySql PortalPortalPropertiesSql
         * @var $propertySqlUpdate PortalPortalPropertiesSql
         */
        $propertySql = WFactory::getSqlService()->getDbClass(__PORTAL_PORTAL_PROPERTIES_SQL);
        $propertySqlUpdate = WFactory::getSqlService()->getDbClass(__PORTAL_PORTAL_PROPERTIES_SQL);

        $propertySql->__id = $id;
        $propertySql->loadDataFromDatabase();
        $view = $propertySql->__viewcount;
        $view++;
        $propertySqlUpdate->__id = $id;
        $propertySqlUpdate->__viewcount = $view;

        $resulttemp = WFactory::getSqlService()->update($propertySqlUpdate);
        if ($resulttemp) {

            WFactory::getLogger()->debug("updated propertyid $id , current viewcount :    {$propertySqlUpdate->__viewcount}");
        }

        return $resulttemp;

    }

    public function acceptImageUpload($uploadModel = null)
    {


        if (is_array($uploadModel))
            $uploadModel = json_decode(json_encode($uploadModel));

        if (!empty($_FILES) || __ISUNITTEST) {

            $tempFile = $_FILES['file']['tmp_name'];          //3
            $targetPath = JPATH_BASE . DS . 'tmp';
            $targetFile = $targetPath . DS . $_FILES['file']['name'];  //5
            move_uploaded_file($tempFile, $targetFile); //6


            if (file_exists($targetFile)) {
                /**
                 * @var $savedSession AddPropertyModel
                 */
                $savedSession = unserialize(WFactory::getHelper()->getSessionVariable("addPropertyModel"));
                $savedSession->images[] = array('path' => $targetFile, 'uploaded' => false);
                WFactory::getHelper()->setSessionVariable("addPropertyModel", serialize($savedSession));
            }

            echo $targetFile;
            exit();
        }

    }

    /**
     * @param $addPropertyModel
     */
    public function acceptIncomingPropertyFromApp($addPropertyModel)
    {
        if (is_object($addPropertyModel))
            $addPropertyModel = $addPropertyModel->property;

        $sqlService = WFactory::getServices()->getSqlService();
        /**
         * @var $senToWebLog PortalPortalSenttowebLogSql
         */
        $senToWebLog = $sqlService->getDbClass(__PORTAL_PORTAL_SENTTOWEB_LOG_SQL);
        $senToWebLog->__command = "CREATED_BY_APP_OWNER";
        $senToWebLog->__data = json_encode($addPropertyModel);
        $senToWebLog->__direction = "INCOMING";
        $senToWebLog->__fromip = $_SERVER['REMOTE_ADDR'];
        $senToWebLog->__toip = "127.0.0.1";
        $senToWebLog->__type = "Property";
        $senToWebLog->__associated_id = 0;
        $senToWebLog->__realted_senttoweb_id = 0;
        $senToWebLog->__date = $sqlService->getMySqlDateTime();


        $id = $sqlService->insert($senToWebLog);


        $companyMail = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_COMPANY)->getCompanyEmail();
        $addYourProperty = "[ADD PROPERTY BY OWNER]";


        $subject = "$addYourProperty New property added by user through app ";
        $googleMap = "http://maps.google.com/maps?q={$addPropertyModel->latitude},{$addPropertyModel->longitude}";

        $message = "---User information  -----<br/>";
        $message .= "<b>Name:</b> {$addPropertyModel->userInfo->name} <br/>";
        $message .= "<b>Phone:</b> {$addPropertyModel->userInfo->phoneNumber} <br/>";
        $message .= "<b>Email:</b> {$addPropertyModel->userInfo->email} <br/>";
        $message .= "<br/>";
        $message .= "---Property information  -----<br/>";

        $message .= "<b>Province:</b> {$addPropertyModel->region_id->label} <br/>";
        $message .= "<b>District:</b> {$addPropertyModel->city_town_id->label} <br/>";
        $message .= "<b>Area:</b> {$addPropertyModel->zip_code_id->label} <br/>";
        $message .= "<b>Address:</b> {$addPropertyModel->street}, {$addPropertyModel->address}<br/>";
        $message .= "<b>Google Map:</b> $googleMap <br/>";


        $message .= "<b>Price:</b> {$addPropertyModel->price} <br/>";

        $type = intval($addPropertyModel->type_id);
        if ($type == 1) {
            $type = "Both sale / rent";
        } else if ($type == 2) {
            $type = "Sale";
        } else if ($type == 3) {
            $type = "Rental";
        }
        $message .= "<b>Type:</b> {$addPropertyModel->category_id->label} ({$addPropertyModel->mode_id->label})<br/>";
        $message .= "<b>Sale/Rent:</b>$type<br/>";
        $message .= "<b>Size:</b> {$addPropertyModel->size}<br/>";
        $message .= "<b>Rooms:</b> {$addPropertyModel->rooms}<br/>";
        $message .= "<b>Available From:</b> {$addPropertyModel->availableFrom} <br/>";

        $featuresFormatted = array();
        $featuresTemp = get_object_vars($addPropertyModel->features);

        foreach ($featuresTemp as $f) {
            if ($f->selected) {
                $featuresFormatted[] = $f->label;
            }
        }
        $featuresFormatted = implode(" , ", $featuresFormatted);
        $message .= "<b>Features:</b> {$featuresFormatted} <br/>";


        $message .= "<b>Description:</b> {$addPropertyModel->description} <br/>";

        $images = array();
        foreach ($addPropertyModel->pictures as $i) {

            $content = "";
            $imagePath = JPATH_ROOT . "/tmp/" . $i->imageName;
            if (file_exists($imagePath)) {
                $content = WFactory::getHelper()->imageToBase64($imagePath, false);
            }
            $images[] = array(
                "type" => "image/" . pathinfo($imagePath, PATHINFO_EXTENSION),
                "name" => pathinfo($imagePath, PATHINFO_FILENAME) . "." . pathinfo($imagePath, PATHINFO_EXTENSION),
                "content" => $content
            );

        }


//        if ($debugMode) {

        //$tos = array("shroukkhan_teset@gmail.com");
        if (__CUSTOMER_ID == "ERA_TH") {
            $emails = array();
            $emails[0] = array('email' => 'eracall@gmail.com', 'type' => 'to');
            $emails[1] = array('email' => 'ohmnine@yahoo.com', 'type' => 'cc');
        }


//        } else {
//            $officeEmail = $office['email'];
//
//            if (WFactory::getHelper()->isNullOrEmptyString($officeEmail))
//                $tos = array($companyMail, "shroukkhan@gmail.com");
//            else
//                $tos = array($officeEmail, $companyMail, "shroukkhan@gmail.com");
//        }

//        if (!WFactory::getHelper()->isNullOrEmptyString($officeName)) {
//            $subject .= "[ $officeName ]";
//        }

        WFactory::getLogger()->info("Sending property details mail to company owner");

        $response = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_MANDRILL)->sendMandrillMail(
            $subject,
            $message,
            $companyMail,
            $emails,
            "",
            true,
            $images
        );

        WFactory::getLogger()->info("Sending property details mail to company owner response: $response");

        WFactory::getLogger()->logEmail("ADD_PROPERTY_PROPERTY_ADDED", $subject, null, $companyMail, $tos, $message, $response);


        // send a thankyou note to the user
        $subject = "Your property has been registered with us successfully";
        $message = "Dear {$addPropertyModel->userInfo->name},<br/>
                    Your property has been registered with us and we shall get back to you shortly.<br/>";
        $response = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_MANDRILL)->sendMandrillMail(
            $subject,
            $message,
            $companyMail,
            $addPropertyModel->userInfo->email,
            "",
            true

        );

        WFactory::getLogger()->info("Sending thankyou for adding property to registerer : $response");
        WFactory::getLogger()->logEmail("ADD_PROPERTY_PROPERTY_ADDED_THANKYOU", $subject, null, $companyMail,
            $addPropertyModel->email, $message, $response);


    }

    public function acceptIncomingPropertyFromAppSaga($addPropertyModel)
    {
        if (is_object($addPropertyModel))
            $addPropertyModel = $addPropertyModel->property;

        $sqlService = WFactory::getServices()->getSqlService();
        /**
         * @var $senToWebLog PortalPortalSenttowebLogSql
         */
        $senToWebLog = $sqlService->getDbClass(__PORTAL_PORTAL_SENTTOWEB_LOG_SQL);
        $senToWebLog->__command = "CREATED_BY_APP_AGENT";
        $senToWebLog->__data = json_encode($addPropertyModel);
        $senToWebLog->__direction = "INCOMING";
        $senToWebLog->__fromip = $_SERVER['REMOTE_ADDR'];
        $senToWebLog->__toip = "127.0.0.1";
        $senToWebLog->__type = "Property";
        $senToWebLog->__associated_id = 0;
        $senToWebLog->__realted_senttoweb_id = 0;
        $senToWebLog->__date = $sqlService->getMySqlDateTime();


        $id = $sqlService->insert($senToWebLog);


        $companyMail = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_COMPANY)->getCompanyEmail();
        $addYourProperty = "[ADD PROPERTY BY AGENT]";


        $subject = "$addYourProperty New property added by user through app ";
        $googleMap = "http://maps.google.com/maps?q={$addPropertyModel->latitude},{$addPropertyModel->longitude}";

        $message = "---User information  -----<br/>";
        $message .= "<b>Name:</b> {$addPropertyModel->userInfo->full_name} <br/>";
        $message .= "<b>Phone:</b> {$addPropertyModel->userInfo->phone} <br/>";
        $message .= "<b>Email:</b> {$addPropertyModel->userInfo->email} <br/>";
        $message .= "<br/>";
        $message .= "---Property information  -----<br/>";

        $message .= "<b>Province:</b> {$addPropertyModel->region_id->label} <br/>";
        $message .= "<b>District:</b> {$addPropertyModel->city_town_id->label} <br/>";
        $message .= "<b>Area:</b> {$addPropertyModel->zip_code_id->label} <br/>";
        $message .= "<b>Address:</b> {$addPropertyModel->street}, {$addPropertyModel->address}<br/>";
        $message .= "<b>Google Map:</b> $googleMap <br/>";


        $message .= "<b>Price:</b> {$addPropertyModel->price} <br/>";

        $type = intval($addPropertyModel->type_id);
        if ($type == 1) {
            $type = "Both sale / rent";
        } else if ($type == 2) {
            $type = "Sale";
        } else if ($type == 3) {
            $type = "Rental";
        }
        $message .= "<b>Type:</b> {$addPropertyModel->category_id->label} ({$addPropertyModel->mode_id->label})<br/>";
        $message .= "<b>Sale/Rent:</b>$type<br/>";
        $message .= "<b>Size:</b> {$addPropertyModel->size}<br/>";
        $message .= "<b>Rooms:</b> {$addPropertyModel->rooms}<br/>";
        $message .= "<b>Available From:</b> {$addPropertyModel->availableFrom} <br/>";

        $featuresFormatted = array();
        $featuresTemp = get_object_vars($addPropertyModel->features);

        foreach ($featuresTemp as $f) {
            if ($f->selected) {
                $featuresFormatted[] = $f->label;
            }
        }
        $featuresFormatted = implode(" , ", $featuresFormatted);
        $message .= "<b>Features:</b> {$featuresFormatted} <br/>";


        $message .= "<b>Description:</b> {$addPropertyModel->description} <br/>";

        $images = array();
        foreach ($addPropertyModel->pictures as $i) {

            $content = "";
            $imagePath = JPATH_ROOT . "/tmp/" . $i->imageName;
            if (file_exists($imagePath)) {
                $content = WFactory::getHelper()->imageToBase64($imagePath, false);
            }
            $images[] = array(
                "type" => "image/" . pathinfo($imagePath, PATHINFO_EXTENSION),
                "name" => pathinfo($imagePath, PATHINFO_FILENAME) . "." . pathinfo($imagePath, PATHINFO_EXTENSION),
                "content" => $content
            );

        }


//        if ($debugMode) {
        //$tos = array("shroukkhan@gmail.com");
        if (__CUSTOMER_ID == "ERA_TH") {
            $emails = array();
            $emails[0] = array('email' => 'air_nantiya@hotmail.com', 'type' => 'to');
            $emails[1] = array('email' => 'ohmnine@yahoo.com', 'type' => 'cc');
        }
//        } else {
//            $officeEmail = $office['email'];
//
//            if (WFactory::getHelper()->isNullOrEmptyString($officeEmail))
//                $tos = array($companyMail, "shroukkhan@gmail.com");
//            else
//                $tos = array($officeEmail, $companyMail, "shroukkhan@gmail.com");
//        }

//        if (!WFactory::getHelper()->isNullOrEmptyString($officeName)) {
//            $subject .= "[ $officeName ]";
//        }

        WFactory::getLogger()->info("Sending property details mail to company owner");

        $response = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_MANDRILL)->sendMandrillMail(
            $subject,
            $message,
            $companyMail,
            $emails,
            "",
            true,
            $images
        );

        WFactory::getLogger()->info("Sending property details mail to company owner response: $response");

        WFactory::getLogger()->logEmail("ADD_PROPERTY_PROPERTY_ADDED", $subject, null, $companyMail, $to, $message, $response);


        // send a thankyou note to the user
        $subject = "Your property has been registered with us successfully";
        $message = "Dear {$addPropertyModel->userInfo->name},<br/>
                    Your property has been registered with us and we shall get back to you shortly.<br/>";
        $response = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_MANDRILL)->sendMandrillMail(
            $subject,
            $message,
            $companyMail,
            $addPropertyModel->userInfo->email,
            "",
            true

        );

        WFactory::getLogger()->info("Sending thankyou for adding property to registerer : $response");
        WFactory::getLogger()->logEmail("ADD_PROPERTY_PROPERTY_ADDED_THANKYOU", $subject, null, $companyMail,
            $addPropertyModel->email, $message, $response);


    }

    public function acceptImageUploadBase64($base64ImageFile)
    {

        if (is_object($base64ImageFile))
            $base64ImageFile = $base64ImageFile->image;
        if (is_array($base64ImageFile))
            $base64ImageFile = $base64ImageFile['image'];

        $imageFilePath = WFactory::getHelper()->base64ImageToFile($base64ImageFile);

        if (file_exists(JPATH_ROOT . "/tmp/$imageFilePath"))
            return array("success" => true, "image" => $imageFilePath);
        else
            return array("success" => false, "image" => '');

    }

    public function addPropertyImages($imageFileArray, $propertyId, $officeId)
    {
        $uploadedImages = array();
        //first delete ALL the images
        if (count($imageFileArray) > 0) {
            //delete old images first...
            $query = "delete from #__portal_property_images where property_id=$propertyId";
            WFactory::getSqlService()->delete($query);
        }


        foreach ($imageFileArray as $image) {

            $uploadModel = $this->getImageUploadModel();
            $uploadModel->propertyId = $propertyId;
            if ($officeId === null)
                $officeId = 0;
            $uploadModel->officeId = $officeId;
            $uploadModel->localUrl = $image;

            $uploadResult = $this->addPropertyImage($uploadModel);
            $uploadedImages[] = array("server_url" => $uploadModel->s3Url, "origin_url" => $uploadModel->localUrl);
        }
        return $uploadedImages;
    }


    /**
     * @param AddPropertyModel $addPropertyModel
     * @param bool $newSignup
     * @param bool $debugMode
     */
    public function sendSignupNotification($addPropertyModel, $newSignup, $debugMode = true)
    {
        ///home/khan/www/softverk-webportal-remaxth/templates/webportal/emailtemplates/signup.html


        $companyMail = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_COMPANY)->getCompanyEmail();
        $addYourProperty = "[ADD PROPERTY]";

        if ($newSignup)
            $subject = "$addYourProperty New user Signup";
        else
            $subject = "$addYourProperty Returning user {$addPropertyModel->email}";

        /**
         * -------------------------------------------------------------------|
         * ------------------------- get user profile ------------------------|
         * -------------------------------------------------------------------|
         * @var $profileTable PortalPortalUsersProfileSql
         */
        $profileTable = WFactory::getServices()->getSqlService()->getDbClass(__PORTAL_PORTAL_USERS_PROFILE_SQL);
        $profileTable->__joomla_user_id = $addPropertyModel->userId;
        $profileTable->loadDataFromDatabase(true);

        $address = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_ADDRESS)->getAddress($profileTable->__address_id);


        $message = "User information : <br/>";
        $message .= "Name: {$addPropertyModel->name} <br/>";
        $message .= "Phone: {$addPropertyModel->phone} <br/>";
        $message .= "Email: {$addPropertyModel->email} <br/>";
        $message .= "Province: {$address['region_name']} <br/>";


        if ($debugMode) {
            $tos = array("shroukkhan@gmail.com");
        } else
            $tos = array($companyMail);

        WFactory::getLogger()->info("Sending Signup mail to company owner");
        foreach ($tos as $to) {
            $response = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_MANDRILL)->sendMandrillMail(
                $subject,
                $message,
                $companyMail,
                $to,
                "",
                true
            );

            WFactory::getLogger()->info("Sending Signup mail to company owner response: $response");

            WFactory::getLogger()->logEmail("ADD_PROPERTY_SIGNUP", $subject, null, $companyMail, $to, $message, $response);
        }


    }

    /**
     * @param AddPropertyModel $addPropertyModel
     * @param bool $debugMode
     */
    public function sendPropertyEmail($addPropertyModel, $debugMode = true)
    {

        $companyMail = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_COMPANY)->getCompanyEmail();
        $addYourProperty = "[ADD PROPERTY]";


        $subject = "$addYourProperty New property added by user ";

        /**
         * -------------------------------------------------------------------|
         * ------------------------- get user profile ------------------------|
         * -------------------------------------------------------------------|
         * @var $profileTable PortalPortalUsersProfileSql
         * @var $propertyDetail PortalPortalPropertiesSql
         */
        $profileTable = WFactory::getServices()->getSqlService()->getDbClass(__PORTAL_PORTAL_USERS_PROFILE_SQL);
        $profileTable->__joomla_user_id = $addPropertyModel->userId;
        $profileTable->loadDataFromDatabase(true);

        $address = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_ADDRESS)->getAddress($profileTable->__address_id);

        $propertyDetail = WFactory::getServices()->getSqlService()->getDbClass(__PORTAL_PORTAL_PROPERTIES_SQL);
        $propertyDetail->__id = $addPropertyModel->property_id;
        $propertyDetail->loadDataFromDatabase();
        $propertyAddress = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_ADDRESS)->getAddress($propertyDetail->__address_id);

        $office = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_OFFICE)->getOffice($propertyDetail->__office_id);
        $officeName = $office['office_name'];
        $googleMap = "http://maps.google.com/maps?q={$propertyAddress["latitude"]},{$propertyAddress["longitude"]}";
        $categoryName = $this->getCategoryName($addPropertyModel->category_id[0]);
        $features = $this->getFeatureNames($addPropertyModel->features);
        $featuresFormatted = "";
        foreach ($features as $f) {
            $featuresFormatted .= " {$f['name']} , ";
        }

        $imagesFormatted = "";
        foreach ($addPropertyModel->images as $i) {
            $imagesFormatted .= "<p>$i->server_url</p><br/>";
        }

        $exclusive = $addPropertyModel->exclusive ? "YES" : "NO";


        $message = "---User information  -----<br/>";
        $message .= "<b>Name:</b> {$addPropertyModel->name} <br/>";
        $message .= "<b>Phone:</b> {$addPropertyModel->phone} <br/>";
        $message .= "<b>Email:</b> {$addPropertyModel->email} <br/>";
        $message .= "<b>Province:</b> {$address['region_name']} <br/>";
        $message .= "<br/>";
        $message .= "---Property information  -----<br/>";

        $message .= "<b>Province:</b> {$propertyAddress["region_name"]} <br/>";
        $message .= "<b>District:</b> {$propertyAddress["city_town_name"]} <br/>";
        $message .= "<b>Address:</b> {$propertyAddress["address"]} <br/>";
        $message .= "<b>Google Map:</b> $googleMap <br/>";
        $message .= "<b>Office:</b> $officeName <br/>";


        $message .= "<b>Price:</b> {$addPropertyModel->price_formatted} <br/>";
        $message .= "<b>Type:</b> $categoryName <br/>";
        $message .= "<b>Sale/Rent:</b> {$propertyDetail->__buy_rent} <br/>";
        $message .= "<b>Size:</b> {$addPropertyModel->size} sq.m.<br/>";
        $message .= "<b>Floor:</b> {$addPropertyModel->floor_level} <br/>";
        $message .= "<b>Available From:</b> {$addPropertyModel->movein} <br/>";
        $message .= "<b>Features:</b> {$featuresFormatted} <br/>";


        $message .= "<b>Description(English):</b> {$addPropertyModel->desc_english} <br/>";
        $message .= "<b>Description(Thai)   :</b> {$addPropertyModel->desc_thai} <br/>";
        $message .= "<b>Exclusive :</b> {$exclusive} <br/>";
        $message .= "<b>Images:</b> {$imagesFormatted} <br/>";

        if (__CUSTOMER_ID == "IHOUSE__TH") {
            $emails = array();
            $emails[0] = array('email' => 'eracall@gmail.com', 'type' => 'to');
            $emails[1] = array('email' => 'ohmnine@yahoo.com', 'type' => 'cc');
        }
        else{
            $emails = array(array('email' => $companyMail, 'type' => 'to'));
        }

        /*
                if ($debugMode) {
                    $tos = array("shroukkhan@gmail.com");
                } else {
                    $officeEmail = $office['email'];

                    if (WFactory::getHelper()->isNullOrEmptyString($officeEmail))
                        $tos = array($companyMail, "shroukkhan@gmail.com");
                    else
                        $tos = array($officeEmail, $companyMail, "shroukkhan@gmail.com");
                }
        */
        if (!WFactory::getHelper()->isNullOrEmptyString($officeName)) {
            $subject .= "[ $officeName ]";
        }

        WFactory::getLogger()->info("Sending property details mail to company owner");

        /* Test sending email
         * $emails = array(array('email' => 'nicky0085.scb@gmail.com', 'type' => 'to'));
         * */


        $response = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_MANDRILL)->sendMandrillMail(
            $subject,
            $message,
            $companyMail,
            $emails,
            "",
            true
        );

        WFactory::getLogger()->info("Sending property details mail to company owner response: $response");

        WFactory::getLogger()->logEmail("ADD_PROPERTY_PROPERTY_ADDED", $subject, null, $companyMail, $emails, $message, $response);


        // send a thankyou note to the user
        $subject = "Your property has been registered with us successfully";
        $message = "Dear {$addPropertyModel->name},<br/>Your property has been registered with us and we shall get back to you shortly.<br/>--";
        $response = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_MANDRILL)->sendMandrillMail(
            $subject,
            $message,
            $companyMail,
            $addPropertyModel->email,
            "",
            true
        );

        WFactory::getLogger()->info("Sending thankyou for adding property to registerer : $response");
        WFactory::getLogger()->logEmail("ADD_PROPERTY_PROPERTY_ADDED_THANKYOU", $subject, null, $companyMail,
            $addPropertyModel->email, $message, $response);

    }


    /**
     * @param $uploadModel ImageUploadModel
     * @return bool
     * @throws Exception
     */
    public function addPropertyImage(&$uploadModel)
    {
        $targetFile = $uploadModel->localUrl;
        if (WFactory::getHelper()->isUnitTest() && $uploadModel->localUrl === null) {
            $targetPath = JPATH_BASE . DS . 'tmp';
            $targetFile = "$targetPath/screenshot.png";
        }

        if (file_exists($targetFile)) {
            //upload To you know where!

            $propertyId = intval($uploadModel->propertyId);
            if ($propertyId === 0) {
                Wfactory::throwPortalException("Failed to upload image for add your property, missing property Id");
                return;
            } else {

                require_once JPATH_ROOT . DS . "libraries" . DS . "webportal" . DS . "services" . DS . "webservice" . DS . "websending" . DS . "websendingBase.php";
                $websendingBase = new WebsendingBase();
                $fileManager = WFactory::getFileManager();
                $commonConfig = WFactory::getConfig()->getWebportalConfigurationArray();
                /**
                 * @var iFileManager
                 */
                $fileManager = $fileManager->getFileManager($commonConfig["filesystem"]);

                $company = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_COMPANY)->getCompany();


                $propertyImagePath = $websendingBase->buildPropertyImagePath(
                    $company->id,
                    "",
                    $uploadModel->officeId,
                    "",
                    0,
                    "",
                    $propertyId,
                    ""
                );

                //now get sequence number [ last image + 1 ]
                $query = "SELECT COUNT(#__portal_property_images.id) AS imagecount
                          FROM #__portal_property_images #__portal_property_images
                         WHERE (#__portal_property_images.property_id = {$propertyId})";
                $imgCount = WFactory::getSqlService()->select($query);
                $imgCount = intval($imgCount[0]['imagecount']);
                $imgSequence = $imgCount + 1;

                $sourceFilePath = $targetFile;
                $uploadModel->localUrl = $sourceFilePath;
                $destinationPath = $propertyImagePath . "/image/{$propertyId}_{$imgSequence}." . pathinfo($sourceFilePath, PATHINFO_EXTENSION);
                $webPathURL = "";
                $tmpResult = $fileManager->putFile($sourceFilePath, $destinationPath, $webPathURL);
                $uploadModel->s3Url = $webPathURL;
                $uploadResult = $tmpResult;

                if ($tmpResult == false || empty($webPathURL)) {
                    WFactory::getLogger()->warn("Failed to upload property image $sourceFilePath to S3");
                } else {
                    WFactory::getLogger()->debug("Uploaded property image $sourceFilePath --> $webPathURL");
                }

// --------------------------------------------- NOT NECESSARY FOR NOW --------------------------------------------------------------
//
//                    $thumbTypes = array("list", "map");
//                    for ($i = 0; $i < count($thumbTypes); $i++) {
//                        $thumb = $thumbTypes[$i];
//
//                        $thumbWidth = $commonConfig["websending"]["{$thumb}page_thumb_size"]["width"];
//                        $thumbHeight = $commonConfig["websending"]["{$thumb}page_thumb_size"]["height"];
//
//                        if ($thumbWidth == null || $thumbHeight == null) {
//                            $thumbWidth = 221;
//                            $thumbHeight = 147;
//
//                            WFactory::getLogger()->warn("thumb width and height not defined, using default!");
//                        }
//
//                        $localThumbPath = $commonConfig["tempFolderPath"] . DS . uniqid() . ".jpg";
//
//                        $websendingBase->createThumbs($sourceFilePath, $localThumbPath, $thumbWidth, $thumbHeight);
//                        $destinationPath = $propertyImagePath . "/image/{$propertyId}_{$imgSequence}_{$thumb}thumb." . pathinfo($localThumbPath, PATHINFO_EXTENSION);
//                        $webPathURL = "";
//                        $tmpResult = $fileManager->putFile($localThumbPath, $destinationPath, $webPathURL);
//
//
//                    }
//
// -------------------------------------------- NOT NECESSARY FOR NOW --------------------------------------------------------------

                /**
                 * @var $imageClass PortalPortalPropertyImagesSql
                 */
                $imageClass = WFactory::getServices()->getSqlService()->getDbClass(__PORTAL_PORTAL_PROPERTY_IMAGES_SQL);

                $imageClass->__property_id = $uploadModel->propertyId;
                $imageClass->__origin_url = $uploadModel->localUrl;
                $imageClass->__server_url = $uploadModel->s3Url;

                //pathinfo($imageData["DefaultImagePath"], PATHINFO_FILENAME) . "." . pathinfo($imageData["DefaultImagePath"], PATHINFO_EXTENSION)
                $imageClass->__image_file_name = basename($uploadModel->s3Url);
                $imageClass->__is_created = 1;

                WFactory::getServices()->getSqlService()->insert($imageClass);

                return $uploadResult;
            }
        }
        return false;
    }

    public function checkIfRouteIsProperty($id)
    {
        if (is_numeric($id)) {
            $query = "SELECT #__portal_properties.id
                      FROM #__portal_properties #__portal_properties
                     WHERE (#__portal_properties.id = $id)";
            $result = WFactory::getSqlService()->select($query);
            if (!empty($result))
                return $result[0]['id'];
        }

        //now check if its one of the property reg id [ directly]
        $query = "SELECT #__portal_properties.id
                      FROM #__portal_properties #__portal_properties
                     WHERE (#__portal_properties.reg_id = '$id' and #__portal_properties.is_deleted=0)";
        $result = WFactory::getSqlService()->select($query);
        if (!empty($result))
            return $result[0]['id'];


        $id = str_replace(":", "-", $id);
        $query = "SELECT #__portal_properties.id
                      FROM #__portal_properties #__portal_properties
                     WHERE (#__portal_properties.reg_id = '$id' and #__portal_properties.is_deleted=0)";
        $result = WFactory::getSqlService()->select($query);
        if (!empty($result))
            return $result[0]['id'];

        //nothing matched..try to get the id out of it...
        $id = explode("-", $id);
        $id = trim($id[count($id) - 1]); //http://localhost/softverk-webportal-remaxth/th//blablahlblah-4652
        if (is_numeric($id)) {
            $query = "SELECT #__portal_properties.id
                      FROM #__portal_properties #__portal_properties
                     WHERE (#__portal_properties.id = $id )";
            $result = WFactory::getSqlService()->select($query);
            if (!empty($result))
                return $result[0]['id'];
        }
        return null;


    }

    function getDetail($propertyId)
    {
        if (is_array($propertyId)) {
            $propertyId = $propertyId['propertyId'];
        }

        $hash = JFactory::getApplication()->input->getBase64('hash', '');
        if (!WFactory::getHelper()->isNullOrEmptyString($hash)) {
            $hash = base64_decode($hash);
        } else
            $hash = false;

        if (is_object($propertyId)) {
            $propertyId = $propertyId->property_id;
        }

        $searchModel = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_SEARCH)->getSearchModel();
        $searchModel->returnType = RETURN_TYPE_DETAIL;
        $searchModel->property_id = $propertyId;

        $propertyDetail = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_PROPERTIES)->search($searchModel);

        /**
         * @var $propertyDetail PropertyDetailsModel
         */
        $propertyDetail = $propertyDetail[0];

        if (__COUNTRY === "IS") {
            $propertyDetail->property_phone_link = "http://ja.is/?q={$propertyDetail->address},{$propertyDetail->zip_code}";
            $propertyDetail->property_registration_link = "http://www.skra.is/default.aspx?pageid=1000&lsvfn=-1&submitbutton=leita&streetname={$propertyDetail->address}";

            $zipInt = intval($propertyDetail->zip_code);

            if ($zipInt >= 100 && $zipInt <= 150) //But the blueprints/teikningar should only be enabled/clickable for zip 100-150.
                $propertyDetail->property_blueprint_link = "http://teikningar.reykjavik.is/fotoweb/Grid.fwx?archiveId=5000&SF_FIELD1_MATCHTYPE=exact&SF_FIELD1=" . urlencode($propertyDetail->address) . "&doSearch=Leita";
        }


        $images = $this->getAllPropertyImages($propertyId);
        $features = $this->getAllPropertyFeatures($propertyId);
        $videos = $this->getAllPropertyVideos($propertyId);

        //a bug fix for jae. can be removed later
        $propertyDetail->sales_agent_full_name = trim(str_replace("Array", "", $propertyDetail->sales_agent_full_name));
        //fix agent name..something wrong in websending and sales agents are names have no spaces in between
        //the following process converts camelCase to spaces

        $propertyDetail->sales_agent_full_name = WFactory::getHelper()->camelToSpaces($propertyDetail->sales_agent_full_name);

        foreach ($images as $i) {
            /**
             * @var $i PortalPortalPropertyImagesSql
             */

            if (!WFactory::getHelper()->isNullOrEmptyString($i->__server_url)) {
                $propertyDetail->images[] = $i->__server_url;

                $__tempImage = new PropertyImage();
                $__tempImage->serverUrl = $i->__server_url;
                $__tempImage->alt = $i->__alt;
                $__tempImage->description = trim($i->__description);
                $propertyDetail->imagesV2[] = $__tempImage;

            }
        }

        $propertyDetail->images = array_unique($propertyDetail->images);


        foreach ($features as $f) {
            $name = trim(preg_replace("/[^A-Za-z0-9 ]/", '', $f->__name_en));
            $name = strtoupper($name);
            /**
             * @var $f PortalPortalPropertyFeaturesSql
             */
            $propertyDetail->features[] = array(
                "name" => JText::_(strtoupper($name)),
                "id" => $f->__feature_id);
        }

        foreach ($videos as $v) {
            /**
             * @var $v PortalPortalPropertyVideosSql
             */
            if (!WFactory::getHelper()->isNullOrEmptyString($v->__origin_url)) {


                $__tempVideo = new PropertyVideo();
                $__tempVideo->originUrl = $v->__origin_url;
                $__tempVideo->providerUrl = $v->__provider;
                $__tempVideo->sequence = intval($v->__sequence);
                $__tempVideo->alt = $v->__alt;
                $__tempVideo->description = trim($v->__description);

                if (strpos($v->__provider, 'youtube.com') !== false || strpos($v->__provider, 'youtu.be') !== false) {
                    $__tempVideo->providerName = 'youtube';
                    $__tempVideo->providerVideoFileName = WFactory::getHelper()->getYoutubeVideoFileName($__tempVideo->originUrl);
                }


                $propertyDetail->videos[] = $__tempVideo;


            }
        }

        //$this->incrementViewCount($propertyId);

        return $propertyDetail;


    }

    /**
     * @param $propertyId
     * @param null $propertyItself | If you want to receive the property detail as well ( this includes no pictures!)
     * @return PropertyAddressModel
     */
    function getPropertyAddress($propertyId, &$propertyItself = null)
    {


        if (is_object($propertyId)) {
            $propertyId = $propertyId->property_id;
        }

        $searchModel = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_SEARCH)->getSearchModel();
        $searchModel->returnType = RETURN_TYPE_DETAIL;
        $searchModel->property_id = $propertyId;

        $propertyDetail = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_PROPERTIES)->search($searchModel);
        /**
         * @var $propertyDetail PropertyDetailsModel
         */
        $propertyDetail = $propertyDetail[0];

        $propertyItself = $propertyDetail;

        if (is_object($propertyDetail))
            $address = $propertyDetail->getAddress();


        return $address;

    }

    /**
     * @param $propertyId
     * @return string
     */
    public function getJRouteFormattedPropertyRoute($propertyId)
    {
        /**
         * this is set in /var/www/softverk-webportal/libraries/webportal/services/property/propertyListModel.php
         * In order to make sure we do not do mysql search again !!!
         * @var $currentPropertyModel PropertyDetailsModel
         */
        $lang = WFactory::getHelper()->getCurrentlySelectedLanguage();

        global $currentPropertyModel;
        if ($currentPropertyModel == null) {
            $address = $this->getPropertyAddress($propertyId, $currentPropertyModel);

        } else {
            $address = WFactory::getSqlService()->getSqlServiceClass(__PROPPERTY_PORTAL_ADDRESS)->getAddressByLanguage($propertyId, $lang);
        }
        if (is_object($address) && $address !== null)
            $address = $address->getJRouteFormattedAddress();
        $category = $currentPropertyModel->category_name;

        //$x = mb_strlen($address);
        $address = WFactory::getSqlService()->getSqlServiceClass(__PROPPERTY_PORTAL_ADDRESS)->getAddressByLanguage($propertyId, $lang);

        $title = $this->getTitleByLanguage($propertyId, $lang);
        //$address .= mb_substr($address, 0, 75);
        //$y= mb_strlen($address);

        if (__TITLE_MODE == true) {
            $result = WFactory::getHelper()->removeUnsupportedUrlChars(($category) . "-" . ($title) . "-$propertyId");
        } else {
            $result = WFactory::getHelper()->removeUnsupportedUrlChars(($category) . "-" . ($address) . "-$propertyId");
        }
        $result = iconv(mb_detect_encoding($result, mb_detect_order(), true), "UTF-8", $result);

        return $result;
    }

    /**
     * @return PropertyListModel
     */
    function getPropertyListModel()
    {
        require_once JPATH_LIBRARY_WEBPORTAL_SERVICES . DS . "property" . DS . "propertyListModel.php";
        return new PropertyListModel();
    }

    function getPropertyMapModel()
    {
        require_once JPATH_LIBRARY_WEBPORTAL_SERVICES . DS . "property" . DS . "propertyMapModel.php";
        return new PropertyMapModel();
    }

    function getUrlToDirectPage($propertyId)
    {

        $directPath = JRoute::_("index.php?option=com_webportal&view=property&property-id={$propertyId}");
        //remove /api/v1/whatever!!
        $directPath = WFactory::getHelper()->getCurrentlySelectedLanguage() . substr($directPath, strpos($directPath, '/property/'));

        $directPath = str_replace('/property/', '/', $directPath);
        $directPath = str_replace('/property/', '/', $directPath);

        return JUri::base() . $directPath;
    }

    /**
     * @return PropertyDetailsModel
     */
    function getPropertyDetailsModel()
    {
        require_once JPATH_LIBRARY_WEBPORTAL_SERVICES . DS . "property" . DS . "propertyDetailsModel.php";
        return new PropertyDetailsModel();
    }

    function deleteProperty($propertyId)
    {
        $resultArray = array(
            "success" => false,
            "message" => ""
        );

        if (is_array($propertyId)) {
            $propertyId = $propertyId['property-id'];
        }

        if (intval($propertyId) > 0) {

            /**
             * @var $officeDb PortalPortalOfficesSql
             */
            $propertyDb = WFactory::getSqlService()->getDbClass(__PORTAL_PORTAL_PROPERTIES_SQL);
            //$propertyDb->__property_id = $propertyId;
            //$propertyDb->loadDataFromDatabase();

            //$propertyDb->__sent_to_web = 0;

            //$updateResult = WFactory::getSqlService()->update($propertyDb);

            /*
            //agents
            $query = "update jos_portal_sales set is_deleted = 1 , show_on_web = 0 where office_id = $officeId";
            $agentsUpdated = WFactory::getSqlService()->update($query);
*/
            //properties
            $query = "update jos_portal_properties set is_deleted = 1 WHERE id = $propertyId";
            $propertiesUpdated = WFactory::getSqlService()->update($query);

            $resultArray = array(
                "success" => true,
                "message" => "Property {$propertyDb->__unique_id} is deleted !"
            );

        }
        $resultArray['message'] = 'Failed to parse property id ';

        //echo json_encode($resultArray);
        return true;
        /**
         * @var $properties PortalPortalPropertiesSql
         */
        //$properties = WFactory::getSqlService()->getDbClass(__PORTAL_PORTAL_PROPERTIES_SQL);
        //$properties->__id = $propertyId;
        //$properties->__is_deleted = true;
        //$properties->__last_update = WFactory::getSqlService()->getMySqlDateTime();

        //return WFactory::getSqlService()->update($properties);

    }

    /**
     * @param $officeDbClass PortalPortalOfficesSql
     * @return bool
     */
    public function updatePropertyOfficeInformation($officeDbClass)
    {
        $officeId = $officeDbClass->__id;
        /**
         * @var $properties PortalPortalPropertiesSql
         */
        $properties = WFactory::getSqlService()->getDbClass(__PORTAL_PORTAL_PROPERTIES_SQL);
        $properties->__office_id = $officeId;

        $propertiesByOffice = $properties->loadDataFromDatabase(false);

        $result = true;
        /**
         * @var $p PortalPortalPropertiesSql
         */
        foreach ($propertiesByOffice as $p) {
            $p->__office_name = $officeDbClass->__office_name;
            $p->__office_email = $officeDbClass->__email;
            $p->__office_logo_path = $officeDbClass->__logo;
            $p->__office_phone = $officeDbClass->__phone;


            $result = $result && WFactory::getSqlService()->update($p);
        }
        return $result;
    }

    /**
     * @param $agentDbClass PortalPortalSalesSql
     * @return bool
     */
    public function updatePropertyAgentInformation($agentDbClass)
    {
        $agentId = $agentDbClass->__id;
        /**
         * @var $properties PortalPortalPropertiesSql
         * @var $p PortalPortalPropertiesSql
         */
        $properties = WFactory::getSqlService()->getDbClass(__PORTAL_PORTAL_PROPERTIES_SQL);
        $properties->__sale_id = $agentId;

        $propertiesByAgent = $properties->loadDataFromDatabase(false);

        $result = true;
        foreach ($propertiesByAgent as $p) {
            $p->__sales_agent_full_name = "{$agentDbClass->__first_name} {$agentDbClass->__middle_name} {$agentDbClass->__last_name}";
            $p->__sales_agent_office_phone = $agentDbClass->__phone;
            $p->__sales_agent_mobile_phone = $agentDbClass->__mobile;
            $p->__sales_agent_email = $agentDbClass->__email;
            $p->__sales_agent_image = $agentDbClass->__image_file_path;
            $result = $result && WFactory::getSqlService()->update($p);
        }
        return $result;
    }

    /**
     * @param $propertyId
     * @return array|bool|object
     */
    public function getAllPropertyImages($propertyId)
    {
        /**
         * @var $imageTable PortalPortalPropertyImagesSql
         */
        $imageTable = WFactory::getSqlService()->getDbClass(__PORTAL_PORTAL_PROPERTY_IMAGES_SQL);
        $imageTable->__property_id = $propertyId;

        $images = $imageTable->loadDataFromDatabase(false, "ORDER BY id asc");


        return $images;

    }

    public function mockFeaturesTree()
    {
        /**
         * @var $featuresTable PortalPortalFeaturesSql
         * @var $f PortalPortalFeaturesSql
         */
        $featuresTable = WFactory::getSqlService()->getDbClass(__PORTAL_PORTAL_FEATURES_SQL);
        $features = $featuresTable->loadDataFromDatabase(false);

        $data = array();
        $limit = 4;
        $index = 0;
        foreach ($features as $f) {


            $data[] = array(
                'id' => $f->__id,
                'type' => $f->__type,
                'description' => $f->__name
            );

            $index++;
            if ($index > $limit)
                break;

        }

        return $data;
    }

    public function getAllPropertyFeatures($propertyId)
    {
        /**
         * @var $featuresTable PortalPortalPropertyFeaturesSql
         */
        $featuresTable = WFactory::getSqlService()->getDbClass(__PORTAL_PORTAL_PROPERTY_FEATURES_SQL);
        $featuresTable->__property_id = $propertyId;

        $features = $featuresTable->loadDataFromDatabase(false);


        return $features;
    }

    public function deletePropertyImages($propertyId, $deleteFromFileSystem = false)
    {

        $query = "SELECT #__portal_property_images.*
                      FROM #__portal_property_images #__portal_property_images
                     WHERE (#__portal_property_images.property_id = $propertyId)";

        $result = WFactory::getSqlService()->select($query);
        $deleteResult = true;
        if ($deleteFromFileSystem) {
            foreach ($result as $r) {
                $filePath = $r['server_url'];
                $deleteResult = $deleteResult && WFactory::getFileManager()->getFileManager()->deleteFile($filePath);
            }
        }
        $deleteQuery = "DELETE FROM #__portal_property_images
                          WHERE (#__portal_property_images.property_id = $propertyId);";

        $deleteResult = $deleteResult && WFactory::getSqlService()->delete($deleteQuery);

        return $deleteResult;

    }

    public function getAllPropertyVideos($propertyId)
    {
        /**
         * @var $videoClass PortalPortalPropertyVideosSql
         */
        $videoTable = WFactory::getSqlService()->getDbClass(__PORTAL_PORTAL_PROPERTY_VIDEOS_SQL);
        $videoTable->__property_id = $propertyId;

        $videos = $videoTable->loadDataFromDatabase(false, "ORDER BY sequence asc");


        return $videos;

    }

    public function togglePropertyPublish()
    {

        $resultArray = array(
            "success" => false,
            "message" => ""
        );
        $input = JFactory::getApplication()->input;
        $publish = $input->getInt('publish', null);
        $propertyId = $input->getInt('property-id', 0);

        /**
         * @var $propertyDb PortalPortalPropertiesSql
         */
        $propertyDb = WFactory::getSqlService()->getDbClass(__PORTAL_PORTAL_PROPERTIES_SQL);
        $propertyDb->__id = $propertyId;
        $propertyDb->loadDataFromDatabase();

        if ($publish === 0 || $publish === 1) {
            $propertyDb->__sent_to_web = $publish;
            $updateResult = WFactory::getSqlService()->update($propertyDb);

            $resultArray['success'] = true;
            $resultArray['message'] = "$publish";


        } else {
            $resultArray['success'] = false;
            $resultArray['message'] = "Failed to read publish state from JInput";
        }

        echo json_encode($resultArray);
        exit(0);

    }

    function convertSizeUnit($input_area)
    {
        if (__COUNTRY == "TH") {

            $rai = 1600;
            $ngan = 400;
            $sqw = 4;

            $rai_text = '<span class="area--unit"> ' . JText::_("RAI") . '</span>';
            $ngan_text = '<span class="area--unit"> ' . JText::_("NGAN") . '</span>';
            $sqw_text = '<span class="area--unit"> ' . JText::_("SQW") . '</span>';
            $sqm_text = '<span class="area--unit"> ' . JText::_("SQM") . '</span>';

            $area_sqm = number_format($input_area);

            if ($input_area >= $sqw) {
                $area_rai = floor(($input_area) / $rai);
                if ($area_rai > 0) {
                    $area_rai_result = $area_rai . $rai_text;
                }

                $area_ngan = floor(round(($input_area) % $rai) / $ngan);
                if ($area_ngan > 0) {
                    $area_ngan_result = $area_ngan . $ngan_text;
                }

                $area_sqw = round(((($input_area) % $rai) % $ngan) / $sqw);
                if ($area_sqw > 0) {
                    $area_sqw_result = $area_sqw . $sqw_text;
                }

                $output_area = $area_rai_result . ' ' . $area_ngan_result . ' ' . $area_sqw_result;
            } else {
                $output_area = $area_sqm . $sqm_text;
            }
        } else {
            $output_area = $input_area;
        }
        return $output_area;
    }

    function calculateSqm($input_category)
    {
        $config = WFactory::getConfig()->getWebportalConfigurationArray();
        $condoTypes = $config['sagaApi']['condoTypes'];
        $houseTypes = $config['sagaApi']['houseTypes'];

        return (in_array(intval($input_category), $condoTypes) || in_array(intval($input_category), $houseTypes));
    }

    function pricePerArea($input_price, $input_area, $input_category)
    {


        $calculateSQM = $this->calculateSqm($input_category);

        if (__COUNTRY == "TH") {

            $raiLimit = 32000;
            $rai = 1600;
            $sqw = 4;

            if ($input_area >= $raiLimit) {
                $area_rai = round(($input_area) / $rai);
                $result = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_CURRENCY)->convertCurrency(round($input_price / $area_rai));
            } else if ($calculateSQM) {
                $result = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_CURRENCY)->convertCurrency(round($input_price / $input_area));
            } else {
                $area_sqw = round(($input_area) / $sqw);
                $result = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_CURRENCY)->convertCurrency(round($input_price / $area_sqw));
            }

            return $result;
        } else {
            $result = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_CURRENCY)->convertCurrency(round($input_price / $input_area));
            return $result;
        }
    }

    //Use only Forbest
    function getInvestment($en_lang, $th_lang)
    {
        if (preg_match('/Investor/i', $en_lang) || preg_match('/Investment/i', $en_lang) || preg_match('/ลงทุน/i', $th_lang)) {
            $investmentProperty = 'true';
        } else {
            $investmentProperty = 'false';
        }

        return $investmentProperty;
    }

    function getTitleByLanguage($propertyId, $lang)
    {

        $query = "SELECT title
                  FROM #__portal_properties
                 WHERE (id = $propertyId)";

        $result = WFactory::getSqlService()->select($query);
        $title = $result[0]['title'];

        // try to parse as json
        try {

            $json_title = json_decode($title);

            if (!empty($json_title)) {
                $json_title = get_object_vars($json_title);
                return $json_title[$lang];
            } else {


                if (preg_match('/<' . $lang . '>/i', $result[0]['title'])) {
                    $title = substr($result[0]['title'], strpos($result[0]['title'], '<' . $lang . '>') + 4);
                    $title = substr($title, 0, strpos($title, '</' . $lang . '>'));
                    $title_lang = $title;
                } //use for legacy version
                else {
                    $title = substr($result[0]['title'], 0, strpos($result[0]['title'], '<'));
                    $title_lang = $title;
                }

                if (!WFactory::getHelper()->isNullOrEmptyString($title_lang)) {
                    return $title_lang;
                } else {
                    return $result[0]['title'];
                }
            }
        } catch (Exception $e) {

        }
    }

    function getAddressByLanguage($propertyId, $lang)
    {

        $query = "SELECT address
                  FROM #__portal_properties
                 WHERE (id = $propertyId)";

        $result = WFactory::getSqlService()->select($query);
        $address = $result[0]['address'];

        // try to parse as json
        try {

            $json_address = json_decode($address);

            if (!empty($json_address)) {
                $json_address = get_object_vars($json_address);
                return $json_address[$lang];

            } else {


                if (preg_match('/<' . $lang . '>/i', $result[0]['address'])) {
                    $address = substr($result[0]['address'], strpos($result[0]['address'], '<' . $lang . '>') + 4);
                    $address = substr($address, 0, strpos($address, '</' . $lang . '>'));
                    $address_lang = $address;
                } //use for legacy version
                else {
                    $address = substr($result[0]['address'], 0, strpos($result[0]['address'], '<'));
                    $address_lang = $address;
                }

                if ($address_lang !== null) {
                    return $address_lang;
                } else {
                    return $result[0]['address'];
                }
            }
        } catch (Exception $e) {

        }
    }

    function getDescriptionByLanguage($propertyId, $lang)
    {

        $query = "SELECT description_text_th
                  FROM #__portal_properties
                 WHERE (id = $propertyId)";

        $result = WFactory::getSqlService()->select($query);

        $description_text_th = $result[0]['description_text_th'];

        // try to parse as json
        try {

            $json_description = json_decode($description_text_th);

            if (!empty($json_description)) {
                $json_description = get_object_vars($json_description);
                return $json_description[$lang];

            } else {


                if (preg_match('/{' . $lang . '}/i', $result[0]['description_text_th'])) {
                    $description = substr($result[0]['description_text_th'], strpos($result[0]['description_text_th'], '{' . $lang . '}') + 4);
                    $description = substr($description, 0, strpos($description, '{/' . $lang . '}'));
                    $description_lang = $description;
                } //use for legacy version

                else {
                    $description = substr($result[0]['description_text_th'], 0, strpos($result[0]['description_text_th'], '{'));
                    $description_lang = $description;
                }

                if (!WFactory::getHelper()->isNullOrEmptyString($description_lang)) {
                    return $description_lang;
                } else {
                    return $result[0]['description_text_th'];
                }
            }
        } catch (Exception $e) {

        }
    }
}
