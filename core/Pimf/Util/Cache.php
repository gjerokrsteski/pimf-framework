<?php
/**
 * Util
 *
 * PHP Version 5
 *
 * A comprehensive collection of PHP utility classes and functions
 * that developers find themselves using regularly when writing web applications.
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

namespace Pimf\Util;

/**
 * Instant caching of data or HTML into a file at you local system.
 *
 * <code>
 *
 * $html = 'some sample data here, as string, array or object!';
 *
 * Cache::put('/path/to/directory/'.'my-data-cache-id.html', $html);
 *
 * $hasData = Cache::retrieve('my.data.cache.id');
 *
 * if ($hasData !== null) {
 *   $html = $hasData
 * }
 * </code>
 *
 * @package Util
 * @author Gjero Krsteski <gjero@krsteski.de>
 */
class Cache
{
  /**
   * Writes temporary data to cache files.
   *
   * @param string $path File path within /tmp to save the file - make sure it exists and is writeable.
   * @param mixed $data The data to save to the temporary file.
   * @param mixed $expires A valid strtotime string when the data expires.
   * @return mixed The contents of the temporary file.
   */
  public static function put($path, $data = null, $expires = '+1 day')
  {
    return self::cache($path, $data, $expires);
  }

  /**
   * Reads temporary data to cache files.
   *
   * @param string $path File path within /tmp to save the file - make sure it exists and is writeable.
   * @return mixed The contents of the temporary file.
   */
  public static function retrieve($path)
  {
    return self::cache($path);
  }

  /**
   * Delete the cached file.
   * @param $path
   * @return bool
   */
  public static function forget($path)
  {
    if (file_exists($path)) {
      @unlink($path);
      clearstatcache();
      return true;
    }

    return false;
  }

  /**
   * Reads/writes temporary data to cache files.
   *
   * @param string $path File path within /tmp to save the file - make sure it exists and is writeable.
   * @param mixed $data The data to save to the temporary file.
   * @param mixed $expires A valid strtotime string when the data expires.
   * @return mixed The contents of the temporary file.
   */
  protected static function cache($path, $data = null, $expires = '+1 day')
  {
    $now      = time();
    $filename = strtolower($path);
    $fileTime = false;

    if (!is_numeric($expires)) {
      $expires = strtotime($expires, $now);
    }

    $timeDiff = $expires - $now;

    if (file_exists($filename)) {
      $fileTime = @filemtime($filename);
    }

    if ($data === null) {

      if (file_exists($filename) && $fileTime !== false) {

        if ($fileTime + $timeDiff < $now) {

          @unlink($filename);

        } else {

          $data = @file_get_contents($filename);

          if (String::isSerialized($data) === true) {
            $data = Serializer::unserialize($data);
          }
        }
      }
    } elseif (is_writable(dirname($filename))) {

      if (!is_string($data)) {
        $data = Serializer::serialize($data);
      }

      @file_put_contents($filename, $data, LOCK_EX);
    }

    return $data;
  }
}
