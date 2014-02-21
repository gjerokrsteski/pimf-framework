<?php
class MemcachedTest extends PHPUnit_Framework_TestCase
{
  /**
   * Call protected/private method of a class.
   *
   * @param object &$object    Instantiated object that we will run method on.
   * @param string $methodName Method name to call
   * @param array  $parameters Array of parameters to pass into method.
   *
   * @return mixed Method return.
   */
  public static function invokeMethod(&$object, $methodName, array $parameters = array())
  {
      $reflection = new \ReflectionClass(get_class($object));
      $method = $reflection->getMethod($methodName);
      $method->setAccessible(true);

      return $method->invokeArgs($object, $parameters);
  }


  # start testing


  public function testConnecting()
  {
    $mock = $this->getMockBuilder('\Memcached')
                 ->disableOriginalConstructor()
                 ->setMethods(array('addServer', 'getVersion'))
                 ->getMock();

    $mock->expects($this->any())->method('addServer')->will($this->returnValue(true));
    $mock->expects($this->any())->method('getVersion')->will($this->returnValue(true));

    $memcache = new \Pimf\Memcached();
    $servers[] = array('host' => '127.0.0.1', 'port' => 11211, 'weight' => 100);

    self::invokeMethod($memcache, 'connect', array($servers, $mock));
  }

  /**
   * @expectedException \RuntimeException
   */
  public function testIfCouldNotEstablishMemcachedConnection()
  {
    $mock = $this->getMockBuilder('\Memcached')
                 ->disableOriginalConstructor()
                 ->setMethods(array('addServer', 'getVersion'))
                 ->getMock();

    $mock->expects($this->any())->method('addServer')->will($this->returnValue(true));
    $mock->expects($this->any())->method('getVersion')->will($this->returnValue(false));

    $memcache = new \Pimf\Memcached();
    $servers[] = array('host' => '127.0.0.1', 'port' => 11211, 'weight' => 100);

    self::invokeMethod($memcache, 'connect', array($servers, $mock));
  }
}
 