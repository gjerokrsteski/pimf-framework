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
 * For accumulating things.
 *
 * It can be used to solve many simple problems such as counting,
 * adding and finding the maximum/minimum or acting as switch or static stack.
 *
 * <code>
 * $registry = new Pimf_Registry();
 * $registry->your_key = "123";
 * </code>
 *
 * @package Pimf
 * @author Gjero Krsteski <gjero@krsteski.de>
 *
 * @property Pimf_EntityManager $em
 * @property Pimf_Logger $logger
 * @property Pimf_Environment $env
 * @property stdClass $conf
 */
class Pimf_Registry
{
  /**
   * The temporary storage for accumulator.
   * @var ArrayObject
   */
  protected static $battery = null;

  /**
   * Re-initialises the data.
   * @return void
   */
  protected static function init()
  {
    if (self::$battery === null) {
      self::$battery = new ArrayObject(array(), ArrayObject::STD_PROP_LIST);
    }
  }

  /**
   * @param mixed $namespace The namespace or identifier.
   * @param mixed $value The value.
   * @throws LogicException If key should be overwritten.
   */
  public function __set($namespace, $value)
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
   * @return mixed|null
   */
  public function __get($namespace)
  {
    self::init();

    return self::$battery->offsetGet($namespace);
  }

  /**
   * Resets the accumulator-storage.
   */
  public function reset()
  {
    self::$battery = null;
  }

  /**
   * @return array
   */
  public function getAll()
  {
    return self::$battery->getArrayCopy();
  }
}
