<?php

class CacheDbaTest extends \PHPUnit_Framework_TestCase
{
    public function testGetReturnsNullWhenNotFound()
    {
        $dba = $this->getMockBuilder('\\Pimf\\Cache\\Storages\\Dba')
            ->disableOriginalConstructor()
            ->setMethods(array('retrieve'))
            ->getMock();

        $dba->expects($this->once())->method('retrieve')->with($this->equalTo('foobar'))->will($this->returnValue(null));

        $this->assertNull($dba->get('foobar'));
    }

    public function testfValueIsReturned()
    {
        $dba = $this->getMockBuilder('\\Pimf\\Cache\\Storages\\Dba')
            ->disableOriginalConstructor()
            ->setMethods(array('get'))
            ->getMock();

        $dba->expects($this->once())->method('get')->will($this->returnValue('bar'));

        $this->assertEquals('bar', $dba->get('some.secret.key'));
    }

    public function testSetMethodProperlyCalls()
    {
        $dba = $this->getMockBuilder('\\Pimf\\Cache\\Storages\\Dba')
            ->disableOriginalConstructor()
            ->setMethods(array('put'))
            ->getMock();

        $dba->expects($this->once())->method('put')->with($this->equalTo('foo'), $this->equalTo('bar'),
            $this->equalTo(60));

        $dba->put('foo', 'bar', 60);
    }

    public function testStoreItemForeverProperlyCalls()
    {
        $dba = $this->getMockBuilder('\\Pimf\\Cache\\Storages\\Dba')
            ->disableOriginalConstructor()
            ->setMethods(array('put'))
            ->getMock();

        $dba->expects($this->once())->method('put')->with($this->equalTo('foo'), $this->equalTo('bar'));

        $dba->forever('foo', 'bar');
    }

    public function testForgetMethodProperlyCalls()
    {
        $dba = $this->getMockBuilder('\\Pimf\\Cache\\Storages\\Dba')
            ->disableOriginalConstructor()
            ->setMethods(array('forget'))
            ->getMock();

        $dba->expects($this->once())->method('forget')->with($this->equalTo('foo'));

        $dba->forget('foo');
    }

}
 