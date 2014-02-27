<?php
/**
 * Pimf
 * @copyright Copyright (c)  Gjero Krsteski (http://krsteski.de)
 * @license http://krsteski.de/new-bsd-license New BSD License
 */

namespace Pimf\Session\Storages;

/**
 * @package Session_Storages
 * @author Gjero Krsteski <gjero@krsteski.de>
 */
class Apc extends Storage {

	/**
	 * @var \Pimf\Cache\Storages\Apc
	 */
	private $apc;

  /**
   * @param \Pimf\Cache\Storages\Apc $apc
   */
  public function __construct(\Pimf\Cache\Storages\Apc $apc)
	{
		$this->apc = $apc;
	}

  /**
   * Load a session from storage by a given ID.
   * @param string $id
   * @return array|mixed|null
   */
  public function load($id)
	{
		return $this->apc->get($id);
	}

  /**
   * @param array $session
   * @param array $config
   * @param bool $exists
   */
  public function save($session, $config, $exists)
	{
		$this->apc->put($session['id'], $session, $config['lifetime']);
	}

  /**
   * @param string $id
   */
  public function delete($id)
	{
		$this->apc->forget($id);
	}

}