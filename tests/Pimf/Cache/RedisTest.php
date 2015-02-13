<?php

class CacheRedisTest extends \PHPUnit_Framework_TestCase
{

  /**
   * @return PHPUnit_Framework_MockObject_MockObject
   */
  protected function getRedis()
  {
    return $this->getMockBuilder('\\Pimf\\Redis')->disableOriginalConstructor()
        ->setMethods(
          array('get', 'expire', 'set', 'del', 'forget', 'select', 'put', 'inline', 'bulk', 'multibulk')
        )->getMock();
  }


  # start testing


  public function testGetReturnsNullWhenNotFound()
  {
    $redis = $this->getRedis();
    $cache = new \Pimf\Cache\Storages\Redis($redis);
    $this->assertNull($cache->get('foo'));
  }


  public function testRedisValueIsReturned()
  {
    $redis = $this->getRedis();

    $redis->expects($this->any())->method('connect')->with('default')->will($this->returnValue($redis));
    $redis->expects($this->once())->method('get')->with('foo')->will($this->returnValue(serialize('foo')));

    $cache = new \Pimf\Cache\Storages\Redis($redis);

    $this->assertEquals('foo', $cache->get('foo'));
  }


  public function testRedisValueIsReturnedForNumerics()
  {
    $redis = $this->getRedis();

    $redis->expects($this->any())->method('connect')->with('default')->will($this->returnValue($redis));
    $redis->expects($this->once())->method('get')->with('foo')->will($this->returnValue(serialize(1)));

    $cache = new \Pimf\Cache\Storages\Redis($redis);

    $this->assertEquals(1, $cache->get('foo'));
  }

  public function testRedisIfKeyInStorage()
  {
    $redis = $this->getRedis();

    $redis->expects($this->any())->method('connect')->with('default')->will($this->returnValue($redis));
    $redis->expects($this->once())->method('get')->with('foo')->will($this->returnValue(serialize(1)));

    $cache = new \Pimf\Cache\Storages\Redis($redis);

    $this->assertTrue($cache->has('foo'));
  }


  public function testSetMethodProperlyCallsRedis()
  {
    $redis = $this->getRedis();

    $redis->expects($this->any())->method('connect')->with('default')->will($this->returnValue($redis));
    $redis->expects($this->once())->method('expire')->with('foo', 60 * 60);

    $cache = new \Pimf\Cache\Storages\Redis($redis);

    $cache->put('foo', 'foo', 60);
  }


  public function testSetMethodProperlyCallsRedisForNumerics()
  {
    $redis = $this->getRedis();

    $redis->expects($this->any())->method('connect')->with('default')->will($this->returnValue($redis));
    $redis->expects($this->any())->method('set')->with('foo', serialize(1));
    $redis->expects($this->once())->method('expire')->with('foo', 60 * 60);

    $cache = new \Pimf\Cache\Storages\Redis($redis);

    $cache->put('foo', 1, 60);
  }

  public function testStoreItemForeverProperlyCallsRedis()
  {
    $redis = $this->getRedis();

    $redis->expects($this->any())->method('connect')->with('default')->will($this->returnValue($redis));
    $redis->expects($this->any())->method('set')->with('foo', serialize('foo'));

    $cache = new \Pimf\Cache\Storages\Redis($redis);

    $cache->forever('foo', 'foo', 60);
  }


  public function testForgetMethodProperlyCallsRedis()
  {
    $redis = $this->getRedis();

    $redis->expects($this->any())->method('connect')->with('default')->will($this->returnValue($redis));
    $redis->expects($this->once())->method('del')->with('foo');

    $cache = new \Pimf\Cache\Storages\Redis($redis);

    $cache->forget('foo');
  }

}
 