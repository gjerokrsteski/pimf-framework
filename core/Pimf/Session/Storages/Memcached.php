<?php
/**
 * Pimf
 *
 * @copyright Copyright (c)  Gjero Krsteski (http://krsteski.de)
 * @license   http://krsteski.de/new-bsd-license New BSD License
 */

namespace Pimf\Session\Storages;

/**
 * @package Session_Storages
 * @author  Gjero Krsteski <gjero@krsteski.de>
 */
class Memcached extends Storage
{
  /**
   * The Memcache cache storage instance.
   *
   * @var \Pimf\Cache\Storages\Memcached
   */
  private $memcached;

  /**
   * @param \Pimf\Cache\Storages\Memcached $memcached
   */
  public function __construct(\Pimf\Cache\Storages\Memcached $memcached)
  {
    $this->memcached = $memcached;
  }

  /**
   * Load a session from storage by a given ID.
   *
   * @param string $id
   *
   * @return array|mixed|null
   */
  public function load($id)
  {
    return $this->memcached->get($id);
  }

  /**
   * Save a given session to storage.
   *
   * @param array $session
   * @param array $config
   * @param bool  $exists
   */
  public function save($session, $config, $exists)
  {
    $this->memcached->put($session['id'], $session, $config['lifetime']);
  }

  /**
   * @param string $id
   */
  public function delete($id)
  {
    $this->memcached->forget($id);
  }
}
