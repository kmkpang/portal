<?php
/**
 * Created by JetBrains PhpStorm.
 * User: khan
 * Date: 7/2/13
 * Time: 12:12 PM
 * To change this template use File | Settings | File Templates.
 */

$jpath_base = (dirname(__FILE__)) . "/../../";
$jpath_base = realpath($jpath_base);
require_once "$jpath_base/phpunitbootstrap.php";
//require_once 'PHPUnit/Framework/TestCase.php';
//require_once 'PHPUnit/Autoload.php';


class SqlServiceTest extends PHPUnit_Framework_TestCase
{


    /**
     * @var WebportalSqlServiceProvider
     */
    var $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = WFactory::getServices()->getSqlService();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }


    public function testGetDbo()
    {
        $db = $this->object->getDbo();

        $this->assertNotEmpty($db);

        // i wanna test if i get unnessary db connection or not..
        $db1 = $this->object->getDbo();

        $this->assertEquals(true, $db === $db1);
    }

    public function testSelect()
    {
        $query = $this->object->getQuery();
        $query->select('*')->from("#__geography_postal_codes");

        $query = (string)$query;
        $result = $this->object->select($query);

        $this->assertNotEmpty($result);

    }

    public function testLoadDataFromDatabase()
    {
        /**
         * @var $office PortalPortalOfficesSql
         */
        $office = WFactory::getServices()->getSqlService()->getDbClass(__PORTAL_PORTAL_OFFICES_SQL);
        $office->__company_id = 1;
        $office->__unique_id = 'C1OF3620140427182601';

        $office = $office->loadDataFromDatabase();


        $this->assertNotNull($office);


    }

    public function testInsert()
    {
        ///var/www/softverk-webportal/libraries/webportal/services/dbclasses/class.portalOffices.php
        require_once JPATH_ROOT . "/libraries/webportal/services/dbclasses/class.portalOffices.php";

        $office = WFactory::getServices()->getSqlService()->getDbClass(__PORTAL_PORTAL_OFFICES_SQL);
        $office->__company_id = 5;

        $this->assertNotNull($this->object->insert($office));
    }

    public function testDelete()
    {
        $query = $this->object->getQuery();
        require_once JPATH_ROOT . "/libraries/webportal/services/dbclasses/class.portalOffices.php";
        $office = WFactory::getServices()->getSqlService()->getDbClass(__PORTAL_PORTAL_OFFICES_SQL);

        $query->delete($office->getTableName());

        $query = (string)$query;
        $result = $this->object->delete($query);

        $this->assertTrue($result);
    }


    public function testUpdate()
    {
        require_once JPATH_ROOT . "/libraries/webportal/services/dbclasses/class.portalOffices.php";

        $office = WFactory::getServices()->getSqlService()->getDbClass(__PORTAL_PORTAL_OFFICES_SQL);
        $office->__company_id = 5;

        $insertId = $this->object->insert($office);

        $office->__id = $insertId;
        $office->__unique_id = 9;
        $office->__company_id = 0;


        $result = $this->object->update($office);
        $this->assertTrue($result);

    }

    public function testGetSqlServiceClass()
    {
        $marketingInfoId = WFactory::getServices()->getSqlService()->getSqlServiceClass(__PROPPERTY_PORTAL_MARKETINGINFO);

        $this->assertNotNull($marketingInfoId);
    }


}
