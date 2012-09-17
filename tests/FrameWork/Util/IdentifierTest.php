<?php
/**
 * @namespace   IdentifierTest.php
 * @copyright (c) 2012 Gjero Krsteski http://www.krsteski.de
 */
class IdentifierTest extends PHPUnit_Framework_TestCase
{
  /**
   * @test
   */
  public function CreatingNewInstance()
  {
    new Pimf_Util_Identifier(1, '23');
  }

  /**
   * @test
   * @expectedException BadMethodCallException
   */
  public function CreatingNewInstanceThrowingExceptionIfNoIdentifiersReceived()
  {
    new Pimf_Util_Identifier();
  }

  /**
   * Array could not be converted to string.
   * @test
   * @expectedException PHPUnit_Framework_Error
   */
  public function CreatingNewInstanceWithMixedArgsThrowingErrorTrigger()
  {
    $identifier = new Pimf_Util_Identifier(1, '23', array(1, 2, 3));
    print $identifier;
  }

  /**
   * @test
   */
  public function CreatingNewInstanceWithMixedArgs()
  {
    $identifier = new Pimf_Util_Identifier(1, '23', 123, 'hohoho');

          ob_start();
          print $identifier;
          $output = ob_get_contents();
          ob_end_clean();

    $this->assertEquals('1_23_123_hohoho', $output);
  }

  /**
   * @test
   */
  public function CreatingNewInstanceWithMixedArgsAndSpecialdelimiter()
  {
    $identifier = new Pimf_Util_Identifier(1, '23', 123, 'hohoho');

    $identifier->setDelimiter('/');

    ob_start();
    print $identifier;
    $output = ob_get_contents();
    ob_end_clean();

    $this->assertEquals('1/23/123/hohoho', $output);
  }

  /**
   * @test
   */
  public function CreatingNewInstanceWithMixedArgsAndSpecialDelimiterInFoxusOfSlaging()
  {
    $identifier = new Pimf_Util_Identifier(1, '23', 123, 'ZZ-TOP', 'Some_Class_name');

    $identifier->setDelimiter('/');

    ob_start();
    print $identifier;
    $output = ob_get_contents();
    ob_end_clean();

    $this->assertEquals('1/23/123/zz/top/some/class/name', $output);
  }
}
