<?php
/**
 * Util
 *
 * @copyright Copyright (c)  Gjero Krsteski (http://krsteski.de)
 * @license   http://opensource.org/licenses/MIT MIT License
 */

namespace Pimf\Util;

/**
 * Provides a simple abstraction to lock anything to a file lock.
 *
 * @package Util
 * @link    https://bugs.php.net/bug.php?id=39736
 * @author  Gjero Krsteski <gjero@krsteski.de>
 */
class Lock
{
    /**
     * @var string
     */
    private $file;

    /**
     * @var resource
     */
    private $handle;

    /**
     * @param  string $name The lock name
     */
    public function __construct($name)
    {
        $lockPath = sys_get_temp_dir();

        $this->file = sprintf(
            '%s/pimf.%s.%s.lock',
            $lockPath,
            preg_replace('/[^a-z0-9\._-]+/i', '-', $name),
            hash('sha256', $name)
        );
    }

    /**
     * Lock the resource
     *
     * @param  bool $blocking wait until the lock is released
     *
     * @return bool        Returns true if the lock was acquired, false otherwise
     * @throws \RuntimeException If the lock file could not be created or opened
     */
    public function lock($blocking = false)
    {
        if ($this->handle) {
            return true;
        }

        // start the silence for both userland and native PHP error handlers
        $errorLevel = error_reporting(0);
        set_error_handler('var_dump', 0);

        if (!$this->handle = fopen($this->file, 'r')) {
            if ($this->handle = fopen($this->file, 'x')) {
                chmod($this->file, 0444);
            } elseif (!$this->handle = fopen($this->file, 'r')) {
                usleep(100);
                $this->handle = fopen($this->file, 'r');
            }
        }

        restore_error_handler();
        error_reporting($errorLevel);

        if (!$this->handle) {
            $error = error_get_last();
            throw new \RuntimeException($error['message'], 0, null, $this->file);
        }

        if (!flock($this->handle, LOCK_EX | ($blocking ? 0 : LOCK_NB))) {
            fclose($this->handle);
            $this->handle = null;

            return false;
        }

        return true;
    }

    /**
     * Release the resource
     */
    public function release()
    {
        if ($this->handle) {
            flock($this->handle, LOCK_UN | LOCK_NB);
            fclose($this->handle);
            $this->handle = null;
        }
    }
}
