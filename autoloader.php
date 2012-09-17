<?php
function gkAutoLoader($className, $projectRoot = __DIR__ ) {

  static $classes;

  if (!$classes) {

    foreach(array('core/', 'app/') as $dirPart) {

        $regexIterator = new RegexIterator(
          new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dirPart)
          ),
          '/^.+\.php$/i',
          RecursiveRegexIterator::GET_MATCH
        );

        foreach (iterator_to_array($regexIterator, false) as $file) {
          $file = str_replace('\\', '/', $file); // Windows compatible
          $path = str_replace($dirPart, '', current($file));
          $name = str_replace('/', '_', $path);
          $name = str_replace('.php', '', $name);

          $classes[$name] = $dirPart . $path;
        }
    }
  }

  if (isset($classes[$className])) {
    require_once $projectRoot . '/'. $classes[$className];
  }

}

spl_autoload_register('gkAutoLoader');
