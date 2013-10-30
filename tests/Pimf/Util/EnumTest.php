<?php
require_once dirname(__FILE__).'/_fixture/EnumFixtures.php';

use Fixture\WeekDays, Fixture\State;

class EnumTest extends PHPUnit_Framework_TestCase
{
  public function testDefaultValue()
  {
    $this->assertEquals("Monday", new WeekDays());
  }

  public function testUsingConstWithConstructor()
  {
    $this->assertEquals("Tuesday", new WeekDays(WeekDays::Tuesday));
  }

  public function testUsingStringWithConstructor()
  {
    $this->assertEquals("Friday", new WeekDays('Friday'));
  }

  public function testCaseInsensativeStringWithConstructor()
  {
    $this->assertEquals("Friday", new WeekDays('fRiDAy'));
  }

  public function testUsingFactoryMethod()
  {
    $this->assertEquals("Friday", WeekDays::Friday());
  }

  public function testGettingIntegerFromValue()
  {
    $day = new WeekDays(WeekDays::Friday);
    $this->assertEquals(5, $day());
  }

  /**
   * @expectedException UnexpectedValueException
   */
  public function testSettingIncorrectValue()
  {
    new WeekDays('FooBar');
  }

  public function testUsingDefaultValue()
  {
    $this->assertEquals("Active", new State());
  }

  public function testUsingConstantWithConstructor()
  {
    $this->assertEquals("Inactive", new State(State::Inactive));
  }

  public function testUsingStaticFactoryMethod()
  {
    $this->assertEquals("Inactive", State::Inactive());
  }
}
