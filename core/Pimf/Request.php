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
 * Request Manager: for controlled access to the global state of the world.
 *
 * @package Pimf
 * @author Gjero Krsteski <gjero@krsteski.de>
 */
class Pimf_Request
{
  /**
   * @var Pimf_Param
   */
  private $postData;

  /**
   * @var Pimf_Param
   */
  private $getData;

  /**
   * @var Pimf_Param
   */
  private $cookieData;

  /**
   * @param array $getData
   * @param array $postData
   * @param array $cookieData
   */
  public function __construct(array $getData, array $postData = array(), array $cookieData = array())
  {
    $this->getData    = new Pimf_Param($this->stripSlashesIfMagicQuotes($getData));
    $this->postData   = new Pimf_Param($this->stripSlashesIfMagicQuotes($postData));
    $this->cookieData = new Pimf_Param($cookieData);
  }

  /**
   * HTTP GET variables.
   * @return Pimf_Param
   */
  public function fromGet()
  {
    return $this->getData;
  }

  /**
   * HTTP POST variables.
   * @return Pimf_Param
   */
  public function fromPost()
  {
    return $this->postData;
  }

  /**
   * HTTP Cookies.
   * @return Pimf_Param
   */
  public function fromCookie()
  {
    return $this->cookieData;
  }

  /**
   * Strip slashes from string or array
   * @param $rawData
   * @param null $overrideStripSlashes
   * @return array|string
   */
  public function stripSlashesIfMagicQuotes($rawData, $overrideStripSlashes = null)
  {
    $strip = is_null($overrideStripSlashes) ? get_magic_quotes_gpc() : $overrideStripSlashes;

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

