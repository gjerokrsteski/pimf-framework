<?php
class Pimf_RegistryTest extends PHPUnit_Framework_TestCase
{
  public function testSettingItems()
  {
    $reg = new Pimf_Registry();

    $reg->list      = array(1,2,3);
    $reg->my_object = (object)array('a'=>1,'b'=>2);
  }

  public function testGettingItems()
  {
    $reg = new Pimf_Registry();

    $this->assertEquals(array(1,2,3), $reg->list);
  }

  public function testGettingItemsStatically()
  {
    $this->assertEquals(array(1,2,3), Pimf_Registry::get('list'));
  }

  public function testSettingItemsStatically()
  {
    Pimf_Registry::set('list', array(1,2,3));
    Pimf_Registry::set('list', array(1,2,3,4,5=>'test'));

    $this->assertEquals(array(1,2,3,4,5=>'test'), Pimf_Registry::get('list'));
  }
}
