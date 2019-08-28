<?php

/**
 * Created by PhpStorm.
 * User: khan
 * Date: 3/9/15
 * Time: 10:30 AM
 */
class UsersService
{

    function __construct()
    {

        if (!defined('JPATH_COMPONENT'))
            define('JPATH_COMPONENT', JPATH_ROOT . 'components/com_users');


        $lang = JFactory::getLanguage();
        $extension = 'com_users';
        $base_dir = JPATH_ROOT;
        $language_tag = 'en-GB';
        $reload = true;
        $lang->load($extension, $base_dir, $language_tag, $reload);
        $extension = 'com_users';
        $lang->load($extension, $base_dir, $language_tag, $reload);

    }

    function authenticateFile()
    {
        $file = JFactory::getApplication()->input->getString('file');
        $file = base64_decode($file);

        $config = WFactory::getConfig()->getWebportalConfigurationArray();

        $agents = $config['users']['agentsGroups'];
        $brokers = $config['users']['brokersGroups'];

        $authorized = false;
        $userGroups = JFactory::getUser()->getAuthorisedGroups();

        WFactory::getLogger()->info("Checking authentication for $file");

        if (strpos($file, '/agentsandbroker/agents/') !== false) {
            //either agent of broker is fine!
            foreach ($userGroups as $key => $g) {
                if (in_array($g, $agents)) {
                    WFactory::getLogger()->info("Authorized to download $file from agent area");
                    $authorized = true;
                }
            }
        } else if (strpos($file, '/agentsandbroker/broker/') !== false) {
            //either agent of broker is fine!
            foreach ($userGroups as $key => $g) {
                if (in_array($g, $brokers)) {
                    WFactory::getLogger()->info("Authorized to download $file from broker area");
                    $authorized = true;
                }
            }
        } else if (strpos($file, '/agentsandbroker/') !== false
            && strpos($file, '/agentsandbroker/broker/') === false
            && strpos($file, '/agentsandbroker/agents/') === false
        ) {
            //either agent of broker is fine!
            foreach ($userGroups as $key => $g) {
                if (in_array($g, $agents) || in_array($g, $brokers)) {
                    WFactory::getLogger()->info("Authorized to download $file from common area");
                    $authorized = true;
                }
            }
        }

        if ($authorized) {
            $fullFile = JPATH_ROOT . DS . $file;
            WFactory::getHelper()->doDownload($fullFile, basename($fullFile));

        } else {
            echo "Unauthorized access!";
        }
        exit(1);

    }

    function login($username, $password)//alias
    {
        return $this->loginToJoomla($username, $password);
    }

    function loginToJoomla($username, $password)
    {
        if (is_array($username)) {
            $password = $username['password'];
            $username = $username['username'];
        }

        $username = $this->__fixEmail($username);


        $app = JFactory::getApplication();

        $credentials = array();
        $credentials['username'] = $username;
        $credentials['password'] = $password;

        $options = array();


        // Perform the log in.
        if (true === $app->login($credentials, $options)) {
            // Success
            $app->setUserState('users.login.form.data', array());
            $user = JFactory::getUser();


            return $this->__getUserInfo($user->id);
        }
        return null;

    }

    private function __getUserInfo($userId)
    {
        $user = JFactory::getUser($userId);
        /** @var  $userProfile PortalPortalUsersProfileSql */
        $userProfile = WFactory::getSqlService()->getDbClass(__PORTAL_PORTAL_USERS_PROFILE_SQL);
        $userProfile->__joomla_user_id = $user->id;
        $userProfile->loadDataFromDatabase();

        $userInfo = array(
            "id" => $user->id,
            "userId" => $user->id,
            "email" => $user->email,
            "phoneNumber" => $userProfile->__phone,
            "name" => $user->name,
            "favorites" => $this->getFavoriteProperties($userId));

        return $userInfo;

    }


