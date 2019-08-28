<?php
/**
 * Created by JetBrains PhpStorm.
 * User: khan
 * Date: 7/2/13
 * Time: 9:30 PM
 * To change this template use File | Settings | File Templates.
 */
$jpath_base = (dirname(__FILE__)) . "/../../";
$jpath_base = realpath($jpath_base);
require_once "$jpath_base/phpunitbootstrap.php";
//require_once 'PHPUnit/Framework/TestCase.php';
//require_once 'PHPUnit/Autoload.php';

/**
 * Class PortalOfficesTest
 * @covers PortalPortalOfficesSql
 */
class PortalOfficesTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PortalPortalOfficesSql
     */
    var $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = WFactory::getServices()->getSqlService()->getDbClass(__PORTAL_PORTAL_OFFICES_SQL);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    public function testXmlLoadin()
    {
        $xmlString = <<< EOD
<?xml version="1.0" encoding="UTF-8"?>
<UploadXML  xmlns:xsd="http://www.w3.org/2001/XMLSchema-instance"
							xsd:schemaLocation="http://www.softverk.is/mapportal/ns/1.1.1/agent_create.xsd">

<!-- System inforamtion //-->
	<System>
				<PublicKey>AAAAB3NzaC1yc2EAAAABJQAAAIEAoAYSkomyr2mkcdux7bk79pKFX9QeBPuOUElP
0jLKL9l52L6iGI1Lh74Xo0v2UzksfWkRM5CBns2H5B4hGU1h5rWS9qkRH9wRDWog
o2waqWCWS0QlLb2agx9v9/lziGhNSy9EXiS3YwZ1Zg/I6uq0wpqc3q3uP+7e9fx2
YFjoG2M=</PublicKey>
				<Version>String</Version>
				<SystemName>Saga: Landmark</SystemName>
				<SentData SentDate="2002-05-30 09:00:00" Command="Create" Type="office"/>
	</System>

			<Offices>
				<Office>
					<Address Country="IS">
						<HouseAddress>Barónsstígur</HouseAddress>
						<Street>1234  Bowlow St.</Street>
						<PostalCodeID>26</PostalCodeID>
						<TownID>12</TownID>
						<RegionID>2</RegionID>
						<Latitude>64.135696</Latitude>
						<Longitude>-21.862106</Longitude>
					</Address>


	<!--- Office information //-->

					<Information>
							<OfficeID>OF12345</OfficeID> <!-- Dosent matter what you put in here, since this is create-->
							<OfficeName>RE/MAX skulu ekki beita ósanngjörnum aðferðum</OfficeName>
							<Email>admin@remax.is</Email>
							<Fax>697-3629</Fax>
							<Phone>697-3620</Phone>
							<URLToPrivatePage>www.remax.is</URLToPrivatePage>
							<logo>http://www.remax.is/img/banner_temp/baer.png</logo>

							<MarketingInfo>
								<LanguageID LanguageID="2"/>
								<OfficeDescription>Siðareglur þessar eru settar af RE/MAX Europe. Þær eru settar í þeim tilgangi að efla siðferði og faglegan rekstur RE/MAX keðjunnar. Allir RE/MAX Europe svæðisstjórar, sérleyfishafar og sölufulltrúar (hér eftir kallaðir "samstarfsaðilar RE/MAX") eru bundnir af ákvæðum þessara siðareglna og hafa skuldbundið </OfficeDescription>
								<Slogan>Siðareglur þessar eru settar af RE/MAX Europe. Þær eru settar í þeim tilgangi að </Slogan>
								<Closer>rope svæðisstjórar, sérleyfishafar og sölufull </Closer>
								<BulletPoint1>1-svæðisstjórar, sérleyfishafar og sölufulltrúar (hér eftir kallaðir "samstarfsaðilar RE/MAX") </BulletPoint1>
								<BulletPoint2>2-svæðisstjórar, sérleyfishafar og sölufulltrúar (hér eftir kallaðir "samstarfsaðilar RE/MAX") </BulletPoint2>
								<BulletPoint3>3-svæðisstjórar, sérleyfishafar og sölufulltrúar (hér eftir kallaðir "samstarfsaðilar RE/MAX") </BulletPoint3>
							</MarketingInfo>
				</Information>
				<Images>
					<DefaultImageSequenceNumber>1</DefaultImageSequenceNumber>
					<Image>
						<SequenceNumber>1</SequenceNumber>
						<FileName>http://www.remax.is/img/banner_temp/baer.png</FileName>
						<DescriptiveName>RE/MAX baer</DescriptiveName>
						<Alt>RE/MAX  baer</Alt>
					</Image>
					<Image>
						<SequenceNumber>2</SequenceNumber>
						<FileName>http://remax.is/media/pictures/1222804564/original/bd61fec1aeab649e543e8cbaa73474ce.jpg</FileName>
						<DescriptiveName>RE/MAX Iceland</DescriptiveName>
						<Alt>RE/MAX</Alt>
					</Image>
				</Images>
			</Office>
		</Offices>
	</UploadXML>


EOD;

        $result = $this->object->loadDataFromXml($xmlString);
        $x = $this->object;
        $this->assertTrue($result);;
    }

}
