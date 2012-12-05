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
 * Session Manager: delivers methods for save session handling.
 *
 * @package Pimf
 * @author Gjero Krsteski <gjero@krsteski.de>
 */
class Pimf_Session
{
  /**
   * @var object Instance of session class
   */
  protected static $instance;

  /**
   * Get an instance of session class
   * @return object Instance of session class
   */
  public static function getInstance()
  {
    if (self::$instance === null) {
      self::$instance = new self();
    }

    return self::$instance;
  }

  /**
   * Constructor - start session
   */
  protected function __construct()
  {
    if (!isset($_SESSION)) {
      session_start();
    }

    if (isset($_SESSION) === false) {
      $_SESSION = array();
    }
  }

  /**
   * Clone - prevent additional instances of the class
   */
  private function __clone() { }

  /**
   * Magic Method to set a session variable
   * @param  string  $key   Registry array key
   * @param  string  $value Value of session key
   * @throws LogicException If stored value is a resource.
   * @return mixed   TRUE on success otherwise FALSE
   */
  public function __set($key, $value)
  {
    if (is_resource($value)) {
      throw new LogicException(
        'storing resources in a session is not permitted!'
      );
    }

    if (isset($_SESSION[$key]) === false) {
      $_SESSION[$key] = $value;
      return true;
    }

    return false;
  }

  /**
   * Magic Method to get a session variable
   * @param  string  $key   Registry array key
   * @return bool    TRUE on success otherwise NULL
   */
  public function &__get($key)
  {
    if (isset($_SESSION[$key])) {
      return $_SESSION[$key];
    }

    return null;
  }

  /**
   * Unset a session variable
   * @param  string  $key   Registry array key
   * @return bool    TRUE on success otherwise FALSE
   */
  public function __unset($key)
  {
    if (isset($_SESSION[$key])) {
      unset($_SESSION[$key]);
      return true;
    }
    return false;
  }

  /**
   * @param mixed $key
   * @return bool
   */
  public function __isset($key)
  {
    return isset($_SESSION[$key]);
  }

  /**
   * @return bool
   */
  public function destroy()
  {
    $sessionState = session_destroy();
    session_write_close();
    unset($_SESSION);

    return $sessionState;
  }

  /**
   * Reset/delete old session and regenerate id.
   */
  public function reset()
  {
    session_regenerate_id(true);
    $_SESSION = array();
  }

  /**
   * Get the current session id.
   * @return string
   */
  public function getId()
  {
    return session_id();
  }
}
