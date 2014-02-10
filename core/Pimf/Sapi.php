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
 * @copyright Copyright (c)  Gjero Krsteski (http://krsteski.de)
 * @license http://krsteski.de/new-bsd-license New BSD License
 */

namespace Pimf;

/**
 * Handles the type of interface between web server and PHP
 *
 * @package Pimf
 * @author Gjero Krsteski <gjero@krsteski.de>
 */
abstract class Sapi
{

  /**
   * Are we in a web environment?
   * @return boolean
   */
  public static function isWeb()
  {
    return self::isApache() || self::isIIS() || self::isCgi();
  }

  /**
   * Are we in a cli environment?
   * @return boolean
   */
  public static function isCli()
  {
    return PHP_SAPI === 'cli';
  }

  /**
   * Are we in a cgi environment?
   * @return boolean
   */
  public static function isCgi()
  {
    return PHP_SAPI === 'cgi-fcgi' || PHP_SAPI === 'cgi';
  }

  /**
   * Are we served through Apache[2]?
   * @return boolean
   */
  public static function isApache()
  {
    return PHP_SAPI === 'apache2handler' || PHP_SAPI === 'apachehandler';
  }

  /**
   * Are we served through IIS?
   * @return boolean
   */
  public static function isIIS()
  {
    return PHP_SAPI == 'isapi';
  }

  /**
   * @return bool
   */
  public static function isWindows()
  {
    return (boolean) (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN');
  }
}
 