<?php
class CacheFileTest extends PHPUnit_Framework_TestCase
{
  public function testGetReturnsNullWhenNotFound()
  {
    $file = $this->getMockBuilder('\\Pimf\\Cache\\Storages\\File')
      ->setConstructorArgs(array('/path/to/nirvana/'))
      ->setMethods(array('retrieve'))
      ->getMock();

    $file->expects($this->once())->method('retrieve')->with($this->equalTo('foobar'))->will($this->returnValue(null));

    $this->assertNull($file->get('foobar'));
  }

  public function testfValueIsReturned()
  {
    $file = $this->getMockBuilder('\\Pimf\\Cache\\Storages\\File')
      ->setConstructorArgs(array('/path/to/nirvana/'))
      ->setMethods(array('get'))
      ->getMock();

    $file->expects($this->once())->method('get')->will($this->returnValue('bar'));

    $this->assertEquals('bar', $file->get('/path/to/nirvana/'));
  }

  public function testSetMethodProperlyCalls()
  {
    $file = $this->getMockBuilder('\\Pimf\\Cache\\Storages\\File')
      ->setConstructorArgs(array('/path/to/nirvana/'))
      ->setMethods(array('put'))
      ->getMock();

    $file->expects($this->once())->method('put')->with($this->equalTo('foo'), $this->equalTo('bar'), $this->equalTo(60));

    $file->put('foo', 'bar', 60);
  }

  public function testStoreItemForeverProperlyCalls()
  {
    $file = $this->getMockBuilder('\\Pimf\\Cache\\Storages\\File')
      ->setConstructorArgs(array('/path/to/nirvana/'))
      ->setMethods(array('put'))
      ->getMock();

    $file->expects($this->once())->method('put')->with($this->equalTo('foo'), $this->equalTo('bar'));

    $file->forever('foo', 'bar');
  }

  public function testForgetMethodProperlyCalls()
  {
    $file = $this->getMockBuilder('\\Pimf\\Cache\\Storages\\File')
      ->setConstructorArgs(array('/path/to/nirvana/'))
      ->setMethods(array('forget'))
      ->getMock();

    $file->expects($this->once())->method('forget')->with($this->equalTo('foo'));

    $file->forget('foo');
  }

}
 