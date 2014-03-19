<?php
class UtilFileTest extends PHPUnit_Framework_TestCase
{
  public function testCreatingNewInstance()
  {
    new \Pimf\Util\File(__FILE__, false);
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

  public function testToMove()
  {

    $dest = dirname(__FILE__) . DS .'_fixture' . DS;

    @touch($dest.'fixture.for.util.file.testing');

    $file = new \Pimf\Util\File($dest.'fixture.for.util.file.testing');

    $this->assertInstanceOf(

      '\\Pimf\\Util\\File',

      $file->move($dest, $dest .'fixture.for.util.file.testing.COPY')

    );

    @unlink($dest .'fixture.for.util.file.testing.COPY');
  }

  /**
   * @expectedException \RuntimeException
   */
  public function testIfNoDirectory()
  {
    $file = new \Pimf\Util\File(null, false);

    $file->move('//path/to/bad/directory/');
  }
}
 