<?php

class RouterTest extends \PHPUnit_Framework_TestCase
{

    public static function setUpBeforeClass()
    {
        \Pimf\Config::load(array('app' => array('default_controller' => 'blog')), true);
    }

    protected static function mockUri($fake)
    {
        \Pimf\Uri::setup($fake, $fake);
    }


    #start testing


    public function testFindingRouteTargetByUnsuitableCondition()
    {
        self::mockUri('/users/show/to log first name here/');

        $route = new \Pimf\Route('/users/show/:first-name', array(), array('first-name' => '[a-zA-Z]{3,}'));
        $router = new \Pimf\Router();
        $router->map($route);

        $this->assertNotInstanceOf('\\Pimf\\Route\\Target', $router->find(), 'bad target');
    }

    public function testFindingCustomTargetByCondition()
    {
        self::mockUri('/users/show/Boby');

        $route = new \Pimf\Route('/:controller/:action/:firstname', array(), array('firstname' => '\w+'));
        $router = new \Pimf\Router();
        $router->map($route);
        $target = $router->find();

        $this->assertInstanceOf('\\Pimf\\Route\\Target', $target, 'bad target');
        $this->assertEquals('users', $target->getController(), 'bad controller name');
        $this->assertEquals('show', $target->getAction(), 'bad action name');
        $this->assertEquals(array('firstname' => 'Boby'), $target->getParams(), 'bad params list');
    }

    public function testWildcardRouteParameters()
    {
        self::mockUri('/hello/Berry/White');

        $route = new \Pimf\Route('/hello/:name+', array('controller' => 'hello'));
        $router = new \Pimf\Router();
        $router->map($route);
        $target = $router->find();

        $this->assertInstanceOf('\\Pimf\\Route\\Target', $target, 'bad target');
        $this->assertEquals('hello', $target->getController(), 'bad controller name');
        $this->assertEquals('index', $target->getAction(), 'bad action name');
        $this->assertEquals(array('name' => array('Berry', 'White')), $target->getParams(), 'bad params list');
    }
}
