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
}
