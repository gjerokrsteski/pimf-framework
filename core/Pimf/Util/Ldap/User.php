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

namespace Pimf\Util\Ldap;

/**
 * Lightweight LDAP user object.
 *
 * @package Util
 * @author Gjero Krsteski <gjero@krsteski.de>
 */
class User
{
  /**
   * @var string Distinguished Name
   */
  protected $dname;

  /**
   * @var string Exactly the same as CN.
   */
  protected $name;

  /**
   * @var string
   */
  protected $firstname;

  /**
   * @var string
   */
  protected $lastname;

  /**
   * @var string Global Unique Identity Code
   */
  protected $objectguid;

  /**
   * @var array List of groups
   */
  protected $memberof;

  /**
   * @param       $dname
   * @param       $firstname
   * @param       $lastname
   * @param array $memberof
   * @param       $name
   * @param       $objectguid
   */
  public function __construct($dname, $firstname, $lastname, array $memberof, $name, $objectguid)
  {
    $this->dname      = ''.$dname;
    $this->firstname  = ''.$firstname;
    $this->lastname   = ''.$lastname;
    $this->memberof   = (array)$memberof;
    $this->name       = ''.$name;
    $this->objectguid = ''.$objectguid;
  }

  /**
   * @param array $user
   *
   * @return \Pimf\Util\Ldap\User
   */
  public static function factory(array $user)
  {
    return new self(
      $user['dn'],
      $user['givenname'][0],
      $user['sn'][0],
      isset($user['memberof']) ? $user['memberof'] : array( 'count' => 0 ),
      $user['cn'][0],
      $user['objectguid'][0]
    );
  }

  /**
   * @codeCoverageIgnore
   * @return string
   */
  public function getDname()
  {
    return $this->dname;
  }

  /**
   * @codeCoverageIgnore
   * @return string
   */
  public function getFirstname()
  {
    return $this->firstname;
  }

  /**
   * @codeCoverageIgnore
   * @return string
   */
  public function getLastname()
  {
    return $this->lastname;
  }

  /**
   * @codeCoverageIgnore
   * @return array
   */
  public function getMemberof()
  {
    return $this->memberof;
  }

  /**
   * @codeCoverageIgnore
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }

  /**
   * @codeCoverageIgnore
   * @return string
   */
  public function getObjectguid()
  {
    return $this->objectguid;
  }

}
 