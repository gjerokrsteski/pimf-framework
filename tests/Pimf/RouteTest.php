<?php

class RouteTest extends \PHPUnit_Framework_TestCase
{
    protected static function mockUri($fake)
    {
        \Pimf\Uri::setup($fake, $fake);
    }

    #start testing


    public function testGetPattern()
    {
        self::mockUri('/pimf/rocks/');

        $route = new \Pimf\Route('/foo');
        $this->assertEquals('/foo', $route->init()->getRule());
    }

    /**
     * @testdox Main page will call controller "Home"
     */
    public function testMainPageWillCallController()
    {
        self::mockUri('/');

        $route = new \Pimf\Route('/', array('controller' => 'home'));

        $this->assertTrue($route->init()->matches(), 'bad route');

        $this->assertEquals(
            array('controller' => 'home'),
            $route->getParams()
        );
    }

    /**
     * @testdox Call controller "Profile" with dynamic method ":action()"
     */
    public function testWillCallControllerProfileWithDynamicMethod()
    {
        self::mockUri('/profile/show');

        $route = new \Pimf\Route('/profile/:action', array('controller' => 'profile'));

        $this->assertTrue($route->init()->matches(), 'bad route');

        $this->assertEquals(
            array('controller' => 'profile', 'action' => 'show'),
            $route->getParams()
        );
    }

    /**
     * @testdox Call controller "Profile" with dynamic method ":action()"
     */
    public function testDefineFiltersForUrlParameters()
    {
        self::mockUri('/users/174');

        $route = new \Pimf\Route('/users/:id', array('controller' => 'users'), array('id' => '[\d]{1,8}'));

        $this->assertTrue($route->init()->matches(), 'bad route');

        $this->assertEquals(
            array('controller' => 'users', 'id' => '174'),
            $route->getParams()
        );
    }

    public function testDefineFiltersForUrlParametersWithNoMatchingIdValue()
    {
        self::mockUri('/users/berry');

        $route = new \Pimf\Route('/users/:first-name', array(), array('first-name' => '[a-zA-Z]{3,}'));

        $this->assertFalse($route->init()->matches(), 'route has not to match');
    }

    public function testGetParamsIfRouteMatches()
    {
        self::mockUri('/pimf/rocks/123');

        $route = new \Pimf\Route('/:controller/:action/:id');

        $this->assertTrue($route->init()->matches(), 'bad route');

        $this->assertEquals(
            array('controller' => 'pimf', 'action' => 'rocks', 'id' => '123'),
            $route->getParams()
        );
    }

    public function testGetParamsIfRouteAndTargetMatches()
    {
        self::mockUri('/users/123');

        $route = new \Pimf\Route('/users/:id', array('controller' => 'users'));

        $this->assertTrue($route->init()->matches(), 'bad route');

        $this->assertEquals(
            array('controller' => 'users', 'id' => '123'),
            $route->getParams()
        );
    }

    public function testDirectRuleMapping()
    {
        self::mockUri('/login');

        $route = new \Pimf\Route('/login', array('controller' => 'auth', 'action' => 'login'));

        $this->assertTrue($route->init()->matches(), 'bad route');

        $this->assertEquals(
            array('controller' => 'auth', 'action' => 'login'),
            $route->getParams()
        );
    }

    public function testByCondition()
    {
        self::mockUri('/users/show/Boby');

        $route = new \Pimf\Route('/users/show/:firstname', array(), array('firstname' => '\w+'));

        $this->assertTrue($route->init()->matches(), 'bad route');

        $this->assertEquals(
            array('firstname' => 'Boby'),
            $route->getParams()
        );
    }
}
