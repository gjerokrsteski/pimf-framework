<?php
class ValidatorFactoryTest extends \PHPUnit_Framework_TestCase
{
  public function testFactorizingValidatorBySetOfRules()
  {
    $attributes = array(
      'fname'    => 'conan',
      'age'      => 33,
      'birth'    => '1888-11-25',
      'monitor'  => 'sonyT2000',
    );

    $rules = array(
      'fname'   => 'alpha|length[>,0]|lengthBetween[1,9]',
      'age'     => 'digit|value[>,18]|value[==,33]',
      'birth'   => 'length[>,0]|date[Y-m-d]',
      'monitor' => 'alphaNumeric'
    );

    $validator = \Pimf\Util\Validator\Factory::get($attributes, $rules);

    $this->assertInstanceOf('Pimf\\Util\\Validator', $validator);
    $this->assertEmpty($msg = $validator->getErrors(), 'with validator errors '.print_r($msg,true));
  }

}
 