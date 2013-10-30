<?php
class EnvironmentTest extends PHPUnit_Framework_TestCase
{
  public function setUp()
  {
    $_SERVER['SERVER_NAME']    = 'pimf';
    $_SERVER['SERVER_PORT']    = '80';
    $_SERVER['SCRIPT_NAME']    = '/lol/index.php';
    $_SERVER['REQUEST_URI']    = '/lol/index.php/bar/xyz';
    $_SERVER['PATH_INFO']      = '/bar/xyz';
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $_SERVER['QUERY_STRING']   = 'one=1&two=2&three=3';
    $_SERVER['HTTPS']          = '';
    $_SERVER['REMOTE_ADDR']    = '127.0.0.1';
    unset($_SERVER['CONTENT_TYPE'], $_SERVER['CONTENT_LENGTH']);
  }

  public function testCreatingNewInstance()
  {
    new \Pimf\Environment($_SERVER);
  }

  public function testRetreivingEnvData()
  {
    $env = new \Pimf\Environment($_SERVER);

    $this->assertEquals(0, $env->getContentLength(), 'on getContentLength');
    $this->assertNotEmpty($env->getIp(), 'on getIp');
    $this->assertNotEmpty($env->getPort(), 'on getPort');
    $this->assertNotEmpty($env->getSelf(), 'on getSelf');
    $this->assertNotEmpty($env->getHost(), 'on getHost');
    $this->assertNotEmpty($env->getHostWithPort(), 'on getHostWithPort');
    $this->assertNotEmpty($env->getPath(), 'on getPath');
    $this->assertNotEmpty($env->getPathInfo(), 'on getPathInfo');
    $this->assertNull($env->getReferer(), 'on getReferer');
    $this->assertNotEmpty($env->getScriptName(), 'on getScriptName');
    $this->assertNotEmpty($env->getServerName(), 'on getServerName');
    $this->assertNotEmpty($env->getUri(), 'on getUri');
    $this->assertNull($env->getUserAgent(), 'on getUserAgent');
    $this->assertFalse($env->isAjax(), 'on isAjax');
    $this->assertFalse($env->isHttp(), 'on isHttp');
    $this->assertFalse($env->isHttps(), 'on isHttps');
  }
}
