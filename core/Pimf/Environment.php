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
 * Server and execution environment information.
 *
 * @author Gjero Krsteski <gjero@krsteski.de>
 */
class Pimf_Environment
{
  /**
   * @var Pimf_Param
   */
  private $envData;

  /**
   * @param array $envData Like $_SERVER
   */
  public function __construct(array $envData)
  {
    $this->envData = new Pimf_Param($envData);
  }

  /**
   * @return Pimf_Param
   */
  public function getData()
  {
    return $this->envData;
  }

  /**
   * Is this an AJAX request?
   * @return bool
   */
  public function isAjax()
  {
    return $this->envData->getParam('X_REQUESTED_WITH') === 'XMLHttpRequest';
  }

  /**
   * Is the application running under HTTP protocol?
   * @return bool
   */
  public function isHttp()
  {
    return (bool) $this->envData->getParam('HTTP');
  }

  /**
   * Is the application running under HTTPS protocol?
   * @return bool
   */
  public function isHttps()
  {
    return $this->envData->getParam('HTTPS') === 'on';
  }

  /**
   * Name and revision of the information protocol
   * via which the page was requested; i.e. 'HTTP/1.0';
   * @return mixed|null
   */
  public function getProtocolInfo()
  {
    return $this->envData->getParam('SERVER_PROTOCOL');
  }

  /**
   * Get Content-Length
   * @return int
   */
  public function getContentLength()
  {
    return (int) $this->envData->getParam('CONTENT_LENGTH');
  }

  /**
   * Get Host
   * @return string
   */
  public function getHost()
  {
    if ($this->envData->getParam('HOST')) {

      if (strpos($this->envData->getParam('HOST'), ':') !== false) {
        $hostParts = explode(':', $this->envData->getParam('HOST'));
        return $hostParts[0];
      }

      return $this->envData->getParam('HOST');
    }

    return $this->envData->getParam('SERVER_NAME');
  }

  /**
   * Get Host with Port
   * @return string
   */
  public function getHostWithPort()
  {
    return sprintf('%s:%s', $this->getHost(), $this->getPort());
  }

  /**
   * Get Port
   * @return int
   */
  public function getPort()
  {
    return (int) $this->envData->getParam('SERVER_PORT');
  }

  /**
   * Filename of the currently executing script.
   * @return string|null
   */
  public function getSelf()
  {
    return $this->envData->getParam('PHP_SELF');
  }

  /**
   * Get Script Name (physical path).
   * @return string
   */
  public function getScriptName()
  {
    return $this->envData->getParam('SCRIPT_NAME');
  }

  /**
   * Get Path (physical path + virtual path)
   * @return string
   */
  public function getPath()
  {
    return $this->getScriptName() . $this->getPathInfo();
  }

  /**
   * Get Path Info (virtual path)
   * @return string
   */
  public function getPathInfo()
  {
    return $this->envData->getParam('PATH_INFO');
  }

  /**
   * Get remote IP
   * @return string
   */
  public function getIp()
  {
    if ($this->envData->getParam('X_FORWARDED_FOR')) {
      return $this->envData->getParam('X_FORWARDED_FOR');
    }

    if ($this->envData->getParam('CLIENT_IP')) {
      return $this->envData->getParam('CLIENT_IP');
    }

    if ($this->envData->getParam('SERVER_NAME')) {
      return gethostbyname($this->envData->getParam('SERVER_NAME'));
    }

    return $this->envData->getParam('REMOTE_ADDR');
  }

  /**
   * Get Referer - it cannot really be trusted.
   * @return string|null
   */
  public function getReferer()
  {
    return $this->envData->getParam('HTTP_REFERER');
  }

  /**
   * Get User Agent
   * @return string|null
   */
  public function getUserAgent()
  {
    if ($this->envData->getParam('USER_AGENT')) {
      return $this->envData->getParam('USER_AGENT');
    }

    if ($this->envData->getParam('HTTP_USER_AGENT')) {
      return $this->envData->getParam('HTTP_USER_AGENT');
    }

    return null;
  }

  /**
   * @return mixed|null
   */
  public function getServerName()
  {
    return $this->envData->getParam('SERVER_NAME');
  }

  /**
   * The REQUEST_URI
   * @return mixed|null
   */
  public function getUri()
  {
    return $this->envData->getParam('REQUEST_URI');
  }

  /**
   * Try to get a request header.
   * @param string $header
   * @return array
   */
  public function getRequestHeader($header)
  {
    $header = str_replace('-', '_', strtoupper($header));
    $value  = $this->envData->getParam('HTTP_' . $header);

    if (!$value) {
      $headers = $this->getRequestHeaders();
      $value   = !empty($headers[$header]) ? $headers[$header] : null;
    }

    return $value;
  }

  /**
   * Try to determine all request headers
   *
   * @return array
   */
  protected function getRequestHeaders()
  {
    $headers = array();

    if (function_exists('apache_request_headers')) {
      $tmpHeaders = apache_request_headers();

      foreach ($tmpHeaders as $key => $value) {
        $headers[str_replace('-', '_', strtoupper($key))] = $value;
      }
    } else {
      foreach ($this->envData as $key => $value) {
        if ('HTTP_' === substr($key, 0, 5)) {
          $headers[substr($key, 5)] = $value;
        }
      }
    }

    return $headers;
  }

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
}
