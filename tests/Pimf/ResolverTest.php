<?php

class ResolverTest extends \PHPUnit_Framework_TestCase
{
    ## prepare the fake environment

    private static $env, $logger, $em;

    public function setUp()
    {
        parent::setUp();

        require_once dirname(__FILE__) . '/_fixture/Index.php';
        require_once dirname(__FILE__) . '/_fixture/Rest.php';

        $_GET = array(
            'controller' => 'index',
            'action'     => 'save'
        );

        \Pimf\Config::load(
            array(
                'app'         => array(
                    'name'               => 'test-app-name',
                    'key'                => 'secret-key-here',
                    'default_controller' => 'index',
                    'routeable'          => false,
                ),
                'environment' => 'testing'
            ),
            true
        );

        $_SERVER['REQUEST_METHOD'] = 'POST';
        self::$env = new \Pimf\Environment($_SERVER);

        self::$logger = $this->getMockBuilder('\\Pimf\\Logger')
            ->disableOriginalConstructor()
            ->setMethods(array('error'))
            ->getMock();

        self::$em = $this->getMockBuilder('\\Pimf\\EntityManager')
            ->disableOriginalConstructor()
            ->setMethods(array('load'))
            ->getMock();
    }


    # start testing


    public function testCreatingNewInstance()
    {
        new \Pimf\Resolver(new \Pimf\Request($_GET, $postData = array(),
            $cookieData = array(),
            $cliData = array(),
            $filesData = array(),
            self::$env), dirname(__FILE__) . '/_fixture/', 'Pimf\\', new \Pimf\Router()
        );
    }

    public function testLoadingControllerInstance()
    {
        $resolver = new \Pimf\Resolver(new \Pimf\Request($_GET, $postData = array(),
            $cookieData = array(),
            $cliData = array(),
            $filesData = array(),
            self::$env), dirname(__FILE__) . '/_fixture/', 'Fixture\\', new \Pimf\Router()
        );

        $this->assertInstanceOf('\Pimf\Controller\Base', $resolver->process(self::$env, self::$em, self::$logger));
    }

    public function testIfNoActionGiven()
    {
        \Pimf\Config::load(
            array(
                'app'         => array(
                    'name'               => 'test-app-name',
                    'key'                => 'secret-key-here',
                    'default_controller' => 'index',
                    'routeable'          => false,
                ),
                'environment' => 'production'
            ),
            true
        );

        $resolver = new \Pimf\Resolver(new \Pimf\Request(array(), $postData = array(),
            $cookieData = array(),
            $cliData = array(),
            $filesData = array(),
            self::$env), dirname(__FILE__) . '/_fixture/', 'Fixture\\', new \Pimf\Router()
        );

        $this->assertInstanceOf('\Pimf\Controller\Base', $resolver->process(self::$env, self::$em, self::$logger));
    }

    public function testCallingControllerAction()
    {
        $resolver = new \Pimf\Resolver(new \Pimf\Request($_GET, $postData = array(),
            $cookieData = array(),
            $cliData = array(),
            $filesData = array(),
            self::$env), dirname(__FILE__) . '/_fixture/', 'Fixture\\', new \Pimf\Router()
        );

        $this->assertEquals(

            'indexAction',

            $resolver->process(self::$env, self::$em, self::$logger)->render()

        );
    }

    /**
     * @runInSeparateProcess
     */
    public function testCallingControllerActionByHttpMethodPut()
    {
        $_MY_SERVER['REQUEST_METHOD'] = 'PUT';
        $_MY_SERVER['REDIRECT_URL'] = '/rest/put/';

        $resolver = new \Pimf\Resolver(
            new \Pimf\Request(array('controller' => 'rest'), $postData = array(),
                $cookieData = array(),
                $cliData = array(),
                $filesData = array(),
                new \Pimf\Environment($_MY_SERVER)),
            dirname(__FILE__) . '/_fixture/',
            'Fixture\\',
            new \Pimf\Router()
        );

        $this->assertEquals(

            false,

            $resolver->process(new \Pimf\Environment($_MY_SERVER), self::$em, self::$logger)->render()

        );
    }


    /**
     * @expectedException \Pimf\Resolver\Exception
     */
    public function testIfNoControllerFoundAtTheRepositoryPath()
    {

        new \Pimf\Resolver(new \Pimf\Request($_GET, $postData = array(),
            $cookieData = array(),
            $cliData = array(),
            $filesData = array(),
            self::$env), '/Undefined_Controller_Repository/', 'Fixture\\', new \Pimf\Router()
        );

    }

