<?php

class SessionDbaTest extends \PHPUnit_Framework_TestCase
{
    protected function getCache()
    {
        return $this->getMockBuilder('\Pimf\Cache\Storages\Dba')
            ->disableOriginalConstructor()
            ->setMethods(array('retrieve', 'forever', 'put', 'forget', 'flush', 'get', 'getFile', 'clean'))
            ->getMock();
    }

    # start testing

    public function testCreatingNewInstance()
    {
        new \Pimf\Session\Storages\Dba($this->getCache());
    }


    public function testGeyKeyThatIsNotAtTheSession()
    {
        $storage = new \Pimf\Session\Storages\Dba($this->getCache());

        $this->assertNull($storage->load('some-bad-key'));
    }


    public function testSavingTheSession()
    {
        $session['id'] = 'cool-secret-id';
        $config['lifetime'] = 60;

        $storage = new \Pimf\Session\Storages\Dba($this->getCache());

        $this->assertNull($storage->save($session, $config, 60));
    }

    public function testDeleteKeyThatIsNotAtTheSession()
    {
        $storage = new \Pimf\Session\Storages\Dba($this->getCache());

        $this->assertNull($storage->delete('some-bad-key'));
    }

    public function testCleaning()
    {
        $storage = new \Pimf\Session\Storages\Dba($this->getCache());

        $this->assertNull($storage->clean(60));
    }
}
 