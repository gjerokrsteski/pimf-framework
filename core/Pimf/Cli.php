<?php
/**
 * Pimf
 *
 * @copyright Copyright (c)  Gjero Krsteski (http://krsteski.de)
 * @license   http://opensource.org/licenses/MIT MIT License
 */

namespace Pimf;

use Pimf\Util\String;

/**
 * A full featured package for managing command-line options and arguments,
 * it allows the developer to easily build complex command line interfaces.
 *
 * @package Pimf
 * @author  Gjero Krsteski <gjero@krsteski.de>
 */
final class Cli
{
  /**
   * Prints out a list of CLI commands from the system,
   * which is defined at the controllers with the "CliAction()" suffix at the method-name.
   *
   * @param string $appClr  Path to application controller repository
   * @param string $coreClr Path to core controller repository
   * @param string $root    Path to home directory
   */
  public static function absorb($appClr = null, $coreClr = null, $root = null)
  {
    echo PHP_EOL . 'PIMF v' . \Pimf\Application::VERSION . ' PHP Command Line Interface by Gjero Krsteski' . PHP_EOL;

    echo '+------------------------------------------------------+' . PHP_EOL;

    self::reflect(self::collect($appClr, $coreClr, $root));
  }

  /**
   * @param array $classes
   */
  public static function reflect(array $classes)
  {
    array_map(
      function ($class) {

        $reflection = new \ReflectionClass($class);

        if ($reflection->isSubclassOf('\Pimf\Controller\Base')) {

          $methods    = $reflection->getMethods();
          $controller = explode('_', $class);

          echo 'controller: ' . strtolower(end($controller)) . '' . PHP_EOL;

          array_map(
            function (\ReflectionMethod $method) {
              if (false !== $command = strstr($method->getName(), 'CliAction', true)) {
                echo PHP_EOL . ' action: ' . $command . ' ' . PHP_EOL;
              }
            }, $methods
          );

          echo PHP_EOL . '+------------------------------------------------------+' . PHP_EOL;
        }

      }, $classes
    );
  }

  /**
   * @param string $appClr
   * @param string $coreClr
   * @param string $root
   *
   * @return array
   */
  public static function collect($appClr = null, $coreClr = null, $root = null)
  {
    $classes = array();

    if (!$root && !$coreClr && !$appClr) {
      $coreClr = str_replace('/', DS, BASE_PATH  . '/pimf-framework/core/Pimf/Controller/');
      $appClr  = str_replace('/', DS, BASE_PATH  . '/app/' . Config::get('app.name') . '/Controller/');
    }

    foreach (array($appClr, $coreClr) as $dir) {

      $iterator
        = new \RegexIterator(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir)), '/^.+\.php$/i', \RecursiveRegexIterator::GET_MATCH);

      foreach (iterator_to_array($iterator, false) as $file) {
        $file = str_replace("\\", '/', current($file));
        $file = str_replace('/', DS, $file);
        $name = str_replace(
          array(BASE_PATH . DS . 'pimf-framework' . DS . 'core' . DS, BASE_PATH . DS . 'app' . DS), '', $file
        );

        $name      = str_replace(DS, '\\', $name);
        $name      = str_replace('.php', '', $name);
        $classes[] = '\\' . $name;
      }
    }

    return $classes;
  }

  /**
   * @param array $commands
   *
   * @return array
   */
  public static function parse(array $commands)
  {
    $cli = array();

    parse_str(implode('&', array_slice($commands, 1)), $cli);

    $command = current(array_keys((array)$cli, ''));

    if (String::contains($command, ':')) {

      list($controller, $action) = explode(':', $command);

      $cli['controller'] = $controller;
      $cli['action']     = $action;
    }

    return $cli;
  }
}
