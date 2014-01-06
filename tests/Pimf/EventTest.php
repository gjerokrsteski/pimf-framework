<?php
class EventDummyClass
{
  private function doSomethingPrivate()
  {
    return __FUNCTION__;
  }

  protected function doSomethingProtected()
  {
    return __FUNCTION__;
  }

  public function doSomethingPublic()
  {
    return __FUNCTION__;
  }
}


class EventTest extends PHPUnit_Framework_TestCase
{
  /**
 	 * Tear down the testing environment.
 	 */
 	public function tearDown()
 	{
 		\Pimf\Event::clear('test.event');
    \Pimf\Event::clear('start');
    \Pimf\Event::clear('queue.dummy.id');
 	}

  public function testCreateAndFireStartEvent()
  {
    $dummy = new EventDummyClass();
    \Pimf\Event::listen('start', array($dummy, 'doSomethingPublic'));
    \Pimf\Event::listen('start', function() {return 'Started as closure!';});
    $responses = \Pimf\Event::fire('start');

    $this->assertInternalType('array', $responses);
    $this->assertNotEmpty($responses);
  }

  /**
   * @expectedException PHPUnit_Framework_Error
   */
  public function testCreateAndFireStartProtectedEvent()
  {
    $dummy = new \EventDummyClass();
    \Pimf\Event::listen('start', array($dummy, 'doSomethingProtected'));
    \Pimf\Event::fire('start');
  }

  /**
   * @expectedException PHPUnit_Framework_Error
   */
  public function testCreateAndFireStartPrivateEvent()
  {
    $dummy = new EventDummyClass();
    \Pimf\Event::listen('start', array($dummy, 'doSomethingPrivate'));
    \Pimf\Event::fire('start');
  }

  public function testListenersAreFiredForEvents()
 	{
    \Pimf\Event::listen('test.event', function() { return 1; });
    \Pimf\Event::listen('test.event', function() { return 2; });
    \Pimf\Event::listen('test.event', function() { return 3; });

 		$responses = \Pimf\Event::fire('test.event');

 		$this->assertEquals(1, $responses[0]);
 		$this->assertEquals(2, $responses[1]);
    $this->assertEquals(3, $responses[2]);
 	}

 	public function testParametersCanBePassedToEvents()
 	{
    \Pimf\Event::listen('test.event', function($var) { return $var; });

 		$responses = \Pimf\Event::fire('test.event', array('Berry'));

 		$this->assertEquals('Berry', $responses[0]);
 	}
}
