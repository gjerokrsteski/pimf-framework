<?php
/**
 * Pimf
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
 * A simply interface because Representational State Transfer has become
 * the standard design architecture for developing web APIs.
 *
 * REST takes advantage of the HTTP request methods to layer itself
 * into the existing HTTP architecture.
 *
 * @package Pimf_Contracts
 * @author Gjero Krsteski <gjero@krsteski.de>
 */
interface Pimf_Contracts_RestFull
{
  /**
   * Used to create a new object on the server.
   * Used to modify an existing object on the server.
   * Used to remove an object on the server.
   * @return mixed
   */
  public function postAction();

  /**
   * Used for basic read requests to the server.
   * @return mixed
   */
  public function getAction();
}
