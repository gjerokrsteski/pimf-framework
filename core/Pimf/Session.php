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
 * @package Pimf
 * @author Gjero Krsteski <gjero@krsteski.de>
 */
class Pimf_Session
{
  /**
   * The session singleton instance for the request.
   * @var Pimf_Session_Payload
   */
  public static $instance;

  /**
   * The third-party storage registrar.
   * @var array
   */
  public static $farm = array();

  /**
   * The string name of the CSRF token stored in the session.
   * @var string
   */
  const CSRF = 'csrf_token';

  /**
   * Create the session payload and load the session.
   * @return void
   */
  public static function load()
  {
    $conf = Pimf_Registry::get('conf');

    static::start($conf['session']['storage']);

    static::$instance->load(Pimf_Cookie::get($conf['session']['cookie']));
  }

  /**
   * Create the session payload instance for the request.
   * @param string $storage
   * @return void
   */
  public static function start($storage)
  {
    static::$instance = new Pimf_Session_Payload(static::factory($storage));
  }

  /**
   * Create a new session storage instance.
   * @param $storage
   * @return Pimf_Session_Storages_Storage
   * @throws RuntimeException
   */
  public static function factory($storage)
  {
    if (isset(static::$farm[$storage])) {
      $resolver = static::$farm[$storage];
      return $resolver();
    }

    $conf = Pimf_Registry::get('conf');

    switch ($storage) {
      case 'apc':
        return new Pimf_Session_Storages_Apc(Pimf_Cache::storage('apc'));

      case 'cookie':
        return new Pimf_Session_Storages_Cookie();

      case 'file':
        return new Pimf_Session_Storages_File($conf['session']['storage_path']);

      case 'pdo':
        return new Pimf_Session_Storages_Pdo(Pimf_Pdo_Factory::get($conf['session']['database']));

      case 'memcached':
        return new Pimf_Session_Storages_Memcached(Pimf_Cache::storage('memcached'));

      case 'memory':
        return new Pimf_Session_Storages_Memory();

      case 'redis':
        return new Pimf_Session_Storages_Redis(Pimf_Cache::storage('redis'));

      case 'dba':
        return new Pimf_Session_Storages_Dba(Pimf_Cache::storage('dba'));

      default:
        throw new RuntimeException("Session storage [$storage] is not supported.");
    }
  }

  /**
   * Retrieve the active session payload instance for the request.
   *
   * <code>
   *    // Retrieve the session instance and get an item
   *    Pimf_Session::instance()->get('name');
   *
   *    // Retrieve the session instance and place an item in the session
   *    Pimf_Session::instance()->put('name', 'Robin');
   * </code>
   *
   * @return Pimf_Session_Payload
   * @throws RuntimeException
   */
  public static function instance()
  {
    if (static::started()) {
      return static::$instance;
    }

    throw new RuntimeException("A storage must be set before using the session.");
  }

  /**
   * Determine if session handling has been started for the request.
   *
   * @return bool
   */
  public static function started()
  {
    return (static::$instance !== null);
  }

  /**
   * Register a third-party cache storage.
   *
   * @param string $storage
   * @param Closure $resolver
   * @return void
   */
  public static function extend($storage, Closure $resolver)
  {
    static::$farm[$storage] = $resolver;
  }

  /**
   * Magic Method for calling the methods on the session singleton instance.
   *
   * <code>
   *    // Retrieve a value from the session
   *    $value = Pimf_Session::get('name');
   *
   *    // Write a value to the session storage
   *    $value = Pimf_Session::put('name', 'Robin');
   *
   *    // Equivalent statement using the "instance" method
   *    $value = Pimf_Session::instance()->put('name', 'Robin');
   * </code>
   *
   * @param $method
   * @param $parameters
   *
   * @return Pimf_Session_Storages_Storage
   */
  public static function __callStatic($method, $parameters)
  {
    return call_user_func_array(
      array(
        static::instance(),
        $method
      ), $parameters
    );
  }
}