<?php
function pimfSuperAutoLoader($className) {

  $directoryToBeLoaded  = dirname(__FILE__) . DIRECTORY_SEPARATOR;

  $loadDirs = array(
    $directoryToBeLoaded . 'core' . '/', 
    $directoryToBeLoaded . 'app' . '/'
  );

  static $classes;

  if (!$classes) {

    foreach($loadDirs as $dirPart) {

        $regexIterator = new RegexIterator(
          new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dirPart)
          ),
          '/^.+\.php$/i',
          RecursiveRegexIterator::GET_MATCH
        );

        foreach (iterator_to_array($regexIterator, false) as $file) {
          $file = current($file);
          $file = str_replace('\\', '/', $file); // Windows compatible
          $path = str_replace($dirPart, '', current($file));
          $name = str_replace('/', '_', $path);
          $name = str_replace('.php', '', $name);

          $classes[$name] = $dirPart . $path;
        }
    }
  }

  if (isset($classes[$className])) {
    require_once $classes[$className];
  }
}

spl_autoload_register('pimfSuperAutoLoader');
