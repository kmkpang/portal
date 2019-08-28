<?php
// No direct access to this file

//This is Dummy, created in order to generate a bacnet menu item.
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

require_once JPATH_LIBRARY_WEBPORTAL_SERVICES . DS . 'modelbase.php';
require_once JPATH_LIBRARY_WEBPORTAL_SERVICES . DS . 'search/searchModel.php';
// /home/khan/www/softverk-webportal-remaxth/libraries/webportal/services/search/searchModel.php

if (!defined('RENT')) {
    define('BUY', "SALE");
    define('RENT', "RENT");
}

/**
 * HTML View class for the Webportal Component
 */
class WebportalViewAddproperty extends JViewLegacy
{


    var $current_step = 1;
    var $form_next_step = 2;
    var $previous_step = 1;
    var $previous_step_link = "";
    var $form_data_value = "";
    var $form_action = "index.php?option=com_webportal&view=addproperty";
    var $form_method = "post";
    var $direct_layout_access = false;
    var $debugMode = false;
    /**
     * @var SearchModel
     */
    var $submitValue = null;
    /**
     * @var AddPropertyModel
     */
    var $addPropertyModel = null;
    /**
     * @var JInput
     */
    var $input;
    var $currentProperty = 0;
    var $itemId;

    function __construct()
    {
        $this->addPropertyModel = new AddPropertyModel();
        parent::__construct();
    }

    // Overwriting JView display method
    function display($tpl = null)
    {
        //test

        //$x = JRoute::_("index.php?option=com_webportal&view=addproperty&layout=step1&Itemid=" . $this->itemId);


        $this->itemId = intval(JFactory::getApplication()->getMenu()->getActive()->id);
        if ($this->itemId === 0) {
            $config = WFactory::getConfig()->getWebportalConfigurationArray();
            $this->itemId = $config['addYourPropertyItemId'];
        }
        $this->input = JFactory::getApplication()->input;


        //json_decode(base64_decode($this->input->getString('submit_value', '')))
        $submitValue = base64_decode($this->input->getString('submit_value', ''));
        $submitValue = preg_replace_callback('/\\\\u([0-9a-fA-F]{4})/', function ($match) {
            return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UTF-16BE');
        }, $submitValue);

        $this->submitValue = json_decode($submitValue);


        $savedSession = WFactory::getHelper()->getSessionVariable("addPropertyModel");
        $savedSession = unserialize($savedSession);

        if ($savedSession) {

            if (!is_object($savedSession))
                $savedSession = json_decode(json_encode($savedSession));

            if (get_class($savedSession) !== 'AddPropertyModel') {
                $addPropertyModel = new AddPropertyModel();
                $addPropertyModel->convertToAddPropertyModel($savedSession);
                $savedSession = $addPropertyModel;
            }

            $this->addPropertyModel = (object)array_merge((array)$savedSession, (array)$this->submitValue);;

        } else {

            $this->addPropertyModel = new AddPropertyModel();
            $this->addPropertyModel = (object)array_merge((array)$savedSession, (array)$this->submitValue);;
            if ($this->debugMode) {
                $this->_enableDebug();
            }

        }


        $stringClass = serialize($this->addPropertyModel);

        $x = base64_encode($stringClass);

        WFactory::getHelper()->setSessionVariable("addPropertyModel", $stringClass);

        WFactory::getLogger()->debug("addPropertyModel --> \n$stringClass");
        WFactory::getLogger()->debug("addPropertyModel --> \n" . json_encode($this->addPropertyModel));


        if ($this->_verifyAccess())
            $this->_processStep();


        parent::display($tpl);
    }

