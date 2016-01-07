<?php

class PimfCookieTest extends \PHPUnit_Framework_TestCase
{

    # helpers

    public function setUp()
    {
        \Pimf\Cookie::$jar = array();
        \Pimf\Request::$cookieData = new \Pimf\Param(array());
        \Pimf\Config::load(array(
            'ssl' => true,
            'app' => array('key' => '123secure')
        ), true
        );
    }

    protected function setServerVar($key, $value)
    {
        $_SERVER[$key] = $value;
    }

    # unit test

    public function testHasMethodIndicatesIfCookieInSet()
    {
        \Pimf\Cookie::$jar['foo'] = array('value' => \Pimf\Cookie::hash('bar') . '+bar');
        $this->assertTrue(\Pimf\Cookie::has('foo'));
        $this->assertFalse(\Pimf\Cookie::has('bar'));

        \Pimf\Cookie::put('baz', 'foo');
        $this->assertTrue(\Pimf\Cookie::has('baz'));
    }

    public function testGetMethodCanReturnValueOfCookies()
    {
        \Pimf\Cookie::$jar['foo'] = array('value' => \Pimf\Cookie::hash('bar') . '+bar');
        $this->assertEquals('bar', \Pimf\Cookie::get('foo'));

        \Pimf\Cookie::put('bar', 'baz');
        $this->assertEquals('baz', \Pimf\Cookie::get('bar'));
    }

    public function testForeverShouldUseATonOfMinutes()
    {
        \Pimf\Cookie::forever('foo', 'bar');
        $this->assertEquals(\Pimf\Cookie::hash('bar') . '+bar', \Pimf\Cookie::$jar['foo']['value']);

        $this->setServerVar('HTTPS', 'on');

        \Pimf\Cookie::forever('bar', 'baz', 'path', 'domain', false);

        $this->assertEquals('path', \Pimf\Cookie::$jar['bar']['path']);
        $this->assertEquals('domain', \Pimf\Cookie::$jar['bar']['domain']);
        $this->assertFalse(\Pimf\Cookie::$jar['bar']['secure']);

        $this->setServerVar('HTTPS', 'off');
    }

    public function testForgetSetsCookieWithExpiration()
    {
        \Pimf\Cookie::forget('bar', 'path', 'domain');

        $this->assertEquals('path', \Pimf\Cookie::$jar['bar']['path']);
        $this->assertEquals('domain', \Pimf\Cookie::$jar['bar']['domain']);
        $this->assertFalse(\Pimf\Cookie::$jar['bar']['secure']);
    }
}