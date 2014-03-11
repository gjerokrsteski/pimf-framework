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
}
 