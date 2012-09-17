<?php
class UuidTest extends PHPUnit_Framework_TestCase
{
  /**
   * @test
   */
  public function RetreiveNewUuid()
  {
    $this->assertNotSame(
      Pimf_Util_Uuid::generate(),
      Pimf_Util_Uuid::generate()
    );
  }
}