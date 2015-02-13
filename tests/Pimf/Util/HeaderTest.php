<?php

class UtilHeaderTest extends PHPUnit_Framework_TestCase
{

  private static $env;

  public function setUp()
  {
    parent::setUp();
    $server['SERVER_PROTOCOL'] = 'HTTP/1.1';
    self::$env                 = new \Pimf\Environment($server);
  }


  # start testing
  /**
   * @runInSeparateProcess
   */
  public function testCacheNone()
  {
    Pimf\Util\Header::cacheNone();
    $headers = xdebug_get_headers();
    $this->assertContains('Expires: 0', $headers);
    $this->assertContains('Pragma: no-cache', $headers);
    $this->assertContains('Cache-Control: no-cache,no-store,max-age=0,s-maxage=0,must-revalidate', $headers);
  }

  /**
   * @runInSeparateProcess
   */
  public function testCacheNoValidate()
  {
    Pimf\Util\Header::cacheNoValidate(60);
    $headers = xdebug_get_headers();
    $this->assertContains('Cache-Control: public,max-age=60', $headers);
  }

  /**
   * @runInSeparateProcess
   */
  public function testNotModified()
  {
    Pimf\Util\Header::sendNotModified();
  }

  /**
   * @runInSeparateProcess
   * @outputBuffering enabled
   */
  public function testSendNotFound()
  {
    Pimf\Util\Header::sendNotFound('page not found', false);

    $this->stringContains('Server Error: 404 (Not Found)');
  }

  /**
   * @runInSeparateProcess
   * @outputBuffering enabled
   */
  public function testSendInternalServerError()
  {
    Pimf\Util\Header::sendInternalServerError('internal server error', false);

    $this->stringContains('Server Error: 500 (Internal Server Error)');
  }
}
 