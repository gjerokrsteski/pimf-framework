<?php
class LineByLineTest extends PHPUnit_Framework_TestCase
{
  public function testCreatingNewInstance()
  {
    new Pimf_Util_LineByLine(function(){});
  }

  /**
   * @expectedException RuntimeException
   */
  public function testIfNoCallableOnCreatingNewInstance()
  {
    new Pimf_Util_LineByLine('no callable');
  }

  public function testLoadingFile()
  {
    $linebyline = new Pimf_Util_LineByLine(function ($line) {
      return $line . 'rocks';
    });

    $feedback = $linebyline->read(dirname(__FILE__) . '/_fixture/line-by-line.txt', true);

    $this->assertNotEmpty($feedback);
    $this->assertInternalType('array', $feedback);
    $this->assertEquals(3, count($feedback));
    $this->assertEquals('rambo1rocks', $feedback[0]);
    $this->assertEquals('rambo2rocks', $feedback[0]);
    $this->assertEquals('rambo3rocks', $feedback[0]);
  }
}