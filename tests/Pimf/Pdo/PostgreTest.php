<?php

class PdoPostgreTest extends \PHPUnit_Framework_TestCase
{
  public function testCreatingNewInstance()
  {
    new \Pimf\Pdo\Postgre();
  }

  public function testMakeHappyConnection()
  {
    try{

      $configuration = array(
        'host'     => 'localhost',
        'database' => 'db_blog',
        'username' => 'root',
        'password' => '',
        'charset'  => 'utf8',
        'port'     => '3306',
        'schema'   => 'sausalito',
      );

      $pdo = new \Pimf\Pdo\Postgre();

      $connection = $pdo->connect($configuration);

      $this->assertInstanceOf('\Pimf\Database', $connection);


    } catch (PDOException $pdoe) {

      $this->markTestSkipped($pdoe->getMessage());

    }

  }
}
 