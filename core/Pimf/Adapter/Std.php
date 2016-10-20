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
 * Class Std will treat event streams unbuffered.
 *
 * @package Pimf\Adapter
 */
class Std implements Streamable
{
    /**
     * Opens stream to stdout
     */
    const OUT = "php://stdout";

    /**
     * Opens stream to stderr
     */
    const ERR = "php://stderr";

    /**
     * Type of stream
     * @var string
     */
    protected $type;

    /**
     * Std constructor.
     * @param string $type
     */
    public function __construct($type = self::OUT)
    {
        $this->type = $type;
    }

    /** @inheritdoc */
    public function open()
    {
        return fopen($this->type, "w");
    }
}
