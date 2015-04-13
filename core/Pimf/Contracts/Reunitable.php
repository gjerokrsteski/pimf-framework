<?php
/**
 * Pimf
 *
 * @copyright Copyright (c)  Gjero Krsteski (http://krsteski.de)
 * @license   http://opensource.org/licenses/MIT MIT License
 */

namespace Pimf\Contracts;

/**
 * A simply interface to give the view-adapters ro re-unite the template an the variables.
 *
 * @package Contracts
 * @author  Gjero Krsteski <gjero@krsteski.de>
 */
interface Reunitable
{
    /**
     * Puts the template an the variables together.
     *
     * @throws \Exception
     * @return string
     */
    public function reunite();
}
