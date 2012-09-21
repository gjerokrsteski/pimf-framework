<?php
class Pimf_RegistryTest extends PHPUnit_Framework_TestCase
{
  public function testSettingItems()
  {
    $reg = new Pimf_Registry();

    $reg->list      = array(1,2,3);
    $reg->my_object = (object)array('a'=>1,'b'=>2);
  }

  /**
   * @expectedException LogicException
   */
  public function testOverwritingItems()
  {
    $reg = new Pimf_Registry();
    $reg->list= 1;
  }

  public function testGettingItems()
  {
    $reg = new Pimf_Registry();

    $this->assertEquals(array(1,2,3), $reg->list);
  }
}
