<?php
/**
 * Pimf
 *
 * PHP Version 5
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

namespace Pimf;
use Pimf\Registry, Pimf\Environment, Pimf\Header;

/**
 * Defines the default exception handler if an exception is not caught within a try/catch block.
 * Execution will stop after the exception_handler is called.
 *
 * @package Pimf
 * @author Gjero Krsteski <gjero@krsteski.de>
 */
class Error
{
  /**
   * Handle an exception and display the exception report.
   * @param Exception $exception
   */
  public static function exception(\Exception $exception)
  {
    static::log($exception);

    ob_get_level() and ob_end_clean();

    // if detailed errors are enabled, just format the exception into
    // a simple error message and display it.
    $conf = Registry::get('conf');
    $msg  = null;

    if (isset($conf['error']['debug_info']) && $conf['error']['debug_info'] === true) {
      if (Environment::isCli()) {
        $msg = "+++ Untreated Exception +++".PHP_EOL.
        "Message: " . $exception->getMessage() .PHP_EOL.
        "Location: " . $exception->getFile() . " on line " . $exception->getLine() .PHP_EOL.
        "Stack Trace: " .PHP_EOL. $exception->getTraceAsString() .PHP_EOL;
      } else {
        $msg =
          "<html><h2>Untreated Exception</h2>
          <h3>Message:</h3>
          <pre>" . $exception->getMessage() . "</pre>
          <h3>Location:</h3>
          <pre>" . $exception->getFile() . " on line " . $exception->getLine() . "</pre>
          <h3>Stack Trace:</h3>
          <pre>" . $exception->getTraceAsString() . "</pre></html>";
      }
      die($msg);
    }

    Header::clear();
    Header::sendInternalServerError($msg);
    exit(1);
  }

  /**
   * Handle a native PHP error as an ErrorException.
   * @param int $code
   * @param string $error
   * @param string $file
   * @param int $line
   */
  public static function native($code, $error, $file, $line)
  {
    if (error_reporting() === 0) {
      return;
    }

    // create an ErrorException for the PHP error
    $exception = new \ErrorException($error, $code, 0, $file, $line);

    $conf = Registry::get('conf');

    if (in_array($code, (array)$conf['error']['ignore_levels'])) {
      return static::log($exception);
    }

    // display the ErrorException
    static::exception($exception);
  }

  /**
   * Handle the PHP shutdown event.
   */
  public static function shutdown()
  {
    // if a fatal error occurred
    $error = error_get_last();

    if (!is_null($error)) {
      extract($error, EXTR_SKIP);
      static::exception(new \ErrorException($message, $type, 0, $file, $line));
    }
  }

  /**
   * Log an exception.
   * @param Exception $exception
   */
  public static function log(\Exception $exception)
  {
    $conf = Registry::get('conf');

    if (isset($conf['error']['log']) && $conf['error']['log'] === true) {
      Registry::get('logger')->error($exception->getMessage() .' '. $exception->getTraceAsString());
    }
  }
}