    function addPropertyToFavorite($propertyId, $userId)
    {
        if (is_object($propertyId)) {
            $propertyId = get_object_vars($propertyId);
        }

        if (is_array($propertyId)) {
            $userId = $propertyId['userId'];
            $propertyId = $propertyId['propertyId'];
        }

        $query = "SELECT #__portal_properties_users.id
                      FROM #__portal_properties_users
                     WHERE     (#__portal_properties_users.property_id = $propertyId)
                           AND (#__portal_properties_users.user_id = $userId)";

        $result = WFactory::getSqlService()->select($query);
        if (count($result) === 0) {

            /** @var $favoriteProperty PortalPortalPropertiesUsersSql */
            $favoriteProperty = WFactory::getSqlService()->getDbClass(__PORTAL_PORTAL_PROPERTIES_USERS_SQL);
            $favoriteProperty->__property_id = $propertyId;
            $favoriteProperty->__user_id = $userId;
            $id = WFactory::getSqlService()->insert($favoriteProperty);
            $result = false;
            if (intval($id) > 0) {
                $result = true;
            }
        } else {
            $result = true;
        }


        return array("success" => $result,
            "userId" => $userId,
            "message" => $this->__getUserInfo($userId));
    }

    function removePropertyFromFavorite($propertyId, $userId)
    {
        if (is_object($propertyId)) {
            $propertyId = get_object_vars($propertyId);
        }

        if (is_array($propertyId)) {
            $userId = $propertyId['userId'];
            $propertyId = $propertyId['propertyId'];
        }

        $query = "DELETE FROM #__portal_properties_users 
                WHERE property_id = $propertyId AND user_id = $userId ";

        WFactory::getSqlService()->delete($query);

        return array("success" => true,
            "userId" => $userId,
            "message" => $this->__getUserInfo($userId));

    }

    function getFavoriteProperties($userId)
    {
        if (is_object($userId)) {
            $userId = get_object_vars($userId);
        }
        if (is_array($userId)) {
            $userId = $userId['userId'];
        }
        $query = "Select DISTINCT  property_id from #__portal_properties_users 
                WHERE user_id = $userId ";

        $result = WFactory::getSqlService()->select($query);
        $finalResult = array();
        foreach ($result as $r)
            $finalResult[] = $r['property_id'];
        return $finalResult;

    }


    /**
     * @param $emailAddress
     * @return bool|JUser
     */
    function getUserAccountByEmail($emailAddress)
    {
        // Find the user id for the given email address.
        $db = WFactory::getSqlService()->getDbo();
        $query = $db->getQuery(true);
        $query->select('id');
        $query->from($db->quoteName('#__users'));
        $query->where($db->quoteName('email') . ' = ' . $db->Quote($emailAddress));

        // Get the user object.
        $db->setQuery((string)$query);

        try {
            $userId = $db->loadResult();
        } catch (RuntimeException $e) {
            WFactory::getLogger()->error("getUserAccountByEmail request : $emailAddress error: -->" . JText::sprintf('COM_USERS_DATABASE_ERROR', $e->getMessage()));
            return false;
        }

        // Check for a user.
        if (empty($userId)) {
            WFactory::getLogger()->warn("getUserAccountByEmail request : $emailAddress error: -->" . JText::_('COM_USERS_INVALID_EMAIL'));
            return false;
        }

        // Get the user object.
        $user = JUser::getInstance($userId);

        return $user;
    }

    function resetPassword($emailAddress)
    {
        $emailAddress = $this->__fixEmail($emailAddress);
        return $this->sendResetRequest($emailAddress);
    }

    private function __fixEmail($emailAddress)
    {
        if (strpos($emailAddress, ' ') !== false)
            return str_replace(' ', '+', $emailAddress);
        return $emailAddress;
    }

    function sendResetRequest($emailAddress)
    {
        $response = array('success' => false, 'message' => '');
        if (is_array($emailAddress))
            $emailAddress = $emailAddress['emailAddress'];

        $emailAddress = $this->__fixEmail($emailAddress);

        /*UsersModelReset require*/
        require_once JPATH_ROOT . "/components/com_users/models/reset.php";
        require_once JPATH_ROOT . "/components/com_users/helpers/route.php";

        $model = new UsersModelReset();
        $config = JFactory::getConfig();
        /*
                $app = JFactory::getApplication();

                $data = array("email"=>$emailAddress);

                // Submit the password reset request.
                $return = $model->processResetRequest($data);
        */


        $user = $this->getUserAccountByEmail($emailAddress);

        if (!$user) {
            WFactory::getLogger()->warn('User by email address ' . $emailAddress . " not found", __LINE__, __FILE__);
            return $response = array('success' => false, 'message' => 'User not found');
        }

        // Make sure the user isn't blocked.
        if ($user->block) {
            WFactory::getLogger()->warn("resend request : $emailAddress error: --> " . JText::_('COM_USERS_USER_BLOCKED'));
            return $response = array('success' => false, 'message' => 'User blocked');
        }

        // Make sure the user isn't a Super Admin.
        if ($user->authorise('core.admin')) {
            WFactory::getLogger()->warn("resend request : $emailAddress error: --> " . JText::_('COM_USERS_REMIND_SUPERADMIN_ERROR'));
            return $response = array('success' => false, 'message' => 'Cant reset super admin!');
        }

        // Make sure the user has not exceeded the reset limit
        if (!$model->checkResetLimit($user)) {
            $resetLimit = (int)JFactory::getApplication()->getParams()->get('reset_time');
            WFactory::getLogger()->error(JText::plural('COM_USERS_REMIND_LIMIT_ERROR_N_HOURS', $resetLimit));

            return $response = array('success' => false, 'message' => 'Too many reset!');;
        }
        // Set the confirmation token.
        $token = JApplication::getHash(JUserHelper::genRandomPassword());
        $salt = JUserHelper::getSalt('crypt-md5');
        $hashedToken = md5($token . $salt) . ':' . $salt;

        $user->activation = $hashedToken;

        // Save the user to the database.
        if (!$user->save(true)) {
            return new JException(JText::sprintf('COM_USERS_USER_SAVE_FAILED', $user->getError()), 500);
        }


        // Assemble the password reset confirmation link.
        $mode = $config->get('force_ssl', 0) == 2 ? 1 : -1;
        $itemid = UsersHelperRoute::getLoginRoute();
        $itemid = $itemid !== null ? '&Itemid=' . $itemid : '';
        $link = 'index.php?option=com_users&view=reset&layout=confirm' . $itemid;

        // Put together the email template data.
        $data = $user->getProperties();
        $data['fromname'] = $config->get('fromname');
        $data['mailfrom'] = $config->get('mailfrom');
        $data['sitename'] = $config->get('sitename');
        $data['link_text'] = JRoute::_($link, false, $mode);
        $data['link_html'] = JRoute::_($link, true, $mode);
        $data['token'] = $token;

        $subject = JText::sprintf(
            'COM_USERS_EMAIL_PASSWORD_RESET_SUBJECT',
            $data['sitename']
        );

        $body = JText::sprintf(
            'COM_USERS_EMAIL_PASSWORD_RESET_BODY',
            $data['sitename'],
            $data['token'],
            $data['link_text']
        );

        // Send the password reset request email.
        //  sendMandrillMail($subject, $body, $from, $to, $fromName = "", $isHtml = false)
        $result = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_MANDRILL)->sendMandrillMail(
            $subject,
            $body,
            $data['mailfrom'],
            $user->email,
            false
        );
        //$return = JFactory::getMailer()->sendMail($data['mailfrom'], $data['fromname'], $user->email, $subject, $body);
        // Check for an error.
        if ($result === null) {
            return $response = array('success' => false, 'message' => 'Failed to send email');
        }

        return $response = array('success' => true, 'message' => 'Reset email sent');;
    }

    function handleUserGrouping($usertype, $joomlaUserId)
    {
        //if type is PILOT(or ind ) , then he should ONLY have his groups as defined in config
        //if type if NOT PILOT, then we retrive his current groups AND merge it with predefined groups
        $configArray = WFactory::getConfig()->getFaiConfigurationArray();

        if ($usertype === 'ind') {
            $groups = $configArray['pilotGroupIds'];
        } else {
            $groups = $configArray['nonPilotGroupIds'];
            $user = JFactory::getUser($joomlaUserId);
            $userCurrentGroups = $user->groups;
            $groups = array_unique(array_merge($groups, $userCurrentGroups));

        }

        return $this->updateJoomlaUserGroup($joomlaUserId, $groups);

    }

    function updateJoomlaUserGroup($userId, $groups)
    {
        jimport('joomla.user.user');
        /* get the com_user params */
        jimport('joomla.application.component.helper'); // include libraries/application/component/helper.php


        // "generate" a new JUser Object
        $user = JFactory::getUser($userId); // it's important to set the "0" otherwise your admin user information will be loaded

        $username = $user->username;

        if (!empty($groups))
            $data['groups'] = $groups;

        $data['id'] = $userId;

        if (!$user->bind($data)) { // now bind the data to the JUser Object, if it not works....
            WFactory::getLogger()->warn(JText::_($user->getError()), __LINE__, __FILE__);
            return WFactory::getServices()->getServiceResponse(false, array(), JText::_($user->getError()));
        }

        if (!$user->save()) { // if the user is NOT saved...
            WFactory::getLogger()->warn(JText::_($user->getError()), __LINE__, __FILE__);;
            return WFactory::getServices()->getServiceResponse(false, array(), JText::_($user->getError()));
        }

        WFactory::getLogger()->info("User $username group updated to " . json_encode($groups), __LINE__, __FILE__);
        return WFactory::getServices()->getServiceResponse(true, $user, "");
    }

    /**
     * @param $email
     * @return array|bool|string
     */
    function blockJoomlaUser($email)
    {
        $user = $this->getUserAccountByEmail($email);
        if ($user === false) {
            return false;
        }
        $data['block'] = 1;
        $data['activated'] = 1;

        if (!$user->bind($data)) { // now bind the data to the JUser Object, if it not works....
            WFactory::getLogger()->warn(JText::_($user->getError()), __LINE__, __FILE__);
            return WFactory::getServices()->getServiceResponse(false, array(), JText::_($user->getError()));
        }

        if (!$user->save()) { // if the user is NOT saved...
            WFactory::getLogger()->warn(JText::_($user->getError()), __LINE__, __FILE__);;
            return WFactory::getServices()->getServiceResponse(false, array(), JText::_($user->getError()));
        }
        return true;
    }

    /**
     * @param $email
     * @return array|bool|string
     */
    function unblockJoomlaUser($email)
    {
        $user = $this->getUserAccountByEmail($email);
        if ($user === false) {
            return false;
        }
        $data['block'] = 0;
        $data['activated'] = 1;

        if (!$user->bind($data)) { // now bind the data to the JUser Object, if it not works....
            WFactory::getLogger()->warn(JText::_($user->getError()), __LINE__, __FILE__);
            return WFactory::getServices()->getServiceResponse(false, array(), JText::_($user->getError()));
        }

        if (!$user->save()) { // if the user is NOT saved...
            WFactory::getLogger()->warn(JText::_($user->getError()), __LINE__, __FILE__);;
            return WFactory::getServices()->getServiceResponse(false, array(), JText::_($user->getError()));
        }
        return true;
    }


    function updateUserInfo($name, $phoneNumber, $email, $password)
    {
        if (is_object($name)) {
            $name = get_object_vars($name);
        }
        if (is_array($name)) {
            $phoneNumber = $name['phoneNumber'];
            $email = $name['email'];
            $password = $name['password'];
            $name = $name['name'];
        }
        $email = $this->__fixEmail($email);
        $username = $this->getUserNameFromEmail($email);
        $userId = $this->getUseridFromUsername($username);

        WFactory::getLogger()->info("Updating user info with $email / $userId / $username");

        $result = true;
        if (intval($userId) > 0) {
            //update joomla user first!
            $user = JFactory::getUser($userId);

            $mergable = array('name' => $name);
            if (!WFactory::getHelper()->isNullOrEmptyString($password)) {
                $mergable = array('password' => $password, 'password2' => $password, 'name' => $name);
            }

            if (!$user->bind($mergable)) {
                $result &= false;
            }
            if (!$user->save()) {
                $result &= true;
            }

            $query = "SELECT * FROM #__portal_users_profile where joomla_user_id=$userId";
            $profile = WFactory::getSqlService()->select($query);
            $profileId = $profile[0]['id'];

            if (intval($profileId) > 0) {
                //update user profile now...
                /** @var  $userProfile PortalPortalUsersProfileSql */
                $userProfile = WFactory::getSqlService()->getDbClass(__PORTAL_PORTAL_USERS_PROFILE_SQL);
                $userProfile->__id = $profileId;
                $userProfile->loadDataFromDatabase();
                WFactory::getLogger()->info("Existing user profile is :\n" . json_encode($userProfile));
                $userProfile->__phone = $phoneNumber;
                WFactory::getLogger()->info("New user profile is :\n" . json_encode($userProfile));

                WFactory::getSqlService()->update($userProfile);
            } else {
                /** @var  $userProfile PortalPortalUsersProfileSql */
                $userProfile = WFactory::getSqlService()->getDbClass(__PORTAL_PORTAL_USERS_PROFILE_SQL);
                $userProfile->__name = $name;
                $userProfile->__username = $username;
                $userProfile->__email = $email;
                $userProfile->__joomla_user_id = $userId;
                $userProfile->__phone = $phoneNumber;

                $id = WFactory::getSqlService()->insert($userProfile);
            }


            $userInfo = $this->__getUserInfo($user->id);

        }

        return array("success" => $result,
            "userId" => $userId,
            "message" => $userInfo);

    }


    function signUp($name, $email, $phoneNumber, $username, $password)
    {
        if (is_object($name)) {
            $name = get_object_vars($name);
        }
        if (is_array($name)) {
            $email = $name['email'];
            $phoneNumber = $name['phoneNumber'];
            $username = $name['username'];
            $password = $name['password'];
            $name = $name['name'];
        }

        $email = $this->__fixEmail($email);

        if (WFactory::getHelper()->isNullOrEmptyString($username))
            $username = $email;

        $config = WFactory::getConfig()->getWebportalConfigurationArray();
        $result = $this->createJoomlaUser($name, $email, $config['users']['propertySubmitterGroups'], 0, $username, $password);

        //now set the phone number in the profile and be done with it!
        if ($result['success'] === true) {
            //           /** @var  $userProfile PortalPortalUsersProfileSql */
//            $userProfile = WFactory::getSqlService()->getDbClass(__PORTAL_PORTAL_USERS_PROFILE_SQL);
//            $userProfile->__joomla_user_id = $result['data']->id;
//            $userProfile->loadDataFromDatabase();
//            $userProfile->__phone = $phoneNumber;
//            WFactory::getSqlService()->update($userProfile);


            $query = "SELECT * FROM #__portal_users_profile where joomla_user_id=" . $result['data']->id;
            $profile = WFactory::getSqlService()->select($query);
            $profileId = $profile[0]['id'];

            if (intval($profileId) > 0) {
                //update user profile now...
                /** @var  $userProfile PortalPortalUsersProfileSql */
                $userProfile = WFactory::getSqlService()->getDbClass(__PORTAL_PORTAL_USERS_PROFILE_SQL);
                $userProfile->__id = $profileId;
                $userProfile->loadDataFromDatabase();
                WFactory::getLogger()->info("Existing user profile is :\n" . json_encode($userProfile));
                $userProfile->__phone = $phoneNumber;
                WFactory::getLogger()->info("New user profile is :\n" . json_encode($userProfile));

                WFactory::getSqlService()->update($userProfile);
            } else {
                /** @var  $userProfile PortalPortalUsersProfileSql */
                $userProfile = WFactory::getSqlService()->getDbClass(__PORTAL_PORTAL_USERS_PROFILE_SQL);
                $userProfile->__name = $name;
                $userProfile->__username = $username;
                $userProfile->__email = $email;
                $userProfile->__joomla_user_id = $result['data']->id;
                $userProfile->__phone = $phoneNumber;

                $id = WFactory::getSqlService()->insert($userProfile);
            }


        } else {
            return array("success" => false,
                "message" => array(
                    "email" => $email,
                    "phoneNumber" => $phoneNumber,
                    "name" => $name,
                    "favorites" => []),
                "error" => $result['message'],
            );
        }

        return array("success" => $result['success'],
            "userId" => $result['success'] === true ? $result['data']->id : 0,
            "message" => $this->loginToJoomla($username, $password));

    }

    /**
     * @param $name
     * @param $email
     * @param $groupId
     * @param int $block
     * @param null $username
     * @param null $password
     *
     * @return array|string
     */
    function createJoomlaUser($name, $email, $groupId, $block = 0, $username = null, $password = null)
    {
        jimport('joomla.user.user');
        /* get the com_user params */
        jimport('joomla.application.component.helper'); // include libraries/application/component/helper.php
        $usersParams = &JComponentHelper::getParams('com_users'); // load the Params

        // "generate" a new JUser Object
        $user = JFactory::getUser(0); // it's important to set the "0" otherwise your admin user information will be loaded

        $data = array(); // array for all user settings

        $data['name'] = $name;

        $data['name'] = trim($data['name']);
        $data['username'] = $username === null ? $email : $username;
        $data['email'] = $email;
        $data['groups'] = (is_array($groupId)) ? $groupId : array($groupId);

        if ($password === null) {
            $password = md5(uniqid("@PassWO1213"));
        }

        $data['password'] = $password; // set the password
        $data['password2'] = $password; // confirm the password
        $data['sendEmail'] = 0;
        $data['id'] = null;

        /* Now we can decide, if the user will need an activation */

        /*$useractivation = $usersParams->get('useractivation'); // in this example, we load the config-setting
        if ($useractivation == 1) { // yeah we want an activation

            jimport('joomla.user.helper'); // include libraries/user/helper.php
            $data['block'] = $blockuser; // block the User
            $data['activation'] = JUtility::getHash(JUserHelper::genRandomPassword()); // set activation hash (don't forget to send an activation email)

        } else { // no we need no activation

            $data['block'] = 0; // don't block the user

        }*/

        $data['block'] = $block;
        $data['activated'] = 1;

        if (!$user->bind($data)) { // now bind the data to the JUser Object, if it not works....
            WFactory::getLogger()->warn(JText::_($user->getError()), __LINE__, __FILE__);
            return WFactory::getServices()->getServiceResponse(false, array(), JText::_($user->getError()));
        }

        if (!$user->save()) { // if the user is NOT saved...
            WFactory::getLogger()->warn(JText::_($user->getError()), __LINE__, __FILE__);;
            return WFactory::getServices()->getServiceResponse(false, array(), JText::_($user->getError()));
        }

        WFactory::getLogger()->info("New user inserted with username $username", __LINE__, __FILE__);
        return WFactory::getServices()->getServiceResponse(true, $user, "");


    }

    function getUserNameFromEmail($emailAddress)
    {
        /**
         * @var $usersTable PortalUsersSql
         * */
        $usersTable = WFactory::getSqlService()->getDbClass(__PORTAL_USERS_SQL);

        $usersTable->__email = $emailAddress;

        $usersTable->loadDataFromDatabase(true);

        return $usersTable->__username;
    }

    function getUseridFromUsername($username)
    {
        /**
         * @var $usersTable PortalUsersSql
         * */
        $usersTable = WFactory::getSqlService()->getDbClass(__PORTAL_USERS_SQL);

        $usersTable->__username = $username;

        $usersTable->loadDataFromDatabase(true);

        return $usersTable->__id;
    }


    function updateJoomlaUserNameEmail($oldUserName, $emailAddress, $newUserName, $newEmail)
    {
        /**
         * @var $usersTable PortalUsersSql
         * */
        $usersTable = WFactory::getSqlService()->getDbClass(__PORTAL_USERS_SQL);

        if ($oldUserName !== null)
            $usersTable->__username = $oldUserName;

        if ($emailAddress)
            $usersTable->__email = $emailAddress;

        $usersTable = $usersTable->loadDataFromDatabase(true);

        if ($usersTable) {

            $usersTable->__username = $newUserName;
            $usersTable->__email = $newEmail;

            $result = WFactory::getSqlService()->update("default", $usersTable);

            if ($result) {
                return WFactory::getServices()->getServiceResponse(true, get_object_vars($usersTable), "Username and email updated");
            }
        }
        return WFactory::getServices()->getServiceResponse(false, null, "Username and email failed to update");
    }


}