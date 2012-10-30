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

  /**
 	 * Converts a value to enum type.
 	 *
 	 * This method checks if the value is of the specified enumerable type.
 	 * A value is a valid enumerable value if it is equal to the name of a constant
 	 * in the specified enumerable type (class).
 	 *
 	 * @param string $value The enumerable value to be checked.
 	 * @param string $className The enumerable class name (make sure it is included before calling this function).
   * @param boolean $returnEnumValue Sets if whether to enum-name or enum-value be returned.
 	 * @return string The value of the valid enumeration.
 	 * @throws InvalidArgumentException if the value is not a valid enumerable value
 	 */
   public static function ensureEnum($value, $className, $returnEnumValue = true)
   {
     static $types;

     if (!isset($types[$className])) {
       $types[$className] = new ReflectionClass($className);
     }

     if ($returnEnumValue === true) {
       if ($types[$className]->hasConstant($value)) {
         return $types[$className]->getConstant($value);
       }
     }

     if (in_array($value, array_values($types[$className]->getConstants()))) {
       foreach ($types[$className]->getConstants() as $enumName => $enumValue) {
         if ($enumValue == $value) {
           return $enumName;
         }
       }
     }

     throw new InvalidArgumentException(
       new Pimf_Util_Message(
         'Invalid enumerable value "%value". Please make sure it is among (%enum).',
         array('value' => $value, 'enum'=> implode(', ', $types[$className]->getConstants()))
       )
     );
   }

}
