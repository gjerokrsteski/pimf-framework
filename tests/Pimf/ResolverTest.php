<?php
class ResolverTest extends PHPUnit_Framework_TestCase
{
  ## prepare the environment

  public function setUp()
  {
    parent::setUp();

    require_once dirname(__FILE__) . '/_fixture/Index.php';

    $_GET    = array(
      'controller'=> 'index',
      'action'    => 'save'
    );

    \Pimf\Registry::set('conf',
      array(
        'app' => array(
          'name' => 'test-app-name',
          'key' => 'secret-key-here',
          'default_controller' => 'index',
          'routeable' => false,
        ),
        'environment' => 'testing'
      )
    );

    $_SERVER['REQUEST_METHOD'] = 'POST';
    \Pimf\Registry::set('env', new \Pimf\Environment($_SERVER));
  }


  # start testing


  public function testCreatingNewInstance()
  {
    new \Pimf\Resolver(new \Pimf\Request($_GET), dirname(__FILE__).'/_fixture/');
  }

  public function testLoadingControllerInstance()
  {
    $resolver = new \Pimf\Resolver(
      new \Pimf\Request($_GET),
      dirname(__FILE__).'/_fixture/',
      'Fixture\\'
    );

    $this->assertInstanceOf('\Pimf\Controller\Base', $resolver->process());
  }

  public function testIfNoActionGiven()
  {
    \Pimf\Registry::set('conf',
       array(
         'app' => array(
           'name' => 'test-app-name',
           'key' => 'secret-key-here',
           'default_controller' => 'index',
           'routeable' => false,
         ),
         'environment' => 'production'
       )
     );

    $resolver = new \Pimf\Resolver(
      new \Pimf\Request(array()),
      dirname(__FILE__).'/_fixture/',
      'Fixture\\'
    );

    $this->assertInstanceOf('\Pimf\Controller\Base', $resolver->process());
  }

  public function testCallingControllerAction()
  {
    $resolver = new \Pimf\Resolver(
      new \Pimf\Request($_GET),
      dirname(__FILE__).'/_fixture/',
      'Fixture\\'
    );

    $this->assertEquals(

      'indexAction',

      $resolver->process()->render()

    );
  }

  /**
   * @expectedException \Pimf\Resolver\Exception
   */
  public function testIfNoControllerFoundAtTheRepositoryPath()
  {
    new \Pimf\Resolver(
      new \Pimf\Request($_GET),
      '/Undefined_Controller_Repository/',
      'Fixture\\'
    );
  }

  public function testIfNoActionFoundAtControllerTheRouterFindsTheIndexAction()
  {
    $resolver = new \Pimf\Resolver(
      new \Pimf\Request(array(),array(),array(),array('action'=>'un de fi ned')),
      dirname(__FILE__).'/_fixture/',
      'Fixture\\'
    );

    $this->assertEquals(

      'indexAction',

      $resolver->process()->render()

    );
  }

  public function testIfAppIsRouteable()
  {
    \Pimf\Registry::set('conf',
      array(
        'app' => array(
          'name' => 'test-app-name',
          'key' => 'secret-key-here',
          'default_controller' => 'index',
          'routeable' => true,
        ),
        'environment' => 'testing'
      )
    );

    $router = new \Pimf\Router();
    \Pimf\Registry::set('router',
      $router->map(new \Pimf\Route('index/save'))
    );

    $resolver = new \Pimf\Resolver(
      new \Pimf\Request(array(),array('action'=>'save')),
      dirname(__FILE__).'/_fixture/',
      'Fixture\\'
    );

    $this->assertEquals(

      'saveAction',

      $resolver->process()->render()

    );
  }

  /**
   * @expectedException \Pimf\Resolver\Exception
   */
  public function testThatDirectoryTraversalAttackIsNotFunny()
  {
    new \Pimf\Resolver(
      new \Pimf\Request(array('controller'=>'.../bad-path'), array(),array(),array()),
       dirname(__FILE__).'/_fixture/',
       'Fixture\\'
     );
  }

  /**
   * @expectedException \Pimf\Resolver\Exception
   */
  public function testThatDirectoryTraversalAttackIsNotFunnyOnProduction()
  {
    \Pimf\Registry::set('conf',
      array(
        'app' => array(
          'name' => 'test-app-name',
          'key' => 'secret-key-here',
          'default_controller' => 'index',
          'routeable' => true,
        ),
        'environment' => 'production'
      )
    );

    new \Pimf\Resolver(
      new \Pimf\Request(array(), array(),array(),array('controller'=>'.../bad-path')),
       dirname(__FILE__).'/_fixture/',
       'Fixture\\'
     );
  }

  /**
   * @expectedException \Pimf\Resolver\Exception
   */
  public function testIfCanNotLoadClassControllerFromRepository()
  {
    \Pimf\Registry::set('conf',
      array(
        'app' => array(
          'name' => 'test-app-name',
          'key' => 'secret-key-here',
          'default_controller' => 'index',
          'routeable' => true,
        ),
        'environment' => 'testing'
      )
    );

    $resolver = new \Pimf\Resolver(
      new \Pimf\Request(array('controller'=>'bad')),
       dirname(__FILE__).'/_fixture/',
       'Fixture\\'
     );

    $resolver->process();
  }


  /**
   * @runInSeparateProcess
   * @outputBuffering enabled
   */
  public function testIfAppCanRedirect()
  {
    \Pimf\Registry::set('env',
      new \Pimf\Environment(
        array('HTTPS' => 'off', 'SCRIPT_NAME' => __FILE__, 'HOST' => 'http://localhost', 'SERVER_PROTOCOL' => 'HTTP/1.0')));

    \Pimf\Registry::set('conf',
      array(
        'app' => array(
          'name' => 'test-app-name',
          'key' => 'secret-key-here',
          'default_controller' => 'index',
          'routeable' => true,
          'url' => 'http://localhost',
          'index' => 'index.php',
          'asset_url' => '',
        ),
        'environment' => 'testing',
        'ssl' => false,
      )
    );

    $router = new \Pimf\Router();
    \Pimf\Registry::set('router',
      $router->map(new \Pimf\Route('index/save'))
    );

    # the test assertion

    $resolver = new \Pimf\Resolver(
      new \Pimf\Request(array(),array('action'=>'save')),
      dirname(__FILE__).'/_fixture/',
      'Fixture\\'
    );

    $resolver->process()->redirect('index/index', false, false);

    $this->expectOutputString('');
  }
}
