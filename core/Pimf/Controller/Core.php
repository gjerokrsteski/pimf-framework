<?php
/**
 * Pimf_Controller
 *
 * PHP Version 5
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.
 * It is also available through the world-wide-web at this URL:
 * http://krsteski.de/new-bsd-license/
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to gjero@krsteski.de so we can send you a copy immediately.
 *
 * @copyright Copyright (c) 2010-2011 Gjero Krsteski (http://krsteski.de)
 * @license http://krsteski.de/new-bsd-license New BSD License
 */

/**
 * @package Pimf_Controller
 * @author Gjero Krsteski <gjero@krsteski.de>
 */
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
    $root = Pimf_Util_String::ensureTrailing('/', dirname(dirname(dirname(dirname(dirname(__FILE__))))));

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

    echo Pimf_Cli_Color::paint('Secure config.core.php' . PHP_EOL);
    chmod($root . 'config.core.php', 0744);

    echo Pimf_Cli_Color::paint('Secure autoload.core.php' . PHP_EOL);
    chmod($root . 'autoload.core.php', 0644);

    echo Pimf_Cli_Color::paint('Create logging files' . PHP_EOL);
    $fp = fopen($file = $conf['bootstrap']['local_temp_directory'].'pimf-logs.txt', "at+"); fclose($fp); chmod($file, 0777);
    $fp = fopen($file = $conf['bootstrap']['local_temp_directory'].'pimf-warnings.txt', "at+"); fclose($fp); chmod($file, 0777);
    $fp = fopen($file = $conf['bootstrap']['local_temp_directory'].'pimf-errors.txt', "at+"); fclose($fp); chmod($file, 0777);

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
