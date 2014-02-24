<?php
/**
 * Cli
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

namespace Pimf\Cli;

/**
 * Responsible for accessing I/O streams that allow access to PHP's own input and output streams.
 *
 * @package Cli
 * @author Gjero Krsteski <gjero@krsteski.de>
 */
class Io
{
  /**
   * Allow direct access to the corresponding input stream of the PHP process.
   * @param string $prompt
   * @param string $validation A regex pattern
   *
   * <code>
   *
   *  Have a look at the examples for $validation:
   *
   *  Regular Expression	| Will match...
   *  -------------------------------------------------------------
   *  .*                  | Not empty
   *  foo	                | The string "foo"
   *  ^foo	              | "foo" at the start of a string
   *  foo$	              | "foo" at the end of a string
   *  ^foo$	              | "foo" when it is alone on a string
   *  [abc]	              | a, b, or c
   *  [a-z]	              | Any lowercase letter
   *  [^A-Z]	            | Any character that is not a uppercase letter
   *  (gif|jpg)	          | Matches either "gif" or "jpeg"
   *  [a-z]+	            | One or more lowercase letters
   *  [0-9\.\-]	          | Аny number, dot, or minus sign
   *
   * </code>
   *
   * @return string
   */
  public static function read($prompt, $validation = "/.*/")
  {
    $value = '';
    static $fp;

    if (!$fp) {
      $fp = fopen("php://stdin", "r");
    }

    while (true) {

      echo Color::paint("Please enter a " . $prompt . ":\n");

      $value = fgets($fp, 1024);
      $value = substr($value, 0, -1);

      if (strlen($value) > 0 && preg_match($validation, $value)) {
        break;
      }

      echo Color::paint("Value format for " . $prompt . " is invalid!\n", 'red');
    }

    return $value;
  }
}
