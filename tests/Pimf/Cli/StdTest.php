<?php

class CliStdTest extends PHPUnit_Framework_TestCase
{
  public function testCreatingNewInstance()
  {
    new \Pimf\Cli\Std('php://memory');
  }

  public function testValidatingInput()
  {
    $std = new \Pimf\Cli\Std('php://memory');

    // no std-in here... sorry!

    $this->assertFalse($std->value());
    $this->assertTrue($std->valid('(mysql|sqlite)', 'mysql'));
    $this->assertFalse($std->valid('(mysql|sqlite)', 'non-matching'));
  }

  /**
   * @runInSeparateProcess
   * @outputBuffering enabled
   */
  public function testReadingAndPositiveValidation()
  {
    $std = $this->getMockBuilder('\Pimf\Cli\Std')
      ->enableOriginalConstructor()
      ->setConstructorArgs(array('php://memory'))
      ->setMethods(array('value'))
      ->getMock();

    $std->expects($this->any())
                 ->method('value')
                 ->will($this->returnValue('sqlite'));


    $this->assertEquals(

      'sqlite',

      $std->read('Which PDO driver do you desire?', '(mysql|sqlite)')

    );
  }
}
 