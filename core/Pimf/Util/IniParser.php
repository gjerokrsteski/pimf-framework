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
 * @package Pimf_Util
 * @author Gjero Krsteski <gjero@krsteski.de>
 */
class Pimf_Util_IniParser
{
  /**
   * Filename of our .ini file.
   * @var string
   */
  protected $file;

  /**
   * @param string $file
   */
  public function __construct($file)
  {
    $this->setFile($file);
  }

  /**
   * @return array
   * @throws LogicException
   */
  public function parse()
  {
    if (empty($this->file)) {
      throw new LogicException("Need a file to parse.");
    }

    return $this->process(file_get_contents($this->file));
  }

  /**
   * Parses a string with INI contents
   * @param string $src
   * @return array
   */
  public function process($src)
  {
    $simpleParsed      = parse_ini_string($src, true);
    $inheritanceParsed = array();

    foreach ($simpleParsed as $k=> $v) {
      if (false === strpos($k, ':')) {
        $inheritanceParsed[$k] = $v;
        continue;
      }

      $sects = array_map('trim', array_reverse(explode(':', $k)));
      $root  = array_pop($sects);
      $arr   = $v;

      foreach ($sects as $s) {
        $arr = array_merge($inheritanceParsed[$s], $arr);
      }

      $inheritanceParsed[$root] = $arr;
    }

    return $this->parseKeys($inheritanceParsed);
  }

  /**
   * @param string $file
   * @return Pimf_Util_IniParser
   * @throws InvalidArgumentException
   */
  public function setFile($file)
  {
    if (!file_exists($file) || !is_readable($file)) {
      throw new InvalidArgumentException("The file '{$file}' cannot be opened.");
    }

    $this->file = $file;
    return $this;
  }

  /**
   * @param array $arr
   * @return array
   */
  private function parseKeys(array $arr)
  {
    $output = new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);

    foreach ($arr as $k=> $v) {

      if ($v === (array)$v) { // is a section
        $output[$k] = $this->parseKeys($v);
        continue;
      }

      // not a section
      $v = $this->parse_value($v);

      if (false === strpos($k, '.')) {
        $output[$k] = $v;
        continue;
      }

      $output = $this->recKeys(
        explode('.', $k), $v, $output
      );
    }

    return $output;
  }

  /**
   * @param mixed $keys
   * @param mixed $value
   * @param mixed $parent
   * @return array
   */
  protected function recKeys($keys, $value, $parent)
  {
    if (!$keys) {
      return $value;
    }

    $k = $this->parse_value(array_shift($keys));

    if (!array_key_exists($k, $parent)) {
      $parent[$k] = new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);
    }

    $v          = $this->recKeys($keys, $value, $parent[$k]);
    $parent[$k] = $v;

    return $parent;
  }

  /**
   * Parses and formats the code in a code-sum pair
   * @param string $key
   * @return string
   */
  protected function parse_key($key)
  {
    return $key;
  }

  /**
   * Parses and formats the sum in a code-sum pair
   * @param string $value
   * @return mixed
   */
  protected function parse_value($value)
  {
    if (preg_match('/\[\s*.*?(?:\s*,\s*.*?)*\s*\]/', $value)) {
      return explode(',', trim(preg_replace('/\s+/', '', $value), '[]'));
    }
    return $value;
  }
}
