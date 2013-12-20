<?php
class UriTest extends PHPUnit_Framework_TestCase {

	public function tearDown()
	{
		\Pimf\Uri::$uri = null;
    \Pimf\Uri::$segments = array();
	}

	protected function fakeUri($uri)
	{
    $_SERVER = array('REQUEST_URI' => $uri, 'SCRIPT_NAME' => __FILE__, 'PATH_INFO' => $uri);
    \Pimf\Registry::set('env', new \Pimf\Environment($_SERVER));
  }

  public function requestUriProvider()
 	{
 		return array(
 			array('/user', 'user'),
 			array('/user/', 'user'),
 			array('', '/'),
 			array('/', '/'),
 			array('//', '/'),
 			array('/user', 'user'),
 			array('/user/', 'user'),
 			array('/user/profile', 'user/profile'),
 		);
 	}


  # start testing

	/**
	 * @dataProvider requestUriProvider
	 */
	public function testCorrectURIIsReturnedByCurrentMethod($uri, $expectation)
	{
		$this->fakeUri($uri);
		$this->assertEquals($expectation, \Pimf\Uri::current());
	}
}