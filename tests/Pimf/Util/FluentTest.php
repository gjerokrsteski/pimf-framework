<?php
class FluentTest extends PHPUnit_Framework_TestCase
{
  public function testCreatingNewInstance()
  {
    new Pimf_Util_Fluent(array( 'name' => 'Lammy' ));
  }

  public function testUsingMagic()
  {
    $person = new Pimf_Util_Fluent();

    $person->name('Lammy')->age(25)->nullable();

    $this->assertSame('Lammy', $person->name);
    $this->assertSame(25, $person->age);
    $this->assertSame(null, $person->nullable);
  }

  public function testUsingAsArrayMagic()
  {
    $person = new Pimf_Util_Fluent();

    $person->name('Lammy')->age(25)->nullable();

    $this->assertSame('Lammy', $person['name']);
    $this->assertSame(25, $person['age']);
    $this->assertSame(null, $person['nullable']);
  }
}
