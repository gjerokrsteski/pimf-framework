<?php
class LoggerTest extends PHPUnit_Framework_TestCase
{
  private static $localeStorageDir;

  private static $env;

  public static function setUpBeforeClass()
  {
    self::$localeStorageDir = dirname(__FILE__) . '/_drafts/';

    self::$env = new \Pimf\Environment(array('REMOTE_ADDR'=>'localhost.test', 'PHP_SELF'=>'self.test.php'));

  }

  public static function tearDownAfterClass()
  {
    @unlink(self::$localeStorageDir . 'pimf-logs.txt');
    @unlink(self::$localeStorageDir . 'pimf-warnings.txt');
    @unlink(self::$localeStorageDir . 'pimf-errors.txt');
  }

  #tests

  public function testCreatingNewInstanceAndDestructingIt()
  {
    new \Pimf\Logger(self::$localeStorageDir, self::$env );
  }

  public function testCreatingNewInstanceWithTrailingSeparatorAndDestructingIt()
  {
    new \Pimf\Logger(self::$localeStorageDir, self::$env , true);
  }

  public function testInitialisingTheFileResources()
  {
    $logger = new \Pimf\Logger(self::$localeStorageDir, self::$env );

    $logger->init();
  }

  public function testLoggingMethods()
  {
    $logger = new \Pimf\Logger(self::$localeStorageDir, self::$env );
    $logger->init();

    $logger->debug('debug msg')->error('error msg')->info('info msg')->warn('warn msg');
  }
}
