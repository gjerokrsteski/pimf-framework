<?php

class LockTest extends \PHPUnit_Framework_TestCase
{

    public function testCreatingNewInstance()
    {
        new \Pimf\Util\Lock('lock');
    }


    public function testConstructSanitizeName()
    {
        $lock = new \Pimf\Util\Lock('<?php echo "% hello word ! %" ?>');

        $file = sprintf(
            '%s/pimf.-php-echo-hello-word-.4b3d9d0d27ddef3a78a64685dda3a963e478659a9e5240feaf7b4173a8f28d5f.lock',
            sys_get_temp_dir()
        );

        @unlink($file);

        $lock->lock();

        $this->assertFileExists($file);

        $lock->release();
    }

    public function testLockRelease()
    {
        $name = 'pimf-test-filesystem.lock';

        $lock1 = new \Pimf\Util\Lock($name);
        $lock2 = new \Pimf\Util\Lock($name);

        $this->assertTrue($lock1->lock());
        $this->assertFalse($lock2->lock());

        $lock1->release();

        $this->assertTrue($lock2->lock());
        $lock2->release();
    }

    public function testLockTwice()
    {
        $name = 'pimf-test-filesystem.lock';

        $lockHandler = new \Pimf\Util\Lock($name);

        $this->assertTrue($lockHandler->lock());
        $this->assertTrue($lockHandler->lock());

        $lockHandler->release();
    }

    public function testLockIsReleased()
    {
        $name = 'pimf-test-filesystem.lock';

        $lock1 = new \Pimf\Util\Lock($name);
        $lock2 = new \Pimf\Util\Lock($name);

        $this->assertTrue($lock1->lock());
        $this->assertFalse($lock2->lock());

        $lock1 = null;

        $this->assertTrue($lock2->lock());
        $lock2->release();
    }
}
