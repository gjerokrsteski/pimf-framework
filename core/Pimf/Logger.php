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
 * @copyright Copyright (c)  Gjero Krsteski (http://krsteski.de)
 * @license http://krsteski.de/new-bsd-license New BSD License
 */

namespace Pimf;

/**
 * Logger with common logging options into a file.
 *
 * @package Pimf
 * @author Gjero Krsteski <gjero@krsteski.de>
 */
class Logger
{
  /**
   * @var resource
   */
  private $handle;

  /**
   * @var resource
   */
  private $warnHandle;

  /**
   * @var resource
   */
  private $errorHandle;

  /**
   * @var string
   */
  private $logfile;

  /**
   * @var string
   */
  private $storageDir;

  /**
   * @var bool
   */
  private $separator;

  /**
   * @param string $localeStorageDir Use better the local TMP dir or dir with mod 777.
   * @param null|string $logFileName
   * @param bool $trailingSeparator
   */
  public function __construct($localeStorageDir, $logFileName = '', $trailingSeparator = true)
  {
    $this->storageDir = (string)$localeStorageDir;
    $this->logfile    = (string)$logFileName;
    $this->separator  = (bool)$trailingSeparator;
  }

  /**
   * @throws \RuntimeException If something went wrong on creating the log dir and file.
   */
  public function init()
  {
    if(is_resource($this->errorHandle)
      && is_resource($this->handle)
      && is_resource($this->warnHandle)) {
      return;
    }

    if (!$this->logfile ) {
      $this->logfile = 'pimf-logs.txt';
    }

    if (!is_dir($this->storageDir)) {
      mkdir($this->storageDir, 0777);
    }

    if (!is_dir($this->storageDir)) {
      throw new \RuntimeException('log_dir must be a directory ' . $this->storageDir);
    }

    if (!is_writable($this->storageDir)) {
      throw new \RuntimeException('log_dir is not writable ' . $this->storageDir);
    }

    if (true === $this->separator) {
      $this->storageDir = rtrim(realpath($this->storageDir), '\\/') . DIRECTORY_SEPARATOR;
    }

    $this->handle = fopen($this->storageDir . $this->logfile, "at+");

    if ($this->handle === false) {
      throw new \RuntimeException("failed to obtain a handle to log file '" . $this->storageDir . $this->logfile  . "'");
    }

    $warningLogFile   = $this->storageDir . "pimf-warnings.txt";
    $this->warnHandle = fopen($warningLogFile, "at+");

    if ($this->warnHandle === false) {
      throw new \RuntimeException("failed to obtain a handle to warning log file '" . $warningLogFile . "'");
    }

    $errorLogFile          = $this->storageDir . "pimf-errors.txt";
    $this->errorHandle = fopen($errorLogFile, "at+");

    if ($this->errorHandle === false) {
      throw new \RuntimeException("failed to obtain a handle to error log file '" . $errorLogFile . "'");
    }
  }

  /**
   * @param string $msg
   * @return Logger
   */
  public function debug($msg)
  {
    if ($this->iniGetBool('display_errors') === true) {
      $this->write((string)$msg, 'DEBUG');
    }

    return $this;
  }

  /**
   * @param string $msg
   * @return Logger
   */
  public function warn($msg)
  {
    if ($this->iniGetBool('display_errors') === true) {
      $this->write((string)$msg, 'WARNING');
    }

    return $this;
  }

  /**
   * @param string $msg
   * @return Logger
   */
  public function error($msg)
  {
    $this->write((string)$msg, 'ERROR');

    return $this;
  }

  /**
   * @param string $msg
   * @return Logger
   */
  public function info($msg)
  {
    if ($this->iniGetBool('display_errors') === true) {
      $this->write((string)$msg, 'INFO');
    }

    return $this;
  }

  /**
   * @param $msg
   * @param string $severity
   * @throws \RuntimeException
   */
  protected function write($msg, $severity = 'DEBUG')
  {
    $msg = $this->format($msg, $severity);

    // if severity is WARNING then write to warning file
    if ($severity == 'WARNING') {
      if ($this->warnHandle !== false) {
        fwrite($this->warnHandle, $msg);
      }
    } // if severity is ERROR then write to error file
    else if ($severity == 'ERROR') {
      if ($this->errorHandle !== false) {
        fwrite($this->errorHandle, $msg);
      }
    } else if ($this->handle !== false) {
      if (fwrite($this->handle, $msg) === false) {
        throw new \RuntimeException("There was an error writing to log file.");
      }
    }
  }

  public function __destruct()
  {
    if (is_resource($this->handle)
      && is_resource($this->warnHandle)
      && is_resource($this->errorHandle)) {

      if (fclose($this->handle) === false) {
        // Failure to close the log file
        $this->write("Logger failed to close the handle to the log file", 'ERROR_SEVERITY');
      }

      fclose($this->warnHandle);
      fclose($this->errorHandle);
    }
  }

  /**
   * Formats the error message in representable manner.
   * @param $message
   * @param $severity
   * @return string
   */
  private function format($message, $severity)
  {
    $registry = new Registry();

    $remoteIP = $registry->env->getIp();
    $me       = $registry->env->getSelf();

    $msg = date("m-d-Y") . " " . date("G:i:s") . " ";
    $msg .= $registry->env->getIp();

    $IPLength       = strlen($remoteIP);
    $numWhitespaces = 15 - $IPLength;

    for ($i = 0; $i < $numWhitespaces; $i++) {
      $msg .= " ";
    }

    $msg .= " " . $severity . ": ";

    //get the file name
    $lastSlashIndex = strrpos($me, "/");
    $fileName       = $me;

    if ($lastSlashIndex !== false) {
      $fileName = substr($me, $lastSlashIndex + 1);
    }

    $msg .= $fileName . "\t";
    $msg .= $severity;
    $msg .= ": " . $message . "\r\n";

    return $msg;
  }

  /**
   * @param string $varname
   * @return bool
   */
  protected function iniGetBool($varname)
  {
    $varvalue = ini_get($varname);

    switch (strtolower($varvalue)) {
      case 'on':
      case 'yes':
      case 'true':
        return 'assert.active' !== $varname;
      case 'stdout':
      case 'stderr':
        return 'display_errors' === $varname;
      default:
        return (bool)(int)$varvalue;
    }
  }
}