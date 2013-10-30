<?php
class ResolverTest extends PHPUnit_Framework_TestCase
{
  public static function setUpBeforeClass()
  {
    require_once dirname(__FILE__).'/_fixture/Index.php';

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
        ),
        'environment' => 'testing'
      )
    );
  }

  public function testCreatingNewInstance()
  {
    new \Pimf\Resolver(new \Pimf\Request($_GET));
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

  /**
   * @expectedException Exception
   */
  public function testIfNoActionFoundAtController()
  {
    $resolver = new \Pimf\Resolver(
      new \Pimf\Request(array(),array(),array(),array('action'=>'undefined')),
      dirname(__FILE__).'/_fixture/',
      'Fixture\\'
    );

    $resolver->process()->render();
  }
}
