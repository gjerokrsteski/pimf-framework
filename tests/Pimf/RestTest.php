<?php
class Pimf_RestTest extends PHPUnit_Framework_TestCase
{
  public static function setUpBeforeClass()
  {
    require_once dirname(__FILE__).'/_fixture/Rest.php';

    Pimf_Registry::set('env', new Pimf_Environment(array('REQUEST_METHOD'=>'GET')));
  }

  public function testInstancingControllerDirectly()
  {
    $controller = new Fixture_Controller_Rest(new Pimf_Request(array()));

    $result = $controller->getAction();

    $this->assertInternalType('array', $result);
    $this->assertEmpty($result);
  }

  /**
   * @runInSeparateProcess
   */
  public function testCallingControllerAction()
  {
    $resolver = new Pimf_Resolver(
      new Pimf_Request(array(
            'controller'=> 'rest',
            'action'    => 'get',
            'data'      => array(1,2,3)
          )),
      dirname(__FILE__).'/_fixture/',
      'Fixture_'
    );

    $rest = $resolver->process();

    $this->assertInstanceOf('Fixture_Controller_Rest', $rest);
    $this->assertEquals(array(), $rest->render());
  }
}
