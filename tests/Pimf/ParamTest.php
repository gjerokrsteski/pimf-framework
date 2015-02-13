<?php
class ParamTest extends \PHPUnit_Framework_TestCase
{
  public function testCreatingNewInstance()
  {
    new \Pimf\Param();
  }

  public function testGetAll()
  {
    $param = new \Pimf\Param(array('a'=>1, 'b'=>2));

    $this->assertEquals(array('a'=>1, 'b'=>2), $param->getAll());
  }

  public function testGetOne()
  {
    $param = new \Pimf\Param(array('a'=>1, 'b'=>2));

    $this->assertEquals(2, $param->get('b', null, false));
  }

  public function testGetOneIfNotFound()
  {
    $param = new \Pimf\Param(array('a'=>1, 'b'=>2));

    $this->assertEquals('not-found', $param->get('bad-key', 'not-found'));
  }

  public function testGetOneFiltered()
  {
    $param = new \Pimf\Param(array('a'=>1, 'b'=> array(1,2,3)));

    $this->assertEquals(1, $param->get('a'));
    $this->assertEquals(array(1,2,3), $param->get('b'));
  }
}
 