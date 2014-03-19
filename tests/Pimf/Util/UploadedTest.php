<?php
class UtilUploadedTest extends PHPUnit_Framework_TestCase
{
  public function testCreatingNewInstance()
  {
    new \Pimf\Util\Uploaded(__FILE__, 'new-uploaded-file-name.txt');
  }

  public function testLoadingFileInfo()
  {
    $uploaded = new \Pimf\Util\Uploaded(__FILE__, 'new-uploaded-file-name.txt');

    $this->assertEquals('new-uploaded-file-name.txt', $uploaded->getClientOriginalName());
    $this->assertEquals('application/octet-stream', $uploaded->getClientMimeType());
    $this->assertEquals('UploadedTest.php', $uploaded->getFilename());
    $this->assertEquals(0, $uploaded->getError());
    $this->assertTrue($uploaded->isValid());
    $this->assertTrue($uploaded->getMaxFilesize() > 1);
    $this->assertFalse($uploaded->getClientSize() > 1);
  }

  public function testMovingFile()
  {
    $dest = dirname(__FILE__) . DS . '_fixture' . DS;
    @touch($dest . 'fixture.for.util.file.testing');

    $uploaded = new \Pimf\Util\Uploaded($dest . 'fixture.for.util.file.testing', 'fixture.for.util.file.testing',
      $mime = null, $size = null, $error = null, $test = true);

    $this->assertInstanceOf(

      '\Pimf\Util\File',
      $uploaded->move($dest, 'fixture.for.util.file.testing.COPY')

    );

    @unlink($dest . 'fixture.for.util.file.testing.COPY');

  }
}
 