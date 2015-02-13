<?php
class UriTest extends \PHPUnit_Framework_TestCase {

  private static $env;

	public function tearDown()
	{
		\Pimf\Uri::$uri = null;
    \Pimf\Uri::$segments = array();
	}

	protected function fakeUri($uri)
	{
    $_SERVER = array('REQUEST_URI' => $uri, 'SCRIPT_NAME' => __FILE__, 'PATH_INFO' => $uri);
    self::$env = new \Pimf\Environment($_SERVER);
    $envData = self::$env->data();

    \Pimf\Util\Header\ResponseStatus::setup($envData->get('SERVER_PROTOCOL', 'HTTP/1.0'));

    \Pimf\Util\Header::setup(
      self::$env->getUserAgent(),
      self::$env->HTTP_IF_MODIFIED_SINCE,
      self::$env->HTTP_IF_NONE_MATCH
    );

    \Pimf\Url::setup(self::$env->getUrl(), self::$env->isHttps());
    \Pimf\Uri::setup(self::$env->PATH_INFO, self::$env->REQUEST_URI);
    \Pimf\Util\Uuid::setup(self::$env->getIp(), self::$env->getHost());
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
    $this->assertEquals($expectation, \Pimf\Uri::current());
	}

  public function testDetermineIfCurrentUriMatchesGivenPattern()
  {
    $this->fakeUri('/user/profile');
 		$this->assertTrue(\Pimf\Uri::is('user/profile'));
  }
}