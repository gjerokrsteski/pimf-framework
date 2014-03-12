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
  }
}
 