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

		$link = 'test.com';
		$event->setLink('eblast', $link);
		
		$testLink = $event->getLink('eblast');

		$this->assertEquals('http://test.com', $testLink);
	}

	public function testStatus()
	{
		$event = new Event;
		$status = "This is a test status.";
		$event->setStatus($status);

		$testStatus = $event->getStatus();

		$this->assertEquals($status, $testStatus);
	}

	public function testGetInfo()
	{
		$event = new Event;
		$imgFile = array('name'		=> 'test.jpg',
						 'tmp_name' => '12345.jpg',
						 'type'		=> 'image/jpeg');
		$event->setFile('eblast', $imgFile);

		$path = 'mailings/events/2012/03';
		$event->setPath('eblast', $path);

		$link = 'http://test.com';
		$event->setLink('eblast', $link);

		$eblastInfo = $event->getInfo('eblast');

		$testEblastInfo = array('img'  => 'test.jpg', 
					  			'link' => 'http://test.com',
								'path' => 'mailings/events/2012/03');

		$this->assertEquals($testEblastInfo, $eblastInfo);
	}

	public function testParseFile()
	{
		//Create mock object
		$event = $this->getMock('Event', array('_uploadFile', '_createEblastFile'));

		//Stub out methods to return specified values
        $event->expects($this->any())
              ->method('_uploadFile')
              ->will($this->returnValue(null));

        $event->expects($this->any())
              ->method('_createEblastFile')
              ->will($this->returnValue(null));

		//Create ReflectionClass
		$refEvent = new ReflectionClass($event);
		$parseFile = $refEvent->getMethod('_parseFile');
		$parseFile->setAccessible("true");

		$this->assertFalse($parseFile->invoke($event, 'banner'));
		$this->assertFalse($parseFile->invoke($event, 'eblast'));

		$imgFile = array('name'		=> 'test.jpg',
						 'tmp_name' => '12345.jpg',
						 'type'		=> 'image/jpeg');
		$event->setFile('eblast', $imgFile);

		$path = 'mailings/events/2012/03';
		$event->setPath('eblast', $path);

		$link = 'http://test.com';
		$event->setLink('eblast', $link);

		$eblastInfo = $event->getInfo('eblast');

		$testEblastInfo = array('img'  => 'test.jpg', 
					  			'link' => 'http://test.com',
								'path' => 'mailings/events/2012/03');

		$parseFile->invoke($event, 'eblast');

		$testHtml = file_get_contents(dirname(__FILE__) . '/files/eblast_3.txt');
		$testHtml = trim($testHtml);

		$html = $event->getEblastHtml();
		$this->assertEquals($testHtml, $html);
	}

	public function testSetDirectory()
	{
		//Create mock object
		$event = $this->getMock('Event', array('_createDirectory', '_getDate'));

        $event->expects($this->any())
              ->method('_createDirectory')
              ->will($this->returnValue(null));

		$date = array('year'  => '2012',
				 	  'month' => '03',
				 	  'day'	  => '01');

        $event->expects($this->any())
              ->method('_getDate')
              ->will($this->returnValue($date));

		//Create ReflectionClass
		$refEvent = new ReflectionClass($event);
		$setDirectory = $refEvent->getMethod('_setDirectory');
		$setDirectory->setAccessible("true");

		$this->assertFalse($setDirectory->invoke($event, 'eblast', ''));

		$setDirectory->invoke($event, 'eblast', 'Test Name');
		$status = "Event name was not submitted.";
		$this->assertEquals($status, $event->getStatus());

		$this->assertFalse($setDirectory->invoke($event, 'testing', 'Test Name'));
		$status = "Event is neither eblast or banner";
		$this->assertEquals($status, $event->getStatus());

		$testPath = 'mailings/events/2012/03/test-name';
		$path = $setDirectory->invoke($event, 'eblast', 'Test Name');
		$this->assertEquals($testPath, $path);

		$testPath = 'images/events/2012/test-name';
		$path = $setDirectory->invoke($event, 'banner', 'Test Name');
		$this->assertEquals($testPath, $path);

		$testPath = 'mailings/events/2012/03/test-name';
		$path = $setDirectory->invoke($event, 'eblast', 'Test Name');
		$this->assertEquals($testPath, $path);
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

	public function testEblastHtml()
	{
		$event = new Event;

		$event->setImg('eblast', 'eblast_img.jpg');
		$event->setPath('eblast', 'mailings/events/2012/03/test-event');

		$event->setEblastHtml();
		$html = $event->getEblastHtml();
		$testHtml = file_get_contents(dirname(__FILE__) . '/files/eblast_1.txt');
		$testHtml = trim($testHtml);
		$this->assertEquals($testHtml, $html);

		$event->setLink('eblast', 'http://cnn.com');
		$event->setEblastHtml();
		$html = $event->getEblastHtml();
		$testHtml = file_get_contents(dirname(__FILE__) . '/files/eblast_2.txt');
		$testHtml = trim($testHtml);
		$this->assertEquals($testHtml, $html);
	}

	public function testGetDirectory()
	{
		$event = $this->getMock('Event', array('_createDirectory'));

        $event->expects($this->any())
              ->method('_createDirectory')
              ->will($this->returnValue(null));

		$refEvent = new ReflectionClass($event);
		$getDirectory = $refEvent->getMethod('_setDirectory');
		$getDirectory->setAccessible("true");

		$testDirectory = $getDirectory->invoke($event, 'eblast', 'Test Event');

		$year = date('Y');
		$month = date('m');
		$directory = "mailings/events/$year/$month/test-event";

		$this->assertEquals($directory, $testDirectory);
	}

	public function testUpload()
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

		$this->assertFalse($event->upload('Test Event'));

		$imgFile = array('name'		=> 'test.gif',
						 'tmp_name' => '12345.gif',
						 'type'		=> 'image/gif');

		$event->setFile('banner', $imgFile);
		$status = "The files uploaded must be jpeg format.";

		$this->assertFalse($event->upload('Test Event'));
		$this->assertEquals($status, $event->getStatus());

		$imgFile = array('name'		=> 'test.jpg',
						 'tmp_name' => '12345.jpg',
						 'type'		=> 'image/jpeg');

		$event->setFile('banner', $imgFile);
		$event->upload('Test Event');
		$status = "File successfully uploaded.";

		$this->assertEquals($status, $event->getStatus());

		$imgFile = array('name'		=> 'test.jpg',
						 'tmp_name' => '12345.jpg',
						 'type'		=> 'image/jpeg');
		$event->setFile('eblast', $imgFile);
		$event->upload('Test Event');

		$this->assertEquals($status, $event->getStatus());
	}
}
