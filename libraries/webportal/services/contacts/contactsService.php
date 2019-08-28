<?php

/**
 * Created by PhpStorm.
 * User: khan
 * Date: 5/19/14
 * Time: 1:21 PM
 */

require_once JPATH_LIBRARY_WEBPORTAL_SERVICES . DS . 'contacts' . DS . 'contactModel.php';
require_once(JPATH_BASE . DS . 'templates' . DS . 'generic' . DS . 'controllers' . DS . 'helpers.php');

class ContactsService
{

    /**
     * @return ContactModel
     */
    function getContactsModel()
    {
        return new ContactModel();
    }

    function siteName()
    {
        return JFactory::getConfig()->get('sitename');
    }


    function sendMailToDefaultCompany($contactModel)
    {
        $companyMail = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_COMPANY)->getCompanyEmail();
        if (WFactory::getHelper()->isUnitTest())
            $companyMail = "shroukkhan@gmail.com";

        /* Hard code for 12 Real Estate - Forwarding mail to K.Kwang personal mail */
        if ($companyMail == "info@12-realestate.com") {
            $companyMail = "krittiyakwang@gmail.com";
            WFactory::getLogger()->info("Forwarding email to 12 Real Estate Personal email");
        }

        $contactModel = get_object_vars($contactModel);
        foreach ($contactModel as &$value) {
            $value = urldecode($value);
        }

        $this->sendContactEmail($contactModel, $companyMail);

    }

    /**
     * @param $contactModel ContactModel
     * @return mixed
     */
    function sendPropertyMailToFriend($contactModel)
    {

        //$x = json_encode($contactModel);

        WFactory::getLogger()->debug("sendPropertyMailToFriend called with data: " . json_encode($contactModel, JSON_PRETTY_PRINT));

        $mailTo = $contactModel->to_email;
        $mailTo = explode(',', $mailTo);

        $companyMail = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_COMPANY)->getCompanyEmail();
        $sendThankyou = true;
        if (WFactory::getHelper()->isNullOrEmptyString($contactModel->from_email)) {
            $sendThankyou = false;
            $contactModel->from_email = $companyMail;
        }
        if (WFactory::getHelper()->isNullOrEmptyString($contactModel->from_name)) {
            $contactModel->from_name = $this->siteName();
        }

        if (count($contactModel->property_id) == 1) {
            $propertyDetails = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_PROPERTY)->getDetail($contactModel->property_id);
            $emailTemplate = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_HTMLMAIL)->getPropertyEmailTempalte(defined('__PROPERTY_EMAIL_TEMPLATE')?__PROPERTY_EMAIL_TEMPLATE:"tempo", $propertyDetails);

            $propertyDetails->message = $contactModel->message;

            $saleRentText = "Property for {$propertyDetails->buy_rent}";
            $saleRentText = strtoupper($saleRentText);
            $saleRentText = JText::_(trim($saleRentText));

            if (WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_TEMPLATE)->getParam('propertyTitle') == 'true') {
                $subject = "$saleRentText , {$propertyDetails->title}";
            } else {
                $subject = "$saleRentText , {$propertyDetails->address}, {$propertyDetails->zip_code} {$propertyDetails->zip_code_name}";
            }

        } else {
            /** @var $searchModel SearchModel
             */
            $searchModel = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_SEARCH)->getSearchModel();
            $searchModel->property_id = $contactModel->property_id;
            $propertyDetails = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_PROPERTIES)->getList($searchModel);

            //$send2friendMultiplePropertiesTemplateName=defined('__PROPERTIES_EMAIL_TEMPLATE')?__PROPERTIES_EMAIL_TEMPLATE:"twoColumnProperties";
            // @paisit : Always make program backward compatible. __PROPERTIES_EMAIL_TEMPLATE was defined by you?if so, make sure the system works even if it is not defined yet
            //this has to be a different template than __PROPERTIES_EMAIL_TEMPLATE , because it is MULTIPLE PRORPERTY, hard coding for now :)
            $emailTemplate = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_HTMLMAIL)->getPropertiesEmailTemplate('twoColumnProperties',
                $propertyDetails, $contactModel->message
            );

            $subject = "Properties from " . JUri::base();
        }

        foreach ($mailTo as $to) {
            $response = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_MANDRILL)->sendMandrillMail($subject,
                $emailTemplate, //body
                $contactModel->from_email, //from
                $to,//to
                $contactModel->from_name,//from name
                true);
