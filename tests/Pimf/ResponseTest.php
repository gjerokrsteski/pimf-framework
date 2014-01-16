<?php
class ResponseTest extends PHPUnit_Framework_TestCase
{
  public function testCreatingNewInstance()
  {
    new \Pimf\Response('POST');
  }

  /**
   * @expectedException \RuntimeException
   */
  public function testCreatingNewInstanceExpectingExceptionIfNoRequestMethodGiven()
  {
    new \Pimf\Response(null);
  }

  /**
   * @expectedException \LogicException
   */
  public function testBombingExceptionIfMultipleTypesUsed()
  {
    $response =new \Pimf\Response('POST');

    $response->asHTML()->asJSON();
  }

  /**
   * @outputBuffering enabled
   */
  public function testSendingJsonData()
  {
    $response = new \Pimf\Response('POST');
    $response->asJSON()->send(array('hello'=>'Barry'), false);

    $this->expectOutputString('{"hello":"Barry"}');
  }

  /**
   * @outputBuffering enabled
   */
  public function testSendingTextData()
  {
    $response = new \Pimf\Response('POST');
    $response->asTEXT()->send('hello Barry!', false);

    $this->expectOutputString('hello Barry!');
  }

  /**
   * @outputBuffering enabled
   */
  public function testSendingXmlData()
  {
    $response = new \Pimf\Response('GET');
    $response->asTEXT()->send('<hello>Barry!</hello>', false);

    $this->expectOutputString('<hello>Barry!</hello>');
  }
}
 