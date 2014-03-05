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
class Memory extends Storage
{
  /**
   * The session payload that will be returned by the storage.
   *
   * @var array
   */
  public $session;

  /**
   * Load a session from storage by a given ID.
   *
   * @param string $id
   *
   * @return array
   */
  public function load($id)
  {
    return $this->session;
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
    //...
  }

  /**
   * Delete a session from storage by a given ID.
   *
   * @param string $id
   */
  public function delete($id)
  {
    //...
  }
}
