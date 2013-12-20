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
 * @param string $route like controller/action
 * @param array  $params
 * @param null   $https
 * @param bool   $asset
 *
 * @return string
 */
function url($route = '', array $params = array(), $https = null, $asset = false)
{
  $conf = \Pimf\Registry::get('conf');
  if($conf['app']['routeable'] === false) {
    list($controller, $action) = explode('/', $route);
    $params = array_merge(compact('controller', 'action'), $params);
    return \Pimf\Url::base().'?'.http_build_query($params);
  }

  $slug = implode('/', $params);
  if ($slug != '')  {
    $slug = '/' . $slug;
  }

  return \Pimf\Url::to($route, $https, $asset) . $slug;
}
