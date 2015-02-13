<?php
/**
 * Pimf
 *
 * @copyright Copyright (c)  Gjero Krsteski (http://krsteski.de)
 * @license   http://opensource.org/licenses/MIT MIT License
 */
namespace Pimf;

/**
 * A well-known object that other objects can use to find common objects and services.
 *
 * @package Pimf
 * @author  Gjero Krsteski <gjero@krsteski.de>
 */
class Config
{

  /**
   * The temporary storage for the accumulator.
   *
   * @var \ArrayObject
   */
  protected static $battery;

  /**
   * @param array $config
   * @param bool $override Used for testing only!
   */
  public static function load(array $config, $override = false)
  {
    if (!self::$battery || $override === true) {
      self::$battery = new \ArrayObject($config, \ArrayObject::STD_PROP_LIST + \ArrayObject::ARRAY_AS_PROPS);
    }
  }

  /**
   * Get an item from an array using "dot" notation.
   *
   * @param string|integer $index The index or identifier.
   * @param mixed $default
   *
   * @return mixed|null
   */
  public static function get($index, $default = null)
  {
    if (self::$battery->offsetExists($index)) {
      return self::$battery->offsetGet($index);
    }

    $array = self::$battery->getArrayCopy();

    foreach ((array)explode('.', $index) as $segment) {

      if (!is_array($array) || !array_key_exists($segment, $array)) {
        return $default;
      }

      $array = $array[$segment];
    }

    return $array;
  }
}
