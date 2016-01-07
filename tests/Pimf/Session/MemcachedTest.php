<?php

class SessionMemcachedTest extends \PHPUnit_Framework_TestCase
{
    protected function getCache()
    {
        return $this->getMockBuilder('\Pimf\Cache\Storages\Memcached')
            ->disableOriginalConstructor()
            ->setMethods(array('retrieve', 'forever', 'put', 'forget', 'flush', 'get'))
            ->getMock();
    }

    # start testing

    public function testCreatingNewInstance()
    {
        new \Pimf\Session\Storages\Memcached($this->getCache());
    }


    public function testGeyKeyThatIsNotAtTheSession()
    {
        $storage = new \Pimf\Session\Storages\Memcached($this->getCache());

        $this->assertNull($storage->load('some-bad-key'));
    }


    public function testSavingTheSession()
    {
        $session['id'] = 'cool-secret-id';
        $config['lifetime'] = 60;

        $storage = new \Pimf\Session\Storages\Memcached($this->getCache());

        $this->assertNull($storage->save($session, $config, 60));
    }

    public function testDeleteKeyThatIsNotAtTheSession()
    {
        $storage = new \Pimf\Session\Storages\Memcached($this->getCache());

        $this->assertNull($storage->delete('some-bad-key'));
    }
}
 