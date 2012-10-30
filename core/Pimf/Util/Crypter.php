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
 * Encodes data with MIME base64 and decodes data encoded with MIME base64.
 *
 * @package Pimf_Util
 * @author Gjero Krsteski <gjero@krsteski.de>
 */
class Pimf_Util_Crypter
{
  /**
   * Encrypt a string.
   * @param string $string the string to encrypt.
   * @return string the encrypted string
   */
  public static function encrypt($string)
  {
    return base64_encode(self::key().$string);
  }

  /**
   * Decrypt a string.
   * @param string $encrypted The encrypted string
   * @return string The decrypted string.
   */
  public static function decrypt($encrypted)
  {
    return str_replace(self::key(), '', base64_decode($encrypted, true));
  }

  /**
   * Get the name of the algorithm.
   * @return string name of the algorithm.
   */
  public function getAlgorithm()
  {
    return 'Base64';
  }

  /**
   * Get the encryption key from the application configuration.
   * @return string
   */
  protected static function key()
  {
    $config = Pimf_Registry::get('config');
    return $config['app']['name'];
  }
}