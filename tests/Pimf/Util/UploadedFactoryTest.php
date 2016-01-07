<?php

class UtilUploadedFactoryTest extends \PHPUnit_Framework_TestCase
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

    public function testIfErrorOnFile()
    {
        $file['tmp_name'] = __FILE__;
        $file['name'] = 'new-uploaded-file-name.txt';
        $file['type'] = 'application/octet-stream';
        $file['size'] = 0;
        $file['error'] = UPLOAD_ERR_NO_FILE;

        $uploaded = \Pimf\Util\Uploaded\Factory::get($file, true);

        $this->assertNull($uploaded);
    }

    public function testIfErrorOnFileName()
    {
        $file['tmp_name'] = __FILE__;
        $file['name'] = null;
        $file['type'] = 'application/octet-stream';
        $file['size'] = 0;
        $file['error'] = 0;

        $uploaded = \Pimf\Util\Uploaded\Factory::get($file, true);

        $this->assertNull($uploaded);
    }

    public function testWhenUploadingMultipleFiles()
    {
        $multiple_files = Array(
            'name'     => Array(
                0 => 'new-uploaded-file-name.txt',
                1 => 'new-uploaded-file-name.txt',
            ),
            'type'     => Array(
                0 => 'application/octet-stream',
                1 => 'application/octet-stream',
            ),
            'tmp_name' => Array(
                0 => __FILE__,
                1 => __FILE__,
            ),
            'error'    => Array(
                0 => 0,
                1 => 0,
            ),
            'size'     => Array(
                0 => 123,
                1 => 456,
            ),
        );

        $healed = \Pimf\Util\Uploaded\Factory::heal($multiple_files);

        foreach ($healed as $file) {

            $this->assertInstanceOf(

                '\\Pimf\\Util\\Uploaded',

                \Pimf\Util\Uploaded\Factory::get($file, true)

            );
        }
    }

    public function testIfDataForHealingIsNotArray()
    {
        $this->assertEquals('123', \Pimf\Util\Uploaded\Factory::heal('123'));
    }

}
 