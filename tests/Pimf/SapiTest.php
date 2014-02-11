<?php
class SapiTest extends PHPUnit_Framework_TestCase
{
  public function testHappyPathAtCliContext()
  {
    $this->assertInternalType('boolean', \Pimf\Sapi::isApache(), 'wow no apache here');
    $this->assertInternalType('boolean', \Pimf\Sapi::isWindows(), 'ohhh no we have windows here');
    $this->assertInternalType('boolean', \Pimf\Sapi::isCgi(), 'no cgi here');
    $this->assertInternalType('boolean', \Pimf\Sapi::isIIS(), 'ohhh no we have iis here');
    $this->assertInternalType('boolean', \Pimf\Sapi::isWeb(), 'ohhh no we are at the world wide web here');
  }
}
 