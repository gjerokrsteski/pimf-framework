<?php
class ResolverTest extends PHPUnit_Framework_TestCase
{

  ## prepare the environment


  public static function setUpBeforeClass()
  {
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

  public function testCallingControllerAction()
  {
    $resolver = new \Pimf\Resolver(
      new \Pimf\Request($_GET),
      dirname(__FILE__).'/_fixture/',
      'Fixture\\'
    );

    $resolver->process();
  }

  /**
   * @expectedException \Pimf\Resolver\Exception
   */
  public function testIfNoControllerFoundAtTheRepositoryPath()
  {
    $resolver = new \Pimf\Resolver(
      new \Pimf\Request($_GET),
      '/Undefined_Controller_Repository/',
      'Fixture\\'
    );

    $resolver->process()->render();
  }


  public function testIfNoActionFoundAtControllerTheRouterFindsTheIndexAction()
  {
    $resolver = new \Pimf\Resolver(
      new \Pimf\Request(array(),array(),array(),array('action'=>'un de fi ned')),
      dirname(__FILE__).'/_fixture/',
      'Fixture\\'
    );

    $resolver->process()->render();
  }
}
