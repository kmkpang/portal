<?php

$jpath_base = (dirname(__FILE__)) . "/../../../";
$jpath_base = realpath($jpath_base);
require_once "$jpath_base/phpunitbootstrap.php";
//require_once 'PHPUnit/Framework/TestCase.php';
//require_once 'PHPUnit/Autoload.php';
//require_once 'PHPUnit/Autoload.php';

/**
 * Created by PhpStorm.
 * User: khan
 * Date: 7/23/14
 * Time: 1:20 AM
 */
class MandrillServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var MandrillService
     */
    var $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_MANDRILL);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    public function testSend()
    {
        $this->assertNotEmpty($this->object->sendMandrillMail("Test", "More Test...\r\n hahaha", "khan@fingi.com", "shroukkhan@gmail.com"));
    }

    public function test__overrideSystemMail()
    {
        $mailer = JFactory::getMailer();
        $subject = "test__overrideSystemMail";
        $mailer->setSubject($subject);
        $mailer->addRecipient('shroukkhan@gmail.com');
        $mailer->addRecipient('khan@fingi.com');

        $message = 'Holla amigo,';
        $message .= "<br/>You have a message from test__overrideSystemMail<br/><br/>";
        $message .= "Message:<br/>";
        $message .= 'Huka Chuka Luka';

        // $contactModel->message;
        $mailer->setBody($message);
        $mailer->IsHTML(true);
        $success = $this->object->overrideSystemMail($mailer);

        $this->assertNotNull($success);
    }
}
 