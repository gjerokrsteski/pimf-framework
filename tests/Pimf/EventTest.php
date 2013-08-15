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
 		Pimf_Event::clear('test.event');
    Pimf_Event::clear('start');
    Pimf_Event::clear('queue.dummy.id');
 	}

  public function testCreateAndFireStartEvent()
  {
    $dummy = new EventDummyClass();
    Pimf_Event::listen('start', array($dummy, 'doSomethingPublic'));
    Pimf_Event::listen('start', function() {return 'Started as closure!';});
    $responses = Pimf_Event::fire('start');

    $this->assertInternalType('array', $responses);
    $this->assertNotEmpty($responses);
  }

  /**
   * @expectedException PHPUnit_Framework_Error
   */
  public function testCreateAndFireStartProtectedEvent()
  {
    $dummy = new EventDummyClass();
    Pimf_Event::listen('start', array($dummy, 'doSomethingProtected'));
    Pimf_Event::fire('start');
  }

  /**
   * @expectedException PHPUnit_Framework_Error
   */
  public function testCreateAndFireStartPrivateEvent()
  {
    $dummy = new EventDummyClass();
    Pimf_Event::listen('start', array($dummy, 'doSomethingPrivate'));
    Pimf_Event::fire('start');
  }

  public function testQueuedEvents()
  {
    $dummy = new EventDummyClass();

    //#1 registering an event flusher for the queue
    Pimf_Event::flusher('queue.dummy.id', function($key, $data, $arg1, $arg2)
    {
        // do anytime something with $key, $data, $arg1, $arg2
    });

    //#2 registering queued events
    Pimf_Event::queue('queue.dummy.id', 1, array($dummy, 'param1', 'param_etc'));
    Pimf_Event::queue('queue.dummy.id', 2, array($dummy, 'param11', 'param_etc'));
    Pimf_Event::queue('queue.dummy.id', 3, array($dummy, 'param12', 'param_etc'));

    //run flusher and flush all queued events
    Pimf_Event::flush('queue.dummy.id');
  }

  public function testListenersAreFiredForEvents()
 	{
    Pimf_Event::listen('test.event', function() { return 1; });
    Pimf_Event::listen('test.event', function() { return 2; });
    Pimf_Event::listen('test.event', function() { return 3; });

 		$responses = Pimf_Event::fire('test.event');

 		$this->assertEquals(1, $responses[0]);
 		$this->assertEquals(2, $responses[1]);
    $this->assertEquals(3, $responses[2]);
 	}

 	public function testParametersCanBePassedToEvents()
 	{
    Pimf_Event::listen('test.event', function($var) { return $var; });

 		$responses = Pimf_Event::fire('test.event', array('Berry'));

 		$this->assertEquals('Berry', $responses[0]);
 	}
}
