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
 * A well-known object that other objects can use to find common objects and services.
 * Acts also as a dependency injection container.
 *
 * <code>
 * $registry = new Pimf_Registry();
 * $registry->your_key = "123";
 *
 * // or ..
 *
 * Pimf_Registry::set('your_key', "123")
 * Pimf_Registry::get('your_key')
 * </code>
 *
 * @package Pimf
 * @author Gjero Krsteski <gjero@krsteski.de>
 *
 * @property Pimf_EntityManager $em
 * @property Pimf_Logger $logger
 * @property Pimf_Environment $env
 * @property array $conf
 */
class Pimf_Registry
{
  /**
   * The temporary storage for accumulator.
   * @var ArrayObject
   */
  protected static $battery;

  /**
   * Re-initialises the data.
   * @return void
   */
  protected static function init()
  {
    if (!self::$battery) {
      self::$battery = new ArrayObject(array(), ArrayObject::STD_PROP_LIST);
    }
  }

  /**
   * @param mixed $namespace The namespace or identifier.
   * @param mixed $value The value.
   */
  public function __set($namespace, $value)
  {
    self::set($namespace, $value);
  }

  /**
   * @param mixed $namespace The namespace or identifier.
   * @param mixed $value The value.
   * @throws LogicException If key should be overwritten.
   */
  public static function set($namespace, $value)
  {
    self::init();

    if (self::$battery->offsetExists($namespace)) {
      throw new LogicException(
        'key ['.$namespace.'] can not be overwritten at the registry'
      );
    }

    self::$battery->offsetSet($namespace, $value);
  }

  /**
   * @param mixed $namespace The namespace or identifier.
   * @return mixed
   */
  public function __get($namespace)
  {
    return self::get($namespace);
  }

  /**
   * @param mixed $namespace The namespace or identifier.
   * @return mixed|null
   */
  public static function get($namespace)
  {
    self::init();

    return self::$battery->offsetGet($namespace);
  }
}
