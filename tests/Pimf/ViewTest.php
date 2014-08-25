<?php
class ViewTest extends PHPUnit_Framework_TestCase
{
  public static function setUpBeforeClass()
  {
    \Pimf\Registry::set('conf',
      array(
        'app' => array(
          'name' => 'test-app',
        ),
        'environment' => 'testing'
      )
    );
  }

  public function testCreatingNewInstance()
  {
    new \Pimf\View();
  }

  public function testWhenViewIsTreatedLikeAString()
  {
    $view = new \Pimf\View('default.phtml', array(), dirname(__FILE__) .'/_fixture/app/test-app/_templates/');
    $view->assign('age', 123);

    $this->assertStringStartsWith('123', ''.$view);
  }

  public function testProducing()
  {
    $view = new \Pimf\View();

    $view2 = $view->produce('default.phtml');

    $this->assertInstanceOf('\Pimf\View', $view2);
    $this->assertEquals($view, $view2);
  }

  public function testPartialRendering()
  {
    $view = new \Pimf\View('default.phtml', array(), dirname(__FILE__) .'/_fixture/app/test-app/_templates/');
    $res = $view->partial('default.phtml', array('age'=>123));

    $this->assertStringStartsWith('123', $res);
  }

  public function testLoopRendering()
  {
    $view = new \Pimf\View('default.phtml', array(), dirname(__FILE__) .'/_fixture/app/test-app/_templates/');
    $res = $view->loop('default.phtml', array(array('age'=>123), array('age'=>456)));

    $this->assertStringStartsWith('123', $res);
    $this->assertStringEndsWith('456', $res);
  }

  /**
   * @expectedException \OutOfRangeException
   */
  public function testIfTemplateNotFound()
  {
    $view = new \Pimf\View('bad-bad-template.phtml', array(), dirname(__FILE__) .'/_fixture/app/test-app/_templates/');
    $view->render();
  }

  /**
   * @expectedException \OutOfBoundsException
   */
  public function testIfRenderingUndefinedProperty()
  {
    $view = new \Pimf\View('default.phtml', array(), dirname(__FILE__) .'/_fixture/app/test-app/_templates/');
    $view->render();
  }
}
 