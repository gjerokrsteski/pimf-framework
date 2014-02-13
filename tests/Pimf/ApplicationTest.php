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

  public function testCreatingNewInstance()
  {
    new \Pimf\Application();
  }

  public function testHappyBootstraping()
  {
    $this->assertNull(

      \Pimf\Application::bootstrap(self::$conf, $server = array())

    );
  }
}
 