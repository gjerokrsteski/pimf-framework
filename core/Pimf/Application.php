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
 * @copyright Copyright (c) Gjero Krsteski (http://krsteski.de)
 * @license http://krsteski.de/new-bsd-license New BSD License
 */

namespace Pimf;

use Pimf\Environment, Pimf\Cookie, Pimf\Session, Pimf\Logger, Pimf\Error,
    Pimf\Registry, Pimf\Cli, Pimf\Resolver, Pimf\Request, Pimf\Util\String, Pimf\EntityManager;

/**
 * Provides a facility for applications which provides reusable resources,
 * common-based bootstrapping and dependency checking.
 *
 * @package Pimf
 * @author Gjero Krsteski <gjero@krsteski.de>
 *
 */
final class Application
{
  const VERSION = '1.8';

  private static $bootstrapped;

  /**
   * Run a application, let application accept a request, route the request,
   * dispatch to controller/action, render response and return response to client finally.
   *
   * @param array $get Array of variables passed to the current script via the URL parameters.
   * @param array $post Array of variables passed to the current script via the HTTP POST method.
   * @param array $cookie Array of variables passed to the current script via HTTP Cookies.
   *
   * @throws \LogicException If application not bootstrapped.
   * @return void
   */
  public static function run(array $get, array $post, array $cookie)
  {
    if (self::$bootstrapped !== true) {
      throw new \LogicException('Please bootstrap first, than run the application!');
    }

    $cli = array();

    if (Environment::isCli()) {

      $cli = Cli::parse((array)Registry::get('env')->argv);

      if (count($cli) < 1 || isset($cli['list'])) {
        Cli::absorb(); exit(0);
      }
    }

    $conf = Registry::get('conf');
    $root = String::ensureTrailing('/', dirname(dirname(dirname(dirname(__FILE__)))));

    If (isset($cli['controller']) && $cli['controller'] == 'core') {
      $prefix     = 'Pimf\\';
      $repository = $root.'pimf-framework/core/Pimf/Controller';
    } else {
      $prefix     = String::ensureTrailing('\\', $conf['app']['name']);
      $repository = $root.'app/' . $conf['app']['name'] . '/Controller';
    }

    $resolver = new Resolver(
      new Request($get, $post, $cookie, $cli), $repository, $prefix
    );

    $sessionized = (Environment::isWeb() && $conf['session']['storage'] !== '');

    if ($sessionized) {
      Session::load();
    }

    $pimf = $resolver->process();

    if ($sessionized) {
      Session::save();
      // Cookies must be sent before any output.
      Cookie::send();
    }

    $pimf->render();
  }

  /**
   * Mechanism used to do some initial config before a Application runs.
   *
   * @param array $config The array of configuration options.
   * @param array $server Array of information such as headers, paths, and script locations.
   *
   * @return boolean
   */
  public static function bootstrap(array $config, array $server = array())
  {
    if (self::$bootstrapped === true) {
      return true;
    }

    ini_set('default_charset', $config['encoding']);
    date_default_timezone_set($config['timezone']);

    // configure necessary things for the application.
    $registry = new Registry();
    $registry->conf = $config;
    $registry->env  = new Environment($server);
    $registry->logger = new Logger($config['bootstrap']['local_temp_directory']);
    $registry->logger->init();

    if (Environment::isWeb()){
      ob_start('mb_output_handler');
    }

    ini_set('display_errors', 'On');

    // setup the error reporting.
    if ($config['environment'] == 'testing') {

      error_reporting(E_ALL | E_STRICT);
      $dbConf = $config['testing']['db'];

    } else {

       // setup the error and exception handling.
      set_exception_handler(function($e){
        Error::exception($e);
      });

      set_error_handler(function($code, $error, $file, $line){
        Error::native($code, $error, $file, $line);
      });

      register_shutdown_function(function(){
        Error::shutdown();
      });

      error_reporting(-1);
      $dbConf = $config['production']['db'];
    }

    // start checking the dependencies.
    $problems = array();

    // check php-version.
    if (version_compare(PHP_VERSION, $config['bootstrap']['expected']['php_version']) == -1) {
      $problems[] = 'You have PHP '. PHP_VERSION
                   .' and you need PHP '.$config['bootstrap']['expected']['php_version'].' or higher!';
    }

    try {

      if(is_array($dbConf) && $config['environment'] != 'testing') {
        $registry->em = new EntityManager(\Pimf\Pdo\Factory::get($dbConf), $config['app']['name']);
      }

      $root = String::ensureTrailing('/', dirname(dirname(dirname(dirname(__FILE__)))));

      if($config['app']['routeable'] === true) {
        $registry->router = new Router();

        if(file_exists($routes = $root .'app/' . $config['app']['name'] . '/routes.php')){
          foreach((array)(include $routes) as $route) {
            $registry->router->map($route);
          }
        }
      }

      if(file_exists($events = $root .'app/' . $config['app']['name'] . '/events.php')){
        include_once $events;
      }

    } catch (\Exception $e) {
      $problems[] = $e->getMessage();
    }

    if (!empty($problems)) {
      echo PHP_EOL .'+++ Please install following php/extensions to ensure PIMF proper working +++'.PHP_EOL;
      die(implode(PHP_EOL.PHP_EOL, $problems));
    }

    self::$bootstrapped = true;
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

  /**
   * Stopping the PHP process for PHP-FastCGI users to speed up some PHP queries.
   */
  public static function finish()
  {
    if (function_exists('fastcgi_finish_request')) {
      fastcgi_finish_request();
    }
  }
}
