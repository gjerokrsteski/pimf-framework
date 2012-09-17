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
 * Identifier util for unified resource generation.
 *
 * <code>
 * $identifier = new class Pimf_Util_Identifier(1, '23', 123, 'ZZ-TOP', 'Some_Class_name');
 *
 * print $identifier; // --> '1_23_123_zz_top_some_class_name'
 *
 * $identifier->setDelimiter('/');
 *
 * print $identifier->generate(); // --> '1/23/123/zz/top/some/class/name'
 * </code>
 *
 * @package Pimf_Util
 * @author Gjero Krsteski <gjero@krsteski.de>
 */
class Pimf_Util_Identifier
{
  /**
   * @var string
   */
  protected static $delimiter = '_';

  /**
   * @var array
   */
  protected $args = array();

  /**
   * Create a new Cache Identifier based on the given parameters.
   * Integer and string but not array and objects.
   *
   * @throws BadMethodCallException If no identifiers received.
   */
  public function __construct()
  {
    $args = func_get_args();

    if (!count($args) || !implode('', $args)) {
      throw new BadMethodCallException('No identifiers received');
    }

    $this->args = $args;
  }

  /**
   * Return String representation of this Cache Identifier.
   *
   * @return string
   */
  public function __toString()
  {
    return $this->generate();
  }

  /**
   * @return string
   */
  public function generate()
  {
    return (string) $this->slag();
  }

  /**
   * Slags the identifier.
   * @return string
   */
  protected function slag()
  {
    $ident = str_replace('-', '_', implode(self::getDelimiter(), $this->args));
    $ident = str_replace('_', self::getDelimiter(), $ident);
    $ident = trim($ident);
    $ident = str_replace(' ', '', $ident);

    return strip_tags(strtolower($ident));
  }

  /**
   * Set the delimiter used to create a Cache Identifier.
   *
   * @param string $delimiter The delimiter character.
   */
  public function setDelimiter($delimiter)
  {
    self::$delimiter = $delimiter;
  }

  /**
   * Get the delimiter used to create a Cache Identifier.
   *
   * @return string
   */
  public function getDelimiter()
  {
    return self::$delimiter;
  }

  /**
   * Mainly used for unit tests.
   */
  public function clear()
  {
    self::$delimiter = '_';
  }
}
