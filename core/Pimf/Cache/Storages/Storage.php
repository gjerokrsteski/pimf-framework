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
 * @copyright Copyright (c) 2010-2011 Gjero Krsteski (http://krsteski.de)
 * @license http://krsteski.de/new-bsd-license New BSD License
 */

/**
 * @package Pimf_Cache_Storages
 * @author Gjero Krsteski <gjero@krsteski.de>
 */
abstract class Pimf_Cache_Storages_Storage
{
  /**
   * Determine if an item exists in the cache.
   * @param $key
   * @return bool
   */
  public function has($key)
  {
    return ($this->get($key) !== null);
  }

  /**
   * Get an item from the cache.
   *
   * <code>
   *    // Get an item from the cache storage
   *    $name = Pimf_Cache::storage('name');
   *
   *    // Return a default value if the requested item isn't cached
   *    $name = Pimf_Cache::get('name', 'Robin');
   * </code>
   *
   * @param $key
   * @param null $default
   * @return mixed|null
   */
  public function get($key, $default = null)
  {
    return (!is_null($item = $this->retrieve($key))) ? $item : $default;
  }

  /**
   * Retrieve an item from the cache storage.
   * @param string $key
   * @return mixed
   */
  abstract protected function retrieve($key);

  /**
   * Write an item to the cache for a given number of minutes.
   *
   * <code>
   *    // Put an item in the cache for 15 minutes
   *    Pimf_Cache::put('name', 'Robin', 15);
   * </code>
   *
   * @param string $key
   * @param mixed $value
   * @param int $minutes
   * @return void
   */
  abstract public function put($key, $value, $minutes);

  /**
   * Get an item from the cache, or cache and return the default value.
   *
   * <code>
   *    // Get an item from the cache, or cache a value for 15 minutes
   *    $name = Pimf_Cache::remember('name', 'Robin', 15);
   *
   *    // Use a closure for deferred execution
   *    $count = Pimf_Cache::remember('count', function() { return User::count(); }, 15);
   * </code>
   *
   * @param string $key
   * @param mixed $default
   * @param int $minutes
   * @param string $function
   * @return mixed
   */
  public function remember($key, $default, $minutes, $function = 'put')
  {
    if (!is_null($item = $this->get($key, null)))
      return $item;

    $this->$function($key, $default, $minutes);

    return $default;
  }

  /**
   * Get an item from the cache, or cache the default value forever.
   *
   * @param string $key
   * @param mixed $default
   * @return mixed
   */
  public function sear($key, $default)
  {
    return $this->remember($key, $default, null, 'forever');
  }

  /**
   * Delete an item from the cache.
   *
   * @param string $key
   * @return void
   */
  abstract public function forget($key);

  /**
   * Get the expiration time as a UNIX timestamp.
   *
   * @param int $minutes
   * @return int
   */
  protected function expiration($minutes)
  {
    return time() + ($minutes * 60);
  }
}
