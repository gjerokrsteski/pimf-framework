<?php
class UtilFileTest extends PHPUnit_Framework_TestCase
{
  public function testCreatingNewInstance()
  {
    new \Pimf\Util\File(__FILE__);
  }

  /**
   * @expectedException \OutOfRangeException
   */
  public function testIfFileNotAtSystem()
  {
    new \Pimf\Util\File('//path/to/bad/file/');
  }

  public function testExtension()
  {
    $file = new \Pimf\Util\File(__FILE__);

    $this->assertEquals('php', $file->getExtension());
  }

  /**
   * @expectedException \RuntimeException
   */
  public function testIfRenamingIntoSameName()
  {
    $file = new \Pimf\Util\File(__FILE__);

    $file->move(dirname(__FILE__), $file->getFilename());
  }
}
 