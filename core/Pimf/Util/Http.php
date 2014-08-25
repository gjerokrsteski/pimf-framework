<?php
/**
 * Util
 *
 * @copyright Copyright (c)  Gjero Krsteski (http://krsteski.de)
 * @license   http://krsteski.de/new-bsd-license New BSD License
 */

namespace Pimf\Util;

/**
 * Manages a raw HTTP header sending.
 *
 * @package Util
 * @author  Gjero Krsteski <gjero@krsteski.de>
 */
class Http
{
    /**
     * @return array of HTTP method strings
     */
    public static function safeMethods()
    {
        return array ('HEAD', 'GET', 'OPTIONS', 'TRACE');
    }

    /**
     * @return bool
     *
     * @param string HTTP method
     */
    public static function isSafeMethod($method)
    {
        return in_array($method, self::safeMethods());
    }

    /**
     * @return bool
     *
     * @param string HTTP method
     */
    public static function isUnsafeMethod($method)
    {
        return !in_array($method, self::safeMethods());
    }

    /**
     * Service with the same parameters called twice, he should leave the same state.
     *
     * @return array list of (always) idempotent HTTP methods
     */
    public static function idempotentMethods()
    {
        return array ('HEAD', 'GET', 'PUT', 'DELETE', 'OPTIONS', 'TRACE', 'PATCH');
    }

    /**
     * @return bool
     *
     * @param string HTTP method
     */
    public static function isIdempotent($method)
    {
        return in_array($method, self::idempotentMethods());
    }

    /**
     * @return bool
     *
     * @param string HTTP method
     */
    public static function isNotIdempotent($method)
    {
        return !in_array($method, self::idempotentMethods());
    }

    /**
     * @return array of HTTP method strings
     */
    public static function canHaveBody()
    {
        return array ('POST', 'PUT', 'PATCH', 'OPTIONS');
    }
}