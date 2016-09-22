<?php

class RequestTest extends \PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
        $_GET = array(
            'controller' => 'index',
            'action'     => 'save'
        );
        $_POST = array(
            'firstname' => 'billy',
            'lastname'  => 'gatter'
        );
        $_COOKIE = array(
            'name' => 'pimf',
            'date' => '01-01-2017'
        );
    }

    public function testCreatingFullNewInstance()
    {
        new \Pimf\Request($_GET, $postData = array(),
            $cookieData = array(),
            $cliData = array(),
            $filesData = array(),
            new \Pimf\Environment(array())
        );
    }

    public function testGetData()
    {
        $request = new \Pimf\Request($_GET, $postData = array(),
            $cookieData = array(),
            $cliData = array(),
            $filesData = array(),
            new \Pimf\Environment(array())
        );

        $this->assertNotNull($request->fromGet()->get('controller'));
        $this->assertEquals('index', $request->fromGet()->get('controller'));
    }

    public function testPostData()
    {
        $request = new \Pimf\Request(array(), $_POST,
            $cookieData = array(),
            $cliData = array(),
            $filesData = array(),
            new \Pimf\Environment(array())
        );

        $this->assertNotNull($request->fromPost()->get('firstname'));
        $this->assertEquals('billy', $request->fromPost()->get('firstname'));
    }

    public function testCookieData()
    {
        $request = new \Pimf\Request(array(), array(), $_COOKIE,
            $cliData = array(),
            $filesData = array(),
            new \Pimf\Environment(array())
        );

        $this->assertNotNull($request->fromCookie()->get('date'));
        $this->assertEquals('01-01-2017', $request->fromCookie()->get('date'));
    }


    public function testCliData()
    {
        $request = new \Pimf\Request(array(), array(), array(),
            $cliData = array('date' => '01-01-2017'),
            $filesData = array(),
            new \Pimf\Environment(array())
        );

        $this->assertNotNull($request->fromCli()->get('date'));
        $this->assertEquals('01-01-2017', $request->fromCli()->get('date'));
    }

    /**
     * @expectedException LogicException
     */
    public function testLoadingInputContentTwiceTime()
    {
        $request = new \Pimf\Request(array(), array(), array(),
            $cliData = array('date' => '01-01-2017'),
            $filesData = array(),
            new \Pimf\Environment(array())
        );

        $request->getContent(true);
        $request->getContent();
    }

    /**
     * @runInSeparateProcess
     */
    public function testLoadingInputContent()
    {
        $request = new \Pimf\Request(array(), array(), array(),
            $cliData = array(),
            $filesData = array(),
            new \Pimf\Environment(array())
        );

        $this->stringContains('test', $request->getContent());
    }

    /**
     * @runInSeparateProcess
     */
    public function testLoadingContentAsStreamedResource()
    {
        $request = new \Pimf\Request(array(), array(), array(),
            $cliData = array(),
            $filesData = array(),
            new \Pimf\Environment(array())
        );

        $this->assertInternalType('resource', $request->getContent(true));
    }

    /**
     * @runInSeparateProcess
     */
    public function testInputStreamingByUsingRequestMethodPatch()
    {
        $requestFake
            = new \Pimf\Request(array('from-get' => 'from-get-value'),
            array('from-post' => 'from-post-value'), array(),
            $cliData = array(),
            $filesData = array(),
            new \Pimf\Environment(array(
                'REQUEST_METHOD'    => 'PATCH',
                'HTTP_CONTENT_TYPE' => 'application/x-www-form-urlencoded'
            ))
        );

        $this->assertInstanceOf('\\Pimf\\Param', $requestFake->streamInput());
    }

    /**
     * @runInSeparateProcess
     */
    public function testInputStreamingByUsingRequestMethodPatchFetchingBodyAsResource()
    {
        $requestFake
            = new \Pimf\Request(array('from-get' => 'from-get-value'),
            array('from-post' => 'from-post-value'), array(),
            $cliData = array(),
            $filesData = array(),
            new \Pimf\Environment(array(
                'REQUEST_METHOD'    => 'PATCH',
                'HTTP_CONTENT_TYPE' => 'application/x-www-form-urlencoded'
            ))
        );

        $this->assertTrue(is_resource($requestFake->streamInput(true)));
    }

    /**
     * @runInSeparateProcess
     */
    public function testInputStreamingForNotAllowedHttpMethod()
    {
        $request = new \Pimf\Request(array(), array(), array(),
            $cliData = array(),
            $filesData = array(),
            new \Pimf\Environment(array(
                'REQUEST_METHOD'    => 'GET',
                'HTTP_CONTENT_TYPE' => 'application/x-www-form-urlencoded'
            ))
        );

        $this->assertFalse($request->streamInput());
    }
}
