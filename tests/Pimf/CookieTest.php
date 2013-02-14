<?php
class Pimf_CookieTest extends PHPUnit_Framework_TestCase
{

  # helpers

  public function setUp()
  {
    Pimf_Cookie::$jar = array();
    Pimf_Request::$cookieData = new Pimf_Param(array());
    Pimf_Registry::set(
      'conf', array(
        'ssl' => true,
        'app' => array( 'key' => '123secure' )
      )
    );
  }

  protected function setServerVar($key, $value)
  {
    $_SERVER[$key] = $value;
  }

  # unit test

  public function testHasMethodIndicatesIfCookieInSet()
  {
    Pimf_Cookie::$jar['foo'] = array( 'value' => Pimf_Cookie::hash('bar') . '+bar' );
    $this->assertTrue(Pimf_Cookie::has('foo'));
    $this->assertFalse(Pimf_Cookie::has('bar'));

    Pimf_Cookie::put('baz', 'foo');
    $this->assertTrue(Pimf_Cookie::has('baz'));
  }

  public function testGetMethodCanReturnValueOfCookies()
  {
    Pimf_Cookie::$jar['foo'] = array( 'value' => Pimf_Cookie::hash('bar') . '+bar' );
    $this->assertEquals('bar', Pimf_Cookie::get('foo'));

    Pimf_Cookie::put('bar', 'baz');
    $this->assertEquals('baz', Pimf_Cookie::get('bar'));
  }

  public function testForeverShouldUseATonOfMinutes()
  {
    Pimf_Cookie::forever('foo', 'bar');
    $this->assertEquals(Pimf_Cookie::hash('bar') . '+bar', Pimf_Cookie::$jar['foo']['value']);

    $this->setServerVar('HTTPS', 'on');

    Pimf_Cookie::forever('bar', 'baz', 'path', 'domain', true);

    $this->assertEquals('path', Pimf_Cookie::$jar['bar']['path']);
    $this->assertEquals('domain', Pimf_Cookie::$jar['bar']['domain']);
    $this->assertTrue(Pimf_Cookie::$jar['bar']['secure']);

    $this->setServerVar('HTTPS', 'off');
  }

  public function testForgetSetsCookieWithExpiration()
  {
    Pimf_Cookie::forget('bar', 'path', 'domain');

    $this->assertEquals('path', Pimf_Cookie::$jar['bar']['path']);
    $this->assertEquals('domain', Pimf_Cookie::$jar['bar']['domain']);
    $this->assertFalse(Pimf_Cookie::$jar['bar']['secure']);
  }
}