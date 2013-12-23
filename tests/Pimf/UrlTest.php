<?php
class UrlTest extends PHPUnit_Framework_TestCase
{
  protected static function fakeHttps($switch)
  {
    $_SERVER = array('HTTPS' => $switch, 'SCRIPT_NAME' => __FILE__);
    \Pimf\Registry::set('env', new \Pimf\Environment($_SERVER));
  }

  protected static function fakeConf($index = 'index.php', $ssl = true, $routeable = true)
  {
    \Pimf\Registry::set('conf', array(

      'ssl' => $ssl,

      'app' => array(
    	  'routeable' => $routeable,
        'url' => 'http://localhost',
        'index' => $index,
        'asset_url' => '',
      ),

    ));
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
}