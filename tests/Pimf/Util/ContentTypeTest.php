<?php

class UtilContentTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @runInSeparateProcess
     */
    public function testAsPdf()
    {
        Pimf\Util\Header\ContentType::asPDF();

        $this->assertContains('Content-Type: application/pdf', xdebug_get_headers());
    }

    /**
     * @runInSeparateProcess
     */
    public function testAsZip()
    {
        Pimf\Util\Header\ContentType::asZIP();

        $this->assertContains('Content-Type: application/zip', xdebug_get_headers());
    }

    /**
     * @runInSeparateProcess
     */
    public function testAsXZip()
    {
        Pimf\Util\Header\ContentType::asXZIP();

        $this->assertContains('Content-Type: application/x-zip', xdebug_get_headers());
    }

    /**
     * @runInSeparateProcess
     */
    public function testAsMsWord()
    {
        Pimf\Util\Header\ContentType::asMSWord();

        $this->assertContains('Content-Type: application/msword', xdebug_get_headers());
    }


    /**
     * @runInSeparateProcess
     */
    public function testAsOctetSream()
    {
        Pimf\Util\Header\ContentType::asOctetStream();

        $this->assertContains('Content-Type: application/octet-stream', xdebug_get_headers());
    }

    /**
     * @runInSeparateProcess
     */
    public function testAsJson()
    {
        Pimf\Util\Header\ContentType::asJSON();

        $this->assertContains('Content-Type: application/json; charset=utf-8', xdebug_get_headers());
    }
}
 