<?php
class RouterTest extends PHPUnit_Framework_TestCase
{
  public static function setUpBeforeClass()
  {
    \Pimf\Registry::set('conf', array('app'=> array('default_controller' => 'blog')));
  }

  protected static function mockUri($fake)
  {
    \Pimf\Registry::set('env', new \Pimf\Environment(array('REQUEST_URI' => $fake)));
  }


  #start testing


  public function testFindingRouteTargetByUnsuitableCondition()
  {
    self::mockUri('/users/show/to log firs name here/');

    $route = new \Pimf\Route('/users/show/:first-name', array(), array('first-name' => '[a-zA-Z]{3,}'));
    $router = new \Pimf\Router();
    $router->map($route);

    $this->assertNotInstanceOf('\\Pimf\\Route\\Target', $router->find(), 'bad target');
  }

  public function testFindingCustomTargetByCondition()
  {
    self::mockUri('/users/show/Boby');

    $route  = new \Pimf\Route('/:controller/:action/:firstname', array(), array('firstname' => '\w+'));
    $router = new \Pimf\Router();
    $router->map($route);
    $target = $router->find();

    $this->assertInstanceOf('\\Pimf\\Route\\Target', $target, 'bad target');
    $this->assertEquals('users', $target->getController(), 'bad controller name');
    $this->assertEquals('show', $target->getAction(), 'bad action name');
    $this->assertEquals(null, $target->getId(), 'bad id');
    $this->assertEquals(array('firstname' => 'Boby'), $target->getParams(), 'bad params list');
  }
}