    function _verifyAccess()
    {
        if (WFactory::getHelper()->isPostBack()) {
            $this->current_step = intval($this->input->get('current_step', 1));
            $this->form_next_step = intval($this->input->get('form_next_step', 2));
        } else {
            $this->direct_layout_access = true;
            $active = JFactory::getApplication()->getMenu()->getActive();
            $layout = "";
            if (isset($active->query['layout'])) {
                $layout = $active->query['layout'];
            } else if ($this->input->get('layout', false)) {
                $layout = $this->input->get('layout', false);
            }
            $this->current_step = intval(trim(str_replace('step', '', $layout)));
            if ($this->current_step === 0)
                $this->current_step = 1;
        }

        if ($this->current_step == 1) {
            return true;
        }
        if ($this->current_step == 2 && $this->addPropertyModel->userId > 0) {
            return true;
        }
        if (
            ($this->current_step == 3 || $this->current_step == 4 || $this->current_step == 5) &&
            $this->addPropertyModel->userId > 0 &&
            $this->addPropertyModel->property_id > 0
        ) {
            return true;
        } else
            $this->__processFailed(JText::_("MISSING INFORMATION"), $this->addPropertyModel->property_id, --$this->current_step);


    }


    function _processStep()
    {

        if (WFactory::getHelper()->isPostBack()) {
            $this->current_step = intval($this->input->get('current_step', 1));
            $this->form_next_step = intval($this->input->get('form_next_step', 2));


            $function = "__processStep" . $this->current_step;

            if (method_exists($this, $function)) {
                $result = $this->$function();

                if ($result) {
                    $this->current_step++;
                    $this->form_next_step++;
                    //SAVE IT ANYWAY ...if successful !
                    WFactory::getHelper()->setSessionVariable("addPropertyModel", serialize($this->addPropertyModel));
                }

            } else {
                $msg = "Invalid step method called $function";
                WFactory::getLogger()->fatal($msg, __LINE__, __FILE__);
                WFactory::throwPortalException($msg);
            }

        } else {
            //TODO: Access control to this ....
            //first time access
            $active = JFactory::getApplication()->getMenu()->getActive();
            if (isset($active->query['layout'])) {
                $this->setLayout($active->query['layout']);
                $this->direct_layout_access = true;
            } else if ($this->input->get('layout', false)) {

                $propertyId = $this->input->get('property-id', 0);

                $this->setLayout($this->input->get('layout'));

                if ($propertyId !== 0)
                    $this->addPropertyModel->property_id = $propertyId;


                $this->direct_layout_access = true;
            }


        }


    }

