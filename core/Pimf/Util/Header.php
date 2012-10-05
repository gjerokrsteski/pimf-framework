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
 * Manages a raw HTTP header sending.
 *
 * @package Pimf_Util
 * @author Gjero Krsteski <gjero@krsteski.de>
 */
class Pimf_Util_Header
{
  /**
   * Removes previously set headers.
   */
  public static function clear()
  {
    if (!headers_sent() && error_get_last() == null) {
      header_remove();
    }
  }

  public static function useContentTypeJson()
  {
    header('Content-Type: application/json', true);
  }

  public static function useContentTypePdf()
  {
    header('Content-Type: application/pdf', true);
  }

  public static function useContentTypeCsv()
  {
    header('Content-Type: text/csv', true);
  }

  public static function useContentTypeTextPlain()
  {
    header('Content-Type: text/plain', true);
  }

  public static function useContentTypeZip()
  {
    header('Content-Type: application/zip', true);
  }

  public static function useContentTypeXZip()
  {
    header('Content-Type: application/x-zip', true);
  }

  public static function useContentTypeMSWord()
  {
    header('Content-Type: application/msword', true);
  }

  public static function useContentTypeOctetStream()
  {
    header('Content-Type: application/octet-stream', true);
  }

  /**
   * Sends file as header through any firewall and browser - IE6, IE7, IE8, IE9, FF3.6, FF11, Safari, Chrome, Opera.
   * @link http://reeg.junetz.de/DSP/node16.html
   * @link http://www.php.net/manual/de/function.header.php#88038
   * @param string $fileOrString
   * @param string $fileName
   */
  public static function sendDownloadDialog($fileOrString, $fileName)
  {
    $registry = new Pimf_Registry();

    $disposition = (false !== strpos($registry->env->getUserAgent(), 'MSIE 5.5')) ? '' : 'attachment; ';

    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: private", false);
    header("Content-Disposition: " . $disposition . "filename=" . $fileName . ";");

    if (is_file($fileOrString)) {
      readfile($fileOrString);
    } else {
      echo $fileOrString;
    }
    exit(0);
  }
}
