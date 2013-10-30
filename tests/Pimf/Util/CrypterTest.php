<?php
class CrypterTest extends PHPUnit_Framework_TestCase
{
  protected static $testString = 'pimf rocks!';

  protected function setUp()
  {
    \Pimf\Registry::set('config', array('app'=>array('name'=>'MyTestApp')));
  }

  public function testEncryptAndDecrypt()
  {
    $encrypt = Pimf\Util\Crypter::encrypt(self::$testString);

    $decrypt = Pimf\Util\Crypter::decrypt($encrypt);

    $this->assertSame($decrypt, self::$testString);
  }
}
