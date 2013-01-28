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

     foreach (array( 'app/' ) as $dir) {

       $iterator = new RegexIterator(
         new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir)),
         '/^.+\.php$/i',
         RecursiveRegexIterator::GET_MATCH
       );

       foreach (iterator_to_array($iterator, false) as $file) {
         $file = str_replace('\\', '/', $file);
         $path = str_replace($dir, '', current($file));
         $name = str_replace('/', '_', $path);
         $name = str_replace('.php', '', $name);
         $classes[] = $name;
       }
     }

     echo Pimf_Cli_Color::paint(
        'PIMF v'.Pimf_Application::VERSION.' PHP Command Line Interface by Gjero Krsteski'.PHP_EOL.PHP_EOL
       .'+--------------------------------------------------------------------------------------+'. PHP_EOL
       .'| Sample: php pimf controller=index action=insert title="Conan" content="action movie" |'. PHP_EOL
       .'+--------------------------------------------------------------------------------------+'. PHP_EOL.PHP_EOL
     );

     array_map(
       function ($class) {

         $conf = Pimf_Registry::get('conf');

         if (preg_match("/" . $conf['app']['name'] . "_Controller/i", $class)) {

           $reflection = new ReflectionClass ($class);
           $methods    = $reflection->getMethods();
           $controller = explode('_', $class);

           echo Pimf_Cli_Color::paint('controller: ' . end($controller) . '' . PHP_EOL);

           array_map(
             function ($method) {
               if (false !== $command = strstr($method->getName(), 'CliAction', true)) {

                 echo Pimf_Cli_Color::paint(' action: ' . $command . ' ');

                 $options = substr($method->getDocComment(), 3, -2);
                 $options = str_replace(array( '  ' ), ' ', $options);
                 $options = str_replace('* @argument ', ' --', $options);

                 echo Pimf_Cli_Color::paint(PHP_EOL . ' arguments: ' . $options . PHP_EOL . PHP_EOL);
               }
             }, $methods
           );

           echo Pimf_Cli_Color::paint(
             '+--------------------------------------------------------------------------------------+'. PHP_EOL
           );
         }
       }, $classes
     );
   }

}
