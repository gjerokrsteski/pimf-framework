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

use Pimf\Session, Pimf\Cli, Pimf\Resolver, Pimf\Util\String;

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
  const VERSION = '1.8.6';

  /**
   * Please bootstrap first, than run the application!
   *
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
    $cli = array();
    if (Sapi::isCli()) {
      $cli = Cli::parse((array)Registry::get('env')->argv);
      if (count($cli) < 1 || isset($cli['list'])) {
        Cli::absorb(); exit(0);
      }
    }

    $conf       = Registry::get('conf');
    $root       = String::ensureTrailing('/', dirname(dirname(dirname(dirname(__FILE__)))));
    $prefix     = String::ensureTrailing('\\', $conf['app']['name']);
    $repository = $root . 'app/' . $conf['app']['name'] . '/Controller';

    if (isset($cli['controller']) && $cli['controller'] == 'core') {
      $prefix     = 'Pimf\\';
      $repository = $root.'pimf-framework/core/Pimf/Controller';
    }

    $resolver = new Resolver(
      new Request($get, $post, $cookie, $cli), $repository, $prefix
    );

    $sessionized = (Sapi::isWeb() && $conf['session']['storage'] !== '');

    if ($sessionized) {
      Session::load();
    }

    $pimf = $resolver->process();

    if ($sessionized) {
      Session::save();
      Cookie::send();
    }

    $pimf->render();
  }

  /**
   * Mechanism used to do some initial config before a Application runs.
   *
   * @param array $conf The array of configuration options.
   * @param array $server Array of information such as headers, paths, and script locations.
   *
   * @return boolean
   */
  public static function bootstrap(array $conf, array $server = array())
  {
    ini_set('default_charset', $conf['encoding']);
    date_default_timezone_set($conf['timezone']);

    // configure necessary things for the application.
    $registry = new Registry();
    $registry->conf   = $conf;
    $registry->env  = new Environment($server);
    $registry->logger = new Logger($conf['bootstrap']['local_temp_directory']);
    $registry->logger->init();

    if (Sapi::isWeb()){
      ob_start('mb_output_handler');
    }

    ini_set('display_errors', 'On');

    // setup the error reporting.
    if ($conf['environment'] == 'testing') {

      error_reporting(E_ALL | E_STRICT);
      $dbConf = $conf['testing']['db'];

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
      $dbConf = $conf['production']['db'];
    }

    // start checking the dependencies.
    $problems = array();

    // check php-version.
    if (version_compare(PHP_VERSION, $conf['bootstrap']['expected']['php_version']) == -1) {
      $problems[] = 'You have PHP '. PHP_VERSION
                   .' and you need PHP '.$conf['bootstrap']['expected']['php_version'].' or higher!';
    }

    try {

      // load pdo driver
      if(is_array($dbConf) && $conf['environment'] != 'testing') {
        $registry->em = new EntityManager(\Pimf\Pdo\Factory::get($dbConf), $conf['app']['name']);
      }

      $root = String::ensureTrailing('/', dirname(dirname(dirname(dirname(__FILE__)))));

      // load defined routes
      if($conf['app']['routeable'] === true
        && file_exists($routes = $root .'app/' . $conf['app']['name'] . '/routes.php')
      ) {
        $registry->router = new Router();
          foreach((array)(include $routes) as $route) {
            $registry->router->map($route);
          }
        }

      // load defined event-listeners
      if(file_exists($events = $root .'app/' . $conf['app']['name'] . '/events.php')) {
        include_once $events;
      }

    } catch (\Exception $e) {
      $problems[] = $e->getMessage();
    }

    if (!empty($problems)) {
      die(implode(PHP_EOL.PHP_EOL, $problems));
    }
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
