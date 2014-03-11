<?php

class PdoSqlserverTest extends PHPUnit_Framework_TestCase
{
  public function testCreatingNewInstance()
  {
    new \Pimf\Pdo\Sqlserver();
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
        'dsn_type'   => 'dblib',
      );

      $pdo = new \Pimf\Pdo\Sqlserver();

      $connection = $pdo->connect($configuration);

      $this->assertInstanceOf('\Pimf\Database', $connection);


    } catch (PDOException $pdoe) {

      $this->markTestSkipped($pdoe->getMessage());

    }

  }
}
 