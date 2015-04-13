<?php

class EnvironmentTest extends \PHPUnit_Framework_TestCase
{
    protected static function serverData()
    {
        $server['SERVER_NAME'] = 'pimf';
        $server['SERVER_PORT'] = '80';
        $server['SCRIPT_NAME'] = '/lol/index.php';
        $server['REQUEST_URI'] = '/lol/index.php/bar/xyz';
        $server['PATH_INFO'] = '/bar/xyz';
        $server['REQUEST_METHOD'] = 'GET';
        $server['QUERY_STRING'] = 'one=1&two=2&three=3';
        $server['HTTPS'] = '';
        $server['REMOTE_ADDR'] = '192.168.1.33';
        $server['SERVER_PROTOCOL'] = 'HTTP/1.0';
        $server['HOST'] = 'localhost';
        $server['PHP_SELF'] = 'index.php';
        $server['HTTP_ACCEPT'] = 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
        $server['HTTP_ACCEPT_CHARSET'] = 'ISO-8859-1,utf-8;q=0.7,*;q=0.3';
        $server['HTTP_CONNECTION'] = 'keep-alive';
        $server['HTTP_HOST'] = 'localhost';
        $server['HTTP_REFERER'] = 'http://localhost/';
        $server['HTTP_USER_AGENT'] = 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.17 (KHTML, like Gecko) Chrome/24.0.1312.57';

        return $server;
    }

    ## start testing

    public function testCreatingNewInstance()
    {
        new \Pimf\Environment(self::serverData());
    }

    public function testRetrievingEnvData()
    {
        $env = new \Pimf\Environment(self::serverData());

        $this->assertEquals(0, $env->CONTENT_LENGTH, 'on getContentLength');
        $this->assertNotEmpty($env->getIp(), 'on getIp');
        $this->assertNotEmpty($env->SERVER_PORT, 'on getPort');
        $this->assertNotEmpty($env->PHP_SELF, 'on getSelf');
        $this->assertNotEmpty($env->getHost(), 'on getHost');
        $this->assertNotEmpty($env->getHostWithPort(), 'on getHostWithPort');
        $this->assertNotEmpty($env->getPath(), 'on getPath');
        $this->assertNotEmpty($env->PATH_INFO, 'on getPathInfo');
        $this->assertNotEmpty($env->SERVER_PROTOCOL, 'on getProtocolInfo');
        $this->assertNotEmpty($env->HTTP_REFERER, 'on getReferer');
        $this->assertNotEmpty($env->SCRIPT_NAME, 'on getScriptName');
        $this->assertNotEmpty($env->SERVER_NAME, 'on getServerName');
        $this->assertNotEmpty($env->REQUEST_URI, 'on getUri');
        $this->assertEquals('http://localhost', $env->getUrl(), 'on getUrl');
        $this->assertNotEmpty($env->getUserAgent(), 'on getUserAgent');
        $this->assertFalse($env->isAjax(), 'on isAjax');
        $this->assertFalse($env->isHttp(), 'on isHttp');
        $this->assertFalse($env->isHttps(), 'on isHttps');
        $this->assertEquals('GET', $env->REQUEST_METHOD, 'on getRequestMethod');

        $this->assertEquals(self::serverData(), $env->data()->getAll(), 'on comparing all data');

        $this->assertEquals('http://localhost/', $env->getRequestHeader('REFERER'), 'on getRequestHeader');
        $this->assertNull($env->getRequestHeader('SOME-BAD-HEADER'), 'on getRequestHeader with some bad header');

        $headers['ACCEPT'] = 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
        $headers['ACCEPT_CHARSET'] = 'ISO-8859-1,utf-8;q=0.7,*;q=0.3';
        $headers['CONNECTION'] = 'keep-alive';
        $headers['HOST'] = 'localhost';
        $headers['REFERER'] = 'http://localhost/';
        $headers['USER_AGENT'] = 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.17 (KHTML, like Gecko) Chrome/24.0.1312.57';

        $this->assertEquals($headers, $env->getRequestHeaders(), 'on getRequestHeaders');
    }

    public function testFetchingHostWithPort()
    {
        $server = self::serverData();
        $server['HOST'] = 'localhost:666';
        $env = new \Pimf\Environment($server);

        $this->assertEquals('localhost', $env->getHost());
    }

    public function testFetchingNotExistingHost()
    {
        $server = self::serverData();
        unset($server['HOST']);
        $env = new \Pimf\Environment($server);

        $this->assertEquals('pimf', $env->getHost());
    }

    public function testFetchingIpByX_FORWARDED_FOR()
    {
        $server = self::serverData();
        $server['X_FORWARDED_FOR'] = '192.168.1.33';
        $env = new \Pimf\Environment($server);

        $this->assertEquals('192.168.1.33', $env->getIp());
    }

    public function testFetchingIpByCLIENT_IP()
    {
        $server = self::serverData();
        $server['CLIENT_IP'] = '100.168.1.33';
        $env = new \Pimf\Environment($server);

        $this->assertEquals('100.168.1.33', $env->getIp());
    }

    public function testFetchingIpByREMOTE_ADDR()
    {
        $server = self::serverData();
        unset($server['SERVER_NAME']);
        $env = new \Pimf\Environment($server);

        $this->assertEquals('192.168.1.33', $env->getIp());
    }

    public function testFetchingUserAgentByUSER_AGENT()
    {
        $server = self::serverData();
        $server['USER_AGENT'] = 'Chrome/24.0.1312.57';
        $env = new \Pimf\Environment($server);

        $this->assertEquals('Chrome/24.0.1312.57', $env->getUserAgent());
    }

    public function testFetchingUserAgentByIfNonPresent()
    {
        $server = self::serverData();
        unset($server['USER_AGENT']);
        unset($server['HTTP_USER_AGENT']);
        $env = new \Pimf\Environment($server);

        $this->assertNull($env->getUserAgent());
    }
}
