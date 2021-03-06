<?php

class Denkmal_Model_MessageTest extends CMTest_TestCase {

	protected function tearDown() {
		CMTest_TH::clearEnv();
	}

	public function testCreate() {
		$text = 'foo bar';
		$venue = Denkmal_Model_Venue::create('Example', true, false, false);
		$message = Denkmal_Model_Message::create($venue, $text);
		$this->assertEquals($venue, $message->getVenue());
		$this->assertSame($text, $message->getText());
		$this->assertSameTime(time(), $message->getCreated()->getTimestamp());
	}

	/**
	 * @expectedException CM_Exception_Nonexistent
	 */
	public function testOnDelete() {
		$venue = Denkmal_Model_Venue::create('Example', true, false, false);
		$message = Denkmal_Model_Message::create($venue, 'foo');
		$message->delete();
		new Denkmal_Model_Message($message->getId());
	}
}
