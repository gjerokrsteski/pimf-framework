<?php
class CacheTest extends PHPUnit_Framework_TestCase
{
  public function testCreatingNewInstance()
  {
    new \Pimf\Util\Cache();
  }

  public function testWritingData()
  {
    $data     = 'some sample data here, as string, array or object!';
    $path     = dirname(__FILE__) . '/_fixture/';
    $cache_id = 'my-data-cache-id.html';

    $cached = \Pimf\Util\Cache::put($path.$cache_id, $data, '+10 seconds');

    $this->assertNotNull($cached);
  }

  public function testRetrievingData()
  {
    $data     = 'some sample data here, as string, array or object!';
    $path     = dirname(__FILE__) . '/_fixture/';
    $cache_id = 'my-data-cache-id.html';

    $cached = \Pimf\Util\Cache::retrieve($path.$cache_id);

    $this->assertEquals($data, $cached);
  }

  public function testDeletingCachedFile()
  {
    $path     = dirname(__FILE__) . '/_fixture/';
    $cache_id = 'my-data-cache-id.html';

    $deleted = \Pimf\Util\Cache::forget($path.$cache_id);

    $this->assertTrue($deleted);
  }
  
}
 