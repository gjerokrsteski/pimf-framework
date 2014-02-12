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
use Pimf\Param;

/**
 * Server and execution environment information.
 *
 * @package Pimf
 * @author Gjero Krsteski <gjero@krsteski.de>
 */
class Environment extends Sapi
{
  /**
   * @var Param
   */
  private $envData;

  /**
   * @param array $envData Like $_SERVER
   */
  public function __construct(array $envData)
  {
    $this->envData = new Param($envData);
  }

  /**
   * @return Param
   */
  public function getData()
  {
    return $this->envData;
  }

  /**
   * @param $key
   * @return mixed|null
   */
  public function __get($key)
  {
    return $this->envData->get($key, null, false);
  }

  /**
   * Is this an AJAX request?
   * @return bool
   */
  public function isAjax()
  {
    return $this->X_REQUESTED_WITH === 'XMLHttpRequest';
  }

  /**
   * Is the application running under HTTP protocol?
   * @return bool
   */
  public function isHttp()
  {
    return (bool) $this->HTTP;
  }

  /**
   * Is the application running under HTTPS protocol?
   * @return bool
   */
  public function isHttps()
  {
    return $this->HTTPS === 'on';
  }

  /**
   * Name and revision of the information protocol
   * via which the page was requested; i.e. 'HTTP/1.0';
   * @return mixed|null
   */
  public function getProtocolInfo()
  {
    return $this->SERVER_PROTOCOL;
  }

  /**
   * Get Content-Length
   * @return int
   */
  public function getContentLength()
  {
    return (int) $this->CONTENT_LENGTH;
  }

  /**
   * Get Host
   * @return string
   */
  public function getHost()
  {
    if ($this->HOST) {

      if (strpos($this->HOST, ':') !== false) {
        $hostParts = explode(':', $this->HOST);
        return $hostParts[0];
      }

      return $this->HOST;
    }

    return $this->SERVER_NAME;
  }

  /**
   * Get Host with Port
   * @return string
   */
  public function getHostWithPort()
  {
    return ''.$this->getHost().':'.$this->getPort();
  }

  /**
   * Get Port
   * @return int
   */
  public function getPort()
  {
    return (int) $this->SERVER_PORT;
  }

  /**
   * Filename of the currently executing script.
   * @return string|null
   */
  public function getSelf()
  {
    return $this->PHP_SELF;
  }

  /**
   * Get Script Name (physical path).
   * @return string
   */
  public function getScriptName()
  {
    return $this->SCRIPT_NAME;
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
    return $this->PATH_INFO;
  }

  /**
   * Get remote IP
   * @return string
   */
  public function getIp()
  {
    if ($this->X_FORWARDED_FOR) {
      return $this->X_FORWARDED_FOR;
    }

    if ($this->CLIENT_IP) {
      return $this->CLIENT_IP;
    }

    if ($this->SERVER_NAME) {
      return gethostbyname($this->SERVER_NAME);
    }

    return $this->REMOTE_ADDR;
  }

  /**
   * Get Referer - it cannot really be trusted.
   * @return string|null
   */
  public function getReferer()
  {
    return $this->HTTP_REFERER;
  }

  /**
   * Get User Agent
   * @return string|null
   */
  public function getUserAgent()
  {
    if ($this->USER_AGENT) {
      return $this->USER_AGENT;
    }

    if ($this->HTTP_USER_AGENT) {
      return $this->HTTP_USER_AGENT;
    }

    return null;
  }

  /**
   * @return mixed|null
   */
  public function getServerName()
  {
    return $this->SERVER_NAME;
  }

  /**
   * The REQUEST_URI
   * @return mixed|null
   */
  public function getUri()
  {
    return $this->REQUEST_URI;
  }

  /**
   * Gives you the current page URL
   * @return string
   */
  public function getUrl()
  {
    $protocol = strpos(strtolower($this->getProtocolInfo()),'https') === false ? 'http' : 'https';

    return $protocol . '://' . $this->getHost();
  }

  /**
   * Try to get a request header.
   * @param string $header
   * @return array
   */
  public function getRequestHeader($header)
  {
    $header = str_replace('-', '_', strtoupper($header));
    $value  = $this->{'HTTP_' . $header};

    if (!$value) {
      $headers = $this->getRequestHeaders();
      $value   = !empty($headers[$header]) ? $headers[$header] : null;
    }

    return $value;
  }

  /**
   * Try to determine all request headers
   * @return array
   */
  public function getRequestHeaders()
  {
    $headers = array();

    foreach ($this->envData->getAll() as $key => $value) {
      if ('HTTP_' === substr($key, 0, 5)) {
        $headers[substr($key, 5)] = $value;
      }
    }

    return $headers;
  }

  /**
   * Which request method was used to access the page
   * @return string Lower case get|post|put|delete|head|options
   */
  public function getRequestMethod()
  {
    return strtolower($this->REQUEST_METHOD);
  }
}
