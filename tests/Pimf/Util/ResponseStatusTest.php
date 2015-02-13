<?php

class UtilResponseStatusTest extends \PHPUnit_Framework_TestCase
{
  private static $env;

  public function setUp()
  {
    parent::setUp();

    $server['SERVER_PROTOCOL'] = 'HTTP/1.0';
    self::$env = new \Pimf\Environment($server);
  }


  # start testing


  /**
   * @runInSeparateProcess
   */
  public function testSendXFrameDeny()
  {
    Pimf\Util\Header\ResponseStatus::sendXFrameDeny();

    $this->assertContains('X-Frame-Options: DENY', xdebug_get_headers());
  }

  /**
   * @runInSeparateProcess
   */
  public function testSendXFrameSameOrigin()
  {
    Pimf\Util\Header\ResponseStatus::sendXFrameSameOrigin();

    $this->assertContains('X-Frame-Options: SAMEORIGIN', xdebug_get_headers());
  }

  /**
   * @runInSeparateProcess
   */
  public function testSendContinue()
  {
    Pimf\Util\Header\ResponseStatus::sendContinue();
  }

  /**
   * @runInSeparateProcess
   */
  public function testSendProcessing()
  {
    Pimf\Util\Header\ResponseStatus::sendProcessing();
  }

  /**
   * @runInSeparateProcess
   */
  public function testSendOK()
  {
    Pimf\Util\Header\ResponseStatus::sendOK();
  }

  /**
   * @runInSeparateProcess
   */
  public function testSendCreated()
  {
    Pimf\Util\Header\ResponseStatus::sendCreated();
  }

  /**
   * @runInSeparateProcess
   */
  public function testSendAccepted()
  {
    Pimf\Util\Header\ResponseStatus::sendAccepted();
  }

  /**
   * @runInSeparateProcess
   */
  public function testSendNoAuthInfo()
  {
    Pimf\Util\Header\ResponseStatus::sendNoAuthInfo();
  }

  /**
   * @runInSeparateProcess
   */
  public function testSendNoContent()
  {
    Pimf\Util\Header\ResponseStatus::sendNoContent();
  }

  /**
   * @runInSeparateProcess
   */
  public function testSendMovedPermanently()
  {
    Pimf\Util\Header\ResponseStatus::sendMovedPermanently();
  }

  /**
   * @runInSeparateProcess
   */
  public function testSendFound()
  {
    Pimf\Util\Header\ResponseStatus::sendFound();
  }

  public function sendNotModified()
  {
    Pimf\Util\Header\ResponseStatus::sendNotModified();
  }

  /**
   * @runInSeparateProcess
   */
  public function testSendTemporaryRedirect()
  {
    Pimf\Util\Header\ResponseStatus::sendTemporaryRedirect();
  }

  /**
   * @runInSeparateProcess
   */
  public function testSendBadRequest()
  {
    Pimf\Util\Header\ResponseStatus::sendBadRequest();
  }

  /**
   * @runInSeparateProcess
   */
  public function testSendUnauthorized()
  {
    Pimf\Util\Header\ResponseStatus::sendUnauthorized();
  }

  /**
   * @runInSeparateProcess
   */
  public function testSendPaymentRequired()
  {
    Pimf\Util\Header\ResponseStatus::sendPaymentRequired();
  }

  /**
   * @runInSeparateProcess
   */
  public function testSendForbidden()
  {
    Pimf\Util\Header\ResponseStatus::sendForbidden();
  }

  /**
   * @runInSeparateProcess
   */
  public function testSendMethodNotAllowed()
  {
    Pimf\Util\Header\ResponseStatus::sendMethodNotAllowed();
  }

  /**
   * @runInSeparateProcess
   */
  public function testSendNotAcceptable()
  {
    Pimf\Util\Header\ResponseStatus::sendNotAcceptable();
  }

  /**
   * @runInSeparateProcess
   */
  public function testSendProxyAuthRequired()
  {
    Pimf\Util\Header\ResponseStatus::sendProxyAuthRequired();
  }

  /**
   * @runInSeparateProcess
   */
  public function testSendRequestTimeout()
  {
    Pimf\Util\Header\ResponseStatus::sendRequestTimeout();
  }

  /**
   * @runInSeparateProcess
   */
  public function testSendUnsupportedMediaType()
  {
    Pimf\Util\Header\ResponseStatus::sendUnsupportedMediaType();
  }

  /**
   * @runInSeparateProcess
   */
  public function testSendLocked()
  {
    Pimf\Util\Header\ResponseStatus::sendLocked();
  }

  /**
   * @runInSeparateProcess
   */
  public function testSendServiceUnavailable()
  {
    Pimf\Util\Header\ResponseStatus::sendServiceUnavailable();
  }
}
 