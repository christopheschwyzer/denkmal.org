<?php

class Denkmal_ParamsTest extends CMTest_TestCase {

	public function testGetVenue() {
		$venue = Denkmal_Model_Venue::create('Foo', true, false, false);

		$params = new Denkmal_Params(array('venue1' => $venue, 'venue2' => $venue->getId()));

		$this->assertEquals($venue, $params->getVenue('venue1'));
		$this->assertEquals($venue, $params->getVenue('venue2'));
	}

	public function testGetSong() {
		$file = CM_File::createTmp();
		$song = Denkmal_Model_Song::create('Foo', $file);

		$params = new Denkmal_Params(array('song1' => $song, 'song2' => $song->getId()));

		$this->assertEquals($song, $params->getSong('song1'));
		$this->assertEquals($song, $params->getSong('song2'));
	}
}
