<?php

require_once dirname(__FILE__) . '/_fixture/app/test-app/DataMapper/World.php';

class EntityManagerTest extends \PHPUnit_Framework_TestCase
{
  public function testCreatingNewInstance()
  {
    new \Pimf\EntityManager(new \Pimf\Database('sqlite::memory:'));
  }

  /**
   * @expectedException BadMethodCallException
   */
  public function testLoadingNotExistingEntity()
  {
    $em = new \Pimf\EntityManager(new \Pimf\Database('sqlite::memory:'));

    $em->load('notexistingentity');
  }

  public function testLoadingExistingEntity()
  {
    $em = new \Pimf\EntityManager(new \Pimf\Database('sqlite::memory:'), '\Fixture');

    $this->assertInstanceOf('Fixture\DataMapper\World', $em->load('world'));
  }

  public function testLoadingExistingEntityFromIdentityMap()
  {
    $em = new \Pimf\EntityManager(new \Pimf\Database('sqlite::memory:'), '\Fixture');

    $this->assertInstanceOf('Fixture\DataMapper\World', $em->load('world'));
    $this->assertInstanceOf('Fixture\DataMapper\World', $em->load('world'));
  }

  public function testCallingEntityWithMagic()
  {
    $em = new \Pimf\EntityManager(new \Pimf\Database('sqlite::memory:'), '\Fixture');

    $this->assertInstanceOf('Fixture\DataMapper\World', $em->world);
  }

  public function testGivingPdoBack()
  {
    $em = new \Pimf\EntityManager(new \Pimf\Database('sqlite::memory:'), '\Fixture');

    $this->assertInstanceOf('\PDO', $em->getPDO());
  }
}
