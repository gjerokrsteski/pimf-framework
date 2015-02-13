<?php
/**
 * Pimf
 *
 * @copyright Copyright (c)  Gjero Krsteski (http://krsteski.de)
 * @license   http://opensource.org/licenses/MIT MIT License
 */

namespace Pimf\Adapter;

use Pimf\Contracts\Streamable;

/**
 * Class Socket manages Open Internet or Unix domain socket connection
 *
 * @package Pimf\Adapter
 */
class Socket implements Streamable
{

  /**
   * If you have compiled in OpenSSL support, you may prefix the hostname with either ssl://
   * or tls:// to use an SSL or TLS client connection over TCP/IP to connect to the remote host.
   *
   * @var string $hostname
   */
  protected $host;

  /**
   * The port number.
   *
   * @var int
   */
  protected $port;

  /**
   * @param $host
   * @param $port
   */
  public function __construct($host, $port)
  {
    $this->host = $host;
    $this->port = $port;
  }

  /**
   * @return resource
   * @throws \RuntimeException If error on making connection
   */
  public function open()
  {
    // system level error number and his error-message.
    $error = $message = 0;

    return fsockopen($this->host, $this->port, $error, $message);
  }
}
