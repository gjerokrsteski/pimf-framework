<?php
class CacheWincacheTest extends \PHPUnit_Framework_TestCase
{
  public function testGetReturnsNullWhenNotFound()
  {
    $wincache = $this->getMockBuilder('\\Pimf\\Cache\\Storages\\Wincache')
      ->setConstructorArgs(array('some.secret.key'))
      ->setMethods(array('retrieve'))
      ->getMock();

    $wincache->expects($this->once())->method('retrieve')->with($this->equalTo('foobar'))->will($this->returnValue(null));

    $this->assertNull($wincache->get('foobar'));
  }

  public function testfValueIsReturned()
  {
    $wincache = $this->getMockBuilder('\\Pimf\\Cache\\Storages\\Wincache')
      ->setConstructorArgs(array('some.secret.key'))
      ->setMethods(array('get'))
      ->getMock();

    $wincache->expects($this->once())->method('get')->will($this->returnValue('bar'));

    $this->assertEquals('bar', $wincache->get('some.secret.key'));
  }

  public function testSetMethodProperlyCalls()
  {
    $wincache = $this->getMockBuilder('\\Pimf\\Cache\\Storages\\Wincache')
      ->setConstructorArgs(array('some.secret.key'))
      ->setMethods(array('put'))
      ->getMock();

    $wincache->expects($this->once())->method('put')->with($this->equalTo('foo'), $this->equalTo('bar'), $this->equalTo(60));

    $wincache->put('foo', 'bar', 60);
  }

  public function testStoreItemForeverProperlyCalls()
  {
    $wincache = $this->getMockBuilder('\\Pimf\\Cache\\Storages\\Wincache')
      ->setConstructorArgs(array('some.secret.key'))
      ->setMethods(array('put'))
      ->getMock();

    $wincache->expects($this->once())->method('put')->with($this->equalTo('foo'), $this->equalTo('bar'), $this->equalTo(0));

    $wincache->forever('foo', 'bar');
  }

  public function testForgetMethodProperlyCalls()
  {
    $wincache = $this->getMockBuilder('\\Pimf\\Cache\\Storages\\Wincache')
      ->setConstructorArgs(array('some.secret.key'))
      ->setMethods(array('forget'))
      ->getMock();

    $wincache->expects($this->once())->method('forget')->with($this->equalTo('foo'));

    $wincache->forget('foo');
  }

}
 