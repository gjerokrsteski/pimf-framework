<?php

namespace Pimf;

/**
 * Return the value of the given item.
 * If the given item is a Closure the result of the Closure will be returned.
 *
 * @param mixed $value
 *
 * @return mixed
 */
function value($value)
{
    return (is_callable($value) && !is_string($value)) ? call_user_func($value) : $value;
}

/**
 * Checks if a scalar value is FALSE, without content or only full of whitespaces.
 * For non-scalar values will evaluate if value is empty().
 *
 * @param string $value
 *
 * @return bool
 */
function is_empty($value)
{
    return !isset($value) || (is_scalar($value) ? (trim($value) === '') : empty($value));
}

/**
 * @param string $route controller/action
 * @param array  $params
 * @param null   $https
 * @param bool   $asset
 *
 * @return string
 */
function url($route = '', array $params = array(), $https = null, $asset = false)
{
    return \Pimf\Url::compute($route, $params, $https, $asset);
}

/**
 * Escape HTML entities in a string.
 *
 * @param string $value
 *
 * @return string
 */
function ent($value)
{
    return htmlentities($value, ENT_QUOTES, 'UTF-8', false);
}

/**
 * Relax the system and free up some memory
 */
function relax()
{
    clearstatcache();

    // free up the memory that could be lost
    // through possible roots that are already
    // recorded in the root buffer.
    if (function_exists('gc_collect_cycles')) {
        gc_collect_cycles();
    }
}
