<?php
class CacheMemoryTest extends \PHPUnit_Framework_TestCase
{
  public function testCreatingNewInstance()
  {
    new \Pimf\Cache\Storages\Memory();
  }

  public function testHappyPuttingAndGettingData()
  {
    $cache = new \Pimf\Cache\Storages\Memory();

    $cache->put('scret.key', '123-data', 2);

    $this->assertEquals('123-data', $cache->get('scret.key'));
  }

  public function testGettingDataIfKeyNotAtCache()
  {
    $cache = new \Pimf\Cache\Storages\Memory();

    $this->assertNull($cache->get('scret.key.not.at.cache'));
  }

  public function testGettingDataIfKeyNotAtCacheAndUsingDefaultValue()
  {
    $cache = new \Pimf\Cache\Storages\Memory();

    $this->assertTrue($cache->get('scret.key.not.at.cache', true));
  }

  public function testPuttingForever()
  {
    $cache = new \Pimf\Cache\Storages\Memory();
    $cache->forever('scret.key.2', 'data');

    $this->assertEquals('data', $cache->get('scret.key.2'));
  }

  public function testToForget()
  {
    $cache = new \Pimf\Cache\Storages\Memory();
    $cache->forever('scret.key.2', 'data');
    $cache->forget('scret.key.2');

    $this->assertNull($cache->get('scret.key.2'));
  }

  public function testToFlush()
  {
    $cache = new \Pimf\Cache\Storages\Memory();
    $cache->forever('scret.key.2', 'data');
    $cache->flush();

    $this->assertNull($cache->get('scret.key.2'));
  }

  public function testToRemember()
  {
    $cache = new \Pimf\Cache\Storages\Memory();
    $cache->remember('scret.key.2', 'data', 1);

    $this->assertEquals('data', $cache->get('scret.key.2'));
  }

  public function testToRememberIfNotAtCache()
  {
    $cache = new \Pimf\Cache\Storages\Memory();
    $cache->remember('scret.key.3', 'data', 1);

    $this->assertEquals(null, $cache->get('scret.key.2'));
  }

  public function testToSear()
  {
    $cache = new \Pimf\Cache\Storages\Memory();
    $cache->sear('scret.key.4', 'data');

    $this->assertEquals('data', $cache->get('scret.key.4'));
  }

  public function testIfHasKey()
  {
    $cache = new \Pimf\Cache\Storages\Memory();
    $cache->put('scret.key.4', 'data', 1);

    $this->assertTrue($cache->has('scret.key.4'));
  }
}
 