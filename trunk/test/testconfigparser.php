<?php


class ConfigParserTest extends PHPUnit_Framework_TestCase {
	
	protected function setUp() {
		include_once 'configparser.php';
		/* prepare ini file */
		$filename = "test.ini";
		$mode = "w";
		if (file_exists($filename)) {
			unlink($filename);
		}
		$fp = fopen($filename, $mode);
		$str = "
		[section]
		option1=1
		option2=ahoj
		option3=True
		
		[section_B]
		option1=2.0
		option2=False
		option3=ahoj,cau,zdar
		
		";
		fwrite($fp, $str);
		fclose($fp);
		$this->parser = new Publisher\ConfigParser\ConfigParser($filename);
		unlink($filename);
	}
	
	public function testParseInteger() {
		$this->assertSame(1, $this->parser->getInt("section", "option1"));
	}
	
	public function testParserString() {
		$this->assertSame("ahoj", $this->parser->get("section", "option2"));
	}
	
	public function testParserBooleanTrue() {
		$this->assertTrue($this->parser->getBool("section", "option3"));
	}

	public function testParserBooleanFalse() {
		$this->assertFalse($this->parser->getBool("section_B", "option2"));
	}

	public function testParseFloat() {
		$this->assertSame(2.0, $this->parser->getFloat("section_B", "option1"));
	}

	/**
	 * @expectedException Publisher\ConfigParser\NoSectionError
	 */
	public function testNoSectionThrowException() {
		$this->parser->getInt("section_A", "option1");
	}

	/**
	 * @expectedException Publisher\ConfigParser\NoOptionError
	 */
	public function testNoOptionThrowException() {
		$this->parser->getInt("section", "option4");
	}
}


