<?php
/*
|--------------------------------------------------------------------------
| PIMF super-auto-loading
|--------------------------------------------------------------------------
*/
spl_autoload_extensions(".php");

spl_autoload_register(function($className){

  static $classes;

    if (!$classes) {

      foreach(array( 'core/',  'app/', 'ext/Twig/lib/', 'ext/Haanga/lib/' ) as $dirPart) {

          $regexIterator = new RegexIterator(
            new RecursiveIteratorIterator(
              new RecursiveDirectoryIterator($dirPart)
            ),
            '/^.+\.php$/i',
            RecursiveRegexIterator::GET_MATCH
          );

          foreach (iterator_to_array($regexIterator, false) as $file) {
            $file = str_replace('\\', '/', $file);
            $path = str_replace($dirPart, '', current($file));
            $name = str_replace('/', '_', $path);
            $name = str_replace('.php', '', $name);

            $classes[$name] = $dirPart . $path;
          }
      }
    }

    if (isset($classes[$className])) {
      require $classes[$className];
    }
});
