<?php
class CacheTest extends \PHPUnit_Framework_TestCase
{
  public function testCreatingNewInstance()
  {
    new \Pimf\Cache();
  }

  public function testCreateMemoryStorage()
  {
    $storage = \Pimf\Cache::storage('memory');
    $storage2 = \Pimf\Cache::storage('memory');

    $this->assertInstanceOf('\\Pimf\\Cache\\Storages\\Storage', $storage);
    $this->assertInstanceOf('\\Pimf\\Cache\\Storages\\Storage', $storage2);
    $this->assertEquals($storage, $storage2);
  }

  /**
   * @expectedException \RuntimeException
   */
  public function testBombingExceptionIfStorageNotSupported()
  {
    \Pimf\Cache::storage('some-bad-bad-storage');
  }

  public function testPuttingAndLoadingData()
  {
    $cache = \Pimf\Cache::storage('memory');

    $cache->put('name', 'Robin', 15);

    $this->assertEquals('Robin', $cache->get('name'));
  }

  public function testPuttingAndLoadingDataStatically()
  {
    \Pimf\Cache::storage('memory');

    \Pimf\Cache::put('name', 'Robin', 15);

    $this->assertEquals('Robin', \Pimf\Cache::get('name'));
  }
}
 