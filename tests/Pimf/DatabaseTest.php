<?php
class DatabaseTest extends \PHPUnit_Framework_TestCase
{
  public function testCreatingNewInstance()
  {
    new \Pimf\Database('sqlite::memory:');
  }

  public function testIfNestable()
  {
    $pdo = new \Pimf\Database('sqlite::memory:');

    $this->assertFalse($pdo->nestable());
  }

  public function testBeginTransaction()
  {
    $pdo = new \Pimf\Database('sqlite::memory:');

    $this->assertNull($pdo->beginTransaction());
  }

  public function testCommitTransaction()
  {
    $pdo = new \Pimf\Database('sqlite::memory:');

    $this->assertNull($pdo->beginTransaction());
    $this->assertNull($pdo->commit());
  }

  public function testRollBackTransaction()
  {
    $pdo = new \Pimf\Database('sqlite::memory:');

    $this->assertNull($pdo->beginTransaction());
    $this->assertNull($pdo->rollBack());
  }

  /**
   * @expectedException \LogicException
   */
  public function testRollBackTransactionWithoutTransactionStart()
  {
    $pdo = new \Pimf\Database('sqlite::memory:');

    $pdo->rollBack();
  }
}
 