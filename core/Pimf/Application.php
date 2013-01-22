<?php
/**
 * Pimf
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
 * Provides a facility for applications which provides reusable resources,
 * common-based bootstrapping and dependency checking.
 *
 * @package Pimf
 * @author Gjero Krsteski <gjero@krsteski.de>
 *
 */
final class Pimf_Application
{
  /**
   * Run a application, let application accept a request, route the request,
   * dispatch to controller/action, render response and return response to client finally.
   *
   * @param array $get Array of variables passed to the current script via the URL parameters.
   * @param array $post Array of variables passed to the current script via the HTTP POST method.
   * @param array $cookie Array of variables passed to the current script via HTTP Cookies.
   * @param array $server Array of information such as headers, paths, and script locations.
   *
   * @return void
   */
  public static function run(array $get, array $post, array $cookie, array $server)
  {
    $cliArguments = array();

    if (Pimf_Environment::isCli()) {
      parse_str(implode('&', array_slice($server['argv'], 1)), $cliArguments);

      if (count($cliArguments) < 1 || isset($cliArguments['list'])) {
        self::printCommands();
        exit(1);
      }
    }

    $conf = Pimf_Registry::get('conf');

    $resolver = new Pimf_Resolver(
      new Pimf_Request($get, $post, $cookie, $cliArguments),
      'app' . '/' . $conf['app']['name'] . '/' . 'Controller',
      Pimf_Util_String::ensureTrailing('_', $conf['app']['name'])
    );

    $resolver->process()->render();
  }

  /**
   * Mechanism used to do some initial config before a Application run.
   *
   * @param array $config The array of configuration options.
   *
   * @return void
   */
  public static function bootstrap(array $config)
  {
    static $isBootstrapped;

    if ($isBootstrapped === true) {
      return;
    }

    ini_set('default_charset', $config['encoding']);
    date_default_timezone_set($config['timezone']);

    // setup the error reporting.
    if ($config['environment'] == 'testing') {

      error_reporting(E_ALL | E_STRICT);
      ini_set('display_errors', 'on');
      $dbConf = $config['testing']['db'];

    } else {

      // setup the error and exception handling.
      set_exception_handler(function($e){
        Pimf_Error::exception($e);
      });

      set_error_handler(function($code, $error, $file, $line){
        Pimf_Error::native($code, $error, $file, $line);
      });

      register_shutdown_function(function(){
        Pimf_Error::shutdown();
      });

      error_reporting(-1);
      ini_set('display_errors', 'off');
      $dbConf = $config['production']['db'];
    }

    // start checking the dependencies.
    $problems = array();

    // check php-version.
    if (version_compare(PHP_VERSION, $config['bootstrap']['expected']['php_version']) == -1) {
      $problems[] = 'You have PHP '.PHP_VERSION
                   .' and you need PHP '.$config['bootstrap']['expected']['php_version'].' or higher!';
    }

    // check expected extensions.
    foreach ($config['bootstrap']['expected']['extensions'] as $extension) {
      if (!extension_loaded($extension)) {
        $problems[] = 'No ' . $extension . ' extension loaded!';
      }
    }

    // configure necessary things for the application.
    $registry = new Pimf_Registry();

    try {
      $registry->em     = new Pimf_EntityManager(Pimf_Pdo_Factory::get($dbConf), $config['app']['name']);
      $registry->logger = new Pimf_Logger($config['bootstrap']['local_temp_directory']);
      $registry->logger->init();
      $registry->env    = new Pimf_Environment($_SERVER);
      $registry->conf   = $config;
    } catch (Exception $e) {
      $problems[] = $e->getMessage();
    }

    if (!empty($problems)) {
      die(print_r($problems, true));
    }

    unset($dbDsn, $dbUser, $dbPwd, $extension, $problems, $config);

    $isBootstrapped = true;
  }

  /**
   * Prints out a list of CLI commands for the system.
   * @return void
   */
  protected static function printCommands()
  {
    $classes = array();

    foreach (array( 'app/' ) as $dirPart) {

      $regexIterator = new RegexIterator(
        new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dirPart)),
        '/^.+\.php$/i',
        RecursiveRegexIterator::GET_MATCH
      );

      foreach (iterator_to_array($regexIterator, false) as $file) {
        $file = str_replace('\\', '/', $file);
        $path = str_replace($dirPart, '', current($file));
        $name = str_replace('/', '_', $path);
        $name = str_replace('.php', '', $name);

        $classes[] = $name;
      }
    }

    array_map(
      function ($class) {

        $conf = Pimf_Registry::get('conf');

        if (preg_match("/" . $conf['app']['name'] . "_Controller/i", $class)) {

          $reflection = new ReflectionClass ($class);
          $methods    = $reflection->getMethods();
          $controller = explode('_', $class);

          echo PHP_EOL.'PIMF CLI 2.1 by Gjero Krsteski'.PHP_EOL.PHP_EOL;
          echo 'Usage sample: php pimf controller=index action=insert title="Conan" content="action movie"'. PHP_EOL;
          echo '+------------------------------------------------------------------------------------------+' . PHP_EOL;
          echo 'controller: ' . end($controller) . '' . PHP_EOL.PHP_EOL;

          array_map(
            function ($method) {
              if (false !== $command = strstr($method->getName(), 'CliAction', true)) {
                echo ' action: ' . $command . ' ';
                $options = substr($method->getDocComment(), 3, -2);
                $options = str_replace(array( '  ' ), ' ', $options);
                $options = str_replace('* @argument ', ' --', $options);
                echo PHP_EOL . ' arguments: ' . $options . PHP_EOL;
              }
            }, $methods
          );

          echo PHP_EOL;
        }
      }, $classes
    );
  }

  /**
   * PIMF Application can not be cloned.
   */
  private function __clone() { }

  /**
   * PIMF Application can not be serialized.
   */
  private function __sleep() { }

  /**
   * PIMF Application can not be unserialized.
   */
  private function __wakeup() { }
}