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
  private $fileHandle;

  /**
   * @var resource
   */
  private $warningFileHandle;

  /**
   * @var resource
   */
  private $errorFileHandle;

  /**
   * @var string
   */
  private $logFileName;

  /**
   * @var string
   */
  private $localeStorageDir;

  /**
   * @var bool
   */
  private $trailingSeparator;

  /**
   * @param string $localeStorageDir Use better the local TMP dir or dir with mod 777.
   * @param null|string $logFileName
   * @param bool $trailingSeparator
   */
  public function __construct($localeStorageDir, $logFileName = '', $trailingSeparator = true)
  {
    $this->localeStorageDir  = (string)$localeStorageDir;
    $this->logFileName       = (string)$logFileName;
    $this->trailingSeparator = (bool)$trailingSeparator;
  }

  /**
   * @throws RuntimeException If something went wrong on creating the log dir and file.
   */
  public function init()
  {
    if(is_resource($this->errorFileHandle)
      && is_resource($this->fileHandle)
      && is_resource($this->warningFileHandle)) {
      return;
    }

    if (!$this->logFileName ) {
      $this->logFileName = 'pimf-logs.txt';
    }

    if (!is_dir($this->localeStorageDir)) {
      mkdir($this->localeStorageDir, 0777);
    }

    if (!is_dir($this->localeStorageDir)) {
      throw new \RuntimeException('log_dir must be a directory ' . $this->localeStorageDir);
    }

    if (!is_writable($this->localeStorageDir)) {
      throw new \RuntimeException('log_dir is not writable ' . $this->localeStorageDir);
    }

    if (true === $this->trailingSeparator) {
      $this->localeStorageDir = rtrim(realpath($this->localeStorageDir), '\\/') . DIRECTORY_SEPARATOR;
    }

    $this->fileHandle = fopen($this->localeStorageDir . $this->logFileName, "at+");

    if ($this->fileHandle === false) {
      throw new \RuntimeException("failed to obtain a handle to log file '" . $this->localeStorageDir . $this->logFileName  . "'");
    }

    $warningLogFile          = $this->localeStorageDir . "pimf-warnings.txt";
    $this->warningFileHandle = fopen($warningLogFile, "at+");

    if ($this->warningFileHandle === false) {
      throw new \RuntimeException("failed to obtain a handle to warning log file '" . $warningLogFile . "'");
    }

    $errorLogFile          = $this->localeStorageDir . "pimf-errors.txt";
    $this->errorFileHandle = fopen($errorLogFile, "at+");

    if ($this->errorFileHandle === false) {
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
   * @param $textMessage
   * @param string $severityLevel
   * @throws RuntimeException
   */
  protected function write($textMessage, $severityLevel = 'DEBUG')
  {
    $textMessage = $this->formatMessage($textMessage, $severityLevel);

    // if severity is WARNING then write to warning file
    if ($severityLevel == 'WARNING') {
      if ($this->warningFileHandle !== false) {
        fwrite($this->warningFileHandle, $textMessage);
      }
    } // if severity is ERROR then write to error file
    else if ($severityLevel == 'ERROR') {
      if ($this->errorFileHandle !== false) {
        fwrite($this->errorFileHandle, $textMessage);
      }
    } else if ($this->fileHandle !== false) {
      if (fwrite($this->fileHandle, $textMessage) === false) {
        throw new \RuntimeException("There was an error writing to log file.");
      }
    }
  }

  public function __destruct()
  {
    if (is_resource($this->fileHandle)
      && is_resource($this->warningFileHandle)
      && is_resource($this->errorFileHandle)) {

      if (fclose($this->fileHandle) === false) {
        // Failure to close the log file
        $this->write("Logger failed to close the handle to the log file", 'ERROR_SEVERITY');
      }

      fclose($this->warningFileHandle);
      fclose($this->errorFileHandle);
    }
  }

  /**
   * Formats the error message in representable manner.
   * @param $message
   * @param $severity
   * @return string
   */
  private function formatMessage($message, $severity)
  {
    $registry = new Registry();

    $REMOTEADDR = $registry->env->getIp();
    $PHPSELF    = $registry->env->getSelf();

    $msg = date("m-d-Y") . " " . date("G:i:s") . " ";
    $msg .= $registry->env->getIp();

    $IPLength       = strlen($REMOTEADDR);
    $numWhitespaces = 15 - $IPLength;

    for ($i = 0; $i < $numWhitespaces; $i++) {
      $msg .= " ";
    }

    $msg .= " " . $severity . ": ";

    //get the file name
    $lastSlashIndex = strrpos($PHPSELF, "/");
    $fileName       = $PHPSELF;

    if ($lastSlashIndex !== false) {
      $fileName = substr($PHPSELF, $lastSlashIndex + 1);
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