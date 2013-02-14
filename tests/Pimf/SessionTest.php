<?php
class Pimf_SessionTest extends PHPUnit_Framework_TestCase
{

  #test configuration

  public function setUp()
  {
    Pimf_Session::$instance = null;

    Pimf_Registry::set(
      'conf', array(
        'app'     => array( 'key' => 'rocker' ),
        'session' => array(
          'storage'            => 'cookie',
          'table'              => 'sessions',
          'garbage_collection' => array(100, 100),
          'lifetime'           => 60,
          'expire_on_close'    => false,
          'cookie'             => 'pimf_session',
          'path'               => '/',
          'domain'             => null,
          'secure'             => false,
        ),
      )
    );
  }

  public function tearDown()
  {
    Pimf_Registry::set(
        'conf', array(
          'app'     => array( 'key' => 'rocker' ),
          'session' => array(
            'storage'            => 'cookie',
            'table'              => 'sessions',
            'garbage_collection' => array(2, 100),
            'lifetime'           => 60,
            'expire_on_close'    => false,
            'cookie'             => 'pimf_session',
            'path'               => '/',
            'domain'             => null,
            'secure'             => false,
          ),
        )
      );
  }

  #test helpers

  /**
   * @return Pimf_Session_Payload
   */
  protected function getPayload()
  {
    return new Pimf_Session_Payload($this->getMockStorage());
  }

  /**
   * @return Pimf_Session_Storages_Storage
   */
  protected function getMockStorage()
  {
    $mock = $this->getMock(
      'Pimf_Session_Storages_Storage', array(
        'id',
        'load',
        'save',
        'delete'
      )
    );

    $mock->expects($this->any())->method('id')->will($this->returnValue(Pimf_Util_String::random(40)));

    return $mock;
  }

  /**
   * @return array
   */
  protected function getSession()
  {
    return array(
      'id'            => 'rocker',
      'last_activity' => time(),
      'data'          => array(
        'name'       => 'Robin',
        'age'        => 25,
        'csrf_token' => 'anne',
        ':new:'      => array(
          'votes' => 10,
        ),
        ':old:'      => array(
          'state' => 'AR',
        ),
      )
    );
  }

  protected function verifyNewSession($payload)
  {
    $this->assertFalse($payload->exists);
    $this->assertTrue(isset($payload->session['id']));
    $this->assertEquals(array(), $payload->session['data'][':new:']);
    $this->assertEquals(array(), $payload->session['data'][':old:']);
    $this->assertTrue(isset($payload->session['data'][Pimf_Session::CSRF]));
  }

  #unit tests

  public function testStartedMethodIndicatesIfSessionIsStarted()
  {
    $this->assertFalse(Pimf_Session::started());
    Pimf_Session::$instance = 'rocker';
    $this->assertTrue(Pimf_Session::started());
  }

  public function testLoadMethodCreatesNewSessionWithNullIDGiven()
  {
    $payload = $this->getPayload();
    $payload->load(null);
    $this->verifyNewSession($payload);
  }

  public function testLoadMethodCreatesNewSessionWhenSessionIsExpired()
  {
    $payload                  = $this->getPayload();
    $session                  = $this->getSession();
    $session['last_activity'] = time() - 10000;

    $payload->storage->expects($this->any())->method('load')->will($this->returnValue($session));

    $payload->load('rocker');

    $this->verifyNewSession($payload);
    $this->assertTrue($payload->session['id'] !== $session['id']);
  }

  public function testLoadMethodSetsValidSession()
  {
    $payload = $this->getPayload();
    $session = $this->getSession();

    $payload->storage->expects($this->any())->method('load')->will($this->returnValue($session));

    $payload->load('rocker');

    $this->assertEquals($session, $payload->session);
  }

  public function testLoadMethodSetsCSRFTokenIfDoesntExist()
  {
    $payload = $this->getPayload();
    $session = $this->getSession();

    unset($session['data']['csrf_token']);

    $payload->storage->expects($this->any())->method('load')->will($this->returnValue($session));

    $payload->load('rocker');

    $this->assertEquals('rocker', $payload->session['id']);
    $this->assertTrue(isset($payload->session['data']['csrf_token']));
  }

  public function testSessionDataCanBeRetrievedProperly()
  {
    $payload = $this->getPayload();

    $payload->session = $this->getSession();

    $this->assertTrue($payload->has('name'));
    $this->assertEquals('Robin', $payload->get('name'));
    $this->assertFalse($payload->has('foo'));
    $this->assertEquals('Default', $payload->get('rocker', 'Default'));
    $this->assertTrue($payload->has('votes'));
    $this->assertEquals(10, $payload->get('votes'));
    $this->assertTrue($payload->has('state'));
    $this->assertEquals('AR', $payload->get('state'));
  }

