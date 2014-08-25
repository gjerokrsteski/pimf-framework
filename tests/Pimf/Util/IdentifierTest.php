<?php
class IdentifierTest extends PHPUnit_Framework_TestCase
{
  /**
   * @test
   */
  public function CreatingNewInstance()
  {
    new \Pimf\Util\Identifier(1, '23');
  }

  /**
   * @test
   * @expectedException BadMethodCallException
   */
  public function CreatingNewInstanceThrowingExceptionIfNoIdentifiersReceived()
  {
    new \Pimf\Util\Identifier();
  }

  /**
   * @test
   */
  public function CreatingNewInstanceWithMixedArgs()
  {
    $identifier = new \Pimf\Util\Identifier(1, '23', 123, 'hohoho');

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
    $identifier = new \Pimf\Util\Identifier(1, '23', 123, 'hohoho');

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
    $identifier = new \Pimf\Util\Identifier(1, '23', 123, 'ZZ-TOP', 'Some_Class_name');

    $identifier->setDelimiter('/');

    ob_start();
    print $identifier;
    $output = ob_get_contents();
    ob_end_clean();

    $this->assertEquals('1/23/123/zz/top/some/class/name', $output);
  }

}
