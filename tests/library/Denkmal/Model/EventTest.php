<?php

class Denkmal_Model_EventTest extends CMTest_TestCase {

    /** @var Denkmal_Model_Event */
    private $_event;

    protected function setUp() {
        $venue = DenkmalTest_TH::createVenue('Example');
        $this->_event = Denkmal_Model_Event::create($venue, 'Foo', true, false, new DateTime());
    }

    protected function tearDown() {
        CMTest_TH::clearEnv();
    }

    public function testCreate() {
        $this->assertInstanceOf('Denkmal_Model_Venue', $this->_event->getVenue());
        $this->assertInstanceOf('DateTime', $this->_event->getFrom());
        $this->assertSame(null, $this->_event->getUntil());
        $this->assertSame('Foo', $this->_event->getDescription());
        $this->assertSame(null, $this->_event->getGenres());
        $this->assertSame(null, $this->_event->getSong());
        $this->assertSame(false, $this->_event->getQueued());
        $this->assertSame(true, $this->_event->getEnabled());
        $this->assertSame(false, $this->_event->getHidden());
        $this->assertSame(false, $this->_event->getStarred());
    }

    public function testGetSetVenue() {
        $venue = DenkmalTest_TH::createVenue();
        $this->assertNotEquals($venue, $this->_event->getVenue());
        $this->_event->setVenue($venue);
        $this->assertEquals($venue, $this->_event->getVenue());
    }

    public function testGetSetFrom() {
        $later = new DateTime();
        $later->add(new DateInterval('P1D'));
        $this->_event->setFrom($later);
        $this->assertSame($later->getTimestamp(), $this->_event->getFrom()->getTimestamp());
        $this->assertEquals($this->_event->getTimeZone(), $this->_event->getFrom()->getTimezone());
    }

    public function testGetSetUntil() {
        $now = new DateTime();

        $this->_event->setUntil(null);
        $this->assertEquals(null, $this->_event->getUntil());

        $this->_event->setUntil($now);
        $this->assertSame($now->getTimestamp(), $this->_event->getUntil()->getTimestamp());
        $this->assertEquals($this->_event->getTimeZone(), $this->_event->getUntil()->getTimezone());
    }

    public function testGetUntilEndOfDay() {
        $venue = DenkmalTest_TH::createVenue();
        $event1 = Denkmal_Model_Event::create($venue, 'Foo1', true, false, new DateTime('2014-12-31 2:00'));
        $event2 = Denkmal_Model_Event::create($venue, 'Foo2', true, false, new DateTime('2014-12-31 15:00'));
        $event3 = Denkmal_Model_Event::create($venue, 'Foo3', true, false, new DateTime('2015-01-01 4:00'));
        $event4 = Denkmal_Model_Event::create($venue, 'Foo3', true, false, new DateTime('2015-01-01 5:59'));

        $this->assertEquals(new DateTime('2014-12-31 6:00'), $event1->getUntilEndOfDay());
        $this->assertEquals(new DateTime('2015-01-01 6:00'), $event2->getUntilEndOfDay());
        $this->assertEquals(new DateTime('2015-01-01 6:00'), $event3->getUntilEndOfDay());
        $this->assertEquals(new DateTime('2015-01-01 6:00'), $event4->getUntilEndOfDay());
    }

    public function testGetSetDescription() {
        $this->_event->setDescription('Bar');
        $this->assertSame('Bar', $this->_event->getDescription());
    }

    public function testGetSetGenres() {
        $this->_event->setGenres('Bar');
        $this->assertSame('Bar', $this->_event->getGenres());

        $this->_event->setGenres(null);
        $this->assertSame(null, $this->_event->getGenres());
    }

    public function testGetSetSong() {
        $song = Denkmal_Model_Song::create('Foo', CM_File::createTmp());
        $this->_event->setSong($song);
        $this->assertEquals($song, $this->_event->getSong());

        $this->_event->setSong(null);
        $this->assertSame(null, $this->_event->getSong());
    }

    public function testGetSetQueued() {
        $this->_event->setQueued(true);
        $this->assertSame(true, $this->_event->getQueued());
    }

    public function testGetSetEnabled() {
        $this->_event->setEnabled(false);
        $this->assertSame(false, $this->_event->getEnabled());
    }

    public function testGetSetHidden() {
        $this->_event->setHidden(true);
        $this->assertSame(true, $this->_event->getHidden());
    }

    public function testGetSetStarred() {
        $this->_event->setStarred(true);
        $this->assertSame(true, $this->_event->getStarred());
    }

    /**
     * @expectedException CM_Exception_Nonexistent
     */
    public function testDelete() {
        $this->_event->delete();

        new Denkmal_Model_Event($this->_event->getId());
    }

    public function testToArrayApi() {
        $venue = DenkmalTest_TH::createVenue();
        $from = new DateTime();
        $until = (new DateTime())->add(new DateInterval('PT1H'));
        $song = Denkmal_Model_Song::create('My Song', CM_File::createTmp());
        $link = Denkmal_Model_Link::create('foo', 'http://foo.com', true);
        $event = Denkmal_Model_Event::create($venue, 'hello foo bar', true, false, $from, $until, $song, false, true);

        $render = new CM_Frontend_Render();
        $data = $event->toArrayApi($render);

        $this->assertSame($event->getId(), $data['id']);
        $this->assertSame($venue->getId(), $data['venue']);
        $this->assertSame($from->getTimestamp(), $data['from']);
        $this->assertSame($until->getTimestamp(), $data['until']);
        $this->assertSame(true, $data['starred']);
        $this->assertSame($song->toArrayApi($render), $data['song']);
        $this->assertSame('hello foo bar', $data['description']);
        $this->assertSame('hello <a href="http://foo.com" class="url" target="_blank">foo</a> bar', $data['descriptionHtml']);
    }

    public function testGetDuplicates() {
        $venue = DenkmalTest_TH::createVenue();
        $event1 = Denkmal_Model_Event::create($venue, 'Foo 1', true, false, new DateTime('2016-08-18 22:00'));
        $event2 = Denkmal_Model_Event::create($venue, 'Foo 2', true, false, new DateTime('2016-08-19 00:00'));
        $event3 = Denkmal_Model_Event::create($venue, 'Foo 3', true, false, new DateTime('2016-08-20 01:00'));

        DenkmalTest_TH::reinstantiateModel($event2);
        DenkmalTest_TH::reinstantiateModel($event3);

        $this->assertEquals([$event2], $event1->getDuplicates());
        $this->assertEquals([$event1], $event2->getDuplicates());
        $this->assertEquals([], $event3->getDuplicates());
    }

    public function testAddLinkIfNotExists() {
        $venue = DenkmalTest_TH::createVenue();
        $event = Denkmal_Model_Event::create($venue, 'hello foo bar', true, false, new DateTime('2017-01-01 22:00'));

        $link1 = $event->addLinkIfNotExists('Foo', 'http://foo/1');
        $this->assertInstanceOf(Denkmal_Model_EventLink::class, $link1);
        $this->assertEquals([$link1], new Denkmal_Paging_EventLink_Event($event));

        $link1again = $event->addLinkIfNotExists('Foo', 'http://foo/2');
        $this->assertEquals($link1, $link1again);
        $this->assertEquals([$link1], new Denkmal_Paging_EventLink_Event($event));

        $link2 = $event->addLinkIfNotExists('Bar', 'http://bar');
        $this->assertInstanceOf(Denkmal_Model_EventLink::class, $link2);
        $this->assertEquals([$link2, $link1], new Denkmal_Paging_EventLink_Event($event));
    }
}
