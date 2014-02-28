<?php
class ApplicationTest extends PHPUnit_Framework_TestCase
{
  protected static $conf = array(
    'environment' => 'testing',
    'encoding' => 'UTF-8',
    'timezone' => 'UTC',
    'testing' => array(
      'db' => array(
        'driver' => 'sqlite',
        'database' => ':memory:'
      ),
    ),
    'bootstrap' => array(
      'expected' => array(
        'php_version' => 5.3,
      ),
      'local_temp_directory' => '/tmp/'
    ),
    'app' => array(
      'name'               => 'MyFirstBlog',
      'key'                => 'some5secret5key5here',
      'default_controller' => 'blog',
      'routeable'          => true,
      'url'                => 'http://localhost',
      'index'              => '',
      'asset_url'          => '',
    ),
    'error' => array(
      'ignore_levels' => array(E_USER_DEPRECATED),
      'debug_info' => false,
      'log' => true,
    ),
    'session' => array(
        'storage' => '',
    ),
    'cache' => array(
        'storage' => '',
    ),
  );

  public static function invokeMethod(&$object, $methodName, array $parameters = array())
  {
    $reflection = new \ReflectionClass(get_class($object));
    $method     = $reflection->getMethod($methodName);
    $method->setAccessible(true);

    return $method->invokeArgs($object, $parameters);
  }


  # start testing


  public function testCreatingNewInstance()
  {
    new \Pimf\Application();
  }

  public function testHappyBootstrapping()
  {
    $this->assertNull(

      \Pimf\Application::bootstrap(self::$conf, $server = array())

    );
  }

  public function testSetupErrorHandlingIfTestingEnvironment()
  {
    $app = new \Pimf\Application();

    self::invokeMethod($app, 'setupErrorHandling', array(array('environment'=>'testing')));

    $current_error_reporting = error_reporting();

    $this->assertEquals(E_ALL | E_STRICT, $current_error_reporting);
  }

  /**
   * @runInSeparateProcess
   */
  public function testSetupErrorHandlingIfProductionEnvironment()
  {
    $app = new \Pimf\Application();

    self::invokeMethod($app, 'setupErrorHandling', array(array('environment'=>'production')));

    $current_error_reporting = error_reporting();

    $this->assertEquals(-1, $current_error_reporting);
  }


  public function testLoadPdoDriver()
  {
    $app = new \Pimf\Application();

    $this->assertNull(

      self::invokeMethod($app, 'loadPdoDriver', array(array('environment' => 'testing', 'testing'=>array('db'=>array()))))

    );
  }

  /**
   * @runInSeparateProcess
   */
  public function testLoadingRoutes()
  {
    \Pimf\Registry::set('env', new \Pimf\Environment(array('REQUEST_URI' => 'core/index')));

    $app = new \Pimf\Application();

    $root = dirname(__FILE__).'/_fixture/';

    $this->assertNull(

      self::invokeMethod($app, 'loadRoutes', array(array('app'=> array('name'=>'test-app', 'routeable'=>true)), $root))

    );
  }

  public function testLoadingListeners()
  {
    $app = new \Pimf\Application();

    $root = dirname(__FILE__).'/_fixture/';

    self::invokeMethod($app, 'loadListeners', array(array('app'=> array('name'=>'test-app')), $root));

    $this->assertTrue(\Pimf\Event::listeners('test.loading.listeners'));
  }

  public function testReportProblemIfNotExpectedPhpVersion()
  {
    $app = new \Pimf\Application();

    $fake_php_version = 5.2;
    $problems = array();

    $res = self::invokeMethod($app, 'reportIf', array($problems, $fake_php_version, false));

    $this->assertNotEmpty($res);
    $this->assertEquals(

      'You have PHP '.$fake_php_version.' and you need 5.3 or higher!',

      current($res)

    );
  }

  public function testReportProblemIfPreviousProblemsGiven()
  {
    $app = new \Pimf\Application();

    $fake_php_version = 5.3;
    $problems = array('problem 1', 'problem 2');

    $res = self::invokeMethod($app, 'reportIf', array($problems, $fake_php_version, false));

    $this->assertNotEmpty($res);
    $this->assertEquals($problems, $res);
  }
}
 