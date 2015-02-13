<?php
class UrlTest extends PHPUnit_Framework_TestCase
{
  private static $env;

  /**
   * @param string $switch Set on|off
   */
  protected static function fakeHttps($switch)
  {
    $server = array('HTTPS' => $switch, 'SCRIPT_NAME' => __FILE__, 'HOST' => 'http://localhost', 'SERVER_PROTOCOL' => 'HTTP/1.0');
    self::$env = new \Pimf\Environment($server);
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

  /**
   * @param string $index
   * @param bool   $ssl
   * @param bool   $routeable
   * @param string $url
   * @param string $asset_url
   */
  protected static function fakeConf($index = 'index.php', $ssl = true, $routeable = true, $url = 'http://localhost', $asset_url = '')
  {
    \Pimf\Config::load( array(

      'ssl' => $ssl,

      'app' => array(
    	  'routeable' => $routeable,
        'url' => $url,
        'index' => $index,
        'asset_url' => $asset_url,
      ),

    ),
      true

    );
  }

	/**
	 * Setup the test enviornment.
	 */
	public function setUp()
	{
		\Pimf\Url::$base = null;
    self::fakeConf();
    self::fakeHttps('off');
  }

	/**
	 * Destroy the test enviornment.
	 */
	public function tearDown()
	{
    self::fakeConf('', false);
	}


  ## start testing


	public function testToMethodGeneratesURL()
	{
    self::fakeConf('index.php', true);
		$this->assertEquals('http://localhost/index.php/user/profile', \Pimf\Url::to('user/profile'), 'bad schema 1');
    $this->assertEquals('https://localhost/index.php/user/profile', \Pimf\Url::to('user/profile', true), 'bad schema 2');

    self::fakeConf('', true);
		$this->assertEquals('http://localhost/user/profile', \Pimf\Url::to('user/profile'), 'bad schema is ssl=off');
		$this->assertEquals('https://localhost/user/profile', \Pimf\Url::to('user/profile', true), 'bad schema is ssl=on');

    self::fakeConf('', false);
		$this->assertEquals('http://localhost/user/profile', \Pimf\Url::to('user/profile', true), 'bad schema is ssl=off https=true');
	}

	public function testToAssetGeneratesURLWithoutFrontControllerInURL()
	{
    self::fakeConf('');
		$this->assertEquals('http://localhost/image.jpg', \Pimf\Url::to_asset('image.jpg'), '#1');
		$this->assertEquals('https://localhost/image.jpg', \Pimf\Url::to_asset('image.jpg', true), '#2');

		self::fakeHttps('on');
		$this->assertEquals('https://localhost/image.jpg', \Pimf\Url::to_asset('image.jpg'));
	}

  public function testComputingCleanerURLs()
 	{
    self::fakeConf('', true);
 		$this->assertEquals('http://localhost/user/profile/189', \Pimf\Url::compute('user/profile', array('id'=>189)), 'bad schema is ssl=off');
 		$this->assertEquals('https://localhost/user/profile/189', \Pimf\Url::compute('user/profile', array('id'=>189), true), 'bad schema is ssl=on');

     self::fakeConf('', false);
 		$this->assertEquals('http://localhost/user/profile/189', \Pimf\Url::compute('user/profile', array('id'=>189), true), 'bad schema is ssl=off https=true');
 	}

  public function testComputingRFC_3986_URLs()
  {
    self::fakeConf('', true, false);
 		$this->assertEquals('http://localhost/?controller=user&action=profile&id=189', \Pimf\Url::compute('user/profile', array('id'=>189)), 'bad schema is ssl=off');
 		$this->assertEquals('https://localhost/?controller=user&action=profile&id=189', \Pimf\Url::compute('user/profile', array('id'=>189), true), 'bad schema is ssl=on');

    self::fakeConf('', false, false);
 		$this->assertEquals('http://localhost/?controller=user&action=profile&id=189', \Pimf\Url::compute('user/profile', array('id'=>189), true), 'bad schema is ssl=off https=true');
  }


  public function testComputingFullURL()
  {
    $this->assertEquals( 'http://localhost/index.php/', \Pimf\Url::full() );
  }

  public function testComputingCurrentURL()
  {
    $this->assertEquals( 'http://localhost/index.php/',  \Pimf\Url::current());
  }

  public function testComputingHomeURL()
  {
    $this->assertEquals( 'http://localhost/index.php/',  \Pimf\Url::home() );
  }

  public function testGetBaseURLIfNoAtConfig()
  {
    self::fakeHttps('on');
    self::fakeConf('', false, true, '//xxx');
    $this->assertEquals( '//xxx',  \Pimf\Url::base() );
  }

  public function testGeneratingRealWorldRedirectingURL()
  {
    $this->assertEquals( 'http://web.com',  \Pimf\Url::to('http://web.com') );
  }

  public function testUrlAsHttps()
  {
    self::fakeConf('', true);
    $this->assertEquals( 'https://localhost/user/profile',  \Pimf\Url::as_https('user/profile') );
  }

  public function testGenerateApplicationUrlAssetByExternalUrl()
  {
    $this->assertEquals( 'http://web.com/some.css',  \Pimf\Url::to_asset('http://web.com/some.css') );
  }

  public function testShootUsThroughDifferentServerOrThird_partyContentDeliveryNetwork()
  {
    self::fakeConf('', false, true, '', 'http://web.com/css/');
    $this->assertEquals( 'http://web.com/css/some.css',  \Pimf\Url::to_asset('http://web.com/css/some.css') );
  }

  public function testThatWeDoNotNeedToComeThroughTheFrontController()
  {
    self::fakeConf('index.php', false, true, '', 'http://web.com/css/');
    $this->assertEquals( 'http://web.com/css/some.css',  \Pimf\Url::to_asset('http://web.com/css/some.css') );
  }

  public function testThatWeDoNotNeedToComeThroughTheFrontControllerAndNoNeedOffFullUrl()
  {
    self::fakeConf('index.php', false, true, '', '//web.com/css/');
    $this->assertEquals( '//web.com/css/some.css',  \Pimf\Url::to_asset('//web.com/css/some.css') );
  }
}