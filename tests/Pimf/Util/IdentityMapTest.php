<?php
class xObject
{
  public $name = 'public-conan';
  public $fruits = array('public-banana');
}

class IdentityMapTest extends PHPUnit_Framework_TestCase
{
  public function testCreatingNewInstance()
  {
    new \Pimf\Util\IdentityMap();
  }

  public function testHappyPathOnSettingObjectsAndGettingThemBack()
  {
    $identityMap = new \Pimf\Util\IdentityMap();

    $identityMap->set('object1.id', $object1 = new \xObject());
    $identityMap->set('object2.id', $object2 = new \xObject());

    $this->assertTrue($identityMap->hasId('object1.id'));
    $this->assertTrue($identityMap->hasId('object2.id'));

    $this->assertTrue($identityMap->hasObject($object1));
    $this->assertTrue($identityMap->hasObject($object2));

    $this->assertEquals($identityMap->getObject('object1.id'), $object1);
    $this->assertEquals($identityMap->getObject('object2.id'), $object2);

    $this->assertEquals($identityMap->getId($object1), 'object1.id');
    $this->assertEquals($identityMap->getId($object2), 'object2.id');
  }

  /**
   * @expectedException \OutOfBoundsException
   */
  public function testGetIdByObjectIfNoIdFound()
  {
    $identityMap = new \Pimf\Util\IdentityMap();
    $identityMap->getId(new \stdClass());
  }

  /**
   * @expectedException \OutOfBoundsException
   */
  public function testGetObjectByIdIfNoObjectFound()
  {
    $identityMap = new \Pimf\Util\IdentityMap();
    $identityMap->getObject('bad.bad.id.here');
  }
}
 