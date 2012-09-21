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
    $res = Pimf_Util_String::isUTF8($this->testString);

    $this->assertTrue($res);

    $res = Pimf_Util_String::isUTF8($this->testString, true);

    $this->assertTrue($res);
  }

  public function testCheckUtf8Encoding()
  {
    $res = Pimf_Util_String::checkUtf8Encoding($this->testString);

    $this->assertTrue($res);
  }

  public function testTruncatePreservingTags()
  {
    $res = Pimf_Util_String::truncatePreservingTags(
      $this->testString, 100, ' ...'
    );

    $this->assertEquals(
      file_get_contents(dirname(__FILE__).'/_fixture/expected-after-truncate-preserving-tags.html'),
      $res
    );
  }

  public function testCleanAggressive()
  {
    $res = Pimf_Util_String::cleanAggressive($this->testString);

    $this->assertEquals(
      file_get_contents(dirname(__FILE__).'/_fixture/expects-clean-aggressive.html'),
      str_replace(array(' ', PHP_EOL), '', $res)
    );
  }

  public function testCleanSmart()
  {
    $res = Pimf_Util_String::cleanSmart($this->testString);

    $this->assertEquals(' Mit Hilfe von samp-string.html', $res);
  }

  public function testEnsureTrailing()
  {
    $res = Pimf_Util_String::ensureTrailing('/', 'http://www.example.com');
    $this->assertStringEndsWith('/', $res);

    $res = Pimf_Util_String::ensureTrailing('/', 'http://www.example.com/');
    $this->assertStringEndsWith('/', $res);
    $this->assertStringEndsNotWith('//', $res);

    $res = Pimf_Util_String::ensureTrailing('/', '//uc/receipt/');
    $this->assertStringEndsWith('/', $res);
    $this->assertStringEndsNotWith('//', $res);
  }

  public function testEnsureLeading()
  {
    $res = Pimf_Util_String::ensureLeading('#', '1#2#3#4#5');
    $this->assertStringStartsWith('#1', $res);

    $res = Pimf_Util_String::ensureLeading('#', '#1#2#3#4#5');
    $this->assertStringStartsWith('#1', $res);
  }

  public function testDeleteLeading()
  {
    $res = Pimf_Util_String::deleteLeading('#', '#1#2#3#4#5');
    $this->assertStringStartsWith('1#', $res); // -> 1#2#3#4#5

    $res = Pimf_Util_String::deleteLeading(array('#', '1'), '##111#2#3#4#5');
    $this->assertStringStartsWith('2#', $res); // -> 2#3#4#5
  }

  public function testDeleteTrailing()
  {
    $res = Pimf_Util_String::deleteTrailing('|', '|1|2|3|4|5|');
    $this->assertStringEndsWith('|5', $res); // -> |1|2|3|4|5

    $res = Pimf_Util_String::deleteTrailing(array('|','5'), '|1|2|3|4|5|555');
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

      Pimf_Util_String::isSerialized($data),

      'problem on asserting that '.print_r($data,true). ' is serialized'

    );
  }

  public function testSlagStringFromSpecialChars()
  {
    $this->assertEquals(

      '_1_2_3_These_words_are_quoted',

      Pimf_Util_String::slagSpecialChars('\"[1,2,3,<>#?==(/%/$§"!]{These,words,are,quoted}\"# "')

    );
  }
}
