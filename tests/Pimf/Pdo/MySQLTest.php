<?php

class PdoMySQLTest extends PHPUnit_Framework_TestCase
{
  public function testCreatingNewInstance()
  {
    new \Pimf\Pdo\Mysql();
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
        // 'unix_socket' => '',
      );

      $pdo = new \Pimf\Pdo\Mysql();

      $connection = $pdo->connect($configuration);

      $this->assertInstanceOf('\Pimf\Database', $connection);


    } catch (PDOException $pdoe) {

      $this->markTestSkipped($pdoe->getMessage());

    }

  }
}
 