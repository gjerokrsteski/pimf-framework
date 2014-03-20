<?php

class UtilHeaderTest extends PHPUnit_Framework_TestCase
{
  public function setUp()
  {
    parent::setUp();

    $server['SERVER_PROTOCOL'] = 'HTTP/1.1';
    \Pimf\Registry::set('env', new \Pimf\Environment($server));
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

    $this->expectOutputString('page not found
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Error 404 - Not Found</title>
</head>
<body>

<h1>I think we\'re lost.</h1>

<h2>Server Error: 404 (Not Found)</h2>

<hr>

<h3>What does this mean?</h3>

<p>
  We couldn\'t find the page you requested on our servers. We\'re really sorry
  about that. It\'s our fault, not yours. We\'ll work hard to get this page
  back online as soon as possible.
</p>

<p>
  Perhaps you would like to go to our <a href="/">/</a>
</p>

</body>
</html>
');
  }

    /**
     * @runInSeparateProcess
     * @outputBuffering enabled
     */
    public function testSendInternalServerError()
    {
      Pimf\Util\Header::sendInternalServerError('internal server error', false);

      $this->expectOutputString('internal server error
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Error 500 - Internal Server Error</title>
</head>
<body>

<h1>Whoops!</h1>

<h2>Server Error: 500 (Internal Server Error)</h2>

<hr>

<h3>What does this mean?</h3>

<p>
  Something went wrong on our servers while we were processing your request.
  We\'re really sorry about this, and will work hard to get this resolved as
  soon as possible.
</p>

<p>
  Perhaps you would like to go to our <a href="/">/</a>
</p>

</body>
</html>
');
  }

}
 