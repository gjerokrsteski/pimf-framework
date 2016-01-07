<?php
/**
 * Pimf
 *
 * @copyright Copyright (c)  Gjero Krsteski (http://krsteski.de)
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Pimf;

use Pimf\Util\Header;

/**
 * Defines the default exception handler if an exception is not caught within a try/catch block.
 * Execution will stop after the exception_handler is called.
 *
 * @package Pimf
 * @author  Gjero Krsteski <gjero@krsteski.de>
 */
class Error
{
    /**
     * Handle an exception and display the exception report.
     *
     * @param \Exception $exception
     * @param Logger     $logger
     * @param boolean    $exit
     */
    public static function exception($exception, $logger, $exit = true)
    {
        static::log($exception, $logger);

        ob_get_length() > 0 && ob_get_level() && ob_end_clean();

        if (Config::get('error.debug_info') === true) {
            echo static::format($exception, Sapi::isCli());
            if ($exit) {
                exit;
            }
        }

        if ($exception instanceof \Pimf\Controller\Exception
            || $exception instanceof \Pimf\Resolver\Exception
        ) {
            Event::first('404', array($exception));
            Header::sendNotFound(null, $exit);
        } else {
            Event::first('500', array($exception));
            Header::sendInternalServerError(null, $exit);
        }
    }

    /**
     * If detailed errors are enabled, just format the exception into
     * a simple error message and display it.
     *
     * @param \Exception $exception
     * @param boolean    $isCli
     *
     * @return string
     */
    public static function format($exception, $isCli = false)
    {
        if ($isCli === true) {
            return
                "+++ Untreated Exception +++" . PHP_EOL . "Message: " . $exception->getMessage() . PHP_EOL . "Location: " . $exception->getFile()
                . " on line " . $exception->getLine() . PHP_EOL . "Stack Trace: " . PHP_EOL . $exception->getTraceAsString() . PHP_EOL;
        }

        return "<html>
    <head>
      <style>
        pre { display: block;
            padding: 8.5px;
            margin: 0 0 9px;
            line-height: 18px;
            word-break: break-all;
            word-wrap: break-word;
            white-space: pre;
            white-space: pre-wrap;
            border: 1px solid #ccc;
            border: 1px solid rgba(0, 0, 0, 0.15);
            -webkit-border-radius: 4px;
            -moz-border-radius: 4px;
            border-radius: 6px;
            color: chartreuse;
            background-color: black;
        }
      </style>
    </head>
      <h2>Untreated Exception</h2>
      <h3>Message:</h3>
      <pre>" . $exception->getMessage() . "</pre>
      <h3>Location:</h3>
      <pre>" . $exception->getFile() . " on line " . $exception->getLine() . "</pre>
      <h3>Stack Trace:</h3>
      <pre>" . $exception->getTraceAsString() . "</pre></html>";
    }

    /**
     * Handle a native PHP error as an ErrorException.
     *
     * @param int       $code
     * @param string    $error
     * @param string    $file
     * @param int       $line
     * @param Logger    $logger
     * @param array|int $reporting which PHP errors are reported
     * @param boolean   $exit
     */
    public static function native($code, $error, $file, $line, $logger, $reporting, $exit = true)
    {
        if ($reporting === 0) {
            return;
        }

        // create an ErrorException for the PHP error
        $exception = new \ErrorException($error, $code, 0, $file, $line);

        if (in_array($code, (array)Config::get('error.ignore_levels'))) {
            return static::log($exception, $logger);
        }

        // display the ErrorException
        static::exception($exception, $logger, $exit);
    }

    /**
     * Handle the PHP shutdown event.
     *
     * @param Logger     $logger
     * @param array|null $error
     * @param bool       $exit
     */
    public static function shutdown($logger, $error, $exit = true)
    {
        // if a fatal error occurred
        if (!is_null($error)) {
            static::exception(new \ErrorException($error['message'], $error['type'], 0, $error['file'], $error['line']),
                $logger, $exit);
        }
    }

    /**
     * @param \Exception $exception
     * @param Logger     $logger
     */
    public static function log($exception, Logger $logger)
    {
        if (Config::get('error.log') === true) {
            $logger->error($exception->getMessage() . ' ' . $exception->getTraceAsString());
        }
    }
}
