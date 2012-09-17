<?php
/**
 * @namespace   PropertyValueTest.php
 * @copyright   (c) 2012 Gjero Krsteski http://www.krsteski.de/
 */

class xUser
{
  public $name = 'public-conan';
  public $fruits = array('public-banana');
  protected $email = 'protected-fff@cool.de';
  private $bday = 'private-12-12-3333';
  public function getName() { return $this->name; }
}

class xEnum
{
  const January = 1;
  const February = 2;
}

class PropertyValueTest extends PHPUnit_Framework_TestCase
{
  public static function ensureBooleanProvider()
  {
    return array(
      array('', false),
      array(' ', false),
      array(1, true),
      array('1', true),
      array(0, false),
      array('0', false),
      array(array(), false),
      array(array(1), false),
      array(new stdClass(), false),
      array('true', true),
      array('false', false),
      array('yes', true),
      array('no', false),
    );
  }

  /**
   * @param $value
   * @param $expected
   * @dataProvider ensureBooleanProvider
   */
  public function testEnsureBoolean($value, $expected)
  {
    $this->assertSame($expected, Pimf_Util_PropertyValue::ensureBoolean($value));
  }

  public static function ensureStringProvider()
  {
    return array(
      array('', ''),
      array(' ', ' '),
      array(1, '1'),
      array(1234, '1234'),
      array(1234.1234, '1234.1234'),
      array('1', '1'),
      array(array(), 'Array'),
      array(array(1), 'Array'),
      array(true, 'true'),
      array(false, 'false'),
    );
  }

  /**
   * @param $value
   * @param $expected
   * @dataProvider ensureStringProvider
   */
  public function testEnsureString($value, $expected)
  {
    $this->assertSame($expected, Pimf_Util_PropertyValue::ensureString($value));
  }

  public static function ensureArrayProvider()
  {
    return array(
      array('', array()),
      array(' ', array()),
      array(1, array(1)),
      array(1234, array(1234)),
      array(1234.1234, array(1234.1234)),
      array('1', array('1')),
      array(array(1), array(1)),
      array(true, array(true)),
      array(false, array(false)),
      array('[1,2,3]', array('1','2','3')),
      array(new stdClass(), array()),
      array(new xUser(), array('name' => 'public-conan', 'fruits' => array('public-banana'))),
    );
  }

  /**
   * @param $value
   * @param $expected
   * @dataProvider ensureArrayProvider
   */
  public function testEnsureArray($value, $expected)
  {
    $this->assertSame($expected, Pimf_Util_PropertyValue::ensureArray($value));
  }

  public function testEnsureEnum()
  {
    $this->assertSame(1, Pimf_Util_PropertyValue::ensureEnum('January', 'xEnum'));
  }

  /**
   * @expectedException InvalidArgumentException
   */
  public function testEnsureEnumThrowingException()
  {
    Pimf_Util_PropertyValue::ensureEnum('March', 'xEnum');
  }
}
