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

namespace Pimf;

/**
 * A well-known object that other objects can use to find common objects and services.
 * Acts also as a dependency injection container.
 *
 * <code>
 *  $registry = new Registry();
 *  $registry->your_key = "123";
 *
 *  // or ..
 *
 *  Registry::set('your_key', "123")
 *  Registry::get('your_key')
 * </code>
 *
 * @package Pimf
 * @author Gjero Krsteski <gjero@krsteski.de>
 *
 * @property EntityManager $em
 * @property Logger $logger
 * @property Environment $env
 * @property array $conf
 */
class Registry
{
  /**
   * The temporary storage for the accumulator.
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
      self::$battery = new \ArrayObject(array(), \ArrayObject::STD_PROP_LIST);
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

    if (is_resource($value)) {
      throw new \LogicException(
        'storing resources in a registry is not permitted!'
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
   * @param string|integer $namespace The namespace or identifier.
   * @param mixed $defaultValue
   * @return mixed|null
   */
  public static function get($namespace, $defaultValue = null)
  {
    self::init();

    if(self::$battery->offsetExists($namespace)) {
      return self::$battery->offsetGet($namespace);
    }

    return $defaultValue;
  }
}
