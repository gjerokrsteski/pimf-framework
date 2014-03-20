<?php
class SessionFileTest extends PHPUnit_Framework_TestCase
{
  protected function getCache()
  {
    return $this->getMockBuilder('\Pimf\Cache\Storages\File')
      ->setConstructorArgs(array('/path/to/haven/'))
      ->setMethods(array('clean', 'save', 'delete'))
      ->getMock();
  }

  # start testing

  public function testCreatingNewInstance()
  {
    new \Pimf\Session\Storages\File('/path/to/haven/');
  }


  public function testGeyKeyThatIsNotAtTheSession()
  {
    $storage = new \Pimf\Session\Storages\File('some/bad/path/');
    $this->assertNull( $storage->load('some-bad-key') );
  }


  public function testSavingTheSession()
  {
    $session['id'] = 'cool-secret-id';
    $config['lifetime'] = 60;

    $this->assertNull( $this->getCache()->save($session, $config, 60) );
  }

  public function testDeleteKeyThatIsNotAtTheSession()
  {
    $storage = new \Pimf\Session\Storages\File('some/bad/path/');
    $this->assertNull( $storage->delete('some-bad-key') );
  }

  public function testCleaning()
  {
    $this->assertNull( $this->getCache()->clean(60) );
  }

  public function testSaveNewSession()
  {
    $session['id'] = 'cool-secret-id';
    $config['lifetime'] = 60;

    $storage = new \Pimf\Session\Storages\File(dirname(__FILE__).'/_drafts/');

    $storage->save($session, $config, 60);

    $this->assertNotNull( $storage->load('cool-secret-id') );

  }

  public function testDeletingNewSession()
  {
    $session['id'] = uniqid('cool-secret-id-');
    $config['lifetime'] = 60;

    $storage = new \Pimf\Session\Storages\File(dirname(__FILE__).'/_drafts/');

    $storage->save($session, $config, 60);
    $storage->delete($session['id']);

    $this->assertNull( $storage->load($session['id']) );

  }

  public function testCleaningExpiredSessionsAtTheBag()
  {
    $session1['id'] = uniqid('cool-secret-id-');
    $session2['id'] = uniqid('cool-secret-id-');
    $session3['id'] = uniqid('cool-secret-id-');

    $config['lifetime'] = time();

    $storage = new \Pimf\Session\Storages\File(dirname(__FILE__).'/_drafts/');

    $storage->save($session1, $config, 1);
    $storage->save($session2, $config, 1);
    $storage->save($session3, $config, 1);

    sleep(2);

    $storage->clean(time());

    $this->assertNull( $storage->load($session1['id']) );
    $this->assertNull( $storage->load($session2['id']) );
    $this->assertNull( $storage->load($session3['id']) );

  }
}
 