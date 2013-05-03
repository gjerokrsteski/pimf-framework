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
  const VERSION = '1.6';

  private static $bootstrapped;

  /**
   * Run a application, let application accept a request, route the request,
   * dispatch to controller/action, render response and return response to client finally.
   *
   * @param array $get Array of variables passed to the current script via the URL parameters.
   * @param array $post Array of variables passed to the current script via the HTTP POST method.
   * @param array $cookie Array of variables passed to the current script via HTTP Cookies.
   *
   * @throws LogicException If application not bootstrapped.
   * @return void
   */
  public static function run(array $get, array $post, array $cookie)
  {
    if (static::$bootstrapped !== true) {
      throw new LogicException('Please bootstrap first, than run the application!');
    }

    $cli = array();

    if (Pimf_Environment::isCli()) {

      $cli = Pimf_Cli::parse((array)Pimf_Registry::get('env')->argv);

      if (count($cli) < 1 || isset($cli['list'])) {
        Pimf_Cli::absorb(); exit(0);
      }
    }

    $conf = Pimf_Registry::get('conf');

    If (isset($cli['controller']) && $cli['controller'] == 'core') {
      $prefix     = 'Pimf_';
      $repository = 'core/Pimf/Controller';
    } else {
      $prefix     = Pimf_Util_String::ensureTrailing('_', $conf['app']['name']);
      $repository = 'app/' . $conf['app']['name'] . '/Controller';
    }

    $resolver = new Pimf_Resolver(
      new Pimf_Request($get, $post, $cookie, $cli), $repository, $prefix
    );

    $sessionized = (Pimf_Environment::isWeb() && $conf['session']['storage'] !== '');

    if ($sessionized) {
      Pimf_Session::load();
    }

    $pimf = $resolver->process();

    if ($sessionized) {
      Pimf_Session::save();
      // Cookies must be sent before any output.
      Pimf_Cookie::send();
    }

    $pimf->render();
  }

  /**
   * Mechanism used to do some initial config before a Application runs.
   *
   * @param array $config The array of configuration options.
   * @param array $server Array of information such as headers, paths, and script locations.
   *
   * @return void
   */
  public static function bootstrap(array $config, array $server = array())
  {
    if (static::$bootstrapped === true) {
      return;
    }

    ini_set('default_charset', $config['encoding']);

    if (Pimf_Environment::isWeb()){
      ob_start('mb_output_handler');
    }

    date_default_timezone_set($config['timezone']);
    ini_set('display_errors', 'On');

    // setup the error reporting.
    if ($config['environment'] == 'testing') {

      error_reporting(E_ALL | E_STRICT);
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

      $registry->env    = new Pimf_Environment($server);

      if(is_array($dbConf)) {
        $registry->em = new Pimf_EntityManager(Pimf_Pdo_Factory::get($dbConf), $config['app']['name']);
      }

      $registry->logger = new Pimf_Logger($config['bootstrap']['local_temp_directory']);
      $registry->logger->init();

      $registry->conf   = $config;

    } catch (Exception $e) {
      $problems[] = $e->getMessage();
    }

    if (!empty($problems)) {
      die(print_r($problems, true));
    }

    unset($dbDsn, $dbUser, $dbPwd, $extension, $problems, $config);

    static::$bootstrapped = true;
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