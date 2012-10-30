<?php
class CrypterTest extends PHPUnit_Framework_TestCase
{
  protected static $testString = 'pimf rocks!';

  protected function setUp()
  {
    Pimf_Registry::set('config', array('app'=>array('name'=>'MyTestApp')));
  }

  public function testEncryptAndDecrypt()
  {
    $encrypt = Pimf_Util_Crypter::encrypt(self::$testString);

    $decrypt = Pimf_Util_Crypter::decrypt($encrypt);

    $this->assertSame($decrypt, self::$testString);
  }
}
