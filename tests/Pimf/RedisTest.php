<?php
class RedisTest extends PHPUnit_Framework_TestCase
{
  protected function getRedis()
  {
    return $this->getMockBuilder('\\Pimf\\Redis')
      ->disableOriginalConstructor()
      ->setMethods(array('get', 'expire', 'set', 'del', 'forget', 'select', 'put'))
      ->getMock();
  }

  public function testGetReturnsNullWhenNotFound()
  {
    $redis = $this->getRedis();
    $redis->expects($this->any())->method('connect')->will($this->returnValue($redis));
    $redis->expects($this->once())->method('get')->will($this->returnValue(null));

    $this->assertNull($redis->get('foo'));
  }

}
 