//function logEmail($type, $subject, $propertyId, $sender, $receiver, $message, $fullMail)
            WFactory::getLogger()->logEmail("SEND_PROPERTY_2_FRIEND", $subject, $contactModel->property_id,
                $contactModel->from_email, $to, $emailTemplate, $response);
        }

        if ($sendThankyou) {
            $this->sendThankyouMail(
                $contactModel->from_email,
                $this->siteName(),
                $contactModel->from_name,
                "Confirmation of property sharing with friend"
            );
        }

        $contactModel->contact_name = $contactModel->from_name;
        $contactModel->contact_category = "SEND_TO_FRIEND";
        $contactModel->contact_email = $contactModel->from_email;
        $pIds = count($contactModel->property_id) == 1 ? $contactModel->property_id : json_encode($contactModel->property_id);
        $contactModel->message = "Property id : {$pIds} , Message : {$contactModel->message}";


        $this->saveContact($contactModel);
        return true;
    }

    function getAgentOfficeEmail($agentId)
    {
        $query = "SELECT #__portal_offices.email, #__portal_sales.id
                  FROM #__portal_sales #__portal_sales
                       INNER JOIN #__portal_offices #__portal_offices
                          ON (#__portal_sales.office_id = #__portal_offices.id)
                 WHERE (#__portal_sales.id = $agentId)";

        $result = WFactory::getSqlService()->select($query);
        return $result[0]["email"];
    }

    /**
     * @param $contactModel ContactModel
     * @return mixed
     */
    function sendMailToAgent($contactModel)
    {

        //$x = json_encode($contactModel);

        WFactory::getLogger()->debug("sendMailToAgent called with data: " . json_encode($contactModel, JSON_PRETTY_PRINT));
        $officeEmail = $this->getAgentOfficeEmail($contactModel->agent_id);
        $mailTo = $contactModel->agent_email;
        $propertyDetails = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_PROPERTY)->getDetail($contactModel->property_id);
        //Enable from template
        if (WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_TEMPLATE)->getParam('useOfficeEmailInsteadOfAgents') === 'true') {
            $mailTo = $officeEmail;
        }
        //-----------------------------


        // SEND TO AGENT
        $mailer = JFactory::getMailer();

        if (WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_TEMPLATE)->getParam('propertyTitle') == 'true') {
            $subject = "Mail from {$contactModel->contact_name} -> {$propertyDetails->title}";
        } else {
            $subject = "Mail from {$this->siteName()} website";
        }

        $mailer->setSubject($subject);
        $mailer->addRecipient($mailTo);

        $message = $contactModel->agent_message;
        $message .= "<br/>You have a message from {$contactModel->contact_name} ({$contactModel->contact_email}) ({$contactModel->contact_phone})<br/><br/>";
        $message .= "Message:<br/>";
        $message .= $contactModel->message;

        // $contactModel->message;
        $mailer->setBody($message);
        $mailer->IsHTML(true);
        $success = $mailer->Send();

        if ($success === true && !WFactory::getHelper()->isUnitTest()) {
            WFactory::getLogger()->debug(("mailer sent " . $mailer->ErrorInfo));
        } else {
            WFactory::getLogger()->warn(("mailer failed [ " . $mailer->IsError() . " ] " . $mailer->ErrorInfo));
            WFactory::getLogger()->info("Sending Mandrill mail to agent $mailTo , from {$contactModel->contact_email}");
            $response = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_MANDRILL)->sendMandrillMail(
                $subject,
                $message,
                $contactModel->contact_email,
                $mailTo,
                $contactModel->contact_name,
                true
            );


            WFactory::getLogger()->info("Mandrill response : " . json_encode($response, JSON_PRETTY_PRINT), __LINE__, __FILE__);
        }

        WFactory::getLogger()->logEmail("CONTACT_AGENT", $subject, null, $contactModel->contact_email, $mailTo, $message, $response);

        // SEND COPY TO SENDER
        $mailer = JFactory::getMailer();

        $subject = "Mail from {$this->siteName()}";
        $mailer->setSubject($subject);
        $mailer->addRecipient($contactModel->contact_email);

        $message = "You sent a message to an agent via {$this->siteName()}. <br/><br/>";
        $message .= "Message:<br/>";
        $message .= $contactModel->message;

        $mailer->setBody($message);
        $mailer->IsHTML(true);
        $success = $mailer->Send();

        if ($success === true && !WFactory::getHelper()->isUnitTest()) {
            WFactory::getLogger()->debug(("mailer sent " . $mailer->ErrorInfo));
        } else {
            WFactory::getLogger()->warn(("mailer failed [ " . $mailer->IsError() . " ] " . $mailer->ErrorInfo));
            WFactory::getLogger()->info("Sending Mandrill mail to guest {$contactModel->contact_email}");
            $response = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_MANDRILL)->sendMandrillMail(
                $subject,
                $message,
                $officeEmail,
                $contactModel->contact_email,
                $this->siteName(),
                true
            );
            WFactory::getLogger()->info("Mandrill response : " . json_encode($response));


        }

        WFactory::getLogger()->logEmail("CONTACT_AGENT_COPY2SENDER", $subject, null, $officeEmail, $contactModel->contact_email, $message, $response);

        $this->saveContact($contactModel);
        return $success;
    }

    //function sendMailToPropertyOffice()
    /**
     * @param $contactModel ContactModel
     * @param $mailTo
     * @return bool
     */
    function sendContactEmail($contactModel, $mailTo)
    {

        WFactory::getLogger()->info("Sending Contact mail to $mailTo");

        if (is_array($contactModel))
            $contactModel = WFactory::getHelper()->array2Object($contactModel);

        $mailer = JFactory::getMailer();


        $subject = "Mail from {$this->siteName()} website ";
        if (!WFactory::getHelper()->isNullOrEmptyString($contactModel->contact_category)) {
            $subject .= "regarding : {$contactModel->contact_category}";
        }

        $mailer->setSubject($subject);
//        $mailer->AddReplyTo(array($contactModel->contact_email, $contactModel->contact_name));
//        $mailer->setSender(array($contactModel->contact_email, $contactModel->contact_name, true));
        $mailer->addRecipient($mailTo);

        $modelAsArray = get_object_vars($contactModel);

        $body = "";
        foreach ($modelAsArray as $key => $value) {
            $body .= JText::_($key) . "\t\t: $value\n";
        }


        $mailer->setBody($body);
        $mailer->IsHTML(false);


        $success = $mailer->Send();

        if ($success === true) {

            WFactory::getLogger()->debug(("mailer sent " . $mailer->ErrorInfo));

        } else {
            WFactory::getLogger()->warn(("mailer failed [" . $mailer->IsError() . " ] " . $mailer->ErrorInfo));

            WFactory::getLogger()->info("Sending Mandrill mail", __LINE__, __FILE__);
            $response = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_MANDRILL)->sendMandrillMail(
                $subject,
                $body,
                $contactModel->contact_email,
                $mailTo,
                $contactModel->contact_name,
                false
            );

            WFactory::getLogger()->info("Mandrill response : " . json_encode($response));


        }

        WFactory::getLogger()->logEmail("CONTACT", $subject, null, $contactModel->contact_email, $mailTo, $body, $response);

        $this->sendThankyouMail($contactModel->contact_email, $mailTo, $contactModel->contact_name);
        $this->saveContact($contactModel);
    }

    function sendThankyouMail($mailTo, $mailFrom, $guestName, $subject = "Requesting more information confirmation")
    {

        WFactory::getLogger()->warn("Thankyou mail has been disabled!");
        return true;

        WFactory::getLogger()->info("Sending Thankyou info mail to $mailTo");


        $mailer = JFactory::getMailer();


        //$subject = "Requesting more information confirmation";

        $mailer->setSubject($subject);
        $mailer->addRecipient($mailTo);

        $body = "Dear $guestName,\r\nThank you for your interest in {$this->siteName()}\r\nWe shall get back to you as soon as possible\r\n--{$this->siteName()}";


        $mailer->setBody($body);
        $mailer->IsHTML(false);


        $success = $mailer->Send();

        if ($success === true) {
            WFactory::getLogger()->debug(("mailer sent " . $mailer->ErrorInfo));
            return true;
        } else {
            WFactory::getLogger()->warn(("mailer failed [ " . $mailer->IsError() . " ] " . $mailer->ErrorInfo));
            WFactory::getLogger()->info("Sending Mandrill mail", __LINE__, __FILE__);
            $response = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_MANDRILL)->sendMandrillMail(
                $subject,
                $body,
                $mailFrom,
                $mailTo,
                $this->siteName(),
                false
            );

            WFactory::getLogger()->info("Mandrill response : " . json_encode($response));


        }
        WFactory::getLogger()->logEmail("CONTACT_THANKYOU", $subject, null, $mailFrom, $mailTo, $body, $response);
        return true;

    }

    function registerForSendContactEmail($contactModel, $mailTo)
    {
        if (is_object($contactModel))
            $contactModel = get_object_vars($contactModel);


        $payload = array("contactModel" => $contactModel, "mailTo" => $mailTo);

        $beanStalkdClass = WFactory::getServices()
            ->getServiceClass(__PROPPERTY_PORTAL_BEANSTALKD)
            ->getBeanstalkdModel(__PROPPERTY_PORTAL_CONTACTS, "sendContactEmail", $payload);


        $result = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_BEANSTALKD)->putMailQueue($beanStalkdClass);


        if ($result === null) {
            WFactory::getLogger()->warn("Failed to put beanstalkd job..is beanstalkd configured / running ?");
        } else {
            WFactory::getLogger()->debug("Beanstalkd job registered for registerForSendContactEmail");
        }
    }


    /**
     * @param $contactModel ContactModel
     * @return bool|mixed
     * @throws PortalException
     */
    function saveContact($contactModel)
    {
        if (is_array($contactModel))
            $contactModel = WFactory::getHelper()->array2Object($contactModel);

        /**
         * @var $contactsDbClass PortalPortalContactsSql
         */
        $contactsDbClass = WFactory::getSqlService()->getDbClass(__PORTAL_PORTAL_CONTACTS_SQL);

        $contactModel->timestamp = WFactory::getSqlService()->getMySqlDateTime();

        if (!empty($contactModel->meta_data)) {
            if (is_string($contactModel->meta_data)) {
                if (json_decode($contactModel->meta_data) === null) {
                    WFactory::getLogger()->error("Failed to save contact, meta data is not valid json");
                    WFactory::throwPortalException("Failed to save contact, meta data is not valid json");
                }
            } else {
                if (is_object($contactModel->meta_data))
                    $contactModel->meta_data = get_object_vars($contactModel->meta_data);

                $contactModel->meta_data = json_encode($contactModel->meta_data);
            }
        }

        $contactsDbClass->bind(get_object_vars($contactModel));


        $insertId = WFactory::getSqlService()->insert($contactsDbClass);

        WFactory::getLogger()->debug("Saved contact to database , insert is: $insertId");

        return $insertId;


    }

    /**
     * Generate excel for the #__portal_contacts table
     * @return string  Webpath / Download url for the generated xml..
     * @throws PortalException
     */
    function getContactsAsExcel()
    {
        try {

            $objPHPExcel = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_EXCEL)->getPhpExcel();

            /**
             * @var $contactsDbClass PortalPortalContactsSql
             */
            $contactsDbClass = WFactory::getSqlService()->getDbClass(__PORTAL_PORTAL_CONTACTS_SQL);

            $contacts = $contactsDbClass->loadDataFromDatabase(false);

            $date = WFactory::getSqlService()->getMySqlDateTime();
            // Set document properties
            $objPHPExcel->getProperties()->setCreator("Softverk Webportal")
                ->setLastModifiedBy("Shrouk Khan")
                ->setTitle("Contacts [" . $date . "]")
                ->setSubject("Webportal collected contacts")
                ->setDescription("This document contains all the contacts that were collected by portal in until $date")
                ->setKeywords("office 2007 webportal contacts")
                ->setCategory("Contacts");

            $contactModels = array();

            foreach ($contacts as $c) {
                $model = $this->getContactsModel();
                $model->bindToDb($c);

                $contactModels[] = $model;
            }

            $workSheet = $objPHPExcel->setActiveSheetIndex(0);

            $yindex = 1;
            foreach ($contactModels as $c) {
                $xindex = "A";
                $contactDetails = get_object_vars($c);
                if ($yindex == 1) //header
                {

                    foreach ($contactDetails as $key => $value) {

                        WFactory::getLogger()->debug("Saving header $key to $xindex$yindex");

                        $workSheet->setCellValue("$xindex$yindex", $key);
                        $xindex++;
                    }

                    $workSheet->getStyle('A1:' . $xindex . $yindex)->getFont()->setBold(true);

                } else {
                    $xindex = "A";
                    foreach ($contactDetails as $key => $value) {
                        WFactory::getLogger()->debug("Saving row $value to $xindex$yindex");
                        $workSheet->setCellValue("$xindex$yindex", $value);
                        $xindex++;
                    }
                }
                $yindex++;
            }

            $objPHPExcel->getActiveSheet()->setTitle('Contacts');


            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $fileName = uniqid() . ".xls";

            $filePath = JPATH_BASE . DS . "tmp" . DS . $fileName;
            $objWriter->save($filePath);

            WFactory::getLogger()->debug("Contact list generated and saved in $filePath");

            if (WFactory::getHelper()->isUnitTest())
                return $filePath;

            JFactory::getApplication()->redirect(JUri::base() . "tmp/$fileName");
            //return JUri::base() . "tmp/$fileName";
        } catch (Exception $e) {
            $msg = "Excel generation error! msg : " . $e->getMessage();
            WFactory::getLogger()->fatal($msg);
            WFactory::throwPortalException($msg);
        }


    }


}