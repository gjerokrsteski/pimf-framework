<?php
class CacheApcTest extends \PHPUnit_Framework_TestCase
{
  public function testGetReturnsNullWhenNotFound()
  {
    $apc = $this->getMockBuilder('\\Pimf\\Cache\\Storages\\Apc')
      ->setConstructorArgs(array('some.secret.key'))
      ->setMethods(array('retrieve'))
      ->getMock();

    $apc->expects($this->once())->method('retrieve')->with($this->equalTo('foobar'))->will($this->returnValue(null));

    $this->assertNull($apc->get('foobar'));
  }

  public function testAPCValueIsReturned()
  {
    $apc = $this->getMockBuilder('\\Pimf\\Cache\\Storages\\Apc')
      ->setConstructorArgs(array('some.secret.key'))
      ->setMethods(array('get'))
      ->getMock();

    $apc->expects($this->once())->method('get')->will($this->returnValue('bar'));

    $this->assertEquals('bar', $apc->get('some.secret.key'));
  }

  public function testSetMethodProperlyCallsAPC()
  {
    $apc = $this->getMockBuilder('\\Pimf\\Cache\\Storages\\Apc')
      ->setConstructorArgs(array('some.secret.key'))
      ->setMethods(array('put'))
      ->getMock();

    $apc->expects($this->once())->method('put')->with($this->equalTo('foo'), $this->equalTo('bar'), $this->equalTo(60));

    $apc->put('foo', 'bar', 60);
  }

  public function testStoreItemForeverProperlyCallsAPC()
  {
    $apc = $this->getMockBuilder('\\Pimf\\Cache\\Storages\\Apc')
      ->setConstructorArgs(array('some.secret.key'))
      ->setMethods(array('put'))
      ->getMock();

    $apc->expects($this->once())->method('put')->with($this->equalTo('foo'), $this->equalTo('bar'), $this->equalTo(0));

    $apc->forever('foo', 'bar');
  }

  public function testForgetMethodProperlyCallsAPC()
  {
    $apc = $this->getMockBuilder('\\Pimf\\Cache\\Storages\\Apc')
      ->setConstructorArgs(array('some.secret.key'))
      ->setMethods(array('forget'))
      ->getMock();

    $apc->expects($this->once())->method('forget')->with($this->equalTo('foo'));

    $apc->forget('foo');
  }

}
 