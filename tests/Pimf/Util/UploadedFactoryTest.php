<?php
class UtilUploadedFactoryTest extends PHPUnit_Framework_TestCase
{
  public function testFactorizing()
  {
    $file['tmp_name'] = __FILE__;
    $file['name'] = 'new-uploaded-file-name.txt';
    $file['type'] = 'application/octet-stream';
    $file['size'] = 0;
    $file['error'] = 0;

    $uploaded = \Pimf\Util\Uploaded\Factory::get($file, true);

    $this->assertInstanceOf('\\Pimf\\Util\\Uploaded', $uploaded);
    $this->assertEquals('new-uploaded-file-name.txt', $uploaded->getClientOriginalName());
    $this->assertEquals('application/octet-stream', $uploaded->getClientMimeType());
    $this->assertEquals(0, $uploaded->getError());
    $this->assertTrue($uploaded->isValid());
    $this->assertTrue($uploaded->getMaxFilesize() > 1);
  }
}
 