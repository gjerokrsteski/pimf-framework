<?php

class LoggerTest extends \PHPUnit_Framework_TestCase
{
    private static $localeStorageDir;

    public static function setUpBeforeClass()
    {
        self::$localeStorageDir = dirname(__FILE__) . '/_drafts/';
    }

    public static function tearDownAfterClass()
    {
        @unlink(self::$localeStorageDir . 'pimf-logs.txt');
        @unlink(self::$localeStorageDir . 'pimf-warnings.txt');
        @unlink(self::$localeStorageDir . 'pimf-errors.txt');
    }

    #tests

    public function testCreatingNewInstanceAndDestructingIt()
    {
        new \Pimf\Logger(new \Pimf\Adapter\File(self::$localeStorageDir, "pimf-logs.txt"),
            new \Pimf\Adapter\File(self::$localeStorageDir, "pimf-warnings.txt"),
            new \Pimf\Adapter\File(self::$localeStorageDir, "pimf-errors.txt"));
    }

    public function testInitialisingTheFileResources()
    {
        $logger = new \Pimf\Logger(new \Pimf\Adapter\File(self::$localeStorageDir, "pimf-logs.txt"),
            new \Pimf\Adapter\File(self::$localeStorageDir, "pimf-warnings.txt"),
            new \Pimf\Adapter\File(self::$localeStorageDir, "pimf-errors.txt"));

        $logger->init();
    }

    public function testLoggingMethods()
    {
        $logger = new \Pimf\Logger(new \Pimf\Adapter\File(self::$localeStorageDir, "pimf-logs.txt"),
            new \Pimf\Adapter\File(self::$localeStorageDir, "pimf-warnings.txt"),
            new \Pimf\Adapter\File(self::$localeStorageDir, "pimf-errors.txt"));
        $logger->init();

        $logger->debug('debug msg')->error('error msg')->info('info msg')->warn('warn msg');
    }
}