    public function testIfNoActionFoundAtControllerTheRouterFindsTheIndexAction()
    {
        $resolver = new \Pimf\Resolver(new \Pimf\Request(array(), $postData = array(),
            $cookieData = array(),
            $cliData = array('action' => 'un de fi ned'),
            $filesData = array(),
            self::$env), dirname(__FILE__) . '/_fixture/', 'Fixture\\', new \Pimf\Router()
        );

        $this->assertEquals(

            'indexAction',

            $resolver->process(self::$env, self::$em, self::$logger)->render()

        );
    }

    public function testIfAppIsRouteable()
    {
        \Pimf\Config::load(
            array(
                'app'         => array(
                    'name'               => 'test-app-name',
                    'key'                => 'secret-key-here',
                    'default_controller' => 'index',
                    'routeable'          => true,
                ),
                'environment' => 'testing'
            ),
            true
        );

        $router = new \Pimf\Router();
        $router->map(new \Pimf\Route('index/save'));

        $resolver = new \Pimf\Resolver(new \Pimf\Request(array(), $postData = array('action' => 'save'),
            $cookieData = array(),
            $cliData = array(),
            $filesData = array(),
            self::$env), dirname(__FILE__) . '/_fixture/', 'Fixture\\', $router
        );

        $this->assertEquals(

            'saveAction',

            $resolver->process(self::$env, self::$em, self::$logger)->render()

        );
    }

    /**
     * @expectedException \Pimf\Resolver\Exception
     */
    public function testThatDirectoryTraversalAttackIsNotFunny()
    {
        new \Pimf\Resolver(new \Pimf\Request(array('controller' => '.../bad-path'), $postData = array(),
            $cookieData = array(),
            $cliData = array(),
            $filesData = array(),
            self::$env), dirname(__FILE__) . '/_fixture/', 'Fixture\\', new \Pimf\Router()
        );
    }

    /**
     * @expectedException \Pimf\Resolver\Exception
     */
    public function testThatDirectoryTraversalAttackIsNotFunnyOnProduction()
    {
        \Pimf\Config::load(
            array(
                'app'         => array(
                    'name'               => 'test-app-name',
                    'key'                => 'secret-key-here',
                    'default_controller' => 'index',
                    'routeable'          => true,
                ),
                'environment' => 'production'
            ),
            true
        );

        new \Pimf\Resolver(new \Pimf\Request(array(), $postData = array(),
            $cookieData = array(),
            $cliData = array('controller' => '.../bad-path'),
            $filesData = array(),
            self::$env), dirname(__FILE__) . '/_fixture/', 'Fixture\\', new \Pimf\Router()
        );

    }

    /**
     * @expectedException \Pimf\Resolver\Exception
     */
    public function testIfCanNotLoadClassControllerFromRepository()
    {
        \Pimf\Config::load(
            array(
                'app'         => array(
                    'name'               => 'test-app-name',
                    'key'                => 'secret-key-here',
                    'default_controller' => 'index',
                    'routeable'          => true,
                ),
                'environment' => 'testing'
            ),
            true
        );

        $resolver = new \Pimf\Resolver(new \Pimf\Request(array('controller' => 'bad'), $postData = array(),
            $cookieData = array(),
            $cliData = array(),
            $filesData = array(),
            self::$env), dirname(__FILE__) . '/_fixture/', 'Fixture\\', new \Pimf\Router()
        );


        $resolver->process(self::$env, self::$em, self::$logger);

    }

    /**
     * @runInSeparateProcess
     * @outputBuffering enabled
     */
    public function testIfAppCanRedirect()
    {
        self::$env =
            new \Pimf\Environment(
                array(
                    'HTTPS'           => 'off',
                    'SCRIPT_NAME'     => __FILE__,
                    'HOST'            => 'http://localhost',
                    'SERVER_PROTOCOL' => 'HTTP/1.0'
                ));

        \Pimf\Config::load(
            array(
                'app'         => array(
                    'name'               => 'test-app-name',
                    'key'                => 'secret-key-here',
                    'default_controller' => 'index',
                    'routeable'          => true,
                    'url'                => 'http://localhost',
                    'index'              => 'index.php',
                    'asset_url'          => '',
                ),
                'environment' => 'testing',
                'ssl'         => false,
            ),
            true
        );

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

        $router = new \Pimf\Router();
        $router->map(new \Pimf\Route('index/save'));

        # the test assertion

        $resolver = new \Pimf\Resolver(new \Pimf\Request(array(), $postData = array('action' => 'save'),
            $cookieData = array(),
            $cliData = array(),
            $filesData = array(),
            self::$env), dirname(__FILE__) . '/_fixture/', 'Fixture\\', new \Pimf\Router()
        );

        $resolver->process(self::$env, self::$em, self::$logger)->redirect('index/index', false, false);

        $this->expectOutputString('');
    }
}

