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

namespace Pimf\Util;
use Pimf\Registry, Pimf\Environment;

/**
 * Manages a raw HTTP header sending.
 *
 * @package Util
 * @author Gjero Krsteski <gjero@krsteski.de>
 */
class Header
{
  protected static $cookies;

  /**
   * Removes previously set headers.
   */
  public static function clear()
  {
    if (!headers_sent() && error_get_last() == null) {
      header_remove();
    }
  }

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

  /**
   * @param int $code HTTP response code
   * @param string $status The header string which will be used to figure out the HTTP status code to send.
   * @param bool $replace Whether the header should replace a previous similar header.
   */
  public static function send($code, $status, $replace = true)
  {
    header(''.Registry::get('env')->getProtocolInfo().' ' . $code . ' ' . $status, $replace, $code);
  }

  public static function sendXFrameDeny()
  {
    header('X-Frame-Options: DENY');
  }

  public static function sendXFrameSameOrigin()
  {
    header('X-Frame-Options: SAMEORIGIN');
  }

  public static function sendContinue()
  {
    self::send(100, 'Continue');
  }

  public static function sendProcessing()
  {
    self::send(102, 'Processing');
  }

  public static function sendOK()
  {
    self::send(200, 'OK');
  }

  public static function sendCreated()
  {
    self::send(201, 'Created');
  }

  public static function sendAccepted()
  {
    self::send(202, 'Accepted');
  }

  public static function sendNoAuthInfo()
  {
    self::send(203, 'Non-Authoritative Information');
  }

  public static function sendNoContent()
  {
    self::send(204, 'No Content');
  }

  public static function sendMovedPermanently()
  {
    self::send(301, 'Moved Permanently');
  }

  public static function sendFound()
  {
    self::send(302, 'Found');
  }

  public function sendNotModified()
  {
    self::send(304, 'Not Modified');
  }

  public static function sendTemporaryRedirect()
  {
    self::send(307, 'Temporary Redirect');
  }

  public static function sendBadRequest()
  {
    self::send(400, 'Bad Request');
  }

  public static function sendUnauthorized()
  {
    self::send(401, 'Unauthorized');
  }

  public static function sendPaymentRequired()
  {
    self::send(402, 'Payment Required');
  }

  public static function sendForbidden()
  {
    self::send(403, 'Forbidden');
  }

  /**
   * @param string $msg
   */
  public static function sendNotFound($msg = '')
  {
    self::view(404, $msg);
  }

  public static function sendMethodNotAllowed()
  {
    self::send(405, 'Method Not Allowed');
  }

  public static function sendNotAcceptable()
  {
    self::send(406, 'Not Acceptable');
  }

  public static function sendProxyAuthRequired()
  {
    self::send(407, 'Proxy Authentication Required');
  }

  public static function sendRequestTimeout()
  {
    self::send(408, 'Request Timeout');
  }

  public static function sendUnsupportedMediaType()
  {
    self::send(415, 'Unsupported Media Type');
  }

  public static function sendLocked()
  {
    self::send(423, 'Locked');
  }

  /**
   * @param string $msg
   */
  public static function sendInternalServerError($msg = '')
  {
    self::view(500, $msg);
  }

  public static function sendServiceUnavailable()
  {
    self::send(503, 'Service Unavailable');
  }

  /**
   * @param string $url
   */
  public static function toLocation($url)
  {
    header('Location: '.$url);
    exit(1);
  }

  /**
   * @param int $code
   * @param string $status
   */
  protected static function view($code, $status)
  {
    if(Environment::isCli()) {
      die($status.PHP_EOL);
    }

    self::send($code, $status);

    $conf = Registry::get('conf');
    $root = dirname(dirname(dirname(dirname(__FILE__))));

    $appTpl  = str_replace('/', DIRECTORY_SEPARATOR, $root.'/app/'.$conf['app']['name'].'/_error/'.$code.'.php');
    $coreTpl = str_replace('/', DIRECTORY_SEPARATOR, $root.'/core/Pimf/_error/'.$code.'.php');

    if(file_exists($appTpl) && is_readable($appTpl)){
      die(include $appTpl);
    }

    die(include $coreTpl);
  }

  /**
   * Handles setting pages that are always to be revalidated for freshness by any cache.
   * @param int $last_modified Timestamp in seconds
   */
  public static function exitIfNotModifiedSince($last_modified)
  {
    if (self::isModified($last_modified)) {
      self::sendNotModified();
      exit(0);
    }

    $last_modified = gmdate('D, d M Y H:i:s', $last_modified) . ' GMT';
    header("Cache-Control: must-revalidate");
    header("Last Modified: $last_modified");
  }

  /**
   * Actual HTTP caching validation.
   * @param int $mtime In seconds
   * @param string $etag
   * @return bool
   */
  public static function isModified($mtime, $etag = '')
  {
    $env = Registry::get('env');

    $modified_since = strtotime(preg_replace('/;.*$/', '', $env->HTTP_IF_MODIFIED_SINCE));

    return !($modified_since >= $mtime || $env->HTTP_IF_NONE_MATCH == $etag);
  }

  /**
   * If you want to allow a page to be cached by shared proxies for one minute.
   * @param int $seconds Interval in seconds
   */
  public static function cacheNoValidate($seconds = 60)
  {
    $now    = time();
    $lmtime = gmdate('D, d M Y H:i:s', $now) . ' GMT';
    $extime = gmdate('D, d M Y H:i:s', $now + $seconds) . 'GMT';
    // backwards compatibility for HTTP/1.0 clients
    header("Last Modified: $lmtime");
    header("Expires: $extime");
    // HTTP/1.1 support
    header("Cache-Control: public,max-age=$seconds");
  }

  /**
   * If instead you have a page that has personalization on it
   * (say, for example, the splash page contains local news as well),
   * you can set a copy to be cached only by the browser.
   *
   * @param int $seconds Interval in seconds
   */
  public static function cacheBrowser($seconds = 60)
  {
    $now    = time();
    $lmtime = gmdate('D, d M Y H:i:s', $now) . ' GMT';
    $extime = gmdate('D, d M Y H:i:s', $now + $seconds) . ' GMT';
    // backwards compatibility for HTTP/1.0 clients
    header("Last Modified: $lmtime");
    header("Expires: $extime");
    // HTTP/1.1 support
    header("Cache-Control: private,max-age=$seconds,s-maxage=0");
  }


  /**
   * If you want to try as hard as possible to keep a page from being cached anywhere.
   */
  public static function cacheNone()
  {
    // backwards compatibility for HTTP/1.0 clients
    header("Expires: 0");
    header("Pragma: no-cache");
    // HTTP/1.1 support
    header("Cache-Control: no-cache,no-store,max-age=0,s-maxage=0,must-revalidate");
  }

  /**
   * Sends file as download-header through any firewall to the browsers like >=IE6 >=FF3.6, Safari, Chrome, Opera.
   *
   * @link http://reeg.junetz.de/DSP/node16.html
   * @link http://www.php.net/manual/de/function.header.php#88038
   *
   * @param string $fileOrString
   * @param string $fileName
   */
  public static function sendDownloadDialog($fileOrString, $fileName)
  {
    $disposition = (false !== strpos(Registry::get('env')->getUserAgent(), 'MSIE 5.5')) ? '' : 'attachment; ';

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