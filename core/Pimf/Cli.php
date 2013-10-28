<?php
/**
 * Pimf
 *
 * PHP Version 5
 *
 * A comprehensive collection of PHP utility classes and functions
 * that developers find themselves using regularly when writing web applications.
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
 * A full featured package for managing command-line options and arguments,
 * it allows the developer to easily build complex command line interfaces.
 *
 * @package Pimf
 * @author Gjero Krsteski <gjero@krsteski.de>
 */
class Pimf_Cli
{
   /**
    * Prints out a list of CLI commands from the system,
    * which is defined at the controllers with the "CliAction()" suffix at the method-name.
    * @return void
    */
   public static function absorb()
   {
     $classes = array();

     $conf = Pimf_Registry::get('conf');

     $root = dirname(dirname(dirname(dirname(__FILE__))));
     $coreClr =  $root.'/pimf-framework/core/Pimf/Controller/';
     $appClr =  $root.'/app/'.$conf['app']['name'].'/Controller/';

     foreach (array( $appClr, $coreClr) as $dir) {

       $iterator = new RegexIterator(
         new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir)),
         '/^.+\.php$/i',
         RecursiveRegexIterator::GET_MATCH
       );

       foreach (iterator_to_array($iterator, false) as $file) {
         $file = str_replace('\\', '/', current($file));
         $file = str_replace('/', DIRECTORY_SEPARATOR, $file);
         $name = str_replace(array($root.DIRECTORY_SEPARATOR.'pimf-framework'.DIRECTORY_SEPARATOR.'core'.DIRECTORY_SEPARATOR, $root.DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR), '', $file);
         $name = str_replace(DIRECTORY_SEPARATOR, '_', $name);
         $name = str_replace('.php', '', $name);
         $classes[] = $name;
       }
     }

     echo Pimf_Cli_Color::paint(
       PHP_EOL.'PIMF v'.Pimf_Application::VERSION.' PHP Command Line Interface by Gjero Krsteski'.PHP_EOL
     );

     echo Pimf_Cli_Color::paint(
       '+------------------------------------------------------+'. PHP_EOL
     );

     array_map(
       function ($class) {

           $reflection = new ReflectionClass($class);

           if ($reflection->isSubclassOf('Pimf_Controller_Abstract')){

              $methods    = $reflection->getMethods();
              $controller = explode('_', $class);

              echo Pimf_Cli_Color::paint('controller: ' . strtolower(end($controller)) . '' . PHP_EOL);

              array_map(
                function ($method) {
                  if (false !== $command = strstr($method->getName(), 'CliAction', true)) {
                    echo Pimf_Cli_Color::paint(PHP_EOL.' action: ' . $command . ' '.PHP_EOL);
                  }
                }, $methods
              );

             echo Pimf_Cli_Color::paint(
               PHP_EOL.'+------------------------------------------------------+'. PHP_EOL
             );

           }

       }, $classes
     );
   }

  /**
   * @param array $commands
   * @return array
   */
  public static function parse(array $commands)
  {
    $cli = array();

    parse_str(implode('&', array_slice($commands, 1)), $cli);

    $command = current(array_keys($cli, ''));

    if (Pimf_Util_String::contains($command, ':')){

      list($controller, $action) = explode(':', $command);

      $cli['controller'] = $controller;
      $cli['action'] = $action;
    }

    return $cli;
  }
}
