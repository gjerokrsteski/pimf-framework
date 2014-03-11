<?php
class SessionCookieTest extends PHPUnit_Framework_TestCase
{
  public static function setUpBeforeClass()
  {
    \Pimf\Request::$cookieData = new \Pimf\Param();
  }


  # start testing


  public function testCreatingNewInstance()
  {
    new \Pimf\Session\Storages\Cookie();
  }

  public function testIfNoKayAtCookie()
  {
    $storage = new \Pimf\Session\Storages\Cookie();

    $this->assertNull( $storage->load('some-bad-id') );
  }

  public function testSavingTheSession()
    {
      $session['id'] = 'cool-secret-id';
      $config['lifetime'] = 60;
      $config['path'] = '/nfs/opst/';
      $config['domain'] = 'www.pimf-framework.de';

      $storage = new \Pimf\Session\Storages\Cookie();

      $this->assertNull( $storage->save($session, $config, 60) );
    }

    public function testDeleteKeyThatIsNotAtTheSession()
    {
      $storage = new \Pimf\Session\Storages\Cookie();

      $this->assertNull( $storage->delete('some-bad-key') );
    }

  public function testRefreshing()
  {
    $storage = new \Pimf\Session\Storages\Cookie();

    $session1 = $storage->fresh();
    $session2 = $storage->fresh();

    $this->assertNotEquals($session1, $session2);
  }
}
 