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
 * Request Manager - for controlled access to the global state of the world.
 *
 * @package Pimf
 * @author Gjero Krsteski <gjero@krsteski.de>
 */
class Pimf_Request
{
  /**
   * @var Pimf_Param
   */
  public static $postData;

  /**
   * @var Pimf_Param
   */
  public static $getData;

  /**
   * @var Pimf_Param
   */
  public static $cookieData;

  /**
   * @var Pimf_Param
   */
  public static $cliData;

  /**
   * @param array $getData
   * @param array $postData
   * @param array $cookieData
   * @param array $cliData
   */
  public function __construct(array $getData, array $postData = array(), array $cookieData = array(), array $cliData = array())
  {
    static::$getData    = new Pimf_Param($this->stripSlashesIfMagicQuotes($getData));
    static::$postData   = new Pimf_Param($this->stripSlashesIfMagicQuotes($postData));
    static::$cookieData = new Pimf_Param($cookieData);
    static::$cliData    = new Pimf_Param($this->stripSlashesIfMagicQuotes($cliData));
  }

  /**
   * HTTP GET variables.
   * @return Pimf_Param
   */
  public function fromGet()
  {
    return static::$getData;
  }

  /**
   * CLI arguments passed to script.
   * @return Pimf_Param
   */
  public function fromCli()
  {
    return static::$cliData;
  }

  /**
   * HTTP POST variables.
   * @return Pimf_Param
   */
  public function fromPost()
  {
    return static::$postData;
  }

  /**
   * HTTP Cookies.
   * @return Pimf_Param
   */
  public function fromCookie()
  {
    return static::$cookieData;
  }

  /**
   * Strip slashes from string or array
   * @param $rawData
   * @param null $overrideStripSlashes
   * @return array|string
   */
  public function stripSlashesIfMagicQuotes($rawData, $overrideStripSlashes = null)
  {
    $hasMagicQuotes = function_exists('get_magic_quotes_gpc') ? get_magic_quotes_gpc() : false;

    $strip = !$overrideStripSlashes ? $hasMagicQuotes : $overrideStripSlashes;

    if ($strip) {
      return self::stripSlashes($rawData);
    }

    return $rawData;
  }

  /**
   * Strip slashes from string or array
   * @static
   * @param $rawData
   * @return array|string
   */
  protected static function stripSlashes($rawData)
  {
    return is_array($rawData) ? array_map(
      array(
        'self',
        'stripSlashes'
      ), $rawData
    ) : stripslashes($rawData);
  }
}

