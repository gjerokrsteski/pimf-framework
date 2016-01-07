<?php

class ValueTest extends \PHPUnit_Framework_TestCase
{

    public function testCreatingNewInstance()
    {
        new Pimf\Util\Value('some string');
    }

    public function testIfPrependedValue()
    {
        $value = new Pimf\Util\Value('http://www.example.com');
        $this->assertStringStartsWith('/', '' . $value->prepend('/'));
        $this->assertStringStartsNotWith('//', '' . $value->prepend('/'));
    }

    public function testIfAppendedValue()
    {
        $value = new Pimf\Util\Value('http://www.example.com');
        $this->assertStringEndsWith('/', '' . $value->append('/'));
        $this->assertStringEndsNotWith('//', '' . $value->append('/'));
    }

    public function testIfContains()
    {
        $value = new Pimf\Util\Value('milk man rocks');
        $this->assertTrue($value->contains('man'));
        $this->assertTrue($value->contains(array('rocks', 'milk')));
    }

    public function testIfStarts()
    {
        $value = new Pimf\Util\Value('milk man rocks');
        $this->assertTrue($value->starts('milk'));
        $this->assertFalse($value->starts('man'));
    }

    public function testIfEnds()
    {
        $value = new Pimf\Util\Value('milk man rocks');
        $this->assertTrue($value->ends('rocks'));
        $this->assertFalse($value->ends('man'));
    }

    public function testGettingSecondElementOrRedirectUrl()
    {
        $redirectUrl = new Pimf\Util\Value('/controller/action/');
        $this->assertEquals('controller/action/', '' . $redirectUrl->deleteLeading('/'));
        $this->assertEquals('controller/action', '' . $redirectUrl->deleteTrailing('/'));
        $this->assertEquals(array('controller', 'action'), $redirectUrl->explode('/'));
    }
}
 