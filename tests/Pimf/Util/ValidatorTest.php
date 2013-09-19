<?php
class ValidatorTest extends PHPUnit_Framework_TestCase
{
  public function testAllTypesAsHappyPath()
  {
    $request = array(
      'fname'        => 'conan',
      'lname'        => 'the barbar',
      'email'        => 'conan@movies.com',
      'pass1'        => 'strong123',
      'pass2'        => 'strong123',
      'color'        => 'yellow',
      'sentence'     => 'Hi! Am I here?',
      'age'          => 33,
      'number'       => 12,
      'car'          => 'ferrari',
      'monitor'      => 'sonyT2000',
      'date'         => '12/12/2040',
      'ip-indonesia' => '203.161.24.74',
      'ip-usa'       => '4.59.119.22',
      'some-url'     => 'https://travis-ci.org/gjerokrsteski/pimf',
    );

    $valid = new Pimf_Util_Validator(new Pimf_Param($request));

    $this->assertTrue($valid->length("lname", "<", 15), 'on length validator');

    $this->assertTrue($valid->email("email"), 'on email validator');

    $this->assertTrue($valid->compare("pass1", "pass2", true), 'on compare validator');

    $this->assertTrue($valid->compare("pass1", "pass2", false), 'on compare no inclusive validator');

    $this->assertTrue($valid->lengthBetween("color", 3, 15, true), 'on lengthBetween validator');

    $this->assertTrue($valid->lengthBetween("color", 3, 15, false), 'on lengthBetween no inclusive validator');

    $this->assertTrue($valid->punctuation('sentence'), 'on punctuation validator');

    $this->assertTrue($valid->value("age", ">", 18), 'on value validator');

    $this->assertTrue($valid->valueBetween("number", 11, 16), 'on value between validator');

    $this->assertTrue($valid->valueBetween("number", 11, 16, true), 'on value between with included validator');

    $this->assertTrue($valid->alpha("car"), 'on alpha validator');

    $this->assertTrue($valid->alphaNumeric("monitor"), 'on alpha-num validator');

    $this->assertTrue($valid->digit("age"), 'on digit validator');

    $this->assertTrue($valid->date("date", "mm/dd/yyyy"), 'on date validator');

    $this->assertTrue($valid->ip('ip-indonesia'), 'on ip validator');
    $this->assertTrue($valid->ip('ip-usa'), 'on ip validator');

    $this->assertTrue($valid->url('some-url'), 'on url validator');


    $this->assertEmpty($valid->getErrors());

  }

  public function testSettingInvalidData()
  {
    $valid = new Pimf_Util_Validator(new Pimf_Param(array()));

    $this->assertFalse($valid->length("lname", "<", 0), 'on length validator');

    $this->assertFalse($valid->email("email"), 'on email validator');

    $this->assertFalse($valid->compare("pass1", "pass2", true), 'on compare validator');

    $this->assertFalse($valid->compare("pass1", "pass2", false), 'on compare no inclusive validator');

    $this->assertFalse($valid->lengthBetween("color", 3, 15, true), 'on lengthBetween validator');

    $this->assertFalse($valid->lengthBetween("color", 3, 15, false), 'on lengthBetween no inclusive validator');

    $this->assertFalse($valid->punctuation('sentence'), 'on punctuation validator');

    $this->assertFalse($valid->value("age", ">", 18), 'on value validator');

    $this->assertFalse($valid->valueBetween("number", 11, 16), 'on value between validator');

    $this->assertFalse($valid->valueBetween("number", 16, 16, true), 'on value between with included validator');

    $this->assertFalse($valid->alpha("car"), 'on alpha validator');

    $this->assertFalse($valid->digit("age"), 'on digit validator');

    $this->assertFalse($valid->alphaNumeric("monitor"), 'on alpha-num validator');

    $this->assertFalse($valid->date("date", "mm/dd/yyyy"), 'on date validator');

    $this->assertFalse($valid->ip('ip-indonesia'), 'on ip validator');
    $this->assertFalse($valid->ip('ip-usa'), 'on ip validator');

    $this->assertFalse($valid->url('some-url'), 'on url validator');

    $this->assertNotEmpty($valid->getErrors());
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