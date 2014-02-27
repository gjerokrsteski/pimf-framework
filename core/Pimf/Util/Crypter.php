<?php
/**
 * Util
 *
 * @copyright Copyright (c)  Gjero Krsteski (http://krsteski.de)
 * @license http://krsteski.de/new-bsd-license New BSD License
 */

namespace Pimf\Util;

/**
 * Encodes data with MIME base64 and decodes data encoded with MIME base64.
 *
 * @package Util
 * @author Gjero Krsteski <gjero@krsteski.de>
 */
class Crypter
{
  /**
   * Encrypt a string.
   * @param string $string the string to encrypt.
   * @return string the encrypted string
   */
  public static function encrypt($string)
  {
    return base64_encode($string);
  }

  /**
   * Decrypt a string.
   * @param string $encrypted The encrypted string
   * @return string The decrypted string.
   */
  public static function decrypt($encrypted)
  {
    return base64_decode($encrypted, true);
  }

  /**
   * Get the name of the algorithm.
   * @return string name of the algorithm.
   */
  public function getAlgorithm()
  {
    return 'Base64';
  }
}