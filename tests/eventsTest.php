<?php
require_once("../includes/Event.php");

class EventTest extends PHPUnit_Framework_TestCase
{

	public function testSetFile()
	{
		$event = new Event;
		$imgFile = array('name'		=> 'test.jpg',
						 'tmp_name' => '12345.jpg',
						 'type'		=> 'image/jpeg');
		$event->setFile('eblast', $imgFile);

		$testFile = $event->getFile('eblast');
		$testImg = $event->getImg('eblast');

		$this->assertEquals($testFile, $imgFile);
		$this->assertEquals($testImg, $imgFile['name']);
	}

	public function testPath()
	{
		$event = new Event;
		$path = '/mailings/events/2012/03';
		$event->setPath('eblast', $path);

		$testPath = $event->getPath('eblast');

		$this->assertEquals($path, $testPath);
	}

	public function testLink()
	{
		$event = new Event;
		$link = 'http://test.com';
		$event->setLink('eblast', $link);

		$testLink = $event->getLink('eblast');

		$this->assertEquals($testLink, $link);
	}

	public function testGetBannerInfo()
	{
		$event = new Event;

		$imgFile = array('name' => 'test.jpg');
		$event->setFile('banner', $imgFile);

		$path = '/images/events/2012';
		$event->setPath('banner', $path);

		$bannerInfo = $event->getInfo('banner');

		$testInfo = array('img'  => $imgFile['name'],
						  'link' => null,
						  'path' => $path);

		$this->assertEquals($testInfo, $bannerInfo);

		$link = 'http://test.com';
		$event->setLink('banner', $link);

		$bannerInfo = $event->getInfo('banner');

		$testInfo = array('img'  => $imgFile['name'],
						  'link' => $link,
						  'path' => $path);

		$this->assertEquals($testInfo, $bannerInfo);
	}

	public function testGetEblastInfo()
	{
		$event = new Event;

		$imgFile = array('name' => 'test.jpg');
		$event->setFile('eblast', $imgFile);

		$path = '/images/events/2012';
		$event->setPath('eblast', $path);

		$eblastInfo = $event->getInfo('eblast');

		$testInfo = array('img'  => $imgFile['name'],
						  'link' => 'http://ad2orlando.org',
						  'path' => $path);

		$this->assertEquals($testInfo, $eblastInfo);

		$link = 'http://test.com';
		$event->setLink('eblast', $link);

		$eblastInfo = $event->getInfo('eblast');

		$testInfo = array('img'  => $imgFile['name'],
						  'link' => $link,
						  'path' => $path);

		$this->assertEquals($testInfo, $eblastInfo);
	}

	public function testStatus()
	{
		$event = new Event;
		$status = "This is a test status.";
		$event->setStatus($status);

		$testStatus = $event->getStatus();

		$this->assertEquals($status, $testStatus);
	}

	public function testEblastHtml()
	{

	}

	public function testGetDirectory()
	{
		$event = $this->getMock('Event', array('_createDirectory'));

        $event->expects($this->any())
              ->method('_createDirectory')
              ->will($this->returnValue(null));

		$refEvent = new ReflectionClass($event);
		$getDirectory = $refEvent->getMethod('_getDirectory');
		$getDirectory->setAccessible("true");

		$testDirectory = $getDirectory->invoke($event, 'Test Event');

		$year = date('Y');
		$month = date('m');
		$directory = "/mailings/events/$year/$month/test-event";

		$this->assertEquals($directory, $testDirectory);
	}

	public function testCreateEvent()
	{
		//Create mock object
		$event = $this->getMock('Event', array('_uploadFile', 'setEblastHtml', '_createDirectory', '_createEblastFile'));

		//Stub out methods to return specified values
        $event->expects($this->any())
              ->method('_uploadFile')
              ->will($this->returnValue(null));

        $event->expects($this->any())
              ->method('setEblastHtml')
              ->will($this->returnValue(null));

        $event->expects($this->any())
              ->method('_createDirectory')
              ->will($this->returnValue(null));

        $event->expects($this->any())
              ->method('_createEblastFile')
              ->will($this->returnValue(null));

		$this->assertFalse($event->createEvent('Test Event'));

		$imgFile = array('name'		=> 'test.gif',
						 'tmp_name' => '12345.gif',
						 'type'		=> 'image/gif');

		$event->setFile('banner', $imgFile);
		$status = "The files uploaded must be jpeg format.";

		$this->assertFalse($event->createEvent('Test Event'));
		$this->assertEquals($status, $event->getStatus());

		$imgFile = array('name'		=> 'test.jpg',
						 'tmp_name' => '12345.jpg',
						 'type'		=> 'image/jpeg');

		$event->setFile('banner', $imgFile);
		$event->createEvent('Test Event');
		$status = "File successfully uploaded.";

		$this->assertEquals($status, $event->getStatus());

		$imgFile = array('name'		=> 'test.jpg',
						 'tmp_name' => '12345.jpg',
						 'type'		=> 'image/jpeg');
		$event->setFile('eblast', $imgFile);
		$event->createEvent('Test Event');

		$this->assertEquals($status, $event->getStatus());
	}
}
