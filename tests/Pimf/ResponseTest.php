<?php

class ResponseTest extends \PHPUnit_Framework_TestCase
{

    private static $env;

    protected static function fakeEnv($fake)
    {
        self::$env = new \Pimf\Environment((array)$fake);

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

        return self::$env;
    }

    public function testCreatingNewInstanceExpectingNoExceptionIfComesFromCli()
    {
        $this->assertInstanceOf('Pimf\\Response', new \Pimf\Response(null));
        $this->assertInstanceOf('Pimf\\Response', new \Pimf\Response(null));
    }

    /**
     * @runInSeparateProcess
     */
    public function testSendingJsonData()
    {
        $response = new \Pimf\Response('POST');
        $response->asJSON()->send(array('hello' => 'Barry'), false);

        $this->expectOutputString('{"hello":"Barry"}');
    }

    /**
     * @runInSeparateProcess
     * @outputBuffering enabled
     */
    public function testSendingTextData()
    {
        $response = new \Pimf\Response('POST');
        $response->asTEXT()->send('hello Barry!', false);

        $this->expectOutputString('hello Barry!');
    }

    /**
     * @runInSeparateProcess
     * @outputBuffering enabled
     */
    public function testSendingXmlData()
    {
        $response = new \Pimf\Response('GET');
        $response->asTEXT()->send('<hello>Barry!</hello>', false);

        $this->expectOutputString('<hello>Barry!</hello>');
    }

    /**
     * @runInSeparateProcess
     * @outputBuffering enabled
     * @expectedException RuntimeException
     */
    public function testBombingExceptionIfMultipleCachesSent()
    {
        $response = new \Pimf\Response('GET');
        $response->cacheBrowser(1)->cacheNone();
    }

    /**
     * @runInSeparateProcess
     * @outputBuffering enabled
     * @expectedException RuntimeException
     */
    public function testBombingExceptionIfNo_GET_RequestSent()
    {
        $response = new \Pimf\Response('POST');
        $response->cacheBrowser(1);
    }

    /**
     * @runInSeparateProcess
     * @outputBuffering enabled
     */
    public function testSendingCachedTextData()
    {
        $response = new \Pimf\Response('GET');
        $response->asTEXT()->cacheBrowser(60)->send('Barry is cached at the browser', false);

        $this->expectOutputString('Barry is cached at the browser');
    }

    /**
     * @runInSeparateProcess
     * @outputBuffering enabled
     */
    public function testSendPdfFile()
    {
        $server['USER_AGENT'] = 'Chrome/24.0.1312.57';
        self::fakeEnv($server);


        # start testing

        $response = new \Pimf\Response('GET');
        $response->asPDF()->sendStream(new \SplTempFileObject(-1), 'fake.pdf', false);
    }

    /**
     * @runInSeparateProcess
     * @outputBuffering enabled
     */
    public function testSendCsvFile()
    {
        $server['USER_AGENT'] = 'Chrome/24.0.1312.57';
        self::fakeEnv($server);

        # start testing

        $response = new \Pimf\Response('GET');
        $response->asCSV()->sendStream(new \SplTempFileObject(-1), 'fake.csv', false);
    }

    /**
     * @runInSeparateProcess
     * @outputBuffering enabled
     */
    public function testSendZipFile()
    {
        $server['USER_AGENT'] = 'MSIE 5.5 blahh blahhh';
        self::fakeEnv($server);

        # start testing

        $response = new \Pimf\Response('GET');
        $response->asZIP()->sendStream(new \SplTempFileObject(-1), 'fake.zip', false);
    }

    /**
     * @runInSeparateProcess
     * @outputBuffering enabled
     */
    public function testSendXZipFile()
    {
        $server['USER_AGENT'] = 'Chrome/24.0.1312.57';
        self::fakeEnv($server);

        # start testing

        $response = new \Pimf\Response('GET');
        $response->asXZIP()->sendStream(new \SplTempFileObject(-1), 'fake.zip', false);
    }

    /**
     * @runInSeparateProcess
     * @outputBuffering enabled
     */
    public function testSendMswordFile()
    {
        $server['USER_AGENT'] = 'Chrome/24.0.1312.57';
        self::fakeEnv($server);

        # start testing

        $response = new \Pimf\Response('GET');
        $response->asMSWord()->sendStream(new \SplTempFileObject(-1), 'fake.doc', false);
    }

    /**
     * @runInSeparateProcess
     * @outputBuffering enabled
     */
    public function testSendingAView()
    {
        $view = $this->getMockBuilder('\Pimf\View')
            ->disableOriginalConstructor()
            ->setMethods(array('render'))
            ->getMock();

        $view->expects($this->any())
            ->method('render')
            ->will($this->returnValue('i-am-rendered'));

        $response = new \Pimf\Response('GET');
        $response->asTEXT()->send($view, false);

        $this->expectOutputString('i-am-rendered');
    }

    /**
     * @runInSeparateProcess
     * @outputBuffering enabled
     */
    public function testSendingWithNoCaching()
    {
        $response = new \Pimf\Response('GET');
        $response->asTEXT()->cacheNone()->send('Barry is not cached at the browser!', false);

        $this->expectOutputString('Barry is not cached at the browser!');
    }

    /**
     * @runInSeparateProcess
     * @outputBuffering enabled
     */
    public function testSendingWithNotValidatedCachingForOneSecond()
    {
        $response = new \Pimf\Response('GET');
        $response->asTEXT()->cacheNoValidate(1)->send('Barry is not cached at the browser!', false);

        $this->expectOutputString('Barry is not cached at the browser!');
    }

    /**
     * @runInSeparateProcess
     * @outputBuffering enabled
     */
    public function testSendingWithCachingAndIfNotModifiedSinceOneSecond()
    {
        $server['HTTP_IF_MODIFIED_SINCE'] = gmdate('D, d M Y H:i:s', time()) . ' GMT';
        self::fakeEnv($server);

        # start testing

        $response = new \Pimf\Response('GET');
        $response->asTEXT()->exitIfNotModifiedSince(1)->send('Barry is not cached at the browser!', false);

        $this->expectOutputString('Barry is not cached at the browser!');
    }
}
 