<?php
/**
 * Pimf
 *
 * @copyright Copyright (c)  Gjero Krsteski (http://krsteski.de)
 * @license   http://opensource.org/licenses/MIT MIT
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
     * @var Environment
     */
    public $env;

    /**
     * @param array             $getData
     * @param array             $postData
     * @param array             $cookieData
     * @param array             $cliData
     * @param array             $filesData
     * @param \Pimf\Environment $env
     */
    public function __construct(
        array $getData,
        array $postData = array(),
        array $cookieData = array(),
        array $cliData = array(),
        array $filesData = array(),
        \Pimf\Environment $env
    ) {

        static::$getData = new Param((array)self::stripSlashesIfMagicQuotes($getData));
        static::$postData = new Param((array)self::stripSlashesIfMagicQuotes($postData));
        static::$cookieData = new Param($cookieData);
        static::$cliData = new Param((array)self::stripSlashesIfMagicQuotes($cliData));
        static::$filesData = Util\Uploaded\Factory::get($filesData);
        $this->env = $env;
    }

    /**
     * For fetching body sent via PUT|DELETE|PATCH Http method.
     *
     * @param bool $asResource
     *
     * @return Param|resource|boolean
     */
    public function streamInput($asResource = false)
    {
        if (0 === strpos($this->env->getRequestHeader('CONTENT_TYPE'), 'application/x-www-form-urlencoded')
            && in_array($this->env->data()->get('REQUEST_METHOD', 'GET'), array('PUT', 'DELETE', 'PATCH'))
        ) {

            if ($asResource === true) {
                return $this->getContent($asResource);
            }

            $body = array();
            parse_str($this->getContent(), $body);

            return new Param($body);
        }

        return false;
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
        $strip = !$overrideStripSlashes ? $hasMagicQuotes : $overrideStripSlashes;

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
     * @throws \LogicException When using the resource twice times.
     */
    public function getContent($asResource = false)
    {
        static $content;

        if (false === $content || (true === $asResource && null !== $content)) {
            throw new \LogicException('resource can only be returned once');
        }

        if (true === $asResource) {
            $content = false;

            return fopen('php://input', 'rb');
        }

        if (null === $content) {
            $content = file_get_contents('php://input');
        }

        return $content;
    }
}
