<?php
class LdapUserTest extends PHPUnit_Framework_TestCase
{
  public function testCreatingNewInstance()
  {
    new \Pimf\Util\Ldap\User('', '', '', array(), '', '');
  }

  public function testHappyFactory()
  {
    $user['dn'] = '';
    $user['givenname'][0] = '';
    $user['sn'][0] = '';
    $user['memberof'] = array('count' => 1);
    $user['cn'][0] = '';
    $user['objectguid'][0] = '';

    $this->assertInstanceOf(

      '\\Pimf\\Util\\Ldap\\User',

      \Pimf\Util\Ldap\User::factory($user)

    );

  }

  public function testFactoryIfNoMemberOfGroup()
  {
    $user['dn'] = '';
    $user['givenname'][0] = '';
    $user['sn'][0] = '';
    $user['cn'][0] = '';
    $user['objectguid'][0] = '';

    $this->assertInstanceOf(

      '\\Pimf\\Util\\Ldap\\User',

      $ldap_user = \Pimf\Util\Ldap\User::factory($user)

    );

    $this->assertEquals(array('count' => 0), $ldap_user->getMemberof());
  }
}
 