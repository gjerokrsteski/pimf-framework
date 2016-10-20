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
    private $infoHandle;

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
     * Logger constructor.
     * @param Contracts\Streamable $infoHandle
     * @param Contracts\Streamable $warnHandle
     * @param Contracts\Streamable $errorHandle
     */
    public function __construct(
        Contracts\Streamable $infoHandle,
        Contracts\Streamable $warnHandle,
        Contracts\Streamable $errorHandle)
    {
        $this->infoHandle = $infoHandle->open();
        $this->warnHandle = $warnHandle->open();
        $this->errorHandle = $errorHandle->open();
    }

    /**
     * @throws \RuntimeException If something went wrong on creating the log dir and file.
     */
    public function init()
    {
        if (is_resource($this->errorHandle)
            && is_resource($this->infoHandle)
            && is_resource($this->warnHandle)
        ) {
            return;
        }

        if (!$this->errorHandle || !$this->infoHandle || !$this->warnHandle) {
            throw new \RuntimeException("failed to obtain a Streamable handle for logging");
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
            fwrite($this->infoHandle, $msg);

        }
    }

    public function __destruct()
    {
        if (is_resource($this->infoHandle)
            && is_resource($this->warnHandle)
            && is_resource($this->errorHandle)
        ) {

            if (fclose($this->infoHandle) === false) {
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
