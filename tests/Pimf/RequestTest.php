<?php
class RequestTest extends PHPUnit_Framework_TestCase
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
    new \Pimf\Request($_GET);
  }

  public function testCreatingFullNewInstance()
  {
    new \Pimf\Request($_GET, $_POST, $_COOKIE);
  }

  public function tesGetData()
  {
    $request = new \Pimf\Request($_GET);

    $this->assertNotNull( $request->fromGet()->get('controller') );
    $this->assertEquals( 'index', $request->fromGet()->get('controller') );
  }

  public function tesPostData()
  {
    $request = new \Pimf\Request(array(), $_POST);

    $this->assertNotNull( $request->fromGet()->get('firstname') );
    $this->assertEquals( 'gatter', $request->fromGet()->get('firstname') );
  }

  public function tesCookieData()
  {
    $request = new \Pimf\Request(array(), array(), $_COOKIE);

    $this->assertNotNull( $request->fromGet()->get('date') );
    $this->assertEquals( '01-01-2017', $request->fromGet()->get('date') );
  }

  public function testStripSlashesIfMagicQuotes()
  {
    $request = new \Pimf\Request(array(), array(), array());

    $res = $request->stripSlashesIfMagicQuotes($_GET, true);

    $this->assertNotNull( $res['controller'] );
    $this->assertEquals( 'index', $res['controller']  );
  }
}
