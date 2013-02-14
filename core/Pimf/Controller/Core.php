<?php
class Pimf_Controller_Core extends Pimf_Controller_Abstract
{
  /**
   * Because it is a PIMF restriction!
   */
  public function indexAction()
  {

  }

  /**
   * Checks the applications architecture and creates some security and safety measures.
   */
  public function initCliAction()
  {
    clearstatcache();

    $conf = Pimf_Registry::get('conf');
    $app  = 'app/' . $conf['app']['name'] . '/';
    $root = Pimf_Util_String::ensureTrailing('/', dirname(dirname(dirname(dirname(__FILE__)))));

    $assets = array(
      $root . $app . '_session/',
      $root . $app . '_cache/',
      $root . $app . '_database/',
      $root . $app . '_templates/',
    );

    echo Pimf_Cli_Color::paint('Check app assets' . PHP_EOL);

    foreach ($assets as $asset) {

      if (!is_dir($asset)) {
        echo Pimf_Cli_Color::paint("Pleas create '$asset' directory! " . PHP_EOL, 'red');
      }

      if (!is_writable($asset)) {
        echo Pimf_Cli_Color::paint("Pleas make '$asset' writable! " . PHP_EOL, 'red');
      }
    }

    echo Pimf_Cli_Color::paint('Secure root directory' . PHP_EOL);
    chmod($root, 0755);

    echo Pimf_Cli_Color::paint('Secure .htaccess' . PHP_EOL);
    chmod($root . '.htaccess', 0644);

    echo Pimf_Cli_Color::paint('Secure index.php' . PHP_EOL);
    chmod($root . 'index.php', 0644);

    echo Pimf_Cli_Color::paint('Secure config.php' . PHP_EOL);
    chmod($root . 'config.php', 0744);

    clearstatcache();
  }

  public function create_session_tableCliAction()
  {
    $type = Pimf_Cli_Io::read('database type [mysql|sqlite]', '(mysql|sqlite)');

    var_dump(
      $this->createTable($type, 'session')
    );
  }

  public function create_cache_tableCliAction()
  {
    $type = Pimf_Cli_Io::read('database type [mysql|sqlite]', '(mysql|sqlite)');

    var_dump(
      $this->createTable($type, 'cache')
    );
  }

  protected function createTable($type, $for)
  {
    try {
      $pdo = $file = null;

      $conf = Pimf_Registry::get('conf');

      switch ($for){
        case 'cache':
          $pdo = Pimf_Pdo_Factory::get($conf['cache']['database']);
          $file = 'create-cache-table-'.$type.'.sql';
        break;
        case 'session':
          $pdo = Pimf_Pdo_Factory::get($conf['session']['database']);
          $file = 'create-session-table-'.$type.'.sql';
        break;
      }

      return $pdo->exec(
        file_get_contents(
          dirname(dirname(__FILE__)) .'/_database/'.$file
        )
      );

    } catch (PDOException $e) {
      throw new Pimf_Controller_Exception($e->getMessage());
    }
  }
}
