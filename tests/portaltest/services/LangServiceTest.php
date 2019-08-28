<?php

/**
 * Created by PhpStorm.
 * User: Lian
 * Date: 7/18/14
 * Time: 1:36 PM
 */
class LangServiceTest extends PHPUnit_Framework_TestCase
{
    /** @var LangService */
    var $service;

    function setUp()
    {
        $this->service = WFactory::getServices()->getServiceClass("LangService");
    }

    function testLang()
    {
        $this->service->processFile('/test/test/test.html');
        $this->assertNotEquals("BUY", JText::_("BUY"));
    }

    function testIni()
    {
        // Capture hidden PHP errors from the parsing.
        $track_errors = ini_get('track_errors');
        ini_set('track_errors', true);

		$contents = file_get_contents(JPATH_BASE . '/language/th-TH/th-TH.com_webportal.ini');
		$contents = str_replace('_QQ_', '"\""', $contents);
		$strings = @parse_ini_string($contents);

        $this->assertNotEmpty($strings);

        // Restore error tracking to what it was before.
        ini_set('track_errors', $track_errors);
    }

    /**
     * Test the last 30 troublesome strings.
     */
    function teststrings()
    {
        $this->assertNotEmpty(@parse_ini_string('BEST_VIEWED_IN_CHROME_LINK="Kortaleit virkar best i %s vafranum."'));
        $this->assertNotEmpty(@parse_ini_string('SELECT_THE_SEARCH_TO_FILTER_THE_RESULTS_ON_THE_MAP="Veldu leitarskilyrði"'));
        $this->assertNotEmpty(@parse_ini_string('PLEASE_WAIT="Vinsamlega bíðið"'));
        $this->assertNotEmpty(@parse_ini_string('PAGE_OPTIMIZED_FOR_CHROME="Þessi síða er hönnuð fyrir Google Chrome vafra og getur verið hægari í öðrum."'));
        $this->assertNotEmpty(@parse_ini_string('PROPERTY_WATCH_LABEL="Property Watch"'));
        $this->assertNotEmpty(@parse_ini_string('CALL_FOR_PRICE="Hringdu fyrir verð"'));
        $this->assertNotEmpty(@parse_ini_string('X_PROPERTIES_FOUND_WITH_YOUR_SAVED_SEARCH_Y="%s properties were found with your saved search _QQ_%s_QQ_ (icelandic)"'));
        $this->assertNotEmpty(@parse_ini_string('NEW_PROPERTY_MATCHED_YOUR_PROPERTY_WATCH="A new property has been added that matched one of your saved searches. (icelandic)"'));
        $this->assertNotEmpty(@parse_ini_string('PRICE="ราคา"'));
        $this->assertNotEmpty(@parse_ini_string('ROOMS="จำนวนห้อง"'));
        $this->assertNotEmpty(@parse_ini_string('SIZE="ขนาด"'));
        $this->assertNotEmpty(@parse_ini_string('DAYS="วัน"'));
        $this->assertNotEmpty(@parse_ini_string('FRESHNESS="ความสดชื่น"'));
        $this->assertNotEmpty(@parse_ini_string('PRICE_REDUCTIONS="การลดราคา"'));
        $this->assertNotEmpty(@parse_ini_string('EXCLUSIVE_ENTRANCE="ทางเข้าพิเศษ"'));
        $this->assertNotEmpty(@parse_ini_string('ELEVATOR="ลิฟต์"'));
        $this->assertNotEmpty(@parse_ini_string('WITH_EXTRA_FLAT="ห้องชุดพิเศษ"'));
        $this->assertNotEmpty(@parse_ini_string('WITH_GARAGE="ที่จอด"'));
        $this->assertNotEmpty(@parse_ini_string('SWAPPING="การแลกเปลี่ยน"'));
        $this->assertNotEmpty(@parse_ini_string('OPEN_HOUSE="เปิดบ้าน"'));
        $this->assertNotEmpty(@parse_ini_string('CONTACT_US="ติดต่อเรา"'));
    }
}
 