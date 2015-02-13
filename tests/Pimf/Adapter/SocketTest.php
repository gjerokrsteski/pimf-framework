<?php


class SocketTest extends \PHPUnit_Framework_TestCase
{
  public function testCreatingNewInstance()
  {
    new \Pimf\Adapter\Socket('', 0);
  }

  /**
   * @expectedException \PHPUnit_Framework_Error_Warning
   */
  public function testIfCanNotConnect()
  {
    $socket = new \Pimf\Adapter\Socket('125.125.12.5.0', 13);

    $socket->open();
  }

  public function testIfCanConnect()
  {
    $socket = new \Pimf\Adapter\Socket('127.0.0.1', 80);

    $this->assertInternalType('resource', $res = $socket->open());

    fclose($res);
  }
}
 