<?php

/**
 * Class Gustbook is a fixture
 */
class Gustbook extends \Pimf\Model\AsArray
{
  protected $id = 1;
  private $used = true;
  public $title = 'test-title', $message = 'cool-guestbook';

}

class ToArrayTest extends PHPUnit_Framework_TestCase
{
  public function testThatReturnsOnlyProtectedAndPublicPropertiesOfTheGivenModelObject()
  {
    $guestbook = new Gustbook();

    $this->assertEquals(

      array('id' => 1, 'title' => 'test-title', 'message' => 'cool-guestbook'),

      $guestbook->toArray()

    );
  }

  public function testThatReturnsChangedOnlyProtectedAndPublicPropertiesOfTheGivenModelObject()
  {
    $guestbook = new Gustbook(); $guestbook->message = 'ho ho ho'; $guestbook->title = 'new title';

    $this->assertEquals(

      array('id' => 1, 'title' => 'new title', 'message' => 'ho ho ho'),

      $guestbook->toArray()

    );
  }

}
 