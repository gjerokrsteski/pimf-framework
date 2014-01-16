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

use \Pimf\Util\Header, Pimf\Util\Json as UtilJson;

/**
 * Provides a simple interface around the HTTP an HTTPCache-friendly response generating.
 * Use this class to build and the current HTTP response before it is returned to the client.
 *
 * @package Pimf
 * @author Gjero Krsteski <gjero@krsteski.de>
 */
class Response
{  
  /**
   * The request method send by the client-browser.
   * @var string
   */
  protected $method = null;

  /**
   * If the response attempts to send any cached headers.
   * @var bool
   */
  protected $cached = false;

  /**
   * Type of the data that will be send to the client-browser.
   * @var string
   */
  protected $type = null;

  /**
   * @param string $requestMethod
   *
   * @throws \RuntimeException
   */
  public function __construct($requestMethod)
  {
    $this->method = '' . strtoupper($requestMethod);

    if(!in_array($this->method, array('POST', 'GET'), true)) {
      throw new \RuntimeException('no request-method given');
    }

    Header::clear();
  }

  public function asJSON()
  {
    $this->preventMultipleTypes();
    $this->type = __FUNCTION__;
    Header::contentTypeJson();
    return $this;
  }

  public function asHTML()
  {
    $this->preventMultipleTypes();
    $this->type = __FUNCTION__;
    Header::contentTypeTextHTML();
    return $this;
  }

  public function asPDF()
  {
    $this->preventMultipleTypes();
    $this->type = __FUNCTION__;
    Header::contentTypePdf();
    return $this;
  }

  public function asCSV()
  {
    $this->preventMultipleTypes();
    $this->type = __FUNCTION__;
    Header::contentTypeCsv();
    return $this;
  }

  public function asTEXT()
  {
    $this->preventMultipleTypes();
    $this->type = __FUNCTION__;
    Header::contentTypeTextPlain();
    return $this;
  }

  public function asZIP()
  {
    $this->preventMultipleTypes();
    $this->type = __FUNCTION__;
    Header::contentTypeZip();
    return $this;
  }

  public function asXZIP()
  {
    $this->preventMultipleTypes();
    $this->type = __FUNCTION__;
    Header::contentTypeXZip();
    return $this;
  }

  public function asMSWord()
  {
    $this->preventMultipleTypes();
    $this->type = __FUNCTION__;
    Header::contentTypeMSWord();
    return $this;
  }

  /**
   * Sends a download dialog to the browser.
   *
   * @param string $stream Can be file-path or string.
   * @param string $name   Name of the stream/file that should be shown to the browser.
   */
  public function sendStream($stream, $name)
  {
    Header::clear();
    Header::sendDownloadDialog($stream, $name);
  }

  /**
   * @param mixed $data
   * @param bool  $exit
   */
  public function send($data, $exit = true)
  {
    $body = $data;

    if($this->type === 'asJSON') {
      $body =  UtilJson::encode($data);
    } else if($data instanceof \Pimf\View) {
      $body = $data->render();
    }

    echo ''.$body; if ($exit) exit(0);
  }

  /**
   * @throws \RuntimeException
   */
  private function preventMultipleTypes()
  {
    if(!is_empty($this->type)) {
      Header::clear();
      throw new \RuntimeException('only one content-type can be send');
    }
  }
}
 
