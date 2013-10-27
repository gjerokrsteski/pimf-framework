<?php
/**
 * Pimf_Util
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
 * @copyright Copyright (c) 2010-2011 Gjero Krsteski (http://krsteski.de)
 * @license http://krsteski.de/new-bsd-license New BSD License
 */

/**
 * Represents a enumeration and gives the ability to emulate and create enumeration objects natively in PHP.
 *
 * <code>
 * class Month extends Pimf_Util_Enum {
 *   const __default = self::January;
 *
 *   const January = 1;
 *   const February = 2;
 *   const March = 3;
 *   const April = 4;
 * }
 *
 * echo new Month(Month::June) . PHP_EOL;
 *
 * try {
 * new Month(13);
 * } catch (UnexpectedValueException $uve) {
 *  echo $uve->getMessage() . PHP_EOL;
 * }
 * </code>
 *
 * <pre>
 * Inherited from sub-classes to create enum-like objects. Functions can
 * then type hint to require a certain enum value. All child classes must
 * have a __default constant, and one or more constants representing the
 * enum values. The values of the constants must be integers.
 * </pre>
 *
 * @package Pimf_Util
 * @author Gjero Krsteski <gjero@krsteski.de>
 */
abstract class Pimf_Util_Enum
{
  /**
   * The current enum value
   */
  private $value = null;

  /**
   * All the values of the enum constants
   */
  private $enums = null;
  
  /**
   * @param null $value
   * @throws RuntimeException If Enum constant values must be integers.
   * @throws Exception If Class constant __default does not exist.
   * @throws UnexpectedValueException The value '$value' is not one of the enum constants.
   * @throws InvalidArgumentException The value must be a string or integer.
   */
  public function __construct($value = null)
  {
    // Get all the class constants. They *must* be integer values so this
    // enum class behaves like traditional enums.
    if ($this->enums === null) {

      $refClass    = new ReflectionClass($this);
      $this->enums = $refClass->getConstants();
      unset($refClass);

      foreach ($this->enums as $const => $constval) {
        if ($constval !== (int)$constval) {
          throw new RuntimeException("Enum constant '$const' values must be integers");
        }
      }

      // All derived classes must include a __default constant
      if (!isset($this->enums['__default'])) {
        throw new Exception("Class constant __default does not exist");
      }
    }

    // If $value is a string, check if it's a valid enum constant, and set the value
    // to the constant value.
    $this->value = $value ? : $this->enums['__default'];

    if ($this->value === (string)$this->value) {
      if (!$const_key = $this->getArrayKey($this->value, $this->enums)) {
        throw new UnexpectedValueException("The value '$value' is not one of the enum constants");
      }
      $this->value = $this->enums[$const_key];
    } elseif ($this->value !== (int)$this->value) {
      throw new InvalidArgumentException("The value must be a string or integer");
    }
  }

  /**
   * @param bool $include_default
   * @return array|null
   */
  public function getConstList($include_default = false)
  {
    if (!$include_default) {
      $temp = $this->enums;
      unset($temp['__default']);
      return $temp;
    }
    return $this->enums;
  }

  /**
   * Returns the current integer value of the enum
   *
   * @see http://www.php.net/manual/en/language.oop5.magic.php#language.oop5.magic.invoke
   * @return int
   */
  public function __invoke()
  {
    return $this->value;
  }

  /**
   * Static method for creating an instance of the enum.
   *
   * A factory method for creating a new instance of the inherited enum. The value of
   * the enum will be the static method name.
   *
   * @see http://www.php.net/manual/en/language.oop5.overloading.php#language.oop5.overloading.methods
   *
   * @static
   * @param $value
   * @param $args
   * @return Pimf_Util_Enum
   */
  public static function __callStatic($value, $args)
  {
    return new static($value);
  }

  /**
   * Returns the current string value of the enum.
   *
   * @see http://www.php.net/manual/en/language.oop5.magic.php#language.oop5.magic.tostring
   * @return string
   */
  public function __toString()
  {
    return (string)array_search($this->value, $this->getConstList(false));
  }

  /**
   * Searches $arr for the given key, and returns the found key
   *
   * This is a case-insensitive search. In a case-insensitive manner, the array is search
   * for $key, and returns the found key (With proper case), or false if the key isn't
   * found.
   *
   * @param string $searchKey The key to search for
   * @param array $arr The array to search
   * @return mixed
   */
  private function getArrayKey($searchKey, $arr)
  {
    $allKeays  = array_keys($arr);
    $searchKey = strtolower($searchKey);

    foreach ($allKeays as $key) {

      if ($searchKey == strtolower($key)) {
        return $key;
      }
    }
    return false;
  }
}