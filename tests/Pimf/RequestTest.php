<?php
class Pimf_RequestTest extends PHPUnit_Framework_TestCase
{
  public static function setUpBeforeClass()
  {
    $_GET    = array(
      'controller'=> 'index',
      'action'    => 'save'
    );
    $_POST   = array(
      'firstname'=> 'billy',
      'lastname' => 'gatter'
    );
    $_COOKIE = array(
      'name'=> 'pimf',
      'date'=> '01-01-2017'
    );
  }

  public function testCreatingNewInstance()
  {
    new Pimf_Request($_GET);
  }

  public function testCreatingFullNewInstance()
  {
    new Pimf_Request($_GET, $_POST, $_COOKIE);
  }

  public function tesGetData()
  {
    $request = new Pimf_Request($_GET);

    $this->assertNotNull( $request->fromGet()->getParam('controller') );
    $this->assertEquals( 'index', $request->fromGet()->getParam('controller') );
  }

  public function tesPostData()
  {
    $request = new Pimf_Request(array(), $_POST);

    $this->assertNotNull( $request->fromGet()->getParam('firstname') );
    $this->assertEquals( 'gatter', $request->fromGet()->getParam('firstname') );
  }

  public function tesCookieData()
  {
    $request = new Pimf_Request(array(), array(), $_COOKIE);

    $this->assertNotNull( $request->fromGet()->getParam('date') );
    $this->assertEquals( '01-01-2017', $request->fromGet()->getParam('date') );
  }

  public function testStripSlashesIfMagicQuotes()
  {
    $request = new Pimf_Request(array(), array(), array());

    $res = $request->stripSlashesIfMagicQuotes($_GET, true);

    $this->assertNotNull( $res['controller'] );
    $this->assertEquals( 'index', $res['controller']  );
  }
}
