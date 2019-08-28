<?php

$jpath_base = (dirname(__FILE__)) . "/../../";
$jpath_base = realpath($jpath_base);
require_once "$jpath_base/phpunitbootstrap.php";
//require_once 'PHPUnit/Framework/TestCase.php';
//require_once 'PHPUnit/Autoload.php';


/**
 * Created by JetBrains PhpStorm.
 * User: khan
 * Date: 7/9/13
 * Time: 8:35 AM
 * To change this template use File | Settings | File Templates.
 */

class WebportalFileManagerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var WebportalFileManager
     */
    var $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = WFactory::getFileManager();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    public function testGetFileManager()
    {
        $fileManager = $this->object->getFileManager("s3");
        $this->assertNotNull($fileManager);
        $this->assertTrue(is_a($fileManager, "WebportalS3FileManager"));

    }
}