    function __processStep1()
    {

        WFactory::getLogger()->debug("Processing add your property step 1");

        $newSignup = true;

        /*--- disabled for now ---*/
        //$this->_checkIfUserAlreadyExists();
        $captchaVerification = WFactory::getHelper()->getReCaptchaVerification();
        $captchaVerification = true;
        //verify captcha
        if ($captchaVerification || $this->debugMode) {

            $name = $this->addPropertyModel->name;
            $phone = $this->addPropertyModel->phone;
            $province = $this->addPropertyModel->user_region_id;
            $email = $this->addPropertyModel->email;


            $password1 = $this->input->get('password1', null, 'STRING');
            $password2 = $this->input->get('password2', null, 'STRING');


            $userGroup = WFactory::getConfig()->getWebportalConfigurationArray();
            $userGroup = $userGroup['users']['propertySubmitterGroups'];

            if (WFactory::getHelper()->isNullOrEmptyString($password1))
                $password1 = "AVeryRandomPasswordDISABLEDFORNOW@__";

            /**
             * -------------------------------------------------------------------|
             * -------------------------- insert user ----------------------------|
             * -------------------------------------------------------------------|
             */

            WFactory::getLogger()->debug("Inserting new user for front end usage", __LINE__, __FILE__);
            $user = WFactory::getServices()
                ->getServiceClass(__PROPPERTY_PORTAL_USERS)
                ->createJoomlaUser($name, $email, $userGroup, 0, $email, $password1);
            $existingUser = $user;
            if ($user['success'] === false) {
                //may be he Already exist???

                $existingUser = WFactory::getServices()
                    ->getServiceClass(__PROPPERTY_PORTAL_USERS)
                    ->getUserAccountByEmail($email);
                if ($existingUser) {
                    $newSignup = false;
                    WFactory::getLogger()->info("Already Existing User coming back with email address $email", __LINE__, __FILE__);
                    $user['success'] = true;
                    $user['data'] = $existingUser;
                }


            }


            if ($user['success'] === true || $this->debugMode) {

                $user = $user['data'];
                /*------- disabled for nwo -------*/
//                /**
//                 * -------------------------------------------------------------------|
//                 * -------------------------- login to joomla ------------------------|
//                 * -------------------------------------------------------------------|
//                 */
//                try{
//
//                    $user = WFactory::getServices()
//                        ->getServiceClass(__PROPPERTY_PORTAL_USERS)
//                        ->loginToJoomla($email, $password1);
//
//                }catch(Exception $e){
//
//                }


                if ($user->id == 0) {
                    $user = $existingUser;
                }

                /**
                 * -------------------------------------------------------------------|
                 * -------------------------- insert/get user profile ----------------|
                 * -------------------------------------------------------------------|
                 * @var $profileTable PortalPortalUsersProfileSql
                 */
                $profileTable = WFactory::getServices()->getSqlService()->getDbClass(__PORTAL_PORTAL_USERS_PROFILE_SQL);
                $profileTable->__joomla_user_id = $user->id;
                $result = $profileTable->loadDataFromDatabase(true);
                if (!$result) {
                    $profileTable->__created = WFactory::getSqlService()->getMySqlDateTime();
                    $profileId = WFactory::getServices()->getSqlService()->insert($profileTable);
                }
                $profileTable = $profileTable->loadDataFromDatabase(true);


                /**
                 * -------------------------------------------------------------------|
                 * -------------------------- insert/update address  -----------------|
                 * -------------------------------------------------------------------|
                 * @var $addressTable PortalPortalPropertyAddressesSql
                 */
                $addressTable = WFactory::getServices()->getSqlService()->getDbClass(__PORTAL_PORTAL_PROPERTY_ADDRESSES_SQL);

                //check if address exists!
                if ((int)$profileTable->__address_id > 0) {
                    $addressTable->__id = $profileTable->__address_id;
                }

                $addressTable->__type_id = WFactory::getServices()
                    ->getSqlService()
                    ->getSqlServiceClass(__PROPPERTY_PORTAL_ADDRESS)
                    ->getAddressTypeIdFromAddressType('Front End User Address');
                $addressTable->__region_id = $province;
                $addressTable->__town_id = 0;
                $addressTable->__postal_code_id = 0;
                $addressTable->__address = 0;
                $addressTable->__latitude = 0;
                $addressTable->__longitude = 0;

                if ($addressTable->__id > 0) {
                    WFactory::getServices()->getSqlService()->update($addressTable);
                    $addressId = $addressTable->__id;
                } else
                    $addressId = WFactory::getServices()->getSqlService()->insert($addressTable);


                /**
                 * -------------------------------------------------------------------|
                 * -------------------------- update user profile --------------------|
                 * -------------------------------------------------------------------|
                 * @var $profileTable PortalPortalUsersProfileSql
                 */

                //update his name ins the joomla user table !
                $query = "UPDATE  #__users SET  name =  '$name' WHERE  #__users.id ={$user->id};";
                WFactory::getSqlService()->update($query);

                $profileTable->__address_id = $addressId;
                $profileTable->__phone = $phone;
                $profileTable->__updated = WFactory::getSqlService()->getMySqlDateTime();

                $result = WFactory::getServices()->getSqlService()->update($profileTable);
                //TODO: send mail to eran??

                $this->addPropertyModel->userId = $user->id;
                $this->addPropertyModel->user_region_id = $province;
                $this->addPropertyModel->phone = $phone;
                $this->addPropertyModel->email = $email;
                $this->addPropertyModel->name = $name;


                //send a mail

                WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_PROPERTY)->sendSignupNotification($this->addPropertyModel, $newSignup, false);


                return true;

            } else {


                $this->__processFailed(JText::_("FAILED TO CREATE USER CONTACT SYS ADMIN"));
            }


        } else {
            $this->__processFailed(JText::_("CAPTCHA FAILED"));
        }
    }

    function __processStep2()
    {

        WFactory::getLogger()->debug("Processing add your property step 2");

        /**
         * -------------------------------------------------------------------|
         * -------------------------- insert/get property --------------------|
         * -------------------------------------------------------------------|
         * @var $property PortalPortalPropertiesSql
         */
        $property = WFactory::getServices()->getSqlService()->getDbClass(__PORTAL_PORTAL_PROPERTIES_SQL);
        $propertyId = $this->input->get('property-id', 0);
        if ($propertyId === 0)
            $propertyId = intval($this->addPropertyModel->property_id);
        //create a blank property table entry ..so we get a property NO MATTTER WHAT
        if ($propertyId === 0) {
            $property->__is_deleted = 1;
            $property->__created_date = WFactory::getSqlService()->getMySqlDateTime();
            $property->__full_text_search_helper = "ADD_YOUR_PROPERTY_TYPE";
            $propertyId = WFactory::getSqlService()->insert($property);
        }
        $property->__id = $propertyId;
        $this->input->set('property-id', $propertyId);
        $property = $property->loadDataFromDatabase();
        $this->addPropertyModel->property_id = $propertyId;

        /**
         * -------------------------------------------------------------------|
         * -------------------------- get property address -------------------|
         * -------------------------------------------------------------------|
         * @var $address PortalPortalPropertyAddressesSql
         */
        $address = WFactory::getServices()->getSqlService()->getDbClass(__PORTAL_PORTAL_PROPERTY_ADDRESSES_SQL);
        //load from database!
        $addressId = $property->__address_id;
        if (!$addressId) {
            $address->__type_id = WFactory::getServices()
                ->getSqlService()
                ->getSqlServiceClass(__PROPPERTY_PORTAL_ADDRESS)
                ->getAddressTypeIdFromAddressType('Property address');

            $addressId = WFactory::getSqlService()->insert($address);
            $property->__address_id = $addressId;
            WFactory::getSqlService()->update($property);
        }
        $address->__id = $property->__address_id;
        $address = $address->loadDataFromDatabase();

        $address->__latitude = $this->addPropertyModel->latitude;
        $address->__longitude = $this->addPropertyModel->longitude;
        $address->__region_id = $this->addPropertyModel->region_id;
        $address->__town_id = $this->addPropertyModel->city_town_id;
        $address->__address = $this->addPropertyModel->address;

        //now update some shit !
        WFactory::getSqlService()->update($address);


        /**
         * -------------------------------------------------------------------|
         * -------------------------- update property ------------------------|
         * -------------------------------------------------------------------|
         * @var $property PortalPortalPropertiesSql
         */
        $property->__address_id = $address->__id;
        $property->__current_listing_price = $this->addPropertyModel->price;
        $property->__category_id = $this->addPropertyModel->category_id[0];
        $property->__type_id = $this->addPropertyModel->type_id;
        $property->__buy_rent = $this->addPropertyModel->type_id == 2 ? BUY : RENT;
        $property->__total_area = $this->addPropertyModel->size;
        $property->__floor_level = $this->addPropertyModel->floor_level;
        $property->__availability_date = $this->addPropertyModel->movein;
        $property->__office_id = $this->addPropertyModel->office_id;
        $property->__region_id = $address->__region_id;
        $property->__state_province_id = $address->__town_id;
        $property->__zip_code_id = $address->__postal_code_id;
        $property->__address = $address->__address;
        $property->__last_update = WFactory::getSqlService()->getMySqlDateTime();

        //now update some shit !
        $updateResult = WFactory::getSqlService()->update($property);


        /**
         * -------------------------------------------------------------------|
         * -------------------------- do features ----------------------------|
         * -------------------------------------------------------------------|
         * @var $property PortalPortalPropertiesSql
         */

        //delete ALL features
        $query = "delete from #__portal_property_features where property_id={$property->__id}";
        WFactory::getSqlService()->delete($query);
        $dbFeatures = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_PROPERTIES)->getAllFeatures();
        //now insert the features
        foreach ($this->addPropertyModel->features as $id => $f) {
            if ($f) {
                /**
                 * @var $propertyFeatureTable PortalPortalPropertyFeaturesSql
                 */
                $propertyFeatureTable = WFactory::getSqlService()->getDbClass(__PORTAL_PORTAL_PROPERTY_FEATURES_SQL);

                $propertyFeatureTable->__feature_id = $id;
                $propertyFeatureTable->__name_en = $dbFeatures[$id];
                $propertyFeatureTable->__name_th = $dbFeatures[$id];
                $propertyFeatureTable->__property_id = $property->__id;

                WFactory::getSqlService()->insert($propertyFeatureTable);
            }

        }


        /**
         * -------------------------------------------------------------------|
         * -------------- insert properties to users mapping -----------------|
         * -------------------------------------------------------------------|
         * @var $usersToPropertiesMapping PortalPortalPropertiesUsersSql
         */
        $usersToPropertiesMapping = WFactory::getSqlService()->getDbClass(__PORTAL_PORTAL_PROPERTIES_USERS_SQL);
        $usersToPropertiesMapping->__property_id = $this->addPropertyModel->property_id;
        $usersToPropertiesMapping->__user_id = $this->addPropertyModel->userId;
        $usersToPropertiesMapping->loadDataFromDatabase();
        if ($usersToPropertiesMapping->__id === null) {
            WFactory::getSqlService()->insert($usersToPropertiesMapping);
        }


        if ($updateResult && intval($propertyId) > 0) {
            return true;
        } else
            $this->__processFailed("Failed to update property", "property-id=$propertyId");


    }

    function __processStep3()
    {

        //return true;

        WFactory::getLogger()->debug("Processing add your property step 3");


        /**
         * -------------------------------------------------------------------|
         * ------------------ update property description --------------------|
         * -------------------------------------------------------------------|
         * @var $property PortalPortalPropertiesSql
         */
        $property = WFactory::getServices()->getSqlService()->getDbClass(__PORTAL_PORTAL_PROPERTIES_SQL);
        $propertyId = $this->input->get('property-id', 0);
        if ($propertyId === 0)
            $propertyId = intval($this->addPropertyModel->property_id);
        $property->__id = $propertyId;
        $this->input->set('property-id', $propertyId);
        $property = $property->loadDataFromDatabase();


        $property->__description_text_en = WFactory::getHelper()->tidyHtml($this->addPropertyModel->desc_english);
        $property->__description_text_th = WFactory::getHelper()->tidyHtml($this->addPropertyModel->desc_thai);

        WFactory::getSqlService()->update($property);

        /**
         * -------------------------------------------------------------------|
         * ------------------ handle property images  ------------------------|
         * -------------------------------------------------------------------|
         */
        $images2upload = [];

        foreach ($this->addPropertyModel->images as $key=>$value)
        {
            $images2upload[] =  $this->addPropertyModel->images[$key]['path'];
        }
        $this->addPropertyModel->images = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_PROPERTY)->addPropertyImages(
            $images2upload,
            $this->addPropertyModel->property_id,
            $this->addPropertyModel->office_id);
        
        return true;

