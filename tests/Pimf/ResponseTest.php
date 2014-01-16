<?php
class ResponseTest extends PHPUnit_Framework_TestCase
{

  protected function setUp()
  {
    parent::setUp();
    ob_start(); // <-- very important!
  }

  protected function tearDown()
  {
    header_remove(); // <-- VERY important.
    parent::tearDown();
  }


  # start testing


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
   * @expectedException RuntimeException
   */
  public function testBombingExceptionIfMultipleTypesUsed()
  {
    $response =new \Pimf\Response('POST');

    $response->asHTML()->asMSWord();
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
 