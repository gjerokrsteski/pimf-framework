<?php

class StrTest extends \PHPUnit_Framework_TestCase
{
    protected $testString = '';

    protected function setUp()
    {
        parent::setUp();

        $this->testString = file_get_contents(dirname(__FILE__) . '/_fixture/samp-string.html');
    }

    public function testCheckEncoding()
    {
        $this->assertTrue(\Pimf\Util\Str::checkUtf8Encoding($this->testString));
    }

    public function testCleanAggressive()
    {
        $res = \Pimf\Util\Str::cleanAggressive($this->testString);

        $this->assertEquals(
            file_get_contents(dirname(__FILE__) . '/_fixture/expects-clean-aggressive.html'),
            str_replace(array(' ', PHP_EOL), '', $res)
        );
    }

    public function testCleanXss()
    {
        $res = \Pimf\Util\Str::cleanXss($this->testString);

        $this->assertEquals(
            file_get_contents(dirname(__FILE__) . '/_fixture/expects-clean-xss.html'),
            str_replace(array(' ', PHP_EOL), '', $res)
        );
    }

    public function testEnsureTrailing()
    {
        $res = \Pimf\Util\Str::ensureTrailing('/', 'http://www.example.com');
        $this->assertStringEndsWith('/', $res);

        $res = \Pimf\Util\Str::ensureTrailing('/', 'http://www.example.com/');
        $this->assertStringEndsWith('/', $res);
        $this->assertStringEndsNotWith('//', $res);

        $res = \Pimf\Util\Str::ensureTrailing('/', '//uc/receipt/');
        $this->assertStringEndsWith('/', $res);
        $this->assertStringEndsNotWith('//', $res);
    }

    public function testEnsureLeading()
    {
        $res = \Pimf\Util\Str::ensureLeading('#', '1#2#3#4#5');
        $this->assertStringStartsWith('#1', $res);

        $res = \Pimf\Util\Str::ensureLeading('#', '#1#2#3#4#5');
        $this->assertStringStartsWith('#1', $res);
    }

    public function testDeleteLeading()
    {
        $res = \Pimf\Util\Str::deleteLeading('#', '#1#2#3#4#5');
        $this->assertStringStartsWith('1#', $res); // -> 1#2#3#4#5

        $res = \Pimf\Util\Str::deleteLeading(array('#', '1'), '##111#2#3#4#5');
        $this->assertStringStartsWith('2#', $res); // -> 2#3#4#5
    }

    public function testDeleteTrailing()
    {
        $res = \Pimf\Util\Str::deleteTrailing('|', '|1|2|3|4|5|');
        $this->assertStringEndsWith('|5', $res); // -> |1|2|3|4|5

        $res = \Pimf\Util\Str::deleteTrailing(array('|', '5'), '|1|2|3|4|5|555');
        $this->assertStringEndsWith('|4', $res); // -> |1|2|3|4
    }

    public static function provideSerializedTestData()
    {
        return array(
            array(serialize(array(21.123, 21.124, 2, 0))),
            array(serialize('some string here')),
            array(serialize((object)array('eee' => 21.123, 'asdfasdf' => 21.124))),
        );
    }

    /**
     * @dataProvider provideSerializedTestData
     */
    public function testIsSerialized($data)
    {
        $this->assertTrue(

            \Pimf\Util\Str::isSerialized($data),

            'problem on asserting that ' . print_r($data, true) . ' is serialized'

        );
    }

    public static function providerOfEvilPaths()
    {
        return array(
            array('http://www.example.com/index.foo?item=../../../Config.sys'),
            array("http://www.example.com/index.foo?item=../../../Windows/System32/cmd.exe?/C+dir+C:\\"),
            array('/foo/bar/../controller.php'),
            array('http://www.example.com/%2e%2e%2f'),
            array('http://www.example.com/%2e%2e%5Ccontroller.php'),
            array('/foo/bar/controller.php?action=../00%'),
            array('http://localhost/?controller=../%00'),
            array('http://localhost/?controller=some bad controller name'),
            array('http://localhost/?controller=some%20bad%20controller%20name'),
        );
    }

    /**
     * @dataProvider providerOfEvilPaths
     */
    public function testIsEvilPathContainsBadCombinations($path)
    {
        $this->assertTrue(\Pimf\Util\Str::isEvilPath($path));
    }

    public function testIsEvilPathContainsNoBadCombinations()
    {
        $this->assertFalse(\Pimf\Util\Str::isEvilPath('/foo/bar/controller.php'));
    }

    public function testStartsWith()
    {
        $this->assertTrue(\Pimf\Util\Str::startsWith('//www.krsteski.de', '//'));
    }

    public function testEndsWith()
    {
        $this->assertTrue(\Pimf\Util\Str::endsWith('//www.krsteski.de?index.php', '?index.php'));
    }

    public function testIsPattern()
    {
        $this->assertTrue(\Pimf\Util\Str::is('user/profile', 'user/profile'));
    }

    public function testIsWildcard()
    {
        $this->assertTrue(\Pimf\Util\Str::is('user/*', 'user/profile/update'));
    }

    public function testIsNotEmptyString()
    {
        $this->assertFalse(\Pimf\Util\Str::is('/', 'home'));
    }

    public function testRandom()
    {
        $res = \Pimf\Util\Str::random();

        $this->assertInternalType('string', $res);
        $this->assertEquals(32, strlen($res));
    }

    public function testContains()
    {
        $this->assertTrue(\Pimf\Util\Str::contains('user/save', array('user', 'save', 'user/')));
    }

    public function testNotContains()
    {
        $this->assertFalse(\Pimf\Util\Str::contains('user/save', 'hugo'));
    }
}
