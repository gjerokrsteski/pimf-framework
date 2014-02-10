<?php
/**
 * Util
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
 * @copyright Copyright (c)  Gjero Krsteski (http://krsteski.de)
 * @license http://krsteski.de/new-bsd-license New BSD License
 */

namespace Pimf\Util\Validator;

/**
 * Validator Factory
 *
 * @package Util
 * @author  Gjero Krsteski <gjero@krsteski.de>
 */
abstract class Factory
{
  /**
   * @param array|\Pimf\Param $attributes
   * @param array $rules
   * @return \Pimf\Util\Validator
   */
  public static function get($attributes, array $rules)
  {
    if (! ($attributes instanceof \Pimf\Param)){
      $attributes = new \Pimf\Param((array)$attributes);
    }

    $validator = new \Pimf\Util\Validator($attributes);

    foreach ($rules as $key => $rule) {

      $checks = (is_string($rule)) ? explode('|', $rule) : $rule;

      foreach ($checks as $check) {

        $items      = explode('[', str_replace(']', '', $check));
        $method     = $items[0];
        $parameters = array_merge(array( $key ), (isset($items[1]) ? explode(',', $items[1]) : array()));

        call_user_func_array(array($validator, $method), $parameters);
      }
    }

    return $validator;
  }
}
 