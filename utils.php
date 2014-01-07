<?php
/**
 * Short-cut for constructor method chaining.
 *
 * @param mixed $object
 * @return object|mixed
 */
function by($object)
{
  return $object;
}

/**
 * Return the value of the given item.
 * If the given item is a Closure the result of the Closure will be returned.
 *
 * @param mixed $value
 * @return mixed
 */
function value($value)
{
  return (is_callable($value) and !is_string($value)) ? call_user_func($value) : $value;
}

/**
 * Checks if a scalar value is FALSE, without content or only full of whitespaces.
 * For non-scalar values will evaluate if value is empty().
 *
 * @param $value
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
 * Tells you if it is a Directory Traversal Attack
 *
 * @param string $basepath
 * @param string $userpath
 *
 * @return bool
 */
function isEvilPath($basepath, $userpath)
{
  // check if strange things happening.
  if ( \Pimf\Util\String::contains(array('../', "..\\", '/..', '\..'), $userpath)) {
    return true;
  }

  $realBase     = realpath($basepath);
  $realUserPath = realpath($userpath);

  if ($realUserPath === false
    or strcmp($realUserPath, $realBase) !== 0
    or strpos($realUserPath, $realBase . DIRECTORY_SEPARATOR) !== 0) {
    return true;
  }

  return false;
}
