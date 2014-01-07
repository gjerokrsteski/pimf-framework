<?php
class RestTest extends PHPUnit_Framework_TestCase
{
  public static function setUpBeforeClass()
  {
    require_once dirname(__FILE__) . '/_fixture/Rest.php';

    \Pimf\Registry::set('env', new \Pimf\Environment(array('REQUEST_METHOD'=>'GET')));
  }

  public function testInstancingControllerDirectly()
  {
    $controller = new \Fixture\Controller\Rest(new \Pimf\Request(array()));

    $result = $controller->getAction();

    $this->assertInternalType('array', $result);
    $this->assertEmpty($result);
  }

  /**
   * @runInSeparateProcess
   */
  public function testCallingControllerAction()
  {
    $resolver = new \Pimf\Resolver(
      new \Pimf\Request(array(
            'controller'=> 'rest',
            'action'    => 'get',
            'data'      => array(1,2,3)
          )),
      dirname(__FILE__).'/_fixture/',
      'Fixture\\'
    );

    $rest = $resolver->process();

    $this->assertInstanceOf('\Fixture\Controller\Rest', $rest);
    $this->assertEquals(array(), $rest->render());
  }
}
