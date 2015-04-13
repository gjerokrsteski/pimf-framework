<?php

class SessionMemoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreatingNewInstance()
    {
        new \Pimf\Session\Storages\Memory();
    }


    public function testGeyKeyThatIsNotAtTheSession()
    {
        $storage = new \Pimf\Session\Storages\Memory();

        $this->assertNull($storage->load('some-bad-key'));
    }


    public function testSavingTheSession()
    {
        $session['id'] = 'cool-secret-id';
        $config['lifetime'] = 60;

        $storage = new \Pimf\Session\Storages\Memory();

        $this->assertNull($storage->save($session, $config, 60));
    }

    public function testDeleteKeyThatIsNotAtTheSession()
    {
        $storage = new \Pimf\Session\Storages\Memory();

        $this->assertNull($storage->delete('some-bad-key'));
    }
}
 