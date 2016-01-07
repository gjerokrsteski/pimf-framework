<?php

class SapiTest extends \PHPUnit_Framework_TestCase
{
    public function testHappyPathAtCliContext()
    {
        $this->assertInternalType('boolean', \Pimf\Sapi::isApache(), 'wow no apache here');
        $this->assertInternalType('boolean', \Pimf\Sapi::isWindows(), 'ohhh no we have windows here');
        $this->assertInternalType('boolean', \Pimf\Sapi::isCgi(), 'no CGI here');
        $this->assertInternalType('boolean', \Pimf\Sapi::isIIS(), 'OK we have no IIS here');
        $this->assertInternalType('boolean', \Pimf\Sapi::isWeb(), 'ohhh no we are at the world wide web here');
        $this->assertInternalType('boolean', \Pimf\Sapi::isBuiltInWebServer(), 'no PHPs built-in server here');
        $this->assertInternalType('boolean', \Pimf\Sapi::isHHVM(), 'no HHVM mashine here');
    }
}
 