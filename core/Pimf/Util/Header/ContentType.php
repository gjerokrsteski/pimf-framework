<?php
/**
 * Util
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
 * @copyright Copyright (c)  Gjero Krsteski (http://krsteski.de)
 * @license http://krsteski.de/new-bsd-license New BSD License
 */
namespace Pimf\Util\Header;

/**
 * Manages a raw HTTP header ContentType sending.
 *
 * @package Util_Header
 * @author Gjero Krsteski <gjero@krsteski.de>
 */
abstract class ContentType extends ResponseStatus
{
  public static function contentTypeJson()
  {
    self::contentType('application/json; charset=utf-8');
  }

  public static function contentTypePdf()
  {
    self::contentType('application/pdf');
  }

  public static function contentTypeCsv()
  {
    self::contentType('text/csv');
  }

  public static function contentTypeTextPlain()
  {
    self::contentType('text/plain');
  }

  public static function contentTypeTextHTML()
  {
    self::contentType('text/html');
  }

  public static function contentTypeZip()
  {
    self::contentType('application/zip');
  }

  public static function contentTypeXZip()
  {
    self::contentType('application/x-zip');
  }

  public static function contentTypeMSWord()
  {
    self::contentType('application/msword');
  }

  public static function contentTypeOctetStream()
  {
    self::contentType('application/octet-stream');
  }

  public static function contentType($definition)
  {
    header('Content-Type: '.$definition, true);
  }
}
 