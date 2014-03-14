<?php

class CliStdTest extends PHPUnit_Framework_TestCase
{
  public function testCreatingNewInstance()
  {
    new \Pimf\Cli\Std('php://memory');
  }

  public function testReading()
  {
    $std = new \Pimf\Cli\Std('php://memory');

    // no std-in here... sorry!

    $this->assertFalse($std->value());
    $this->assertTrue($std->valid('(mysql|sqlite)', 'mysql'));
    $this->assertFalse($std->valid('(mysql|sqlite)', 'non-matching'));
  }
}
 