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
 * A helper class that provides static methods to convert component property values to specific types.
 *
 * For example, a boolean-typed property setter method would be as follows:
 *
 * <code>
 * public function setPropertyName($value)
 * {
 *     $value = Pimf_Util_PropertyValue::ensureBoolean($value);
 *     // $value is now of boolean type
 * }
 * </code>
 *
 * <pre>
 * Properties can be of the following types with specific type conversion rules:
 *
 * boolean: string 'true' (case-insensitive) will be converted to true,
 *          string 'false' (case-insensitive) will be converted to false.
 * array:   string starting with '(' and ending with ')' will be considered as
 *          as an array expression and will be evaluated. Otherwise, an array
 *          with the value to be ensured is returned.
 * </pre>
 *
 * @package Pimf_Util
 * @author Gjero Krsteski <gjero@krsteski.de>
 */
class Pimf_Util_PropertyValue
{
	/**
	 * Converts a value to boolean type.
	 * Note, string like "yes", "on", "true" (case-insensitive) will be converted to true,
	 * string 'false', 'no', 'false' (case-insensitive) will be converted to false.
	 * If a string represents a non-zero number, it will be treated as true.
	 * @param mixed $value the value to be converted.
	 * @return boolean
	 */
	public static function ensureBoolean($value)
	{
		return filter_var($value, FILTER_VALIDATE_BOOLEAN);
	}

	/**
	 * Converts a value to array type. If the value is a string and it is
	 * in the form "[a,b,c]" then an array consisting of each of the elements
	 * will be returned. If the value is a string and it is not in this form
	 * then an array consisting of just the string will be returned. If the value
	 * is not a string then
	 * @param mixed $value the value to be converted.
	 * @return array
	 */
	public static function ensureArray($value)
	{
	    if (is_object($value)) {
	      // Gets the public properties of the given object.
	      return get_object_vars($value);
	    }
	
	    if ((string)$value === $value) {
	      $value = trim($value);
	      $len   = mb_strlen($value);
	
	      if ($len >= 2 && $value[0] == '[' && $value[$len - 1] == ']') {
	        $slag = str_replace(array('[',']'),'', $value);
	        return explode(',', $slag);
	      }
	
	      return $len > 0 ? array($value) : array();
	    }
	
	    return (array)$value;
	}

}
