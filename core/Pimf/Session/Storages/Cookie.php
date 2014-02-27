<?php
/**
 * Pimf
 * @copyright Copyright (c)  Gjero Krsteski (http://krsteski.de)
 * @license http://krsteski.de/new-bsd-license New BSD License
 */

namespace Pimf\Session\Storages;
use Pimf\Util\Crypter, Pimf\Cookie as Crumb;

/**
 * @package Session_Storages
 * @author Gjero Krsteski <gjero@krsteski.de>
 */
class Cookie extends Storage
{
	/**
	 * The name of the cookie used to store the session payload.
	 * @var string
	 */
	const payload = 'session_payload';

	/**
	 * Load a session from storage by a given ID.
	 * @param  string  $id
	 * @return array
	 */
  public function load($id)
  {
    if (Crumb::has(static::payload)) {
      return unserialize(Crypter::decrypt(Crumb::get(static::payload)));
    }
  }

  /**
   * Save a given session to storage.
   * @param array $session
   * @param array $config
   * @param bool $exists
   */
  public function save($session, $config, $exists)
	{
    Crumb::put(static::payload, Crypter::encrypt(serialize($session)), $config['lifetime'], $config['path'], $config['domain']);
	}

  /**
   * @param string $id
   */
  public function delete($id)
	{
    Crumb::forget(static::payload);
	}
}