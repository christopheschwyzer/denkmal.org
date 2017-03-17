<?php

class Denkmal_Site_Region_AbstractTest extends CMTest_TestCase {

    /** @var Denkmal_Model_Region */
    private $_region;

    /** @var Denkmal_Site_Region_Abstract|PHPUnit_Framework_MockObject_MockObject */
    private $_site;

    /** @var bool */
    private $_debugBackup;

    protected function setUp() {
             $location = CMTest_TH::createLocation();
        $this->_region = Denkmal_Model_Region::create('My Region', 'my-reg', 'MRG', 'me@example.com', $location);

        $this->_site = $this->getMockSite('Denkmal_Site_Region_Abstract', null, null, ['_getRegionSlug']);
        $this->_site->expects($this->any())->method('_getRegionSlug')->will($this->returnValue('my-reg'));

        $this->_debugBackup = CM_Bootloader::getInstance()->isDebug();
        CM_Bootloader::getInstance()->setDebug(true);
    }

    protected function tearDown() {
        CM_Bootloader::getInstance()->setDebug($this->_debugBackup);
        CMTest_TH::clearEnv();
    }

    public function testHasRegion() {
        $this->assertSame(true, $this->_site->hasRegion());
    }

    public function testGetRegion() {
        $this->assertEquals($this->_region, $this->_site->getRegion());
    }

    public function testGetTheme() {
        $this->assertSame('region-my-reg', $this->_site->getTheme());
    }

    public function testFindSiteByGeoPoint() {
        $siteGraz = new Denkmal_Site_Region_Graz();
        $siteBasel = new Denkmal_Site_Region_Basel();

        $this->assertEquals($siteBasel, Denkmal_Site_Region_Abstract::findSiteByGeoPoint(new CM_Geo_Point(47.5572162, 7.5725677)));
        $this->assertEquals($siteBasel, Denkmal_Site_Region_Abstract::findSiteByGeoPoint(new CM_Geo_Point(47.530664, 7.5790373)));

        $this->assertEquals($siteGraz, Denkmal_Site_Region_Abstract::findSiteByGeoPoint(new CM_Geo_Point(47.0735683, 15.3717501)));

        $this->assertNull(Denkmal_Site_Region_Abstract::findSiteByGeoPoint(new CM_Geo_Point(41.589600, -1.208298)));
    }

    public function testFindSiteByCountry() {
        $siteGraz = new Denkmal_Site_Region_Graz();
        $siteBasel = new Denkmal_Site_Region_Basel();

        $locationSwitzerland = CM_Model_Location::findByAttributes(CM_Model_Location::LEVEL_COUNTRY, ['name' => 'Switzerland']);
        $locationBasel = CM_Model_Location::findByAttributes(CM_Model_Location::LEVEL_CITY, ['name' => 'Basel']);
        $locationAustria = CM_Model_Location::findByAttributes(CM_Model_Location::LEVEL_COUNTRY, ['name' => 'Austria']);

        $this->assertEquals($siteBasel, Denkmal_Site_Region_Abstract::findSiteByCountry($locationSwitzerland));
        $this->assertEquals($siteBasel, Denkmal_Site_Region_Abstract::findSiteByCountry($locationBasel));
        $this->assertEquals($siteGraz, Denkmal_Site_Region_Abstract::findSiteByCountry($locationAustria));
    }

    public function testFindSiteByLocation() {
        $siteGraz = new Denkmal_Site_Region_Graz();
        $siteBasel = new Denkmal_Site_Region_Basel();

        $locationSwitzerland = CM_Model_Location::findByAttributes(CM_Model_Location::LEVEL_COUNTRY, ['name' => 'Switzerland']);
        $locationAustria = CM_Model_Location::findByAttributes(CM_Model_Location::LEVEL_COUNTRY, ['name' => 'Austria']);
        /** @var CM_Model_Location|\Mocka\AbstractClassTrait $locationBaselGeoPoint */
        $locationBaselGeoPoint = $this->mockClass('CM_Model_Location')->newInstanceWithoutConstructor();
        $locationBaselGeoPoint->mockMethod('getGeoPoint')->set(function () {
            return new CM_Geo_Point(47.5572162, 7.5725677);
        });

        $this->assertEquals($siteBasel, Denkmal_Site_Region_Abstract::findSiteByLocation($locationBaselGeoPoint));
        $this->assertEquals($siteBasel, Denkmal_Site_Region_Abstract::findSiteByLocation($locationSwitzerland));
        $this->assertEquals($siteGraz, Denkmal_Site_Region_Abstract::findSiteByLocation($locationAustria));
    }

    public function testGetSiteByRegion() {
        $siteGraz = new Denkmal_Site_Region_Graz();
        $regionGraz = Denkmal_Model_Region::findBySlug('graz');
        $siteBasel = new Denkmal_Site_Region_Basel();
        $regionBasel = Denkmal_Model_Region::findBySlug('basel');
        $regionOther = DenkmalTest_TH::createRegion('Other', 'other', 'oth');

        $this->assertEquals($siteGraz, Denkmal_Site_Region_Abstract::findSiteByRegion($regionGraz));
        $this->assertEquals($siteBasel, Denkmal_Site_Region_Abstract::findSiteByRegion($regionBasel));
        $this->assertNull(Denkmal_Site_Region_Abstract::findSiteByRegion($regionOther));
    }

}
