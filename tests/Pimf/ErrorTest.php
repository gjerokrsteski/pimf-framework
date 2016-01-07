<?php

/**
 * @runTestsInSeparateProcesses
 */
class ErrorTest extends \PHPUnit_Framework_TestCase
{
    public function testErrorLogging()
    {
        $mock = $this->getMockBuilder('\\Pimf\\Logger')
            ->disableOriginalConstructor()
            ->setMethods(array('error'))
            ->getMock();

        $this->assertNull(

            \Pimf\Error::log(new RuntimeException('test'), $mock)

        );
    }

    public function testWithErrorFormatCalledFromTheCli()
    {
        $res = \Pimf\Error::format(new RuntimeException('test'), true);

        $this->stringContains('+++ Untreated Exception +++', $res);
    }

    public function testWithErrorFormatCalledFromTheWebBrowser()
    {
        $res = \Pimf\Error::format(new RuntimeException('test'), false);

        $this->stringContains('<h2>Untreated Exception</h2>', $res);
    }

    public function testIfIgnoreLevelsWillBeLogged()
    {
        \Pimf\Config::load(array(
            'error' => array('ignore_levels' => array(E_USER_DEPRECATED), 'log' => true)
        ), true
        );

        $mock = $this->getMockBuilder('\\Pimf\\Logger')
            ->disableOriginalConstructor()
            ->setMethods(array('error'))
            ->getMock();

        $this->assertNull(

            \Pimf\Error::native(E_USER_DEPRECATED, $error = 0, $file = '', $line = 0, $mock, 1, false)

        );
    }

    public function testNativeHandleExceptionAndDisplayTheExceptionReport()
    {
        $mock = $this->getMockBuilder('\\Pimf\\Logger')
            ->disableOriginalConstructor()
            ->setMethods(array('error'))
            ->getMock();

        ob_start();

        \Pimf\Error::native($code = 1, $error = 0, $file = '', $line = 0, $mock, 1, false);

        $res = ob_get_clean();

        $this->stringContains('+++ Untreated Exception +++', $res);
    }

    public function testNotHandleException()
    {
        $mock = $this->getMockBuilder('\\Pimf\\Logger')
            ->disableOriginalConstructor()
            ->setMethods(array('error'))
            ->getMock();

        $this->assertNull(

            \Pimf\Error::native($code = 0, $error = 0, $file = '', $line = 0, $mock, 0)

        );
    }

    public function testHandleShutdownEventIfNoErrorRaised()
    {
        $mock = $this->getMockBuilder('\\Pimf\\Logger')
            ->disableOriginalConstructor()
            ->setMethods(array('error'))
            ->getMock();

        $this->assertNull(

            \Pimf\Error::shutdown($mock, null)

        );
    }

    public function testIfFatalErrorOccurredAndHandleShutdownEvent()
    {
        $mock = $this->getMockBuilder('\\Pimf\\Logger')
            ->disableOriginalConstructor()
            ->setMethods(array('error'))
            ->getMock();

        $error = array();
        $error['message'] = 'a fatal error occurred';
        $error['type'] = 0;
        $error['file'] = __FILE__;
        $error['line'] = __LINE__;

        ob_start();

        \Pimf\Error::shutdown($mock, $error, false);

        $res = ob_get_clean();

        $this->stringContains('+++ Untreated Exception +++', $res);
    }

    public function testHappyPath()
    {
        $exception = new RuntimeException('boom');

        $mock = $this->getMockBuilder('\\Pimf\\Logger')
            ->disableOriginalConstructor()
            ->setMethods(array('error'))
            ->getMock();

        $env = new \Pimf\Environment(array('SERVER_PROTOCOL' => 'HTTP/1.1'));
        $envData = $env->data();
        \Pimf\Util\Header\ResponseStatus::setup($envData->get('SERVER_PROTOCOL', 'HTTP/1.0'));

        \Pimf\Util\Header::setup(
            $env->getUserAgent(),
            $env->HTTP_IF_MODIFIED_SINCE,
            $env->HTTP_IF_NONE_MATCH
        );

        \Pimf\Url::setup($env->getUrl(), $env->isHttps());
        \Pimf\Uri::setup($env->PATH_INFO, $env->REQUEST_URI);
        \Pimf\Util\Uuid::setup($env->getIp(), $env->getHost());

        ob_start();

        \Pimf\Error::exception($exception, $mock, false);

        $res = ob_get_clean();

        $this->stringContains('500', $res);
    }

    public function testForcing404StatusAsNotFound()
    {
        $exception = new \Pimf\Controller\Exception('boom');

        $mock = $this->getMockBuilder('\\Pimf\\Logger')
            ->disableOriginalConstructor()
            ->setMethods(array('error'))
            ->getMock();

        $env = new \Pimf\Environment(array('SERVER_PROTOCOL' => 'HTTP/1.1'));
        $envData = $env->data();
        \Pimf\Util\Header\ResponseStatus::setup($envData->get('SERVER_PROTOCOL', 'HTTP/1.0'));

        \Pimf\Util\Header::setup(
            $env->getUserAgent(),
            $env->HTTP_IF_MODIFIED_SINCE,
            $env->HTTP_IF_NONE_MATCH
        );

        \Pimf\Url::setup($env->getUrl(), $env->isHttps());
        \Pimf\Uri::setup($env->PATH_INFO, $env->REQUEST_URI);
        \Pimf\Util\Uuid::setup($env->getIp(), $env->getHost());

        ob_start();

        \Pimf\Error::exception($exception, $mock, false);

        $res = ob_get_clean();

        $this->stringContains('404', $res);
    }
}
 