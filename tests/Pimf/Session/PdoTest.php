<?php
class SessionPdoTest extends PHPUnit_Framework_TestCase
{
  /**
   * @return PHPUnit_Framework_MockObject_MockObject
   */
  protected function getCache()
  {
    $store = $this->getMockBuilder('\\Pimf\\Database')
      ->setMethods(array('prepare'))->setConstructorArgs(
        array('sqlite::memory:')
      )->getMock();

    $statement = $this->getMockBuilder('\\stdClass')
      ->setMethods(array('bindValue', 'execute', 'fetchObject'))->getMock(
      );

    $store->expects($this->any())->method('prepare')->will($this->returnValue($statement));

    return $store;
  }


  # start testing


  public function testCreatingNewInstance()
  {
    new \Pimf\Session\Storages\Pdo($this->getCache(), 'key.');
  }


  public function testGeyKeyThatIsNotAtTheSession()
  {
    $storage = new \Pimf\Session\Storages\Pdo($this->getCache(), 'key.');

    $this->assertNull( $storage->load('some-bad-key') );
  }


  public function testSavingTheSessionIfExists()
  {
    $session['id'] = 'cool-secret-id';
    $session['last_activity'] = 215234756239487;
    $session['data'] = array(123);

    $config['lifetime'] = 60;

    $storage = new \Pimf\Session\Storages\Pdo($this->getCache(), 'key.');

    $this->assertNull( $storage->save($session, $config, true) );
  }

  public function testSavingTheSessionIfNotExists()
  {
    $session['id'] = 'cool-secret-id';
    $session['last_activity'] = 215234756239487;
    $session['data'] = array(123);

    $config['lifetime'] = 60;

    $storage = new \Pimf\Session\Storages\Pdo($this->getCache(), 'key.');

    $this->assertNull( $storage->save($session, $config, false) );
  }

  public function testDeleteKeyThatIsNotAtTheSession()
  {
    $storage = new \Pimf\Session\Storages\Pdo($this->getCache());

    $this->assertNull( $storage->delete('some-bad-key') );
  }

  public function testCleaning()
  {
    $storage = new \Pimf\Session\Storages\Pdo($this->getCache(), 'key.');

    $this->assertNull( $storage->clean(60) );
  }
}
 