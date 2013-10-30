<?php
class EntityManagerTest extends PHPUnit_Framework_TestCase
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
}
