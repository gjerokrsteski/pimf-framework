<?php
/**
 * Util
 *
 * PHP Version 5
 *
 * A comprehensive collection of PHP utility classes and functions
 * that developers find themselves using regularly when writing web applications.
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

namespace Pimf\Util;

/**
 * Due to PHP Bug #39736 - serialize() consumes insane amount of RAM.
 *
 * Now we can put objects, strings, integers or arrays. Even instances of SimpleXMLElement can be put too!
 *
 * @package Util
 * @link https://bugs.php.net/bug.php?id=39736
 * @author Gjero Krsteski <gjero@krsteski.de>
 */
class Serializer
{
  /**
   * Serialize things.
   *
   * @param mixed $object Item you want - string, array, integer, object
   * @return string Containing a byte-stream representation.
   */
  public static function serialize($object)
  {
    $masked = false;

    if (false === is_object($object)) {
      $object = self::mask($object);
      $masked = true;
    }

    $objectInformation         = new \stdClass();
    $objectInformation->type   = get_class($object);
    $objectInformation->object = $object;
    $objectInformation->fake   = $masked;

    if ($object instanceof \SimpleXMLElement) {
      $objectInformation->object = $object->asXml();
    }

    return '' . self::serializeNative($objectInformation);
  }

  /**
   * Unserialize things.
   *
   * @param string $object Serialized object.
   * @return mixed
   */
  public static function unserialize($object)
  {
    $objectInformation = self::unserializeNative($object);

    if (true === $objectInformation->fake) {
      $objectInformation->object = self::unmask($objectInformation->object);
    }

    if ($objectInformation->type == 'SimpleXMLElement') {
      $objectInformation->object = simplexml_load_string($objectInformation->object);
    }

    return $objectInformation->object;
  }

  /**
   * @param mixed $value Item value.
   * @throws \RuntimeException If error during serialize.
   * @return string
   */
  public static function serializeNative($value)
  {
    $ret = (extension_loaded('igbinary') && function_exists('igbinary_serialize'))
    	  ? @igbinary_serialize($value)
    	  : @serialize($value);

    if ($ret === false) {
      $lastErr = error_get_last();
      throw new \RuntimeException($lastErr['message']);
    }

    return $ret;
  }

  /**
   * @param string $serialized The serialized item-string.
   * @throws \RuntimeException If error during unserialize.
   * @return mixed
   */
  public static function unserializeNative($serialized)
  {
    $ret = (extension_loaded('igbinary') && function_exists('igbinary_unserialize'))
          ? @igbinary_unserialize($serialized)
          : @unserialize($serialized);

    if ($ret === false) {
      $lastErr = error_get_last();
      throw new \RuntimeException($lastErr['message']);
    }

    return $ret;
  }

  /**
   * @param mixed $item Item
   * @return \stdClass
   */
  private static function mask($item)
  {
    return (object) $item;
  }

  /**
   * @param mixed $item Item
   * @return array
   */
  private static function unmask($item)
  {
    if (isset($item->scalar)) {
      return $item->scalar;
    }

    return (array) $item;
  }
}
