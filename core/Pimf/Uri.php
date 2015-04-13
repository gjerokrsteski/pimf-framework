<?php
/**
 * Pimf
 *
 * @copyright Copyright (c)  Gjero Krsteski (http://krsteski.de)
 * @license   http://opensource.org/licenses/MIT MIT
 */
namespace Pimf;

use Pimf\Util\Str as Str;

/**
 * URI
 *
 * @package Pimf
 * @author  Gjero Krsteski <gjero@krsteski.de>
 */
class Uri
{

    /**
     * @var string
     */
    private static $pathInfo;

    /**
     * @var string
     */
    private static $requestUri;

    /**
     * The URI for the current request.
     *
     * @var string
     */
    public static $uri;

    /**
     * The URI segments for the current request.
     *
     * @var array
     */
    public static $segments = array();

    /**
     * @param string $pathInfo
     * @param string $requestUri
     */
    public static function setup($pathInfo, $requestUri)
    {
        self::$pathInfo = $pathInfo;
        self::$requestUri = $requestUri;
    }

    /**
     * Get the full URI including the query string.
     *
     * @return string
     */
    public static function full()
    {
        return self::$requestUri;
    }

    /**
     * Get the URI for the current request.
     *
     * @return string
     */
    public static function current()
    {
        if (!is_null(static::$uri)) {
            return static::$uri;
        }

        //Format a given URI.
        $uri = trim(self::$pathInfo, '/') ?: '/';

        //Set the URI segments for the request.
        $segments = explode('/', trim($uri, '/'));
        static::$segments = array_diff($segments, array(''));

        return static::$uri = $uri;
    }

    /**
     * Determine if the current URI matches a given pattern.
     *
     * @param  string $pattern
     *
     * @return bool
     */
    public static function is($pattern)
    {
        return Str::is($pattern, static::current());
    }
}
