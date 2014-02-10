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

namespace Pimf\Util;

/**
 * Validator
 *
 * @package Util
 * @author  Gjero Krsteski <gjero@krsteski.de>
 */
class Validator
{
  /**
   * @var bool
   */
  protected $valid = false;

  /**
   * @var array
   */
  protected $errors = array();

  /**
   * @var \Pimf\Param
   */
  protected $attributes;

  /**
   * @param \Pimf\Param $attributes
   */
  public function __construct(\Pimf\Param $attributes)
  {
    $this->attributes = $attributes;
  }

  /**
   * <code>
   *  $attributes = array(
   *    'fname'    => 'conan',
   *    'age'      => 33,
   *   );
   *
   *   $rules = array(
   *     'fname'   => 'alpha|length[>,0]|lengthBetween[1,9]',
   *     'age'     => 'digit|value[>,18]|value[==,33]',
   *   );
   *
   *  $validator = Validator::factory($attributes, $rules);
   *
   * </code>
   *
   * @param array $attributes
   * @param array|\Pimf\Param $rules
   * @return Validator
   */
  public static function factory($attributes, array $rules)
  {
    if (! ($attributes instanceof \Pimf\Param)){
      $attributes = new \Pimf\Param((array)$attributes);
    }

    $validator = new self($attributes);

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

  /**
   * check to see if valid email address
   * @param string $field
   * @return bool
   */
  public function email($field)
  {
    if (filter_var(trim($this->attributes->get($field)), FILTER_VALIDATE_EMAIL) !== false) {
      return true;
    }

    $this->setError($field, __FUNCTION__);
    return false;
  }

  /**
   * Check is a valid IP.
   * @param $field
   * @return bool
   */
  public function ip($field)
 	{
    if (filter_var(trim($this->attributes->get($field)), FILTER_VALIDATE_IP) !== false) {
      return true;
    }

    $this->setError($field, __FUNCTION__);
    return false;
 	}

  /**
   * Check is a valid URL.
   * @param $field
   * @return bool
   */
  public function url($field)
 	{
    if (filter_var(trim($this->attributes->get($field)), FILTER_VALIDATE_URL) !== false) {
      return true;
    }

    $this->setError($field, __FUNCTION__);
    return false;
 	}

  /**
   * check to see if two fields are equal.
   * @param string $field1
   * @param string $field2
   * @param bool $caseInsensitive
   * @return bool
   */
  public function compare($field1, $field2, $caseInsensitive = false)
  {
    $field1value = $this->attributes->get($field1);
    $field2value = $this->attributes->get($field2);

    $valid = (strcmp($field1value, $field2value) == 0);

    if ($caseInsensitive) {
      $valid = (strcmp(strtolower($field1value), strtolower($field2value)) == 0);
    }

    if ($valid === false) {
      $this->setError($field1 . "|" . $field2, __FUNCTION__);
    }

    return $valid;
  }

  /**
   * check to see if the length of a field is between two numbers
   * @param string $field
   * @param int $min
   * @param int $max
   * @param bool $inclusive
   * @return bool
   */
  public function lengthBetween($field, $min, $max, $inclusive = false)
  {
    $fieldValue = strlen(trim($this->get($field)));

    $valid = ($fieldValue <= $max && $fieldValue >= $min);

    if (!$inclusive) {
      $valid = ($fieldValue < $max && $fieldValue > $min);
    }

    if ($valid === false) {
      $this->setError($field, __FUNCTION__);
    }

    return $valid;
  }

  /**
   * check to see if there is punctuation
   * @param string $field
   * @return bool
   */
  public function punctuation($field)
  {
    if (preg_match("/[^\w\s\p{P}]/", ''.$this->get($field)) > 0) {
      $this->setError($field, __FUNCTION__);
      return false;
    }

    return true;
  }

  /**
   * length functions on a field takes <, >, =, <=, and >= as operators.
   * @param string $field
   * @param string $operator
   * @param int $length
   * @return bool
   */
  public function length($field, $operator, $length)
  {
    return $this->middleware($field, strlen(trim($this->get($field))), $operator, $length);
  }

  /**
   * Number value functions takes <, >, =, <=, and >= as operators.
   *
   * @param string $field
   * @param string $operator
   * @param string|int $value
   * @return bool
   */
  public function value($field, $operator, $value)
  {
    return $this->middleware($field, $this->get($field), $operator, $value);
  }

  /**
   * @param string $fieldName
   * @param string $comparing
   * @param string $operator
   * @param string|integer $expecting
   *
   * @return bool
   */
  protected function middleware($fieldName, $comparing, $operator, $expecting)
  {
    $valid = false;

    if(in_array($operator, array("<", ">", "==", "<=", ">="), true)) {
      $func = create_function('$a,$b', 'return ($a '.''.$operator.' $b);');
      $valid = $func($comparing, $expecting);
    }

    if ($valid === false) {
      $this->setError($fieldName, $operator);
    }

    return $valid;
  }

  /**
   * check if a number value is between $max and $min
   * @param string $field
   * @param int $min
   * @param int $max
   * @param bool $inclusive
   * @return bool
   */
  public function valueBetween($field, $min, $max, $inclusive = false)
  {
    $fieldValue = $this->get($field);

    $valid = ($fieldValue <= $max && $fieldValue >= $min);

    if (!$inclusive) {
      $valid = ($fieldValue < $max && $fieldValue > $min);
    }

    if ($valid === false) {
      $this->setError($field, __FUNCTION__);
    }

    return $valid;
  }

  /**
   * check if a field contains only decimal digit
   * @param string $field
   * @return bool
   */
  public function digit($field)
  {
    if (ctype_digit((string)$this->get($field))) {
      return true;
    }

    $this->setError($field, __FUNCTION__);
    return false;
  }


  /**
   * check if a field contains only alphabetic characters
   * @param string $field
   * @return bool
   */
  public function alpha($field)
  {
    if (ctype_alpha((string)$this->get($field))) {
      return true;
    }

    $this->setError($field, __FUNCTION__);
    return false;
  }

  /**
   * check if a field contains only alphanumeric characters
   * @param string $field
   * @return bool
   */
  public function alphaNumeric($field)
  {
    if (ctype_alnum((string)$this->get($field))) {
      return true;
    }

    $this->setError($field, __FUNCTION__);
    return false;
  }

  /**
   * Check if field is a date by specified format.
   *
   * acceptable separators are "/" "." "-"
   * acceptable formats use "m" for month, "d" for day, "y" for year
   *
   * date("date", "mm.dd.yyyy") will match a field called "date" containing 01-12.01-31.nnnn where n is any real number
   *
   * @param string $field
   * @param string $format
   * @return bool
   */
  public function date($field, $format)
  {
    $fieldValue = $this->get($field);

    try {
      $date = new \DateTime($fieldValue);

      if ($fieldValue === $date->format($format)) {
        $this->valid = false;
        return true;
      }

    } catch(\Exception $e) {
      // do nothing
    }

    $this->valid = false;
    $this->setError($field, __FUNCTION__);
    return false;
  }

  /**
   * @param string $field
   * @param int $error
   * @return void
   */
  protected function setError($field, $error)
  {
    $this->errors = array_merge_recursive($this->errors, array( $field => $error ));
  }

  /**
   * @param string $attribute
   *
   * @return mixed|null
   * @throws \OutOfBoundsException If attribute not at range
   */
  protected function get($attribute)
  {
    if(!$value= $this->attributes->get($attribute)) {
      throw new \OutOfBoundsException('no attribute with name "'.$attribute.'" set');
    }
    return $value;
  }

  /**
   * @return array
   */
  public function getErrors()
  {
    return $this->errors;
  }

  /**
   * A list of human readable messages.
   * @return array
   */
  public function getErrorMessages()
  {
    $messages = array();

    foreach ($this->getErrors() as $key => $value) {

      if (strstr($key, "|")) {
        $key = str_replace("|", " and ", $key);
      }

      if(is_array($value)) {
        $value = implode(' and ', $value);
      }

      $messages[] = "Error on field '$key' by '$value' check";
    }

    return $messages;
  }

  /**
   * @return bool
   */
  public function isValid()
  {
    return empty($this->errors);
  }
}
