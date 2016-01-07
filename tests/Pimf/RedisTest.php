<?php

class RedisTest extends \PHPUnit_Framework_TestCase
{
    const CRLF = "\r\n";

    /**
     * @return \Pimf\Redis
     */
    protected function getRedis()
    {
        return new \Pimf\Redis(new Pimf\Adapter\Socket('krsteski.de', 80), 0);
    }

    /**
     * @return \Pimf\Redis
     */
    protected function getRedisMock()
    {
        return $this->getMockBuilder('\\Pimf\\Redis')
            ->disableOriginalConstructor()
            ->setMethods(array('get', 'expire', 'set', 'del', 'forget', 'select', 'put', 'inline', 'bulk', 'multibulk'))
            ->getMock();
    }

    /**
     * Call protected/private method of a class.
     *
     * @param object &$object Instantiated object that we will run method on.
     * @param string $methodName Method name to call
     * @param array  $parameters Array of parameters to pass into method.
     *
     * @return mixed Method return.
     */
    public static function invokeMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }


    ## start testing


    public function testCreatingNewInstance()
    {
        new \Pimf\Redis(new Pimf\Adapter\Socket('127.0.0.1', 80), 0);
    }

    public function testGetDatabaseConnectionInstance()
    {
        \Pimf\Config::load(
            array(
                'cache' => array(
                    'storage' => 'redis',
                    'server'  => array('host' => '127.0.0.1', 'port' => 11211, 'database' => 0)
                )
            ),
            true
        );

        $this->assertInstanceOf(

            '\\Pimf\\Redis',

            \Pimf\Redis::database()
        );

    }

    /**
     * @expectedException \RuntimeException
     */
    public function testIfRedisDatabaseNotDefined()
    {
        \Pimf\Config::load(
            array(
                'cache' => array(
                    'storage' => '',
                    'server'  => array('host' => '127.0.0.1', 'port' => 11211, 'database' => 0)
                )
            ),
            true
        );

        \Pimf\Redis::database('default2');
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testGetReturnsNullWhenNotFound()
    {
        $redis = $this->getRedis();
        $redis->expects($this->any())->method('connect')->will($this->returnValue($redis));
        $redis->expects($this->once())->method('get')->will($this->returnValue(null));

        $this->assertNull($redis->get('foo'));
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testIfUnknownRedisResponse()
    {
        $redis = $this->getRedis();

        self::invokeMethod($redis, 'parse', array('bad-response-string'));
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testIfResponseIsRedisError()
    {
        $redis = $this->getRedis();

        self::invokeMethod($redis, 'parse', array('-erro' . "\r\n"));
    }

    public function testHappyParsing()
    {
        $redis = $this->getRedisMock();

        $redis->expects($this->any())->method('inline')->will($this->returnValue('ok'));
        $redis->expects($this->any())->method('bulk')->will($this->returnValue('ok'));
        $redis->expects($this->any())->method('multibulk')->will($this->returnValue('ok'));

        $this->assertEquals('ok', self::invokeMethod($redis, 'parse', array('+ foo' . "\r\n")));
        $this->assertEquals('ok', self::invokeMethod($redis, 'parse', array(': foo' . "\r\n")));
        $this->assertEquals('ok', self::invokeMethod($redis, 'parse', array('* foo' . "\r\n")));
        $this->assertEquals('ok', self::invokeMethod($redis, 'parse', array('$ foo' . "\r\n")));
    }

    public function testBuildingCommandBasedFromGivenMethodAndParameters()
    {
        $redis = $this->getRedis();

        $this->assertEquals(

            '*3' . self::CRLF . '$6' . self::CRLF . 'LRANGE' . self::CRLF . '$1' . self::CRLF . '0' . self::CRLF . '$1' . self::CRLF . '5' . self::CRLF . '',

            self::invokeMethod($redis, 'command', array('lrange', array(0, 5))),

            'problem on LRANGE'
        );

        $this->assertEquals(

            '*2' . self::CRLF . '$3' . self::CRLF . 'GET' . self::CRLF . '$4' . self::CRLF . 'name' . self::CRLF,

            self::invokeMethod($redis, 'command', array('get', array('name'))),

            'problem on GET name var'
        );

    }
}
 