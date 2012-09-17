<?php
/**
 * Pimf_Models_Json
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
 * @copyright Copyright (c) 2010-2011 Gjero Krsteski (http://krsteski.de)
 * @license http://krsteski.de/new-bsd-license New BSD License
 */

/**
 * @package Pimf_Models_Json
 * @author Gjero Krsteski <gjero@krsteski.de>
 */
interface  Pimf_Models_Json_Interface
{
  /**
   * Prepares the properties-structure for the output.
   * @return mixed
   */
  public function prepare();
}
