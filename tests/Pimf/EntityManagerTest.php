<?php
class Pimf_EntityManagerTest extends PHPUnit_Framework_TestCase
{
  public function testCreatingNewInstance()
  {
    new Pimf_EntityManager(new Pimf_Pdo('sqlite::memory:'));
  }

  /**
   * @expectedException BadMethodCallException
   */
  public function testLoadingNotExistingEntity()
  {
    $em = new Pimf_EntityManager(new Pimf_Pdo('sqlite::memory:'));

    $em->load('notexistingentity');
  }
}
