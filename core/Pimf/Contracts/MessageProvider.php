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

namespace Pimf\Contracts;

/**
 * A simply interface to get messages for the instance.
 *
 * @package Contracts
 * @author Gjero Krsteski <gjero@krsteski.de>
 */
interface MessageProvider
{
	/**
	 * Get the messages for the instance.
	 * @return Message[]
	 */
	public function getMessages();

}