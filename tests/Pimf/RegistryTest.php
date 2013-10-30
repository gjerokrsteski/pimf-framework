<?php
class RegistryTest extends PHPUnit_Framework_TestCase
{
  public function testSettingItems()
  {
    $reg = new \Pimf\Registry();

    $reg->list      = array(1,2,3);
    $reg->my_object = (object)array('a'=>1,'b'=>2);
  }

  public function testGettingItems()
  {
    $reg = new \Pimf\Registry();

    $this->assertEquals(array(1,2,3), $reg->list);
  }

  public function testGettingItemsStatically()
  {
    $this->assertEquals(array(1,2,3), \Pimf\Registry::get('list'));
  }

  public function testSettingItemsStatically()
  {
    \Pimf\Registry::set('list', array(1,2,3));
    \Pimf\Registry::set('list', array(1,2,3,4,5=>'test'));

    $this->assertEquals(array(1,2,3,4,5=>'test'), \Pimf\Registry::get('list'));
  }
}
