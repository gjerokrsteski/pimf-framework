<?php
/**
 * Pimf
 *
 * @copyright Copyright (c)  Gjero Krsteski (http://krsteski.de)
 * @license   http://opensource.org/licenses/MIT MIT License
 */

namespace Pimf\Contracts;

/**
 * A simply interface to get instance as an array.
 *
 * @package Contracts
 * @author  Gjero Krsteski <gjero@krsteski.de>
 */
interface Arrayable
{
  /**
   * Get the instance as an array.
   *
   * @return array
   */
  public function toArray();

}
