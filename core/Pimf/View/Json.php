<?php
/**
 * Pimf_View
 *
 * PHP Version 5
 *
 * A comprehensive collection of PHP utility classes and functions
 * that developers find themselves using regularly when writing web applications.
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
 * A view for smooth JSON communication.
 *
 * @link http://twig.sensiolabs.org/documentation
 * @package Pimf_View
 * @author Gjero Krsteski <gjero@krsteski.de>
 */
class Pimf_View_Json extends Pimf_View
{
  public function render()
  {
    Pimf_Util_Header::clear();
    Pimf_Util_Header::contentTypeJson();

    die(Pimf_Util_Json::encode($this->data->getArrayCopy()));
  }
}
