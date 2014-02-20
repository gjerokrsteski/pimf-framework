<?php
class ErrorTest extends PHPUnit_Framework_TestCase
{
  public function testErrorLogging()
  {
    $mock = $this->getMockBuilder('\\Pimf\\Logger')
                      ->disableOriginalConstructor()
                      ->setMethods(array('error'))
                      ->getMock();

    \Pimf\Registry::set('logger', $mock);

    $this->assertNull(

      \Pimf\Error::log(new RuntimeException('test'))

    );
  }

  public function testWithErrorFormat()
  {
    \Pimf\Registry::set('conf',
     array_merge(
       (array)\Pimf\Registry::get('conf'),
       array(
         'error' => array(
           'ignore_levels' => array(0),
           'debug_info' => true,
           'log' => true,
         ),
       )
     )
    );

    $res = \Pimf\Error::format(new RuntimeException('test'));

    $this->stringContains('+++ Untreated Exception +++', $res);
  }

  public function testNativeHandleExceptionAndDisplayTheExceptionReport()
  {
    $this->assertNull(

      \Pimf\Error::native($code = 0, $error = 0, $file = '', $line = 0)

    );
  }

  public function testHandleShutdownEvent()
  {
    $this->assertNull(

      \Pimf\Error::shutdown()

    );
  }
}
 