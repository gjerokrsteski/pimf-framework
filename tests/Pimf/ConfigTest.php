<?php
class ConfigTest extends \PHPUnit_Framework_TestCase
{
  public function testSettingItems()
  {
    \Pimf\Config::load(array(), true);
  }

  public function testGettingItemsStatically()
  {
    \Pimf\Config::load(array('list'=>array(1,2,3)), true);

    $this->assertEquals(array(1,2,3), \Pimf\Config::get('list'));
  }


  public function testGettingItemsIfNotAtBatteryBecauseNoOverride()
  {
    \Pimf\Config::load(array('list'=>array(1,2,3)));
    \Pimf\Config::load(array('list-two'=>array(4,5,6)));

    $this->assertEquals(array(1,2,3), \Pimf\Config::get('list'));
    $this->assertNull(\Pimf\Config::get('list-two'));
  }


  public function testFetchingSegments()
  {
    \Pimf\Config::load(array ('list' => array ('of' => array ('numbers' => array (1, 2, 3)))), true);

    $this->assertEquals(array(1,2,3), \Pimf\Config::get('list.of.numbers'));
  }

}
