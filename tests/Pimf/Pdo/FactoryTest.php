<?php
class PdoFactoryTest extends PHPUnit_Framework_TestCase
{
  public function testCreatingNewInstance()
  {
    new \Pimf\Pdo\Factory();
  }

  public static function badDriverConfig()
  {
    return array(

      array(array()),

      array(array('driver'=>'')),

      array(array('driver'=>null)),

    );
  }

  public static function varyBadDriverConfig()
  {
    return array(

      array(null),

      array(''),

    );
  }


  public static function notSupportedDivers()
  {
    return array(
      array(array('driver'=>' MySQL')),
      array(array('driver'=>'mysqli')),
      array(array('driver'=>'mongo')),
    );
  }


  /**
   * @expectedException RuntimeException
   * @dataProvider PdoFactoryTest::badDriverConfig
   */
  public function testIfNoDriverSpecified($driver)
  {
    \Pimf\Pdo\Factory::get($driver);
  }

  /**
   * @expectedException PHPUnit_Framework_Error
   * @dataProvider PdoFactoryTest::varyBadDriverConfig
   */
  public function testIfVaryBadDriverSpecified($driver)
  {
    \Pimf\Pdo\Factory::get($driver);
  }

  /**
   * @expectedException UnexpectedValueException
   * @dataProvider PdoFactoryTest::notSupportedDivers
   */
  public function testNotSupportedDrivers($driver)
  {
    \Pimf\Pdo\Factory::get($driver);
  }

  /**
   * @expectedException RuntimeException
   */
  public function testIfDriverIsSupportedButNotLoaded()
  {
    \Pimf\Pdo\Factory::get(array('driver'=>'sqlserver'));
  }
}
 