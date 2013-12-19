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

namespace Pimf\Session\Storages;
use Pimf\Session\Storages\Storage, Pimf\Contracts\Cleanable;

/**
 * @package Session_Storages
 * @author Gjero Krsteski <gjero@krsteski.de>
 */
class File extends Storage implements Cleanable
{
  /**
   * The path to which the session files should be written.
   * @var string
   */
  private $path;

  /**
   * @param string $path
   */
  public function __construct($path)
  {
    $this->path = (string)$path;
  }

  /**
   * Load a session from storage by a given ID.
   * @param string $id
   * @return array|mixed
   */
  public function load($id)
  {
    if (file_exists($path = $this->path . $id)) {
      return unserialize(file_get_contents($path));
    }
  }

  /**
   * Save a given session to storage.
   * @param array $session
   * @param array $config
   * @param bool $exists
   */
  public function save($session, $config, $exists)
  {
    file_put_contents($this->path . $session['id'], serialize($session), LOCK_EX);
  }

  /**
   * @param string $id
   */
  public function delete($id)
  {
    if (file_exists($this->path . $id)) {
      @unlink($this->path . $id);
    }
  }

  /**
   * Delete all expired sessions from persistent storage.
   * @param int $expiration
   * @return mixed|void
   */
  public function clean($expiration)
  {
    $files = glob($this->path . '*');

    if ($files === false) {
      return;
    }

    foreach ($files as $file) {
      if (filetype($file) == 'file' && filemtime($file) < $expiration) {
        @unlink($file);
      }
    }
  }
}