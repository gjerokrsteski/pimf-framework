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
      'date'         => '1999-12-13',
      'ip-indonesia' => '203.161.24.74',
      'ip-usa'       => '4.59.119.22',
      'some-url'     => 'http://www.krsteski.de',
    );

    $valid = new \Pimf\Util\Validator(new \Pimf\Param($request));

    $this->assertTrue($valid->length("lname", "<", 15), 'on length validator');
    $this->assertTrue($valid->length("lname", "==", 10), 'on length validator is 10');
    $this->assertTrue($valid->length("lname", ">=", 10), 'on length validator bigger same 10');
    $this->assertTrue($valid->length("lname", "<=", 10), 'on length validator smaller same 10');
    $this->assertTrue($valid->length("lname", ">", 1), 'on length validator bigger 1');

    $this->assertTrue($valid->email("email"), 'on email validator');

    $this->assertTrue($valid->compare("pass1", "pass2", true), 'on compare validator');

    $this->assertTrue($valid->compare("pass1", "pass2", false), 'on compare no inclusive validator');

    $this->assertTrue($valid->lengthBetween("color", 3, 15, true), 'on lengthBetween validator');

    $this->assertTrue($valid->lengthBetween("color", 3, 15, false), 'on lengthBetween no inclusive validator');

    $this->assertTrue($valid->punctuation('sentence'), 'on punctuation validator');

    $this->assertTrue($valid->value("age", ">", 18), 'on value validator');
    $this->assertTrue($valid->value("age", "<", 50), 'on value validator');
    $this->assertTrue($valid->value("age", ">=", 33), 'on value validator');
    $this->assertTrue($valid->value("age", "<=", 33), 'on value validator');
    $this->assertTrue($valid->value("age", "==", 33), 'on value validator');

    $this->assertTrue($valid->valueBetween("number", 11, 16), 'on value between validator');

    $this->assertTrue($valid->valueBetween("number", 11, 16, true), 'on value between with included validator');

    $this->assertTrue($valid->alpha("car"), 'on alpha validator');

    $this->assertTrue($valid->alphaNumeric("monitor"), 'on alpha-num validator');

    $this->assertTrue($valid->digit("age"), 'on digit validator');

    $this->assertTrue($valid->date("date", "Y-m-d"), 'on date validator');

    $this->assertTrue($valid->ip('ip-indonesia'), 'on ip validator');
    $this->assertTrue($valid->ip('ip-usa'), 'on ip validator');

    $this->assertTrue($valid->url('some-url'), 'on url validator');


    $this->assertEmpty($valid->getErrors());

    $this->assertTrue($valid->isValid());

  }

  public function testSettingInvalidData()
  {
    $request = array(
      'fname'        => 'conan',
      'lname'        => 'the barbar',
      'email'        => 'conan[at]movies.com',
      'pass1'        => 'strong124',
      'pass2'        => 'strong12356',
      'color'        => 'yellow',
      'sentence'     => 'Hi! Am I 1 2 3 (/ 65765%&$"§453/%&§}[}]z here?',
      'age'          => '18 years',
      'number'       => 1,
      'car'          => 'ferrari 2000',
      'monitor'      => 'sony 123 =(/%$§"%&4 ²³][²³',
      'date'         => '1999-15-13',
      'ip-indonesia' => '999.161.24.74',
      'ip-usa'       => '0.59.999.22',
      'some-url'     => 'ftp:/krsteski.de',
    );

    $valid = new \Pimf\Util\Validator(new \Pimf\Param($request));

    $this->assertFalse($valid->length("lname", "<", 0), 'on length validator');
    $this->assertFalse($valid->length("lname", '', 0), 'on length validator if no operator');

    $this->assertFalse($valid->email("email"), 'on email validator');

    $this->assertFalse($valid->compare("pass1", "pass2", true), 'on compare validator');

    $this->assertFalse($valid->compare("pass1", "pass2", false), 'on compare no inclusive validator');

    $this->assertFalse($valid->lengthBetween("color", 3, 4, true), 'on lengthBetween validator');

    $this->assertFalse($valid->lengthBetween("color", 3, 4, false), 'on lengthBetween no inclusive validator');

    $this->assertFalse($valid->punctuation('sentence'), 'on punctuation validator');

    $this->assertFalse($valid->value("age", ">", 18), 'on value validator');
    $this->assertFalse($valid->value("age", "", 18), 'on value validator if no operator');

    $this->assertFalse($valid->valueBetween("number", 11, 16), 'on value between validator');

    $this->assertFalse($valid->valueBetween("number", 16, 16, true), 'on value between with included validator');

    $this->assertFalse($valid->alpha("car"), 'on alpha validator');

    $this->assertFalse($valid->digit("age"), 'on digit validator');

    $this->assertFalse($valid->alphaNumeric("monitor"), 'on alpha-num validator');

    $this->assertFalse($valid->date("date", "Y-m-d"), 'on date validator');

    $this->assertFalse($valid->ip('ip-indonesia'), 'on ip validator');
    $this->assertFalse($valid->ip('ip-usa'), 'on ip validator');

    $this->assertFalse($valid->url('some-url'), 'on url validator');


    $this->assertNotEmpty($valid->getErrorMessages());
    $this->assertFalse($valid->isValid());
  }

  /**
   * @expectedException \OutOfBoundsException
   */
  public function testBombingExceptionIfNoAttributeFoundAtRequestData()
  {
    $valid = new \Pimf\Util\Validator(new \Pimf\Param($request = array('age'=>18)));

    $valid->value("age-not-at-request", ">", 18);
  }
}