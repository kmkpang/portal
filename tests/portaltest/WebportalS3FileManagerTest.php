<?php
/**
 * Created by JetBrains PhpStorm.
 * User: khan
 * Date: 7/2/13
 * Time: 5:32 PM
 * To change this template use File | Settings | File Templates.
 */

$jpath_base = (dirname(__FILE__)) . "/../../";
$jpath_base = realpath($jpath_base);
require_once "$jpath_base/phpunitbootstrap.php";
//require_once 'PHPUnit/Framework/TestCase.php';
//require_once 'PHPUnit/Autoload.php';


class WebportalS3FileManagerTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var WebportalS3FileManager
     */
    var $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = WFactory::getFileManager()->getFileManager();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    public function test__getFile()
    {

        $image="/home/khan/www/softverk-webportal-generic/tests/portaltest/testImages/logo.jpg";
        $url="";
        $result=$this->object->putFile($image,"1/104/logo-1.jpg",$url);

        $file = JPATH_ROOT . "/tmp/test.jpg";

        $result = $this->object->getFile("1/104/logo-1.jpg", $file);

        print_r($result);
    }


    public function testListBuckets()
    {
        $this->assertNotEmpty($this->object->s3ListBuckets());
    }

    public function testUploadImage()
    {
        ////var/www/softverk-webportal/tests/portaltest/testImages/testImage1.png
        $baseImagePath = JPATH_ROOT . "/tests" . DS . "portaltest/testImages/testImage1.png";
        $pathURL = "";
        $this->assertTrue($this->object->putFile($baseImagePath, "test/testImage1.png", $pathURL));
        $this->assertTrue(WFactory::getHelper()->checkIfFileAtURLExists($pathURL));

    }

    public function testUploadWebImage()
    {
        $baseImagePath = "http://www.seomofo.com/downloads/new-google-logo-knockoff.png";
        $pathURL = "";
        $this->assertTrue($this->object->putFile($baseImagePath, "test/testImage1.png", $pathURL));
        $this->assertTrue(WFactory::getHelper()->checkIfFileAtURLExists($pathURL));
    }

    public function testDeleteUploadedImage()
    {
        $baseImagePath = JPATH_ROOT . "/tests" . DS . "portaltest/testImages/testImage1.png";
        $pathURL = "";
        $this->assertTrue($this->object->putFile($baseImagePath, "test/testImage1.png", $pathURL));
        $this->assertTrue(WFactory::getHelper()->checkIfFileAtURLExists($pathURL));

        $filePath = $this->object->buildS3PathFromWebURL($pathURL);
        $this->assertTrue($this->object->deleteFile($filePath));
//        sleep(10);//wait some time to actually get the image deleted by system at s3
//        $this->assertFalse(WFactory::getHelper()->checkIfFileAtURLExists($pathURL));
    }

    public function testCreateFolder()
    {
        $folderPath = "test/test1";
        $this->assertTrue($this->object->createFolder($folderPath));
        $folderPath = "test2";
        $this->assertTrue($this->object->createFolder($folderPath));
    }

    public function testDeleteFolder()
    {
        $folderPath = "test/test1";
        $this->assertTrue($this->object->createFolder($folderPath));

        $array = Array("test/test1/dummy.txt");

        $this->assertTrue($this->object->deleteFolder($array));

        $folderPath = "test2";
        $this->assertTrue($this->object->createFolder($folderPath));

        $array = Array("test2/dummy.txt");

        $this->assertTrue($this->object->deleteFolder($array));


    }


}
