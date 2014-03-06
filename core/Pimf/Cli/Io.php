<?php
/**
 * Cli
 *
 * @copyright Copyright (c)  Gjero Krsteski (http://krsteski.de)
 * @license   http://krsteski.de/new-bsd-license New BSD License
 */

namespace Pimf\Cli;

/**
 * Responsible for accessing I/O streams that allow access to PHP's own input and output streams.
 *
 * @package Cli
 * @author  Gjero Krsteski <gjero@krsteski.de>
 */
class Io
{
  /**
   * Allow direct access to the corresponding input stream of the PHP process.
   *
   * @param string $prompt
   * @param string $validation A regex pattern
   *
   * <code>
   *
   *  Have a look at the examples for $validation:
   *
   *  Regular Expression  | Will match...
   *  -------------------------------------------------------------
   *  .*                  | Not empty
   *  foo                  | The string "foo"
   *  ^foo                | "foo" at the start of a string
   *  foo$                | "foo" at the end of a string
   *  ^foo$                | "foo" when it is alone on a string
   *  [abc]                | a, b, or c
   *  [a-z]                | Any lowercase letter
   *  [^A-Z]              | Any character that is not a uppercase letter
   *  (gif|jpg)            | Matches either "gif" or "jpeg"
   *  [a-z]+              | One or more lowercase letters
   *  [0-9\.\-]            | Аny number, dot, or minus sign
   *
   * </code>
   *
   * @return string
   */
  public static function read($prompt, $validation = "/.*/")
  {
    $value = '';
    static $handle;

    if (!$handle) {
      $handle = fopen("php://stdin", "r");
    }

    while (true) {

      echo Color::paint("Please enter a " . $prompt . ":\n");

      $value = fgets($handle, 1024);
      $value = substr($value, 0, -1);

      if (strlen($value) > 0 && preg_match($validation, $value)) {
        break;
      }

      echo Color::paint("Value format for " . $prompt . " is invalid!\n", 'red');
    }

    return $value;
  }
}
