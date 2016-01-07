<?php
/**
 * Pimf
 *
 * @copyright Copyright (c)  Gjero Krsteski (http://krsteski.de)
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Pimf;

/**
 * Logger with common logging options into a file.
 *
 * @package Pimf
 * @author  Gjero Krsteski <gjero@krsteski.de>
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
    private $storageDir;

    /**
     * @var bool
     */
    private $separator;

    /**
     * @var string
     */
    private static $remoteIp;

    /**
     * @var string
     */
    private static $script;

    /**
     * @param string $remoteIp
     * @param string $script
     */
    public static function setup($remoteIp, $script)
    {
        self::$remoteIp = $remoteIp;
        self::$script = $script;
    }

    /**
     * @param string $localeStorageDir Use better the local TMP dir or dir with mod 777.
     * @param bool   $trailingSeparator
     */
    public function __construct($localeStorageDir, $trailingSeparator = true)
    {
        $this->storageDir = (string)$localeStorageDir;
        $this->separator = (bool)$trailingSeparator;
    }

    /**
     * @throws \RuntimeException If something went wrong on creating the log dir and file.
     */
    public function init()
    {
        if (is_resource($this->errorHandle)
            && is_resource($this->handle)
            && is_resource($this->warnHandle)
        ) {
            return;
        }

        if (!is_dir($this->storageDir)) {
            mkdir($this->storageDir, 0777);
        }

        if (true === $this->separator) {
            $this->storageDir = rtrim(realpath($this->storageDir), '\\/') . DS;
        }

        $this->handle = fopen($this->storageDir . "pimf-logs.txt", "at+");
        $this->warnHandle = fopen($this->storageDir . "pimf-warnings.txt", "at+");
        $this->errorHandle = fopen($this->storageDir . "pimf-errors.txt", "at+");

        if (!$this->errorHandle || !$this->handle || !$this->warnHandle) {
            throw new \RuntimeException("failed to obtain a handle to logger file");
        }
    }

    /**
     * @param string $msg
     *
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
     *
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
     *
     * @return Logger
     */
    public function error($msg)
    {
        $this->write((string)$msg, 'ERROR');

        return $this;
    }

    /**
     * @param string $msg
     *
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
     * @param string $msg
     * @param string $severity
     */
    protected function write($msg, $severity = 'DEBUG')
    {
        $msg = $this->format($msg, $severity);

        if ($severity == 'WARNING') {
            fwrite($this->warnHandle, $msg);
        } elseif ($severity == 'ERROR') {
            fwrite($this->errorHandle, $msg);
        } else {
            fwrite($this->handle, $msg);

        }
    }

    public function __destruct()
    {
        if (is_resource($this->handle)
            && is_resource($this->warnHandle)
            && is_resource($this->errorHandle)
        ) {

            if (fclose($this->handle) === false) {
                $this->error('Logger failed to close the handle to the log file');
            }

            fclose($this->warnHandle);
            fclose($this->errorHandle);
        }
    }

    /**
     * Formats the error message in representable manner.
     *
     * @param string $message
     * @param string $severity
     *
     * @return string
     */
    private function format($message, $severity)
    {
        $msg = date("m-d-Y") . " " . date("G:i:s") . " " . self::$remoteIp;

        $IPLength = strlen(self::$remoteIp);
        $numWhitespaces = 15 - $IPLength;

        for ($i = 0; $i < $numWhitespaces; $i++) {
            $msg .= " ";
        }

        $msg .= " " . $severity . ": ";

        $lastSlashIndex = strrpos(self::$script, "/");
        $fileName = self::$script;

        if ($lastSlashIndex !== false) {
            $fileName = substr(self::$script, $lastSlashIndex + 1);
        }

        $msg .= $fileName . "\t";
        $msg .= ": " . $message . "\r\n";

        return $msg;
    }

    /**
     * @param string $varname
     *
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
