<?php
/**
 * Pimf
 *
 * @copyright Copyright (c)  Gjero Krsteski (http://krsteski.de)
 * @license   http://opensource.org/licenses/MIT MIT License
 */
namespace Pimf;

/**
 * Request Manager - for controlled access to the global state of the world.
 *
 * @package Pimf
 * @author  Gjero Krsteski <gjero@krsteski.de>
 */
class Request
{
  /**
   * @var Param
   */
  public static $postData;

  /**
   * @var Param
   */
  public static $getData;

  /**
   * @var Param
   */
  public static $cookieData;

  /**
   * @var Param
   */
  public static $cliData;

  /**
   * @var Util\Uploaded
   */
  public static $filesData;

  /**
   * @var string
   */
  protected $content;

  /**
   * @var Param
   */
  public static $restData;

  /**
   * @param array $getData
   * @param array $postData
   * @param array $cookieData
   * @param array $cliData
   * @param array $filesData
   */
  public function __construct(
    array $getData,
    array $postData = array (),
    array $cookieData = array (),
    array $cliData = array (),
    array $filesData = array ()
  ) {

    static::$getData    = new Param((array)self::stripSlashesIfMagicQuotes($getData));
    static::$postData   = new Param((array)self::stripSlashesIfMagicQuotes($postData));
    static::$cookieData = new Param($cookieData);
    static::$cliData    = new Param((array)self::stripSlashesIfMagicQuotes($cliData));
    static::$filesData  = Util\Uploaded\Factory::get($filesData);
  }

  /**
   * @param Environment $env
   */
  public function fetchRestData(Environment $env)
  {
    if (0 === strpos($env->getRequestHeader('CONTENT_TYPE'), 'application/x-www-form-urlencoded')
      && in_array(strtoupper($env->getData()->get('REQUEST_METHOD', 'GET')), array('PUT', 'DELETE', 'PATCH'))
    ) {
      $data = array();
      parse_str($this->getContent(), $data);
      static::$restData = new Param($data);
    }
  }

  /**
   * HTTP GET variables.
   *
   * @return Param
   */
  public function fromGet()
  {
    return static::$getData;
  }

  /**
   * CLI arguments passed to script.
   *
   * @return Param
   */
  public function fromCli()
  {
    return static::$cliData;
  }

  /**
   * HTTP POST variables.
   *
   * @return Param
   */
  public function fromPost()
  {
    return static::$postData;
  }

  /**
   * HTTP Cookies.
   *
   * @return Param
   */
  public function fromCookie()
  {
    return static::$cookieData;
  }

  /**
   * Strip slashes from string or array
   *
   * @param      $rawData
   * @param null $overrideStripSlashes
   *
   * @return array|string
   */
  public static function stripSlashesIfMagicQuotes($rawData, $overrideStripSlashes = null)
  {
    $hasMagicQuotes = function_exists('get_magic_quotes_gpc') ? get_magic_quotes_gpc() : false;
    $strip          = !$overrideStripSlashes ? $hasMagicQuotes : $overrideStripSlashes;

    if ($strip) {
      return self::stripSlashes($rawData);
    }

    return $rawData;
  }

  /**
   * Strip slashes from string or array
   *
   * @param $rawData
   *
   * @return array|string
   */
  public static function stripSlashes($rawData)
  {
    return is_array($rawData)
      ? array_map(
        function ($value) {
          return \Pimf\Request::stripSlashes($value);
        },
        $rawData
      )
      : stripslashes($rawData);
  }

  /**
   * @param bool $asResource
   *
   * @return resource|string
   * @throws \LogicException
   */
  public function getContent($asResource = false)
  {
    if (false === $this->content || (true === $asResource && null !== $this->content)) {
      throw new \LogicException('can only be called once when using the resource return type');
    }

    if (true === $asResource) {
      $this->content = false;
      return fopen('php://input', 'rb');
    }

    if (null === $this->content) {
      $this->content = file_get_contents('php://input');
    }

    return $this->content;
  }
}
