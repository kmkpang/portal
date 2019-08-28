<?php
/**
 * Created by PhpStorm.
 * User: khan
 * Date: 7/23/14
 * Time: 12:25 AM
 */

require_once JPATH_LIBRARY_WEBPORTAL_SERVICES . DS . 'mandrill' . DS . 'Mandrill.php';

class MandrillService
{
    /**
     * @param $mailer JMail
     * @return mixed|null
     */
    public function overrideSystemMail($mailer)
    {
        $tos = array();
        $to = $mailer->getToAddresses();
        foreach ($to as $t) {
            $t = $t[0];
            $tos[] = $t;
        }

        $from = $mailer->getToAddresses();
        $from = $from[0][0];

        $isHtml = false;
        if (strpos($mailer->ContentType, "html") !== false) {
            $isHtml = true;
        }
        return $this->sendMandrillMail($mailer->Subject,
            $mailer->Body, $from, $tos, $mailer->FromName, $isHtml);

    }

    public function sendMandrillMail($subject, $body, $from, $to, $fromName = "", $isHtml = false, $images = array(), $attachments = array())
    {

        if (is_string($to))
            $to = [["email" => $to, "name" => "Receiver",
                "type" => "to"]];

        if (defined('KHAN_HOME') && KHAN_HOME === true)
            return null;


        $config = $this->getConfig();
        $mandrill = new Mandrill($config['key']);

        try {
            $model = $this->getModel();

            if ($isHtml) {
                $model['message']['html'] = $body;
            } else {
                $model['message']['text'] = $body;
            }

            $model['message']['subject'] = $subject;
            $model['message']['from_email'] = WFactory::getHelper()->isNullOrEmptyString($from) ? JFactory::getConfig()->get('mailfrom') : $from;
            $model['message']['auto_html'] = $isHtml;
            $model['message']['from_name'] = WFactory::getHelper()->isNullOrEmptyString($fromName) ? JFactory::getConfig()->get('fromname') : $fromName;

            if (!WFactory::getHelper()->isValidEmail($model['message']['from_email'])) {
                $model['message']['from_email'] = JFactory::getConfig()->get('mailfrom');
            }

            $model['message']['to'] = $to; //$emails
            $model['message']['headers']['Reply-To'] = $from;
            $model['message']['attachments'] = $attachments;
            $model['message']['images'] = $images;

            WFactory::getLogger()->debug("Calling mandrill with the following stucture: " .
                json_encode($model, JSON_PRETTY_PRINT));

            $result = $mandrill->call("messages/send", $model);

            WFactory::getLogger()->info("Mandrill result is: " . json_encode($result, JSON_PRETTY_PRINT), __LINE__, __FILE__);

            return $result;
        } catch (Exception $e) {

            WFactory::getLogger()->fatal("Mandrill send mail failed with error : " . $e->getMessage(), __LINE__, __FILE__);
        }
        return null;

    }


    private function getConfig()
    {
        $configuration = WFactory::getConfig()->getWebportalConfigurationArray();
        return $configuration['mandrill'];
    }

    private function getModel()
    {

        $config = $this->getConfig();

        $returnValue = json_decode(trim($config['modal']), true);

        return $returnValue;
    }
}