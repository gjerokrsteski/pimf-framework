<?php

class SessionCookieTest extends \PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
        \Pimf\Request::$cookieData = new \Pimf\Param();
    }

    # start testing

    public function testCreatingNewInstance()
    {
        new \Pimf\Session\Storages\Cookie();
    }

    public function testIfNoKayAtCookie()
    {
        $storage = new \Pimf\Session\Storages\Cookie();

        $this->assertNull($storage->load('some-bad-id'));
    }

    public function testRefreshing()
    {
        $storage = new \Pimf\Session\Storages\Cookie();

        $session1 = $storage->fresh();
        $session2 = $storage->fresh();

        $this->assertNotEquals($session1, $session2);
    }
}
