<?php
/**
 * Util
 *
 * @copyright Copyright (c)  Gjero Krsteski (http://krsteski.de)
 * @license   http://opensource.org/licenses/MIT MIT License
 */
namespace Pimf\Util;

/**
 * Value
 *
 * @package Util
 * @author  Gjero Krsteski <gjero@krsteski.de>
 */
class Value
{

  /**
   * @var string
   */
  protected $value;

  /**
   * @param $string
   */
  public function __construct($string)
  {
    $this->value = '' . $string;
  }

  public function __toString()
  {
    return $this->value;
  }

  /**
   * @param $string
   *
   * @return $this
   */
  public function prepend($string)
  {
    $this->value = String::ensureLeading($string, $this->value);
    return $this;
  }

  /**
   * @param $string
   *
   * @return $this
   */
  public function append($string)
  {
    $this->value = String::ensureTrailing($string, $this->value);
    return $this;
  }

  /**
   * @param $string
   *
   * @return $this
   */
  public function deleteTrailing($string)
  {
    $this->value = String::deleteTrailing($string, $this->value);
    return $this;
  }

  /**
   * @param $string
   *
   * @return $this
   */
  public function deleteLeading($string)
  {
    $this->value = String::deleteLeading($string, $this->value);
    return $this;
  }

  /**
   * @param string|array $mixed String or list of strings
   *
   * @return boolean
   */
  public function contains($mixed)
  {
    return String::contains($this->value, $mixed);
  }

  /**
   * @param string|array $with String or list of strings
   *
   * @return boolean
   */
  public function starts($with)
  {
    return String::startsWith($this->value, $with);
  }

  /**
   * @param string|array $with String or list of strings
   *
   * @return boolean
   */
  public function ends($with)
  {
    return String::endsWith($this->value, $with);
  }

  /**
   * @param $byString The delimiter string
   *
   * @return array
   */
  public function explode($byString)
  {
    return explode(''.$byString, $this->value);
  }
}