//        if (count($this->addPropertyModel->files) == count($this->addPropertyModel->images))
//            return true;
//        else
//            $this->__processFailed(JText::_("FAILED TO UPLOAD IMAGES"));


    }

    function __processStep4()
    {

        WFactory::getLogger()->debug("Processing add your property step 4");

        /**
         * -------------------------------------------------------------------|
         * -------------- insert properties to users mapping -----------------|
         * -------------------------------------------------------------------|
         * @var $usersToPropertiesMapping PortalPortalPropertiesUsersSql
         */
        $usersToPropertiesMapping = WFactory::getSqlService()->getDbClass(__PORTAL_PORTAL_PROPERTIES_USERS_SQL);
        $usersToPropertiesMapping->__property_id = $this->addPropertyModel->property_id;
        $usersToPropertiesMapping->__user_id = $this->addPropertyModel->userId;
        $usersToPropertiesMapping->loadDataFromDatabase();
        $usersToPropertiesMapping->__exclusive = $this->addPropertyModel->exclusive;
        WFactory::getSqlService()->update($usersToPropertiesMapping);


        WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_PROPERTY)->sendPropertyEmail($this->addPropertyModel,false);

        return true;

    }

    function __processStep5()
    {

        WFactory::getLogger()->debug("Processing add your property step 5");

        //reset the model,ONLY keep the user joomla id for now
        $modelBkup = $this->addPropertyModel;

        /**
         * -------------------------------------------------------------------|
         * -------------- create new AddPropertyModel()  ---------------------|
         * -------------------------------------------------------------------|
         */
        $this->addPropertyModel = new AddPropertyModel();
        $this->addPropertyModel->userId = $modelBkup->userId;
        $this->addPropertyModel->phone = $modelBkup->phone;
        $this->addPropertyModel->email = $modelBkup->email;
        $this->addPropertyModel->name = $modelBkup->name;
        $this->addPropertyModel->user_region_id = $modelBkup->user_region_id;


        WFactory::getHelper()->setSessionVariable("addPropertyModel", serialize($this->addPropertyModel));

        if ($modelBkup->addAnother) {//redirect to begining
            JFactory::getApplication()->redirect(JUri::base()."index.php?option=com_webportal&view=addproperty&layout=step2&Itemid=" . $this->itemId);
        } else {
            JFactory::getApplication()->redirect(JUri::base());
        }

        return true;

    }


    function __processFailed($msg, $getParam = "", $step = false)
    {
        WFactory::getLogger()->warn("Failed to register a property at step {$this->current_step} because: $msg", __LINE__, __FILE__);
        JFactory::getApplication()->enqueueMessage($msg, 'Error');

        $currentUrl = $_SERVER['HTTP_REFERER'];

        if (!empty($getParam) && strpos($currentUrl, $getParam) === false)
            $currentUrl = $currentUrl . "&$getParam";

        //BUT MAKE SURE YOU SAVE THE PROPERTY MODEL IN SESSION

        WFactory::getHelper()->setSessionVariable("addPropertyModel", serialize($this->addPropertyModel));

        if ($currentUrl === null)
            $currentUrl = JUri::base() . "index.php?option=com_webportal&view=addproperty&layout=step1";
        if ($step) {
            $currentUrl = JUri::base() . "index.php?option=com_webportal&view=addproperty&layout=step$step";
        }
        JFactory::getApplication()->redirect($currentUrl);

    }


    function _setUpStep($currentStep, $extraParam = "")
    {
        $this->addPropertyModel->currentStep = $this->current_step = $currentStep;
        $this->addPropertyModel->nextStep = $this->form_next_step = $currentStep + 1;
        $this->addPropertyModel->previousStep = $this->previous_step = $currentStep - 1;

        $this->form_action = JUri::base() . "index.php?option=com_webportal&view=addproperty&layout=step{$this->form_next_step}";
        if (!empty($extraParam))
            $this->form_action = $this->form_action . "&$extraParam";

        $this->form_action = $this->form_action . "&Itemid={$this->itemId}";
        $this->previous_step_link = str_replace("step{$this->form_next_step}", "step{$this->previous_step}", $this->form_action);

        $this->form_data_value = WFactory::getHelper()->encodeData($this->addPropertyModel);

    }


    function _insertHead()
    {

        ///home/khan/www/softverk-webportal-remaxth/components/com_webportal/views/addproperty/tmpl
        require_once JPATH_ROOT . "/components/com_webportal/views/addproperty/tmpl/_container_head.php";
        require_once JPATH_ROOT . "/components/com_webportal/views/addproperty/tmpl/_form_head.php";


    }

    function _insertTail()
    {

        ///home/khan/www/softverk-webportal-remaxth/components/com_webportal/views/addproperty/tmpl
        require_once JPATH_ROOT . "/components/com_webportal/views/addproperty/tmpl/_form_tail.php";
        require_once JPATH_ROOT . "/components/com_webportal/views/addproperty/tmpl/_container_tail.php";


    }


    function _checkIfUserAlreadyExists()
    {
        if ($this->debugMode)
            return false;


        $email = $this->input->get('email', null, 'STRING');
        //check if user already exists...
        $user = WFactory::getServices()
            ->getServiceClass(__PROPPERTY_PORTAL_USERS)
            ->getUserAccountByEmail($email);

        if (!$user) {
            return false;
        } else {
            WFactory::getLogger()->debug("User with email $email already exists..no new user created", __LINE__, __FILE__);
            $this->__processFailed("Your email already exists in the system . Please login here or if you cant remember your password, please click here");

        }
        return true;
    }


    function _enableDebug()
    {
        $testModel = new AddPropertyModel();
        $testModel->email = ("shroukkhan_") . "@gmail.com";
        $testModel->name = "Shrouk Khan";
        $testModel->region_id = 11;//samut prakan
        $testModel->city_town_id = 1029;//Phra Samut Chedi
        $testModel->address = "Somewhere null";
        $testModel->phone = "+66908805894";
        $testModel->office_id = 53;
        $testModel->category_id = 118;
        $testModel->features = array(1 => true, 2 => true, 4 => true, 58 => true);
        $testModel->price = "02190219";
        $testModel->movein = "1/2/2014";
        $testModel->size = 1000;
        $testModel->floor_level = 2;

        $testModel->type_id = 3;
        $currentModel = (array)$this->addPropertyModel;
        foreach ($currentModel as $i => $v) {
            if ($v === null)
                unset($currentModel[$i]);
        }

        //keep whatever user input and fill in the rest with default debug data
        $merged_model = (object)array_merge((array)$testModel, $currentModel);

        $this->addPropertyModel->bindToDb($merged_model);

    }
}