  public function testDataCanBeSetProperly()
  {
    $payload          = $this->getPayload();
    $payload->session = $this->getSession();

    // Test the "put" and "flash" methods.
    $payload->put('name', 'Masdon');
    $this->assertEquals('Masdon', $payload->session['data']['name']);
    $payload->flash('language', 'php');
    $this->assertEquals('php', $payload->session['data'][':new:']['language']);

    // Test the "reflash" method.
    $payload->session['data'][':new:'] = array( 'name' => 'Robin' );
    $payload->session['data'][':old:'] = array( 'age' => 25 );
    $payload->reflash();
    $this->assertEquals(
      array(
        'name' => 'Robin',
        'age'  => 25
      ), $payload->session['data'][':new:']
    );

    // Test the "keep" method.
    $payload->session['data'][':new:'] = array();
    $payload->keep(array( 'age' ));
    $this->assertEquals(25, $payload->session['data'][':new:']['age']);
  }

  public function testSessionDataCanBeForgotten()
  {
    $payload          = $this->getPayload();
    $payload->session = $this->getSession();

    $this->assertTrue(isset($payload->session['data']['name']));

    $payload->forget('name');

    $this->assertFalse(isset($payload->session['data']['name']));
  }

  public function testFlushMaintainsTokenButDeletesEverythingElse()
  {
    $payload          = $this->getPayload();
    $payload->session = $this->getSession();

    $this->assertTrue(isset($payload->session['data']['name']));

    $payload->flush();

    $this->assertFalse(isset($payload->session['data']['name']));
    $this->assertEquals('anne', $payload->session['data']['csrf_token']);
    $this->assertEquals(array(), $payload->session['data'][':new:']);
    $this->assertEquals(array(), $payload->session['data'][':old:']);
  }

  public function testRegenerateMethodSetsNewIDAndTurnsOffExistenceIndicator()
  {
    $payload         = $this->getPayload();
    $payload->sesion = $this->getSession();
    $payload->exists = true;

    $payload->regenerate();

    $this->assertFalse($payload->exists);
    $this->assertTrue(strlen($payload->session['id']) == 40);
  }

  public function testTokenMethodReturnsCSRFToken()
  {
    $payload          = $this->getPayload();
    $payload->session = $this->getSession();

    $this->assertEquals('anne', $payload->token());
  }

  public function testSaveMethodCorrectlyCallsStorage()
  {
    $payload          = $this->getPayload();
    $session          = $this->getSession();
    $payload->session = $session;
    $payload->exists  = true;
    $conf             = Pimf_Registry::get('conf');

    $expect                  = $session;
    $expect['data'][':old:'] = $session['data'][':new:'];
    $expect['data'][':new:'] = array();

    $payload->storage
      ->expects($this->once())
      ->method('save')->with($this->equalTo($expect), $this->equalTo($conf['session']), $this->equalTo(true));

    $payload->save();

    $this->assertEquals($session['data'][':new:'], $payload->session['data'][':old:']);
  }

  public function testSaveMethodSweepsIfCleanerAndOddsHitWithTimeGreaterThanThreshold()
  {

    $payload          = $this->getPayload();
    $payload->storage = $this->getMock(
      'Pimf_Session_Storages_File', array(
        'save',
        'clean'
      ), array( null )
    );

    $payload->session = $this->getSession();
    $conf             = Pimf_Registry::get('conf');
    $expiration       = time() - ($conf['session']['lifetime'] * 60);

    // Here we set the time to the expected expiration minus 5 seconds
    $payload->storage
      ->expects($this->once())
      ->method('clean')->with($this->greaterThan($expiration - 5));

    $payload->save();
  }

  public function testSaveMethodSweepsIfCleanerAndOddsHitWithTimeLessThanThreshold()
  {

    $payload          = $this->getPayload();
    $payload->storage = $this->getMock(
      'Pimf_Session_Storages_File', array(
        'save',
        'clean'
      ), array( null )
    );

    $payload->session = $this->getSession();
    $conf             = Pimf_Registry::get('conf');
    $expiration       = time() - ($conf['session']['lifetime'] * 60);

    $payload->storage
      ->expects($this->once())
      ->method('clean')->with($this->lessThan($expiration + 5));

    $payload->save();
  }

  public function testCleanerShouldntBeCalledIfStorageIsntCleaner()
  {

    $payload          = $this->getPayload();
    $payload->storage = $this->getMock(
      'Pimf_Session_Storages_Apc', array(
        'save',
        'clean'
      ), array(), '', false
    );

    $payload->session = $this->getSession();

    $payload->storage->expects($this->never())->method('clean');

    $payload->save();
  }

  public function testSaveMethodSetsCookieWithCorrectValues()
  {
    $payload          = $this->getPayload();
    $payload->session = $this->getSession();
    $payload->save();

    $conf = Pimf_Registry::get('conf');

    $this->assertTrue(isset(Pimf_Cookie::$jar[$conf['session']['cookie']]));

    $cookie = Pimf_Cookie::$jar[$conf['session']['cookie']];

    $this->assertEquals(Pimf_Cookie::hash('rocker') . '+rocker', $cookie['value']);
    $this->assertEquals($conf['session']['domain'], $cookie['domain']);
    $this->assertEquals($conf['session']['path'], $cookie['path']);
    $this->assertEquals($conf['session']['secure'], $cookie['secure']);
  }

  public function testActivityMethodReturnsLastActivity()
  {
    $payload                           = $this->getPayload();
    $payload->session['last_activity'] = 10;

    $this->assertEquals(10, $payload->activity());
  }
}