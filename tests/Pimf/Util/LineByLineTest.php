<?php
class LineByLineTest extends PHPUnit_Framework_TestCase
{
  public function testCreatingNewInstance()
  {
    new \Pimf\Util\LineByLine(function(){});
  }

  /**
   * @expectedException RuntimeException
   */
  public function testIfNoCallableOnCreatingNewInstance()
  {
    new \Pimf\Util\LineByLine('no callable');
  }

  public function testLoadingFile()
  {
    $linebyline = new \Pimf\Util\LineByLine(

      function ($line) { return str_replace(PHP_EOL, '', $line) . 'rocks'; }

    );

    $feedback = $linebyline->read(dirname(__FILE__) . '/_fixture/line-by-line.txt', true);

    $this->assertNotEmpty($feedback);
    $this->assertInternalType('array', $feedback);
    $this->assertEquals(3, count($feedback));
    $this->assertEquals('rambo1rocks', $feedback[0]);
    $this->assertEquals('rambo2rocks', $feedback[1]);
    $this->assertEquals('rambo3rocks', $feedback[2]);
  }
}