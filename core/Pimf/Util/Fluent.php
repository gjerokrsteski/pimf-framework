<?php
/**
 * Pimf_Util
 *
 * PHP Version 5
 *
 * A comprehensive collection of PHP utility classes and functions
 * that developers find themselves using regularly when writing web applications.
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
 * Creates ad-hoc object as data container, using fluent interface,
 * for any purpose you need. Implements tons of magic methods.
 *
 * <code>
 *    // Create a new fluent container with attributes
 *    $model = new Pimf_Util_Fluent(array('name' => 'Lammy'));
 *
 *    // or fluently set the value of a few attributes
 *    $model->name('Lammy')->age(25);
 *
 *    // or set the value of an attribute to null.
 *    $model->nullable();
 * </code>
 *
 * @package Pimf_Util
 * @author Gjero Krsteski <gjero@krsteski.de>
 */
class Pimf_Util_Fluent
{
  /**
   * @var ArrayObject
   */
  protected $attributes;

  /**
   * @param array $attributes
   */
  public function __construct(array $attributes = array())
  {
    $this->attributes = new ArrayObject($attributes, ArrayObject::ARRAY_AS_PROPS | ArrayObject::STD_PROP_LIST);
  }

  /**
   * @param string $index
   * @param null $defaultValue
   * @return mixed|null
   */
  protected function getAttribute($index, $defaultValue = null)
  {
    if($this->attributes->offsetExists($index)) {
      return $this->attributes->offsetGet($index);
    }

    return $defaultValue;
  }

  /**
   * Handle dynamic calls to the container to set attributes.
   * @param $method
   * @param $parameters
   * @return Pimf_Util_Fluent
   */
  public function __call($method, $parameters)
  {
    $this->$method = (count($parameters) > 0) ? $parameters[0] : null;

    return $this;
  }

  /**
   * Dynamically retrieve the value of an attribute.
   * @param $key
   * @return mixed
   */
  public function __get($key)
  {
    return $this->getAttribute($key);
  }

  /**
   * Dynamically set the value of an attribute.
   * @param $key
   * @param $value
   */
  public function __set($key, $value)
  {
    $this->attributes->offsetSet($key, $value);
  }

  /**
   * Dynamically check if an attribute is set.
   * @param $key
   * @return bool
   */
  public function __isset($key)
  {
    return $this->attributes->offsetExists($key);
  }

  /**
   * Dynamically unset an attribute.
   * @param $key
   */
  public function __unset($key)
  {
    $this->attributes->offsetUnset($key);
  }
}