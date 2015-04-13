<?php

class PdoSqliteTest extends \PHPUnit_Framework_TestCase
{
    public function testCreatingNewInstance()
    {
        new \Pimf\Pdo\Sqlite();
    }

    public function testMakeHappyConnection()
    {
        try {

            $configuration = array(
                'database' => dirname(__FILE__) . '/_drafts/test.db',

            );

            $pdo = new \Pimf\Pdo\Sqlite();

            $connection = $pdo->connect($configuration);

            $this->assertInstanceOf('\Pimf\Database', $connection);


        } catch (PDOException $pdoe) {

            $this->markTestSkipped($pdoe->getMessage());

        }

    }
}
 