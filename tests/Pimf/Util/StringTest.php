<?php
class StringTest extends PHPUnit_Framework_TestCase
{
  protected $testString = '';

  protected function setUp()
  {
    parent::setUp();

    $this->testString = file_get_contents(dirname(__FILE__).'/_fixture/samp-string.html');
  }

  public function testIfStringIsUtf8()
  {
    $res = \Pimf\Util\String::isUTF8($this->testString);

    $this->assertTrue($res);

    $res = \Pimf\Util\String::isUTF8($this->testString, true);

    $this->assertTrue($res);
  }

  public function testCheckUtf8Encoding()
  {
    $res = \Pimf\Util\String::checkUtf8Encoding($this->testString);

    $this->assertTrue($res);
  }

  public function testCleanAggressive()
  {
    $res = \Pimf\Util\String::cleanAggressive($this->testString);

    $this->assertEquals(
      file_get_contents(dirname(__FILE__).'/_fixture/expects-clean-aggressive.html'),
      str_replace(array(' ', PHP_EOL), '', $res)
    );
  }

  public function testCleanSmart()
  {
    $res = \Pimf\Util\String::cleanSmart($this->testString);

    $this->assertEquals(' Mit Hilfe von samp-string.html', $res);
  }

  public function testEnsureTrailing()
  {
    $res = \Pimf\Util\String::ensureTrailing('/', 'http://www.example.com');
    $this->assertStringEndsWith('/', $res);

    $res = \Pimf\Util\String::ensureTrailing('/', 'http://www.example.com/');
    $this->assertStringEndsWith('/', $res);
    $this->assertStringEndsNotWith('//', $res);

    $res = \Pimf\Util\String::ensureTrailing('/', '//uc/receipt/');
    $this->assertStringEndsWith('/', $res);
    $this->assertStringEndsNotWith('//', $res);
  }

  public function testEnsureLeading()
  {
    $res = \Pimf\Util\String::ensureLeading('#', '1#2#3#4#5');
    $this->assertStringStartsWith('#1', $res);

    $res = \Pimf\Util\String::ensureLeading('#', '#1#2#3#4#5');
    $this->assertStringStartsWith('#1', $res);
  }

  public function testDeleteLeading()
  {
    $res = \Pimf\Util\String::deleteLeading('#', '#1#2#3#4#5');
    $this->assertStringStartsWith('1#', $res); // -> 1#2#3#4#5

    $res = \Pimf\Util\String::deleteLeading(array('#', '1'), '##111#2#3#4#5');
    $this->assertStringStartsWith('2#', $res); // -> 2#3#4#5
  }

  public function testDeleteTrailing()
  {
    $res = \Pimf\Util\String::deleteTrailing('|', '|1|2|3|4|5|');
    $this->assertStringEndsWith('|5', $res); // -> |1|2|3|4|5

    $res = \Pimf\Util\String::deleteTrailing(array('|','5'), '|1|2|3|4|5|555');
    $this->assertStringEndsWith('|4', $res); // -> |1|2|3|4
  }

  public static function provideSerializedTestData()
  {
    return array(
      array(serialize(array(21.123, 21.124, 2, 0))),
      array(serialize('some string here')),
      array(serialize((object)array('eee'=>21.123, 'asdfasdf'=>21.124))),
    );
  }

  /**
   * @dataProvider provideSerializedTestData
   */
  public function testIsSerialized($data)
  {
    $this->assertTrue(

      \Pimf\Util\String::isSerialized($data),

      'problem on asserting that '.print_r($data,true). ' is serialized'

    );
  }

  public function testSlagStringFromSpecialChars()
  {
    $this->assertEquals(

      '_1_2_3_These_words_are_quoted',

      \Pimf\Util\String::slagSpecialChars('\"[1,2,3,<>#?==(/%/$§"!]{These,words,are,quoted}\"# "')

    );
  }

  public static function providerOfEvilPaths()
  {
    return array(
      array('http://www.example.com/index.foo?item=../../../Config.sys'),
      array("http://www.example.com/index.foo?item=../../../Windows/System32/cmd.exe?/C+dir+C:\\"),
      array('/foo/bar/../controller.php'),
      array('http://www.example.com/%2e%2e%2f'),
      array('http://www.example.com/%2e%2e%5Ccontroller.php'),
      array('/foo/bar/controller.php?action=../00%'),
      array('http://localhost/?controller=../%00'),
      array('http://localhost/?controller=some bad controller name'),
      array('http://localhost/?controller=some%20bad%20controller%20name'),
    );
  }

  /**
   * @dataProvider providerOfEvilPaths
   */
  public function testIsEvilPathContainsBadCombinations($path)
  {
    $this->assertTrue(\Pimf\Util\String::isEvilPath($path,$path));
  }
}
