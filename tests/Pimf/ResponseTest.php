<?php
class ResponseTest extends PHPUnit_Framework_TestCase
{
  public function testCreatingNewInstance()
  {
    $response = new \Pimf\Response('POST');

    $this->assertEquals('POST', $response->getMethod());
  }

  /**
   * @expectedException RuntimeException
   */
  public function testCreatingNewInstanceExpectingExceptionIfNoRequestMethodGiven()
  {
    new \Pimf\Response('PUT');
  }

  public function testCreatingNewInstanceExpectingNoExceptionIfComesFromCli()
  {
    $this->assertInstanceOf('Pimf\\Response', new \Pimf\Response(null));
    $this->assertInstanceOf('Pimf\\Response', new \Pimf\Response(NULL));
  }

  /**
   * @runInSeparateProcess
   * @expectedException RuntimeException
   */
  public function testBombingExceptionIfMultipleTypesUsed()
  {
    $response = new \Pimf\Response('POST');
    $response->asHTML()->asMSWord();
  }

  /**
   * @runInSeparateProcess
   * @outputBuffering enabled
   */
  public function testSendingJsonData()
  {
    $response = new \Pimf\Response('POST');
    $response->asJSON()->send(array('hello'=>'Barry'), false);

    $this->expectOutputString('{"hello":"Barry"}');
  }

  /**
   * @runInSeparateProcess
   * @outputBuffering enabled
   */
  public function testSendingTextData()
  {
    $response = new \Pimf\Response('POST');
    $response->asTEXT()->send('hello Barry!', false);

    $this->expectOutputString('hello Barry!');
  }

  /**
   * @runInSeparateProcess
   * @outputBuffering enabled
   */
  public function testSendingXmlData()
  {
    $response = new \Pimf\Response('GET');
    $response->asTEXT()->send('<hello>Barry!</hello>', false);

    $this->expectOutputString('<hello>Barry!</hello>');
  }

  /**
   * @runInSeparateProcess
   * @expectedException RuntimeException
   */
  public function testBombingExceptionIfMultipleCachesSent()
  {
    $response = new \Pimf\Response('GET');
    $response->cacheBrowser(1)->cacheNone();
  }

  /**
   * @runInSeparateProcess
   * @expectedException RuntimeException
   */
  public function testBombingExceptionIfNo_GET_RequestSent()
  {
    $response = new \Pimf\Response('POST');
    $response->cacheBrowser(1);
  }

  /**
   * @runInSeparateProcess
   * @outputBuffering enabled
   */
  public function testSendingCachedTextData()
  {
    $response = new \Pimf\Response('GET');
    $response->asTEXT()->cacheBrowser(60)->send('Barry is cached at the browser', false);

    $this->expectOutputString('Barry is cached at the browser');
  }

  /**
   * @runInSeparateProcess
   */
  public function testSendPdfFile()
  {
    $server['USER_AGENT'] = 'Chrome/24.0.1312.57';
    $env = new \Pimf\Environment($server);
    \Pimf\Registry::set('env', $env);

    # start testing

    $response = new \Pimf\Response('GET');
    $response->asPDF()->sendStream(new \SplTempFileObject(-1), 'fake.pdf', false);
  }

  /**
   * @runInSeparateProcess
   */
  public function testSendCsvFile()
  {
    $server['USER_AGENT'] = 'Chrome/24.0.1312.57';
    $env = new \Pimf\Environment($server);
    \Pimf\Registry::set('env', $env);

    # start testing

    $response = new \Pimf\Response('GET');
    $response->asCSV()->sendStream(new \SplTempFileObject(-1), 'fake.csv', false);
  }

  /**
   * @runInSeparateProcess
   */
  public function testSendZipFile()
  {
    $server['USER_AGENT'] = 'MSIE 5.5 blahh blahhh';
    $env = new \Pimf\Environment($server);
    \Pimf\Registry::set('env', $env);

    # start testing

    $response = new \Pimf\Response('GET');
    $response->asZIP()->sendStream(new \SplTempFileObject(-1), 'fake.zip', false);
  }

  /**
   * @runInSeparateProcess
   */
  public function testSendXZipFile()
  {
    $server['USER_AGENT'] = 'Chrome/24.0.1312.57';
    $env = new \Pimf\Environment($server);
    \Pimf\Registry::set('env', $env);

    # start testing

    $response = new \Pimf\Response('GET');
    $response->asXZIP()->sendStream(new \SplTempFileObject(-1), 'fake.zip', false);
  }

  /**
   * @runInSeparateProcess
   */
  public function testSendMswordFile()
  {
    $server['USER_AGENT'] = 'Chrome/24.0.1312.57';
    $env = new \Pimf\Environment($server);
    \Pimf\Registry::set('env', $env);

    # start testing

    $response = new \Pimf\Response('GET');
    $response->asMSWord()->sendStream(new \SplTempFileObject(-1), 'fake.doc', false);
  }
}
 