<?php
class UtilCacheTest extends PHPUnit_Framework_TestCase
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

    $cached = \Pimf\Util\Cache::put($path.$cache_id, $data, '+2 day');

    $this->assertNotNull($cached, 'it is not cached');
  }

  public function testDeletingDataIfNotExists()
  {
    $path     = dirname(__FILE__) . '/_fixture/';
    $cache_id = 'bad-bad-data-cache-id.html';

    $cached = \Pimf\Util\Cache::forget($path.$cache_id);

    $this->assertFalse($cached, 'it is not deleted');
  }
}
 