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
 * Pimf_Util_Validator
 *
 * @package Pimf_Util
 * @author  Gjero Krsteski <gjero@krsteski.de>
 */
class Pimf_Util_Validator
{
  /**
   * @var bool
   */
  protected $valid = false;

  /**
   * @var bool
   */
  protected $duplicate = false;

  /**
   * @var array
   */
  protected $errors = array();

  /**
   * @var Pimf_Param
   */
  protected $attributes;

  /**
   * @param Pimf_Param $attributes
   */
  public function __construct(Pimf_Param $attributes)
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
   *     'age'     => 'digit|value[>,18]|value[=,33]',
   *   );
   *
   *  $validator = Pimf_Util_Validator::factory($attributes, $rules);
   *
   * </code>
   *
   * @param array $attributes
   * @param array|Pimf_Param $rules
   * @return Pimf_Util_Validator
   */
  public static function factory($attributes, array $rules)
  {
    if (! ($attributes instanceof Pimf_Param)){
      $attributes = new Pimf_Param((array)$attributes);
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
   * length functions on a field takes <, >, =, <=, and >= as operators.
   * @param string $field
   * @param string $operator
   * @param int $length
   * @return bool
   */
  public function length($field, $operator, $length)
  {
    $isValid    = false;
    $fieldValue = $this->attributes->get($field);

    if ($fieldValue === null) {
      $this->setError($field, __FUNCTION__);
      return $isValid;
    }

    $fieldValue = strlen(trim($fieldValue));

    switch ($operator) {
      case "<":
        if ($fieldValue < $length) {
          $isValid = true;
        }
        break;
      case ">":
        if ($fieldValue > $length) {
          $isValid = true;
        }

        break;
      case "=":
        if ($fieldValue == $length) {
          $isValid = true;
        }
        break;
      case "<=":
        if ($fieldValue <= $length) {
          $isValid = true;
        }
        break;
      case ">=":
        if ($fieldValue >= $length) {
          $isValid = true;
        }
        break;
      default:
        if ($fieldValue < $length) {
          $isValid = true;
        }
    }

    if ($isValid === false) {
      $this->setError($field, __FUNCTION__);
    }

    return $isValid;
  }

  /**
   * check to see if valid email address
   * @param string $field
   * @return bool
   */
  public function email($field)
  {
    $address = trim($this->attributes->get($field));

    if (preg_match('#^[a-zA-Z0-9_\.\-]+@[a-zA-Z0-9\-]+\.[a-zA-Z0-9\-\.]+$#', $address)) {
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
    $isValid     = false;

    if ($field1value === null || $field2value === null) {
      $this->setError($field1 . "|" . $field2, __FUNCTION__);
      return $isValid;
    }

    if ($caseInsensitive) {
      if (strcmp(strtolower($field1value), strtolower($field2value)) == 0) {
        $isValid = true;
      }
    } else {
      if (strcmp($field1value, $field2value) == 0) {
        $isValid = true;
      }
    }

    if ($isValid === false) {
      $this->setError($field1 . "|" . $field2, __FUNCTION__);
    }

    return $isValid;
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
    $fieldValue = $this->attributes->get($field);
    $isValid    = false;

    if ($fieldValue === null){
      $this->setError($field, __FUNCTION__);
      return $isValid;
    }

    $fieldValue = strlen(trim($fieldValue));

    if (!$inclusive) {
      if ($fieldValue < $max && $fieldValue > $min) {
        $isValid = true;
      }
    } else {
      if ($fieldValue <= $max && $fieldValue >= $min) {
        $isValid = true;
      }
    }

    if ($isValid === false) {
      $this->setError($field, __FUNCTION__);
    }

    return $isValid;
  }

  /**
   * check to see if there is punctuation
   * @param string $field
   * @return bool
   */
  public function punctuation($field)
  {
    $fieldValue = $this->attributes->get($field);

    if ($fieldValue === null) {
      $this->setError($field, __FUNCTION__);
      return false;
    }

    if (preg_match("#^[[:punct:]]+$#", $fieldValue)) {
      $this->setError($field, __FUNCTION__);
      return false;
    }

    return true;
  }

  /**
   * number value functions takes <, >, =, <=, and >= as operators.
   * @param string $field
   * @param string $operator
   * @param int $length
   * @return bool
   */
  public function value($field, $operator, $length)
  {
    $fieldValue = $this->attributes->get($field);
    $isValid    = false;

    if ($fieldValue === null) {
      $this->setError($field, __FUNCTION__);
      return $isValid;
    }

    switch ($operator) {
      case "<":
        if ($fieldValue < $length) {
          $isValid = true;
        }
        break;
      case ">":
        if ($fieldValue > $length) {
          $isValid = true;
        }
        break;
      case "=":
        if ($fieldValue == $length) {
          $isValid = true;
        }
        break;
      case "<=":
        if ($fieldValue <= $length) {
          $isValid = true;
        }
        break;
      case ">=":
        if ($fieldValue >= $length) {
          $isValid = true;
        }
        break;
      default:
        if ($fieldValue < $length) {
          $isValid = true;
        }
    }

    if ($isValid === false) {
      $this->setError($field, __FUNCTION__);
    }

    return $isValid;
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
    $fieldValue = $this->attributes->get($field);
    $isValid    = false;

    if ($fieldValue === null) {
      $this->setError($field, __FUNCTION__);
      return $isValid;
    }

    if (!$inclusive) {
      if ($fieldValue < $max && $fieldValue > $min) {
        $isValid = true;
      }
    } else {
      if ($fieldValue <= $max && $fieldValue >= $min) {
        $isValid = true;
      }
    }

    if ($isValid === false) {
      $this->setError($field, __FUNCTION__);
    }

    return $isValid;
  }

  /**
   * check if a field contains only decimal digit
   * @param string $field
   * @return bool
   */
  public function digit($field)
  {
    $fieldValue = $this->attributes->get($field);

    if ($fieldValue === null) {
      $this->setError($field, __FUNCTION__);
      return false;
    }

    if (ctype_digit((string)$fieldValue)) {
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
    $fieldValue = $this->attributes->get($field);

    if ($fieldValue === null) {
      $this->setError($field, __FUNCTION__);
      return false;
    }

    if (ctype_alpha((string)$fieldValue)) {
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
    $fieldValue = $this->attributes->get($field);

    if ($fieldValue === null) {
      $this->setError($field, __FUNCTION__);
      return false;
    }

    if (ctype_alnum((string)$fieldValue)) {
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
    $fieldValue = $this->attributes->get($field);

    if ($fieldValue === null) {
      $this->setError($field, __FUNCTION__);
      return false;
    }

    $month               = false;
    $day                 = false;
    $year                = false;
    $monthPos            = null;
    $dayPos              = null;
    $yearPos             = null;
    $monthNum            = null;
    $dayNum              = null;
    $yearNum             = null;
    $separator           = null;
    $separatorCount      = null;
    $fieldSeparatorCount = null;

    //determine the separator
    if (strstr($format, "-")) {
      $separator   = "-";
      $this->valid = true;
    } elseif (strstr($format, ".")) {
      $separator   = ".";
      $this->valid = true;
    } elseif (strstr($format, "/")) {
      $separator   = "/";
      $this->valid = true;
    } else {
      $this->valid = false;
    }

    if ($this->valid) {
      //determine the number of separators in $format and $field
      $separatorCount      = substr_count($format, $separator);
      $fieldSeparatorCount = substr_count($fieldValue, $separator);

      //if number of separators in $format and $field don't match return false
      if (!strstr($fieldValue, $separator) || $fieldSeparatorCount != $separatorCount) {
        $this->valid = false;
      } else {
        $this->valid = true;
      }
    }

    if ($this->valid) {
      //explode $format into $formatArray and get the index of the day, month, and year
      //then get the number of occurances of either m, d, or y
      $formatArray = explode($separator, $format);
      for ($i = 0; $i < sizeof($formatArray); $i++) {
        if (strstr($formatArray[$i], "m")) {
          $monthPos = $i;
          $monthNum = substr_count($formatArray[$i], "m");
        } elseif (strstr($formatArray[$i], "d")) {
          $dayPos = $i;
          $dayNum = substr_count($formatArray[$i], "d");
        } elseif (strstr($formatArray[$i], "y")) {
          $yearPos = $i;
          $yearNum = substr_count($formatArray[$i], "y");
        } else {
          $this->valid = false;
        }
      }

      //set whether $format uses day, month, year
      if ($monthNum) {
        $month = true;
      }

      if ($dayNum) {
        $day = true;
      }

      if ($yearNum) {
        $year = true;
      }

      //explode date field into $dateArray
      //check if the monthNum, dayNum, and yearNum match appropriately to the $dateArray
      $dateArray = explode($separator, $fieldValue);
      if ($month) {
        if (!preg_match("#^[0-9]{" . $monthNum . "}+$#i", $dateArray[$monthPos]) || $dateArray[$monthPos] > 12) {
          $this->valid = false;
        }
      }
      if ($day) {
        if (!preg_match("#^[0-9]{" . $dayNum . "}+$#i", $dateArray[$dayPos]) || $dateArray[$dayPos] > 31) {
          $this->valid = false;
        }
      }
      if ($year) {
        if (!preg_match("#^[0-9]{" . $yearNum . "}+$#i", $dateArray[$yearPos])) {
          $this->valid = false;
        }
      }
    }

    if ($this->valid) {
      $this->resetValid();
      return true;
    }

    $this->resetValid();
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
    if (!array_key_exists($field, $this->errors) || $this->errors[$field] !== $error && !is_array($this->errors[$field])) {
      $tmpArray     = array( $field => $error );
      $this->errors = array_merge_recursive($this->errors, $tmpArray);
      return;
    } elseif (is_array($this->errors[$field])) {
      foreach ($this->errors[$field] as $value) {
        if ($value == $error) {
          $this->duplicate = true;
        } else {
          $this->duplicate = false;
        }
      }
      if (!$this->duplicate) {
        $tmpArray     = array( $field => $error );
        $this->errors = array_merge_recursive($this->errors, $tmpArray);
      }
    } else {
      $this->duplicate = false;
    }
  }

  /**
   * @return array
   */
  public function getErrors()
  {
    return $this->errors;
  }

  /**
   * resets $valid to false
   */
  protected function resetValid()
  {
    $this->valid = false;
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
