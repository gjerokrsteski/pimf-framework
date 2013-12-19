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
 * @copyright Copyright (c)  Gjero Krsteski (http://krsteski.de)
 * @license http://krsteski.de/new-bsd-license New BSD License
 */

namespace Pimf\Session\Storages;
use Pimf\Session\Storages\Storage;

/**
 * @package Session_Storages
 * @author Gjero Krsteski <gjero@krsteski.de>
 */
class Memory extends Storage
{
	/**
	 * The session payload that will be returned by the storage.
	 * @var array
	 */
	public $session;

  /**
   * Load a session from storage by a given ID.
   * @param string $id
   * @return array
   */
  public function load($id)
	{
		return $this->session;
	}

  /**
   * Save a given session to storage.
   * @param array $session
   * @param array $config
   * @param bool $exists
   */
  public function save($session, $config, $exists)
	{
		//...
	}

  /**
   * Delete a session from storage by a given ID.
   * @param string $id
   */
  public function delete($id)
	{
		//...
	}
}