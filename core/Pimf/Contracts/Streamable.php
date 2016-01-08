<?php
/**
 * Pimf
 *
 * @copyright Copyright (c)  Gjero Krsteski (http://krsteski.de)
 * @license   http://opensource.org/licenses/MIT MIT License
 */

namespace Pimf\Contracts;

/**
 * A interface in its simplest definition, a stream is a resource object which exhibits streamable behavior.
 * Please find more here http://php.net/manual/en/intro.stream.php
 *
 * @package Contracts
 * @author  Gjero Krsteski <gjero@krsteski.de>
 */
interface Streamable
{

    /**
     * @return resource
     * @throws \RuntimeException If error on making connection
     */
    public function open();
}