/*
 *  $scope.defaultProperty = {
                text: '',
                type_id: 0,
                mode_id: 2,//1 = ALL (residential + commercial )
                current_listing_price: [$scope.sliders.current_listing_price.floor, $scope.sliders.current_listing_price.ceiling],
                rent_price: [$scope.sliders.rent_price.floor, $scope.sliders.rent_price.ceiling],
                total_number_of_rooms: [$scope.sliders.total_number_of_rooms.floor, $scope.sliders.total_number_of_rooms.ceiling],
                number_of_bedrooms: [$scope.sliders.number_of_bedrooms.floor, $scope.sliders.number_of_bedrooms.ceiling],
                total_area: [$scope.sliders.total_area.floor, $scope.sliders.total_area.ceiling],
                rent_total_area: [$scope.sliders.rent_total_area.floor, $scope.sliders.rent_total_area.ceiling],
                region_id: '',
                city_town_id: '',
                zip_code_id: '',
                address: '',
                order: 'ORDER_BY_NEWEST_FIRST',
                office_id: '',
                sale_id: '',
                office_name: '',
                sale_name: '',
                search_key: '',
                latitude: '',
                longitude: '',
                radius: webportalConfiguration.transportationSearchRadius,
                transport_line: null,
                transport_station: '',
                //----------------------------------------------//
                features: [],//used ONLY in add your property,  //
                price: '',   //used ONLY in add your property,  //
                size: '',    //used ONLY in add your property,  //
                floor: '',   //used ONLY in add your property,  // <<-----------------
                unit: '',    //used ONLY in add your property,  //
                noof: '',    //used ONLY in add your property,  //
                movein: '',   //used ONLY in add your property,  //
                //----------------------------------------------//

                name: '',
                email: '',
                phone: '',
                password1: '',
                password2: ''

            };
 * */

class AddPropertyModel extends SearchModel
{

    function convertToAddPropertyModel($object)
    {
        $objectProperties = get_object_vars($object);
        foreach ($objectProperties as $key => $op) {
            $this->$key = $op;

        }
    }

    var $userId;
    var $user_region_id;
    var $name;
    var $email;
    var $phone;
    var $password1;
    var $password2;
    var $currentStep;
    var $nextStep;
    var $previousStep;
    var $desc_english;
    var $desc_thai;
    var $exclusive;
    var $addAnother;
    var $files = array();
    var $images = array();

}
