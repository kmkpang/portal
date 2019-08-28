<?php

/**
 * Created by PhpStorm.
 * User: khan
 * Date: 5/19/14
 * Time: 1:21 PM
 */

require_once JPATH_LIBRARY_WEBPORTAL_SERVICES . DS . 'requestinfo' . DS . 'requestinfoModel.php';

class RequestinfoService
{

    /**
     * @return RequestinfoModel
     */
    function getRequestinfoModel()
    {
        return new RequestinfoModel();
    }


    function sendMailToDefaultCompany($requestinfoModel)
    {
        $comapnyEmail = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_COMPANY)->getCompanyEmail();
        if (WFactory::getHelper()->isUnitTest())
            $comapnyEmail = "shroukkhan@gmail.com";
        // $this->registerForSendRequestinfoEmail($requestinfoModel, $comapnyEmail);

        $requestinfoModel = get_object_vars($requestinfoModel);
        foreach ($requestinfoModel as &$value) {
            $value = urldecode($value);
        }
        $result = $this->saveRequestinfo($requestinfoModel);
        $this->sendRequestinfoEmail($requestinfoModel, $comapnyEmail);
        return $result;


    }

    //function sendMailToPropertyOffice()
    /**
     * @param $requestinfoModel RequestinfoModel
     * @param $mailTo
     * @return bool
     */
    function sendRequestinfoEmail($requestinfoModel, $mailTo)
    {
        WFactory::getLogger()->info("Sending Request info mail to $mailTo");

        if (is_array($requestinfoModel))
            $requestinfoModel = WFactory::getHelper()->array2Object($requestinfoModel);

        $mailer = JFactory::getMailer();


        $subject = "Requesting more info mail from website regarding : {$requestinfoModel->interested_to_be}";
        $fromName = $requestinfoModel->contact_first_name . " " . $requestinfoModel->contact_last_name;
        $mailer->setSubject($subject);
//        $mailer->AddReplyTo(array($requestinfoModel->requestinfo_email, $requestinfoModel->requestinfo_name));
//        $mailer->setSender(array($requestinfoModel->requestinfo_email, $requestinfoModel->requestinfo_name, true));
        $mailer->addRecipient($mailTo);

        $modelAsArray = get_object_vars($requestinfoModel);

        $body = "";
        foreach ($modelAsArray as $key => $value) {
            $body .= "$key\t\t: $value\n";
        }


        $mailer->setBody($body);
        $mailer->IsHTML(false);


        $success = $mailer->Send();

        if ($success === true) {
            WFactory::getLogger()->debug(("mailer sent " . $mailer->ErrorInfo));

        } else {
            WFactory::getLogger()->warn(("mailer failed [ " . $mailer->IsError() . " ] " . $mailer->ErrorInfo));
            WFactory::getLogger()->info("Sending Mandrill mail");
            $response = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_MANDRILL)->sendMandrillMail(
                $subject,
                $body,
                $requestinfoModel->contact_email,
                $mailTo,
                $fromName,
                false
            );

            WFactory::getLogger()->info("Mandrill response : " . json_encode($response));


        }

        WFactory::getLogger()->logEmail("REQUEST_INFO", $subject, null, $requestinfoModel->contact_email, $mailTo, $body, $response);

        $this->sendThankyouMail($requestinfoModel->contact_email, $mailTo, $fromName);

    }

    function sendThankyouMail($mailTo, $mailFrom, $guestName)
    {

        WFactory::getLogger()->info("Sending Thankyou info mail to $mailTo");


        $mailer = JFactory::getMailer();


        $subject = "Requesting more information confirmation";

        $mailer->setSubject($subject);
        $mailer->addRecipient($mailTo);

        $body = "Dear $guestName,\r\nThank you for your interest in RE/MAX Thailand\r\nWe shall get back to you as soon as possible\r\n--RE/MAX Thailand";


        $mailer->setBody($body);
        $mailer->IsHTML(false);


        $success = $mailer->Send();

        if ($success === true) {
            WFactory::getLogger()->debug(("mailer sent " . $mailer->ErrorInfo));
            return true;
        } else {
            WFactory::getLogger()->warn(("mailer failed [ " . $mailer->IsError() . " ] " . $mailer->ErrorInfo));
            WFactory::getLogger()->info("Sending Mandrill mail");
            $response = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_MANDRILL)->sendMandrillMail(
                $subject,
                $body,
                $mailFrom,
                $mailTo,
                "RE/MAX Thailand",
                false
            );

            WFactory::getLogger()->info("Mandrill response : " . json_encode($response));


        }

        WFactory::getLogger()->logEmail("REQUEST_INFO_THANKYOU", $subject, null, $mailFrom, $mailTo, $body, $response);
        return true;
    }

    function registerForSendRequestinfoEmail($requestinfoModel, $mailTo)
    {
        if (is_object($requestinfoModel))
            $requestinfoModel = get_object_vars($requestinfoModel);


        $payload = array("requestinfoModel" => $requestinfoModel, "mailTo" => $mailTo);

        $beanStalkdClass = WFactory::getServices()
            ->getServiceClass(__PROPPERTY_PORTAL_BEANSTALKD)
            ->getBeanstalkdModel(__PROPPERTY_PORTAL_REQUESTINFO, "sendRequestinfoEmail", $payload);


        $result = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_BEANSTALKD)->putMailQueue($beanStalkdClass);


        if ($result === null) {
            WFactory::getLogger()->warn("Failed to put beanstalkd job..is beanstalkd configured / running ?");
        } else {
            WFactory::getLogger()->debug("Beanstalkd job registered for registerForSendRequestinfoEmail");
        }
    }


    /**
     * @param $requestinfoModel RequestinfoModel
     * @return bool|mixed
     * @throws PortalException
     */
    function saveRequestinfo($requestinfoModel)
    {
        if (is_array($requestinfoModel))
            $requestinfoModel = WFactory::getHelper()->array2Object($requestinfoModel);

        /**
         * @var $requestinfoDbClass PortalPortalRequestinfoSql
         */
        $requestinfoDbClass = WFactory::getSqlService()->getDbClass(__PORTAL_PORTAL_REQUESTINFO_SQL);

        $requestinfoModel->timestamp = WFactory::getSqlService()->getMySqlDateTime();


        $requestinfoDbClass->bind(get_object_vars($requestinfoModel));


        $insertId = WFactory::getSqlService()->insert($requestinfoDbClass);

        WFactory::getLogger()->debug("Saved requestinfo to database , insert is: $insertId");

        return $insertId;


    }

    /**
     * Generate excel for the jos_portal_requestinfo table
     * @return string  Webpath / Download url for the generated xml..
     * @throws PortalException
     */
    function getRequestinfoAsExcel()
    {
        try {

            $objPHPExcel = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_EXCEL)->getPhpExcel();

            /**
             * @var $requestinfoDbClass PortalPortalRequestinfoSql
             */
            $requestinfoDbClass = WFactory::getSqlService()->getDbClass(__PORTAL_PORTAL_REQUESTINFO_SQL);

            $requestinfo = $requestinfoDbClass->loadDataFromDatabase(false);

            $date = WFactory::getSqlService()->getMySqlDateTime();
            // Set document properties
            $objPHPExcel->getProperties()->setCreator("Softverk Webportal")
                ->setLastModifiedBy("Shrouk Khan")
                ->setTitle("Requestinfo [" . $date . "]")
                ->setSubject("Webportal collected requestinfo")
                ->setDescription("This document contains all the requestinfo that were collected by portal in until $date")
                ->setKeywords("office 2007 webportal requestinfo")
                ->setCategory("Requestinfo");

            $requestinfoModels = array();

            foreach ($requestinfo as $c) {
                $model = $this->getRequestinfoModel();
                $model->bindToDb($c);

                $requestinfoModels[] = $model;
            }

            $workSheet = $objPHPExcel->setActiveSheetIndex(0);

            $yindex = 1;
            $xindex = "A";

            // do header
            $requestinfoDetails = get_object_vars($requestinfoModels[0]);
            if ($yindex == 1) //header
            {

                foreach ($requestinfoDetails as $key => $value) {

                    WFactory::getLogger()->debug("Saving header $key to $xindex$yindex");

                    $workSheet->setCellValue("$xindex$yindex", $key);
                    $xindex++;
                }

                $workSheet->getStyle('A1:' . $xindex . $yindex)->getFont()->setBold(true);

            }
            //other rows
            $yindex++;
            foreach ($requestinfoModels as $c) {
                $xindex = "A";
                $requestinfoDetails = get_object_vars($c);
                foreach ($requestinfoDetails as $key => $value) {
                    WFactory::getLogger()->debug("Saving row $value to $xindex$yindex");
                    $workSheet->setCellValue("$xindex$yindex", $value);
                    $xindex++;
                }

                $yindex++;
            }

            $objPHPExcel->getActiveSheet()->setTitle('Requestinfo');


            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $fileName = uniqid() . ".xls";

            $filePath = JPATH_BASE . DS . "tmp" . DS . $fileName;


            WFactory::getLogger()->debug("Requestinfo list generated and saved in $filePath");
            $objWriter->save($filePath);
            if (WFactory::getHelper()->isUnitTest()) {
                return $filePath;
            }


            //    WFactory::getHelper()->doDownload($filePath, "xx.xls");

//            header('Content-type: application/vnd.ms-excel');
//            header('Content-Disposition: attachment; filename="request_info_' . $date . '.xls"');
//            $objWriter->save('php://output');

            JFactory::getApplication()->redirect(JUri::base() . "tmp/$fileName");
        } catch (Exception $e) {
            $msg = "Excel generation error! msg : " . $e->getMessage();
            WFactory::getLogger()->fatal($msg);
            WFactory::throwPortalException($msg);
        }


    }


}