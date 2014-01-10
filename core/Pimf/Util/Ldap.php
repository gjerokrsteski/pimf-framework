<?php
/**
 * Util
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
 * @copyright Copyright (c)  Gjero Krsteski (http://krsteski.de)
 * @license http://krsteski.de/new-bsd-license New BSD License
 */

namespace Pimf\Util;
use Pimf\Registry;

/**
 * Wrapper for Lightweight Directory Access Protocol and for a access to "Directory Servers"
 *
 * For use please add the following to the end of the config.core.php file:
 *
 * <code>
 *
 * 'ldap' => array(
 *
 *    //Hostname of the domain controller
 *    'host' => 'dc',
 *
 *    // The domain name
 *    'domain' => 'example.com',
 *
 *    // Optionally require users to be in this group
 *    //'group' => 'AppUsers',
 *
 *    // Domain credentials the app should use to validate users
 *    // This user does not need any privileges - it's just used to connect to the DC.
 *    'user' => 'ldap-user_here',
 *    'password' => 'ldap-password-here',
 * ),
 *
 * </code>
 *
 * @link http://www.php.net/manual/en/intro.ldap.php
 * @package Util
 * @author Gjero Krsteski <gjero@krsteski.de>
 */
class Ldap
{
  /**
   * @var resource
   */
  protected $conn;

  public function __construct()
  {
    if (!function_exists('ldap_connect')) {
      throw new \RuntimeException(
        'LDAP-auth requires the php-ldap extension to be installed'
      );
    }
  }

  public function __destruct()
  {
    if (is_resource($this->conn)) {
      ldap_unbind($this->conn);
    }
  }

  /**
   * Get the current user of the application.
   * @param $token
   * @return object
   * @throws \RuntimeException
   */
  public function retrieve($token)
  {
    if (empty($token)) {
      throw new \RuntimeException('empty token given');
    }

    if (is_null($this->conn)) {

      $config = Registry::get('ldap');

      try {
        $this->connect($config['user'], $config['password']);
      } catch (\Exception $e) {
        throw new \RuntimeException('LDAP control account error: ' . ldap_error($this->conn));
      }
    }

    if ($user = $this->getUser($token)) {
      return $user;
    }

    throw new \RuntimeException('no user found for ' . $token);
  }

  /**
   * Attempt to log a user into the application.
   * @param string $username
   * @param string $password
   * @return bool|object
   * @throws \Exception
   */
  public function attempt($username, $password)
  {
    $config = Registry::get('ldap');

    try {
      return $this->login($username, $password, $config['group']);
    } catch (\RuntimeException $e) {
      return false;
    }
  }

  /**
   * @param string $user
   * @param string $password
   * @return bool
   * @throws \RuntimeException
   */
  protected function connect($user, $password)
  {
    $config = Registry::get('ldap');

    // guess base DN from domain
    if (!isset($config['basedn'])) {
      $length           = strrpos($config['domain'], '.');
      $config['basedn'] = sprintf(
        'dc=%s,dc=%s', substr($config['domain'], 0, $length), substr($config['domain'], $length + 1)
      );

      // override the basedn
      Registry::set('ldap', $config);
    }

    // connect to the controller
    if (!$this->conn = ldap_connect("ldap://{$config['host']}.{$config['domain']}")) {
      throw new \RuntimeException(
        "could not connect to LDAP host {$config['host']}.{$config['domain']}: " . ldap_error($this->conn)
      );
    }

    // required for Windows AD
    ldap_set_option($this->conn, LDAP_OPT_PROTOCOL_VERSION, 3);
    ldap_set_option($this->conn, LDAP_OPT_REFERRALS, 0);

    // try to authenticate
    if (!@ldap_bind($this->conn, "{$user}@{$config['domain']}", $password)) {
      throw new \RuntimeException(
        'could not bind to AD: ' . "{$user}@{$config['domain']}: " . ldap_error($this->conn)
      );
    }

    return true;
  }

  /**
   * @param string $user
   * @param string $password
   * @param null|string $group
   * @return object
   * @throws \RuntimeException
   */
  protected function login($user, $password, $group = null)
  {
    if (!$this->connect($user, $password)) {
      throw new \RuntimeException(
        'could not connect to LDAP: ' . ldap_error($this->conn)
      );
    }

    $config      = Registry::get('ldap');
    $groupObject = $this->getAccount($group, $config['basedn']);
    $userObject  = $this->getAccount($user, $config['basedn']);

    if ($group && !$this->checkGroup($userObject['dn'], $groupObject['dn'])) {
      throw new \RuntimeException('user is not part of the "' . $group . '" group.');
    }

    return $this->fetch($userObject);
  }

  /**
   * @param string $user
   * @return object Of stdClass
   * @throws \RuntimeException
   */
  protected function fetch($user)
  {
    if (!isset($user['cn'][0])) {
      throw new \RuntimeException('not a valid user object');
    }

    return (object)array(
      'dn'         => $user['dn'],
      'name'       => $user['cn'][0],
      'firstname'  => $user['givenname'][0],
      'lastname'   => $user['sn'][0],
      'objectguid' => $user['objectguid'][0],
      'memberof'   => isset($user['memberof']) ? $user['memberof'] : array( 'count' => 0 ),
    );
  }

  /**
   * Searches the LDAP tree for the specified account or group
   * @param string $account
   * @param string $basedn
   * @return array|null
   * @throws \RuntimeException
   */
  protected function getAccount($account, $basedn)
  {
    if (is_null($this->conn)) {
      throw new \RuntimeException('no LDAP connection bound');
    }

    $result = ldap_search(
      $this->conn, $basedn, "(samaccountname={$account})",
      array('dn', 'givenname', 'sn', 'cn', 'memberof', 'objectguid')
    );

    if ($result === false) {
      return null;
    }

    $entries = ldap_get_entries($this->conn, $result);

    if ($entries['count'] > 0) {
      return $entries[0];
    }
  }

  /**
   * Checks group membership of the user, searching
   * in the specified group and its children (recursively)
   * @param string $userDN
   * @param string $groupDN
   * @return bool
   * @throws \RuntimeException
   */
  public function checkGroup($userDN, $groupDN)
  {
    if (!$user = $this->getUser($userDN)) {
      throw new \RuntimeException('invalid user DN');
    }

    for ($i = 0; $i < $user->memberof['count']; $i++) {
      if ($groupDN == $user->memberof[$i]) {
        return true;
      }
    }

    return false;
  }

  /**
   * @param string $userDN
   * @return null|object
   * @throws \RuntimeException
   */
  public function getUser($userDN)
  {
    if (is_null($this->conn)) {
      throw new \RuntimeException('no LDAP connection bound');
    }

    $result = ldap_read($this->conn, $userDN, '(objectclass=*)');

    if ($result === false) {
      return null;
    }

    $entries = ldap_get_entries($this->conn, $result);

    if (!$entries['count']) {
      return null;
    }

    return $this->fetch($entries[0]);
  }
}

