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
    new \Pimf\Response(null);
  }

  /**
   * @runInSeparateProcess
   * @expectedException RuntimeException
   */
  public function testBombingExceptionIfMultipleTypesUsed()
  {
    //$this->markTestSkipped('need time to fix test case');

    $response = new \Pimf\Response('POST');
    $response->asHTML()->asMSWord();
  }

  /**
   * @runInSeparateProcess
   * @outputBuffering enabled
   */
  public function testSendingJsonData()
  {
    //$this->markTestSkipped('need time to fix test case');

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
    //$this->markTestSkipped('need time to fix test case');

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
    //$this->markTestSkipped('need time to fix test case');

    $response = new \Pimf\Response('GET');
    $response->asTEXT()->send('<hello>Barry!</hello>', false);

    $this->expectOutputString('<hello>Barry!</hello>');
  }
}
 