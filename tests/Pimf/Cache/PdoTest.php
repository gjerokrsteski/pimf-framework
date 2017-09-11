<?php

class CachePdoTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function getStore()
    {
        $store = $this->getMockBuilder('\\Pimf\\Database')
            ->setMethods(array('prepare'))->setConstructorArgs(
                array('sqlite::memory:')
            )->getMock();

        $statement = $this->getMockBuilder('\\stdClass')
            ->setMethods(array('bindValue', 'execute', 'fetchObject'))->getMock();

        $store->expects($this->any())->method('prepare')->will($this->returnValue($statement));

        return $store;
    }


    # start testing


    public function testNullIsReturnedWhenItemNotFound()
    {
        $store = $this->getStore();

        $cache = new \Pimf\Cache\Storages\Pdo($store, 'key.');

        $this->assertNull($cache->get('foo'));
    }


    public function testValueIsReturned()
    {
        $store = $this->getStore();

        $cache = $this->getMockBuilder('\Pimf\Cache\Storages\Pdo')->setConstructorArgs(array(
            $store,
            'key.'
        ))->getMock();

        $cache->expects($this->any())->method('get')->with('foo')->will($this->returnValue(serialize('foo')));

        $this->assertNotNull($cache->get('foo'));

    }

    public function testSetMethodProperlyCallsPdo()
    {
        $store = $this->getStore();

        $cache = $this->getMockBuilder('\Pimf\Cache\Storages\Pdo')->setConstructorArgs(array(
            $store,
            'key.'
        ))->getMock();

        $cache->expects($this->any())->method('expiration')->with(60 * 60);

        $cache->put('foo', 'foo', 60);
    }

    public function testSetMethodProperlyCallsPdoForNumerics()
    {
        $store = $this->getStore();

        $cache = $this->getMockBuilder('\Pimf\Cache\Storages\Pdo')->setConstructorArgs(array(
            $store,
            'key.'
        ))->getMock();

        $cache->expects($this->any())->method('expiration')->with(60 * 60);

        $cache->put('foo', 1, 60);
    }

    public function testStoreItemForeverProperlyCallsPdo()
    {
        $store = $this->getStore();

        $cache = new \Pimf\Cache\Storages\Pdo($store, 'key.');

        $cache->forever('foo', 'foo', 60);
    }

    public function testForgetMethodProperlyCallsRedis()
    {
        $store = $this->getStore();

        $cache = new \Pimf\Cache\Storages\Pdo($store, 'key.');

        $cache->forget('foo');
    }
}
