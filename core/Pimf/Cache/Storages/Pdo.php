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
use Pimf\Cache\Storages\Storage, Pimf\Database;

/**
 * @package Cache_Storages
 * @author Gjero Krsteski <gjero@krsteski.de>
 */
class Pdo extends Storage
{
  /**
   * The cache key from the cache configuration file.
   * @var string
   */
  protected $key;

  /**
   * @var \Pimf\Database
   */
  protected $db;

  /**
   * Create a new database cache storage instance.
   *
   * @param \Pimf\Database $pdo
   * @param string $key
   */
  public function __construct(\Pimf\Database $pdo, $key)
  {
    $this->db  = $pdo;
    $this->key = (string)$key;
  }

  /**
   * Retrieve an item from the cache storage.
   * @param string $key
   * @return mixed|void
   */
  protected function retrieve($key)
  {
    $sth = $this->db->prepare(
      'SELECT * FROM pimf_cache WHERE key = :key'
    );

    $sth->bindValue(':key', $this->key . $key);
    $sth->execute();

    $cache = $sth->fetchObject();

    if ($cache instanceof \stdClass) {

      if (time() >= $cache->expiration) {
        return $this->forget($key);
      }

      return unserialize($cache->value);
    }
  }

  /**
   * Write an item to the cache for a given number of minutes.
   *
   * <code>
   *    // Put an item in the cache for 15 minutes
   *    Cache::put('name', 'Robin', 15);
   * </code>
   *
   * @param  string  $key
   * @param  mixed   $value
   * @param  int     $minutes
   * @return bool
   */
  public function put($key, $value, $minutes)
  {
    $key        = $this->key . $key;
    $value      = serialize($value);
    $expiration = $this->expiration($minutes);

    try {
      $sth = $this->db->prepare(
        "INSERT INTO pimf_cache (key, value, expiration) VALUES (:key, :value, :expiration)"
      );
    } catch (\Exception $e) {
      $sth = $this->db->prepare(
        "UPDATE pimf_cache SET value = :value, expiration = :expiration WHERE key = :key"
      );
    }

    $sth->bindValue(':key', $key);
    $sth->bindValue(':value', $value);
    $sth->bindValue(':expiration', $expiration);
    return $sth->execute();
  }

  /**
   * Write an item to the cache for five years.
   * @param $key
   * @param $value
   * @return bool
   */
  public function forever($key, $value)
  {
    return $this->put($key, $value, 2628000);
  }

  /**
   * Delete an item from the cache.
   * @param string $key
   * @return bool|void
   */
  public function forget($key)
  {
    $sth = $this->db->prepare(
      "DELETE FROM pimf_cache WHERE key = :key"
    );

    $sth->bindValue(':key', $this->key . $key);
    return $sth->execute();
  }
}
