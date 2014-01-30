<?php
/**
 * Pimf
 *
 * PHP Version 5
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.
 * It is also available through the world-wide-web at this URL:
 * http://krsteski.de/new-bsd-license/
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to gjero@krsteski.de so we can send you a copy immediately.
 *
 * @copyright Copyright (c)  Gjero Krsteski (http://krsteski.de)
 * @license http://krsteski.de/new-bsd-license New BSD License
 */

namespace Pimf\Cache\Storages;
use Pimf\Cache\Storages\Storage;

/**
 * @package Cache_Storages
 * @author Gjero Krsteski <gjero@krsteski.de>
 */
class Memory extends Storage
{
  /**
   * The in-memory array of cached items.
   * @var array
   */
  public $storage = array();

  /**
   * Retrieve an item from the cache storage.
   * @param string $key
   * @return mixed|null
   */
  protected function retrieve($key)
  {
    if (array_key_exists($key, $this->storage)) {
      return $this->storage[$key];
    }

    return null;
  }

  /**
   * Write an item to the cache for a given number of minutes.
   *
   * <code>
   *    // Put an item in the cache for 15 minutes
   *    Cache::put('name', 'Robin', 15);
   * </code>
   *
   * @param string $key
   * @param mixed $value
   * @param int $minutes
   */
  public function put($key, $value, $minutes)
  {
    $this->storage[$key] = $value;
  }

  /**
   * Write an item to the cache that lasts forever.
   * @param $key
   * @param $value
   */
  public function forever($key, $value)
  {
    $this->put($key, $value, 0);
  }

  /**
   * Delete an item from the cache.
   * @param string $key
   */
  public function forget($key)
  {
    unset($this->storage[$key]);
  }

  /**
   * Flush the entire cache.
   */
  public function flush()
  {
    $this->storage = array();
  }
}