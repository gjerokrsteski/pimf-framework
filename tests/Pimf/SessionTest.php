<?php
class SessionTest extends PHPUnit_Framework_TestCase
{

  #test configuration

  public function setUp()
  {
    \Pimf\Session::$instance = null;

    \Pimf\Config::load( array(
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
          'database' => array(
            'driver' => 'sqlite',
            'database' => 'test-session.db',
          ),

        ),

        'cache' => array(

          // Cache storage 'pdo', 'file', 'memcached', 'apc', 'redis', 'dba',
          // 'wincache', 'memory' or '' for non
          'storage' => 'memcached',

          // If using Memcached and APC to prevent collisions with other applications on the server.
          'key' => 'pimfmaster',

          // Memcached servers - for more check out: http://memcached.org
          'memcached' => array(
            'servers' => array('host' => '127.0.0.1', 'port' => 11211, 'weight' => 100),
          ),
        ),
      ),
      true
    );
  }

  public function tearDown()
  {
    \Pimf\Config::load( array(
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
        ),
      true
      );
  }

  #test helpers

  /**
   * @return \Pimf\Session\Payload
   */
  protected function getPayload()
  {
    return new \Pimf\Session\Payload($this->getMockStorage());
  }

  /**
   * @return Pimf\Session\Storages\Storage
   */
  protected function getMockStorage()
  {
    $mock = $this->getMock(
      '\\Pimf\\Session\\Storages\\Storage', array(
        'id',
        'load',
        'save',
        'delete'
      )
    );

    $mock->expects($this->any())->method('id')->will($this->returnValue(\Pimf\Util\String::random(40)));

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
    $this->assertTrue(isset($payload->session['data'][\Pimf\Session::CSRF]));
  }



  # start unit tests


  public function testStartedMethodIndicatesIfSessionIsStarted()
  {
    $this->assertFalse(\Pimf\Session::started());
    \Pimf\Session::$instance = 'rocker';
    $this->assertTrue(\Pimf\Session::started());
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
    $payload          = $this->getPayload();
    $payload->session = $this->getSession();
    $payload->exists  = true;

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

  /**
   * @covers \Pimf\Session\Storages\File::clean
   * @covers \Pimf\Session\Storages\File::save
   * @covers \Pimf\Session\Payload::clean
   * @covers \Pimf\Session\Payload::save
   */
  public function testSaveMethodSweepsIfCleanerAndOddsHitWithTimeGreaterThanThreshold()
  {
    $payload          = $this->getPayload();
    $payload->storage = $this->getMock(
      '\\Pimf\\Session\\Storages\\File', array(
        'save',
        'clean'
      ), array( null )
    );

    $payload->session = $this->getSession();
    $expiration       = time() - (\Pimf\Config::get('session.lifetime') * 60);

    // Here we set the time to the expected expiration minus 5 seconds
    $payload->storage
      ->expects($this->once())
      ->method('clean')->with($this->greaterThan($expiration - 5));

    $payload->save();
  }

  /**
   * @covers \Pimf\Session\Storages\File::clean
   * @covers \Pimf\Session\Storages\File::save
   */
  public function testSaveMethodSweepsIfCleanerAndOddsHitWithTimeLessThanThreshold()
  {
    $payload          = $this->getPayload();
    $payload->storage = $this->getMock(
      '\\Pimf\\Session\\Storages\\File', array(
        'save',
        'clean'
      ), array( null )
    );

    $payload->session = $this->getSession();
    $expiration       = time() - (\Pimf\Config::get('session.lifetime') * 60);

    $payload->storage
      ->expects($this->once())
      ->method('clean')->with($this->lessThan($expiration + 5));

    $payload->save();
  }

  /**
   * @covers \Pimf\Session\Storages\Apc::save
   */
  public function testCleanerShouldntBeCalledIfStorageIsntCleaner()
  {
    $payload          = $this->getPayload();
    $payload->storage = $this->getMock(
      '\\Pimf\\Session\\Storages\\Apc', array(
        'save',
        'clean'
      ), array(), '', false
    );

    $payload->session = $this->getSession();
    $payload->storage->expects($this->never())->method('clean');
    $payload->save();
  }

  /**
   * @covers \Pimf\Session\Storages\Memory::save
   */
  public function testCleanerShouldntBeCalledIfMemoryStorageIsntCleaner()
  {
    $payload          = $this->getPayload();
    $payload->storage = $this->getMock(
      '\\Pimf\\Session\\Storages\\Memory', array(
        'save',
        'clean'
      ), array(), '', false
    );

    $payload->session = $this->getSession();
    $payload->storage->expects($this->never())->method('clean');
    $payload->save();
  }

  /**
   * @covers \Pimf\Session\Storages\Cookie::save
   */
  public function testCleanerShouldntBeCalledIfCookieStorageIsntCleaner()
  {
    $payload          = $this->getPayload();
    $payload->storage = $this->getMock(
      '\\Pimf\\Session\\Storages\\Cookie', array(
        'save',
        'clean'
      ), array(), '', false
    );

    $payload->session = $this->getSession();
    $payload->storage->expects($this->never())->method('clean');
    $payload->save();
  }

  /**
   * @covers \Pimf\Session\Storages\Memcached::save
   */
  public function testCleanerShouldntBeCalledIfMemcachedStorageIsntCleaner()
  {
    $payload          = $this->getPayload();
    $payload->storage = $this->getMock(
      '\\Pimf\\Session\\Storages\\Memcached', array(
        'save',
        'clean'
      ), array(), '', false
    );

    $payload->session = $this->getSession();
    $payload->storage->expects($this->never())->method('clean');
    $payload->save();
  }

  /**
   * @covers \Pimf\Session\Storages\Redis::save
   */
  public function testCleanerShouldntBeCalledIfRedisStorageIsntCleaner()
  {
    $payload          = $this->getPayload();
    $payload->storage = $this->getMock(
      '\\Pimf\\Session\\Storages\\Redis', array(
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

    $conf = \Pimf\Config::get('session');

    $this->assertTrue(isset(\Pimf\Cookie::$jar[$conf['cookie']]));

    $cookie = \Pimf\Cookie::$jar[$conf['cookie']];

    $this->assertEquals(\Pimf\Cookie::hash('rocker') . '+rocker', $cookie['value']);
    $this->assertEquals($conf['domain'], $cookie['domain']);
    $this->assertEquals($conf['path'], $cookie['path']);
    $this->assertEquals($conf['secure'], $cookie['secure']);
  }

  public function testActivityMethodReturnsLastActivity()
  {
    $payload                           = $this->getPayload();
    $payload->session['last_activity'] = 10;

    $this->assertEquals(10, $payload->activity());
  }

  /**
   * @expectedException \RuntimeException
   */
  public function testGivingSessionFactoryBadStorage()
  {
    \Pimf\Session::factory('bad-bad-storage');
  }

  public function testFactorizingStoragesMap()
  {
    $this->assertInstanceOf('\\Pimf\\Session\\Storages\\Apc', \Pimf\Session::factory('apc'));
    $this->assertInstanceOf('\\Pimf\\Session\\Storages\\Cookie', \Pimf\Session::factory('cookie'));
    $this->assertInstanceOf('\\Pimf\\Session\\Storages\\File', \Pimf\Session::factory('file'));
    $this->assertInstanceOf('\\Pimf\\Session\\Storages\\Pdo', \Pimf\Session::factory('pdo'));
    $this->assertInstanceOf('\\Pimf\\Session\\Storages\\Memory', \Pimf\Session::factory('memory'));
  }

  public function testStartingSession()
  {
    \Pimf\Session::start('memory');
    $this->assertInstanceOf('\\Pimf\\Session\\Payload', \Pimf\Session::$instance);
  }


  public function testIfInstanceStarted()
  {
    \Pimf\Session::start('memory');

    $this->assertInstanceOf('\\Pimf\\Session\\Payload', \Pimf\Session::instance());
  }

  /**
   * @expectedException \RuntimeException
   */
  public function testIfNoInstanceStarted()
  {
    \Pimf\Session::instance();
  }

  public function testMagicMethodCallingMethods()
  {
    \Pimf\Session::start('memory');
    \Pimf\Session::put('name', 'Robin');

    $this->assertEquals('Robin', \Pimf\Session::get('name'));
  }

  public function testLoadingSession()
  {
    \Pimf\Request::$cookieData = $this->getMock('\\Pimf\\Param');

    \Pimf\Session::load();
  }
}