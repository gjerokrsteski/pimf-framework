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
 * @package Pimf_Session_Storages
 * @author Gjero Krsteski <gjero@krsteski.de>
 */
abstract class Pimf_Session_Storages_Storage
{
  /**
   * Load a session from storage by a given ID.
   * If no session is found for the id, null will be returned.
   *
   * @param string $id
   * @return array|null
   */
  abstract public function load($id);

  /**
   * Save a given session to storage.
   *
   * @param array $session
   * @param array $config
   * @param bool $exists
   * @return void
   */
  abstract public function save($session, $config, $exists);

  /**
   * Delete a session from storage by a given ID.
   *
   * @param string $id
   * @return void
   */
  abstract public function delete($id);

  /**
   * Create a fresh session array with a unique ID.
   *
   * @return array
   */
  public function fresh()
  {
    return array(
      'id'   => $this->id(),
      'data' => array(
        ':new:' => array(),
        ':old:' => array(),
      )
    );
  }

  /**
   * Get a new session ID that isn't assigned to any current session.
   *
   * @return string
   */
  public function id()
  {
    $session = array();

    // Just return any string since the Cookie driver has no idea.
    if ($this instanceof Pimf_Session_Storages_Cookie) {
      return Pimf_Util_String::random(40);
    }

    // We'll find an random ID here.
    do {
      $session = $this->load($id = Pimf_Util_String::random(40));
    } while ($session !== null);

    return $id;
  }
}