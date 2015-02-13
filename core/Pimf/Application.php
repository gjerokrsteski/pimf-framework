<?php
/**
 * Pimf
 *
 * @copyright Copyright (c) Gjero Krsteski (http://krsteski.de)
 * @license   http://opensource.org/licenses/MIT MIT License
 */

namespace Pimf;

use Pimf\Util\String as Str, Pimf\Util\Header, Pimf\Util\Header\ResponseStatus, Pimf\Util\Uuid;

/**
 * Provides a facility for applications which provides reusable resources,
 * common-based bootstrapping and dependency checking.
 *
 * @package Pimf
 * @author  Gjero Krsteski <gjero@krsteski.de>
 *
 */
final class Application
{
  const VERSION = '1.8.6';

  /**
   * @var Environment
   */
  protected static $env;

  /**
   * @var Logger
   */
  protected static $logger;

  /**
   * @var EntityManager
   */
  protected static $em;

  /**
   * @var Router
   */
  protected static $router;

  /**
   * Mechanism used to do initial setup and edging before a application runs.
   *
   * @param array $conf   The array of configuration options.
   * @param array $server Array of information such as headers, paths, and script locations.
   *
   * @return boolean|null
   */
  public static function bootstrap(array $conf, array $server = array ())
  {
    $problems = array ();

    try {

      Config::load($conf);

      $environment = Config::get('environment');

      ini_set('default_charset', Config::get('encoding'));
      date_default_timezone_set(Config::get('timezone'));

      self::setupUtils($server, Config::get('bootstrap.local_temp_directory'));
      self::loadListeners(BASE_PATH . 'app/' . Config::get('app.name') . '/events.php');
      self::setupErrorHandling($environment);
      self::loadPdoDriver($environment, Config::get($environment . '.db'), Config::get('app.name'));
      self::loadRoutes(Config::get('app.routeable'), BASE_PATH . 'app/' . Config::get('app.name') . '/routes.php');

    } catch (\Exception $exception) {
      $problems[] = $exception->getMessage();
    }

    self::reportIf($problems, PHP_VERSION);
  }

  /**
   * Please bootstrap first, than run the application!
   * Run a application, let application accept a request, route the request,
   * dispatch to controller/action, render response and return response to client finally.
   *
   * @param array $get Array of variables passed to the current script via the URL parameters.
   * @param array $post Array of variables passed to the current script via the HTTP POST method.
   * @param array $cookie Array of variables passed to the current script via HTTP Cookies.
   * @param array $files An associative array FILES of items uploaded to the current script via the HTTP POST method.
   *
   * @return void
   */
  public static function run(array $get, array $post, array $cookie, array $files)
  {
    $cli = array();
    if (Sapi::isCli()) {
      $cli = Cli::parse((array)self::$env->argv);
      if (count($cli) < 1 || isset($cli['list'])) {
        Cli::absorb();
        exit(0);
      }
    }

    $prefix     = Str::ensureTrailing('\\', Config::get('app.name'));
    $repository = BASE_PATH . 'app/' . Config::get('app.name') . '/Controller';

    if (isset($cli['controller']) && $cli['controller'] == 'core') {
      $prefix     = 'Pimf\\';
      $repository = BASE_PATH . 'pimf-framework/core/Pimf/Controller';
    }

    $request     = new Request($get, $post, $cookie, $cli, $files, self::$env);
    $resolver    = new Resolver($request, $repository, $prefix, self::$router);
    $sessionized = (Sapi::isWeb() && Config::get('session.storage') !== '');

    if ($sessionized) {
      Session::load();
    }

    $pimf = $resolver->process(self::$env, self::$logger, self::$em);

    if ($sessionized) {
      Session::save();
      Cookie::send();
    }

    $pimf->render();
  }

  /**
   * @param string $environment
   */
  private static function setupErrorHandling($environment)
  {
    if ($environment == 'testing') {
      error_reporting(E_ALL | E_STRICT);
    } else {

      set_exception_handler(
        function ($exception) {
          Error::exception($exception, self::$logger);
        }
      );

      set_error_handler(
        function ($code, $error, $file, $line) {
          Error::native($code, $error, $file, $line, self::$logger, error_reporting());
        }
      );

      register_shutdown_function(
        function () {
          Error::shutdown(self::$logger, error_get_last());
        }
      );

      error_reporting(-1);
    }
  }

  /**
   * @param array $server
   * @param string $tmpPath
   */
  private static function setupUtils(array $server, $tmpPath)
  {
    self::$env = new Environment($server);
    $envData   = self::$env->data();

    Logger::setup(
      self::$env->getIp(),
      $envData->get('PHP_SELF', $envData->get('SCRIPT_NAME'))
    );

    ResponseStatus::setup($envData->get('SERVER_PROTOCOL', 'HTTP/1.0'));

    Header::setup(
      self::$env->getUserAgent(),
      self::$env->HTTP_IF_MODIFIED_SINCE,
      self::$env->HTTP_IF_NONE_MATCH
    );

    Url::setup(self::$env->getUrl(), self::$env->isHttps());
    Uri::setup(self::$env->PATH_INFO, self::$env->REQUEST_URI);
    Uuid::setup(self::$env->getIp(), self::$env->getHost());

    self::$logger = new Logger($tmpPath);
    self::$logger->init();
  }

  /**
   * @param string $environment
   * @param array $dbConf
   * @param string $appName
   */
  private static function loadPdoDriver($environment, array $dbConf, $appName)
  {
    if (is_array($dbConf) && $environment != 'testing') {
      self::$em =  new EntityManager(Pdo\Factory::get($dbConf), $appName);
    }
  }

  /**
   * @param boolean $routeable
   * @param string  $routes Path to routes definition file.
   */
  private static function loadRoutes($routeable, $routes)
  {
    if ($routeable === true && file_exists($routes)) {

      self::$router = new Router();

      foreach ((array)(include $routes) as $route) {

        self::$router->map($route);

      }
    }
  }

  /**
   * @param string $events Path to event listeners
   */
  private static function loadListeners($events)
  {
    if (file_exists($events)) {
      include_once $events;
    }
  }

  /**
   * @param array $problems
   * @param float $version
   * @param bool  $die
   *
   * @return array|void
   */
  private static function reportIf(array $problems, $version, $die = true)
  {
    if (version_compare($version, 5.3) == -1) {
      $problems[] = 'You have PHP ' . $version . ' and you need 5.3 or higher!';
    }

    if (!empty($problems)) {
      return ($die === true) ? die(implode(PHP_EOL . PHP_EOL, $problems)) : $problems;
    }
  }

  /**
   * PIMF Application can not be cloned.
   */
  private function __clone() { }

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
