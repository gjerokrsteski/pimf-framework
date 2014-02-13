<?php
class ResponseTest extends PHPUnit_Framework_TestCase
{
  public function testCreatingNewInstance()
  {
    new \Pimf\Response('POST');
  }

  /**
   * @expectedException RuntimeException
   */
  public function testCreatingNewInstanceExpectingExceptionIfNoRequestMethodGiven()
  {
    new \Pimf\Response('PUT');
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
}
 