<?php
class Pimf_ResolverTest extends PHPUnit_Framework_TestCase
{
  public static function setUpBeforeClass()
  {
    require_once dirname(__FILE__).'/_fixture/Index.php';

    $_GET    = array(
      'controller'=> 'index',
      'action'    => 'save'
    );
  }

  public function testCreatingNewInstance()
  {
    new Pimf_Resolver(new Pimf_Request($_GET));
  }

  public function testLoadingControllerInstance()
  {
    $resolver = new Pimf_Resolver(
      new Pimf_Request($_GET),
      dirname(__FILE__).'/_fixture/',
      'Fixture_'
    );

    $this->assertInstanceOf('Pimf_Controller_Abstract', $resolver->process());
  }

  public function testCallingControllerAction()
  {
    $resolver = new Pimf_Resolver(
      new Pimf_Request($_GET),
      dirname(__FILE__).'/_fixture/',
      'Fixture_'
    );

    $resolver->process()->render();
  }

  /**
   * @expectedException RuntimeException
   */
  public function testIfNoControllerFoundAtTheRepositoryPath()
  {
    $resolver = new Pimf_Resolver(
      new Pimf_Request($_GET),
      '/Undefined_Controller_Repository/',
      'Fixture_'
    );

    $resolver->process()->render();
  }

  /**
   * @expectedException RuntimeException
   */
  public function testIfNoActionFoundAtController()
  {
    $_GET['action'] = 'undefined';

    $resolver = new Pimf_Resolver(
      new Pimf_Request($_GET),
      dirname(__FILE__).'/_fixture/',
      'Fixture_'
    );

    $resolver->process()->render();
  }
}
