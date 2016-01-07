<?php

class CacheMemcachedTest extends \PHPUnit_Framework_TestCase
{
    public function testGetReturnsNullWhenNotFound()
    {
        $memcache = $this->getMock('Memcached', array('get'));
        $memcache->expects($this->once())->method('get')->with($this->equalTo('foobar'))->will($this->returnValue(null));
        $store = new \Pimf\Cache\Storages\Memcached($memcache, 'foo');

        $this->assertNull($store->get('bar'));
    }

    public function testMemcacheValueIsReturned()
    {
        $memcache = $this->getMock('Memcached', array('get'));
        $memcache->expects($this->once())->method('get')->will($this->returnValue('bar'));
        $store = new \Pimf\Cache\Storages\Memcached($memcache, 'foo');

        $this->assertEquals('bar', $store->get('foo'));
    }

    public function testSetMethodProperlyCallsMemcache()
    {
        $memcache = $this->getMock('Memcached', array('set'));
        $memcache->expects($this->once())->method('set')->with($this->equalTo('key.foo3'), $this->equalTo('bar'));
        $store = new \Pimf\Cache\Storages\Memcached($memcache, 'key.');

        $store->put('foo3', 'bar', 1);
    }

    public function testStoreItemForeverProperlyCallsMemcached()
    {
        $memcache = $this->getMock('Memcached', array('set'));
        $memcache->expects($this->once())->method('set')->with(
            $this->equalTo('key.foo'),
            $this->equalTo('bar'),
            $this->equalTo(0)
        );

        $store = new \Pimf\Cache\Storages\Memcached($memcache, 'key.');
        $store->forever('foo', 'bar');
    }

    public function testForgetMethodProperlyCallsMemcache()
    {
        $memcache = $this->getMock('Memcached', array('delete'));
        $memcache->expects($this->once())->method('delete')->with($this->equalTo('key.foo'));
        $store = new \Pimf\Cache\Storages\Memcached($memcache, 'key.');
        $store->forget('foo');
    }

}