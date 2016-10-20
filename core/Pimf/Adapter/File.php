<?php
/**
 * Pimf
 *
 * @copyright Copyright (c)  Gjero Krsteski (http://krsteski.de)
 * @license   http://opensource.org/licenses/MIT MIT License
 */

namespace Pimf\Adapter;

use Pimf\Contracts\Streamable;

/**
 * Class File binds a named resource, specified by filename, to a stream.
 *
 * @package Pimf\Adapter
 */
class File implements Streamable
{
    /**
     * Use better the local TMP dir or dir with mod 777
     * @var string
     */
    protected $storageDir;

    /**
     * @var string
     */
    protected $fileName;

    /**
     * Specifies the type of access you require to the stream
     * @see http://php.net/manual/en/function.fopen.php#mode
     * @var string
     */
    protected $mode;

    /**
     * @param string $storageDir
     * @param string $fileName
     * @param string $mode
     */
    public function __construct($storageDir, $fileName = "pimf-logs.txt", $mode = "at+")
    {
        $this->storageDir = $storageDir;
        $this->fileName = $fileName;
        $this->mode = $mode;
    }

    /* @inheritdoc */
    public function open()
    {
        if (!is_dir($this->storageDir)) {
            mkdir($this->storageDir, 0777);
        }

        $this->storageDir = rtrim(realpath($this->storageDir), '\\/') . DS;

        return fopen($this->storageDir . $this->fileName, $this->mode);
    }
}
