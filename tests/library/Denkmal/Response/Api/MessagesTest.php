<?php

class Denkmal_Response_Api_MessagesTest extends CMTest_TestCase {

	protected function setUp() {
		CM_Config::get()->Denkmal_Site->url = 'http://denkmal.test';
	}

	public function tearDown() {
		CMTest_TH::clearEnv();
	}

	public function testMatch() {
		$request = new CM_Request_Get('/api/messages', array('host' => 'denkmal.test'));
		$response = CM_Response_Abstract::factory($request);
		$this->assertInstanceOf('Denkmal_Response_Api_Messages', $response);
	}

	public function testProcess() {
		$venue = Denkmal_Model_Venue::create('Example', true, false, false);
		$message1 = Denkmal_Model_Message::create($venue, 'Foo 1');
		$message2 = Denkmal_Model_Message::create($venue, 'Foo 2');

		$request = new CM_Request_Get('/api/messages', array('host' => 'denkmal.test'));
		$response = new Denkmal_Response_Api_Messages($request);
		$response->process();

		$expected = array(
			$message1->toArrayApi($response->getRender()),
			$message2->toArrayApi($response->getRender()),
		);

		$this->assertSame($expected, json_decode($response->getContent(), true));
	}
}
