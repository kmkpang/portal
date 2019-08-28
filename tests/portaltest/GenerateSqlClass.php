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
////require_once 'PHPUnit/Framework/TestCase.php';
//require_once 'PHPUnit/Autoload.php';

////var/www/softverk-webportal/libraries/webportal/services/sqlclassgenerator/generator.php
require_once JPATH_ROOT . "/libraries/webportal/services/sqlclassgenerator/generator.php";

class GenerateSqlTest extends PHPUnit_Framework_TestCase
{


    /**
     * @var PropertyPortalLibraryCore
     */
    var $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }


    public function testGeneration()
    {

        $tableList = getTableList();

        $this->assertNotEmpty($tableList);

        foreach ($tableList as $t) {

            $name = str_replace("jos_", "", $t);

            $name = lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $name))));

            $fileName = generateClass($t, $name, "id", "webportal_");
            ///var/www/eign_v2/libraries/propertyportal/dbclasses/class.agents.php
            $this->assertFileExists($fileName);
        }
    }


}
