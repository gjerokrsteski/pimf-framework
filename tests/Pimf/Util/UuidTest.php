<?php
class UuidTest extends PHPUnit_Framework_TestCase
{
  public function testRetreiveNewUuid()
  {
    $this->assertNotSame(
      Pimf_Util_Uuid::generate(),
      Pimf_Util_Uuid::generate()
    );
  }
}