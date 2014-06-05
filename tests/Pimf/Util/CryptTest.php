<?php

class CryptTest extends PHPUnit_Framework_TestCase {

    public function testCreatingNewInstance()
    {
        new \Pimf\Util\Crypt();
    }

    public function testEncryptExpectsBase64Output()
    {
        $cryper = new \Pimf\Util\Crypt();
        $encrypted = $cryper->encrypt('some data here');

        $this->assertTrue(base64_decode($encrypted, true) !== false);
    }

    public function testDecryptExpectsStringOutput()
    {
        $cryper = new \Pimf\Util\Crypt();
        $encrypted = $cryper->encrypt('t i n t e d');
        $decrypted = $cryper->decrypt($encrypted);

        $this->assertInternalType('string', $decrypted);
    }

    public function testEncryptAndDecryptByOneInstance()
    {
        $tinted = json_encode(array('email' => 'test@test.de', 'expires' => time()));

        $cryper = new \Pimf\Util\Crypt();

        $encrypted = $cryper->encrypt($tinted);
        $decrypted = $cryper->decrypt($encrypted);

        $this->assertTrue($decrypted === $tinted, 'decrypted data is not same as tinted data!');
    }

    public function testEncryptAndDecryptByTwoInstances()
    {
        $tinted = json_encode(array('email' => 'test@test.de', 'expires' => time()));

        $cryper = new \Pimf\Util\Crypt();
        $encrypted = $cryper->encrypt($tinted);

        $cryper = new \Pimf\Util\Crypt();
        $decrypted = $cryper->decrypt($encrypted);

        $this->assertTrue($decrypted === $tinted, 'decrypted data is not same as tinted data!');
    }

    /**
     * @expectedException \UnexpectedValueException
     */
    public function testExpectingExceptionIfDecryptGetsNotHisOwnCipherTextBase64()
    {
        $cryper = new \Pimf\Util\Crypt();
        $cryper->decrypt(base64_encode('comes not from mr. crypt'));
    }

}
 