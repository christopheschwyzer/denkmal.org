<?php

class Denkmal_Model_RegionTest extends CMTest_TestCase {

    protected function tearDown() {
        CMTest_TH::clearEnv();
    }

    public function testCreate() {
        $city = DenkmalTest_TH::createLocationCity();
        $region = Denkmal_Model_Region::create('foo', 'bar', 'baz', 'me@example.com', $city);
        $this->assertInstanceOf('Denkmal_Model_Region', $region);
        $this->assertSame('foo', $region->getName());
        $this->assertSame('bar', $region->getSlug());
        $this->assertSame('baz', $region->getAbbreviation());
        $this->assertSame('me@example.com', $region->getEmailAddress());
        $this->assertEquals($city, $region->getLocation());

        $timeZone = $region->getTimeZone();
        $this->assertInstanceOf('DateTimeZone', $timeZone);
        $this->assertSame('America/New_York', $timeZone->getName());
    }

    public function testFindBySlug() {
        $city = DenkmalTest_TH::createLocationCity();
        $region = Denkmal_Model_Region::create('foo', 'slug', 'baz', 'me@example.com', $city);
        $region2 = Denkmal_Model_Region::create('fooBar', 'slug2', 'baz2', 'me@example.com', $city);

        $this->assertEquals($region2, Denkmal_Model_Region::findBySlug('slug2'));
        $this->assertEquals($region, Denkmal_Model_Region::findBySlug('slug'));
        $this->assertNull(Denkmal_Model_Region::findBySlug('slug3'));
    }

    public function testGetBySlug() {
        $city = DenkmalTest_TH::createLocationCity();
        $region = Denkmal_Model_Region::create('My Region 1', 'my-region-1', 'my1', '1@example.com', $city);
        $region2 = Denkmal_Model_Region::create('My Region 2', 'my-region-2', 'my2', '2@example.com', $city);

        $this->assertEquals($region, Denkmal_Model_Region::getBySlug('my-region-1'));
        $this->assertEquals($region2, Denkmal_Model_Region::getBySlug('my-region-2'));
        $exception = $this->catchException(function () {
            Denkmal_Model_Region::getBySlug('my-region-3');
        });

        $this->assertInstanceOf('CM_Exception_Nonexistent', $exception);
        $this->assertSame('Cannot find region by slug.', $exception->getMessage());
    }

    public function testGetSetTwitterCredentials() {
        $city = DenkmalTest_TH::createLocationCity();
        $region = Denkmal_Model_Region::create('foo', 'bar', 'baz', 'me@example.com', $city);
        $credentials = new Denkmal_Twitter_Credentials('my-consumerKey', 'my-consumerSecret');

        $region->setTwitterCredentials($credentials);
        $this->assertEquals($credentials, $region->getTwitterCredentials());

        $region->setTwitterCredentials(null);
        $this->assertEquals(null, $region->getTwitterCredentials());
    }

    public function testGetSetSuspension() {
        $city = DenkmalTest_TH::createLocationCity();
        $region = Denkmal_Model_Region::create('My Region', 'my-region', 'myr', 'bsl@example.com', $city);

        $this->assertInstanceOf('Denkmal_Suspension', $region->getSuspension());
        $this->assertNull($region->getSuspension()->getUntil());

        $until = new DateTime('2016-01-01');
        $region->setSuspension($until);
        $this->assertInstanceOf('Denkmal_Suspension', $region->getSuspension());
        $this->assertEquals($until, $region->getSuspension()->getUntil());
    }

}
