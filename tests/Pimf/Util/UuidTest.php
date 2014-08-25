<?php
class UuidTest extends PHPUnit_Framework_TestCase
{
  public static function setUpBeforeClass()
  {
    parent::setUpBeforeClass();

    \Pimf\Registry::set('env', new \Pimf\Environment(array('REMOTE_ADDR' => '127.0.0.1', 'SERVER_NAME' => 'test.de')));
  }

  public function testRetreiveNewUuid()
  {
    $this->assertNotSame(
      \Pimf\Util\Uuid::generate(),
      \Pimf\Util\Uuid::generate()
    );
  }
}