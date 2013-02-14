<?php
class ValidatorTest extends PHPUnit_Framework_TestCase
{
  public function testAllTypesAsHappyPath()
  {
    $request = array(
      'fname'    => 'conan',
      'lname'    => 'the barbar',
      'email'    => 'conan@movies.com',
      'pass1'    => 'strong123',
      'pass2'    => 'strong123',
      'color'    => 'yellow',
      'sentence' => 'Hi! Am I here?',
      'age'      => 33,
      'number'   => 12,
      'car'      => 'ferrari',
      'monitor'  => 'sonyT2000',
      'date'     => '12/12/2040',
    );

    $validator = new Pimf_Util_Validator(new Pimf_Param($request));

    $this->assertTrue($validator->length("lname", "<", 15), 'on length validator');

    $this->assertTrue($validator->email("email"), 'on email validator');

    $this->assertTrue($validator->compare("pass1", "pass2", true), 'on compare validator');

    $this->assertTrue($validator->compare("pass1", "pass2", false), 'on compare no inclusive validator');

    $this->assertTrue($validator->lengthBetween("color", 3, 15, true), 'on lengthBetween validator');

    $this->assertTrue($validator->lengthBetween("color", 3, 15, false), 'on lengthBetween no inclusive validator');

    $this->assertTrue($validator->punctuation('sentence'), 'on punctuation validator');

    $this->assertTrue($validator->value("age", ">", 18), 'on value validator');

    $this->assertTrue($validator->valueBetween("number", 11, 16), 'on value between validator');

    $this->assertTrue($validator->valueBetween("number", 11, 16, true), 'on value between with included validator');

    $this->assertTrue($validator->alpha("car"), 'on alpha validator');

    $this->assertTrue($validator->alphaNumeric("monitor"), 'on alpha-num validator');

    $this->assertTrue($validator->digit("age"), 'on digit validator');

    $this->assertTrue($validator->date("date", "mm/dd/yyyy"), 'on date validator');

    $this->assertEmpty($validator->getErrors());

  }

  public function testSettingInvalidData()
  {
    $validator = new Pimf_Util_Validator(new Pimf_Param(array()));

    $this->assertFalse($validator->length("lname", "<", 0), 'on length validator');

    $this->assertFalse($validator->email("email"), 'on email validator');

    $this->assertFalse($validator->compare("pass1", "pass2", true), 'on compare validator');

    $this->assertFalse($validator->compare("pass1", "pass2", false), 'on compare no inclusive validator');

    $this->assertFalse($validator->lengthBetween("color", 3, 15, true), 'on lengthBetween validator');

    $this->assertFalse($validator->lengthBetween("color", 3, 15, false), 'on lengthBetween no inclusive validator');

    $this->assertFalse($validator->punctuation('sentence'), 'on punctuation validator');

    $this->assertFalse($validator->value("age", ">", 18), 'on value validator');

    $this->assertFalse($validator->valueBetween("number", 11, 16), 'on value between validator');

    $this->assertFalse($validator->valueBetween("number", 16, 16, true), 'on value between with included validator');

    $this->assertFalse($validator->alpha("car"), 'on alpha validator');

    $this->assertFalse($validator->digit("age"), 'on digit validator');

    $this->assertFalse($validator->alphaNumeric("monitor"), 'on alpha-num validator');

    $this->assertFalse($validator->date("date", "mm/dd/yyyy"), 'on date validator');

    $this->assertNotEmpty($validator->getErrors());
  }

  public function testCreatingAFactoryOfRules()
  {
    $attributes = array(
      'fname'    => 'conan',
      'age'      => 33,
      'birth'    => '12-12-2040',
      'monitor'  => 'sonyT2000',
    );

    $rules = array(
      'fname'   => 'alpha|length[>,0]|lengthBetween[1,9]',
      'age'     => 'digit|value[>,18]|value[=,33]',
      'birth'   => 'length[>,0]|date[mm-dd-yyyy]',
      'monitor' => 'alphaNumeric'
    );

    $validator = Pimf_Util_Validator::factory($attributes, $rules);

    $this->assertEmpty($validator->getErrors());
    $this->assertEmpty($validator->getErrorMessages());
  }
}