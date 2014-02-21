<?php
class CliTest extends PHPUnit_Framework_TestCase
{
  public function testCreatingNewInstance()
  {
    new \Pimf\Cli();
  }

  public function testParsingCommand()
  {
    $command = array('php pimf', 'core:init');

    $this->assertEquals(
      array
      (
          'core:init' => '',
          'controller' => 'core',
          'action' => 'init',
      ),

     \Pimf\Cli::parse($command)

    );
  }

  public function testParsingSomeAppCommand()
  {
    $command = array('php pimf', 'blog:insert');

    $this->assertEquals(
      array
      (
          'blog:insert' => '',
          'controller' => 'blog',
          'action' => 'insert',
      ),

     \Pimf\Cli::parse($command)

    );
  }

  public function testParsingListCommand()
  {
    $command = array('php pimf', 'list');

    $this->assertEquals(
      array
      (
          'list' => '',
      ),

     \Pimf\Cli::parse($command)

    );
  }

  public function testCollectControllers()
  {
    $fixture_path = dirname(__FILE__) . '/_fixture';
    $classes = \Pimf\Cli::collect($fixture_path, $fixture_path, '');

    $this->assertInternalType('array', $classes);
    $this->assertNotEmpty($classes);
    $this->assertCount(4, $classes);
  }

  /**
   * @runInSeparateProcess
   * @outputBuffering enabled
   */
  public function testReflectingControllers()
  {
    require_once dirname(__FILE__) . '/_fixture/Index.php';

    ob_start();

     \Pimf\Cli::reflect(array('\\Fixture\\Controller\\Index'));

    $res = ob_get_clean();

    $this->assertContains('controller: \\fixture\\controller\\index', $res);
    $this->assertContains('action: save', $res);
    $this->assertContains('action: index', $res);
  }
}
 