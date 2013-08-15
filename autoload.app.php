<?php
/*
|--------------------------------------------------------------------------
| Your Application's PHP classes auto-loading
|
| All classes in PIMF are statically mapped. It's just a simple array of
| class to file path maps for ultra-fast file loading.
|--------------------------------------------------------------------------
*/
spl_autoload_register(
  function ($class) {

    // *-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-
    // FEEL FREE TO CHANGE THE MAPPINGS AND DIRECTORIES
    // *-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-

    /**
     * The mappings from class names to file paths.
     */
    static $mappings = array(
      'myfirstblog_controller_blog'  => '/MyFirstBlog/Controller/Blog.php',
      'myfirstblog_datamapper_entry' => '/MyFirstBlog/DataMapper/Entry.php',
      'myfirstblog_model_entry'      => '/MyFirstBlog/Model/Entry.php'
    );

    /**
     * The directories that use the naming convention.
     */
    static $directories = array('app', 'ext/Twig/lib', 'ext/Haanga/lib');

    // *-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-
    //  END OF USER CONFIGURATION!!!
    // *-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-

    // start formatting the class name in the PIMF's naming convention.
    $cn = strtolower($class);

    // load the class from the static heap of classes.
    if (isset($mappings[$cn])) {
      return require __DIR__ . DIRECTORY_SEPARATOR .'app' . $mappings[$cn];
    }

    // THE FALLBACK!!!
    // otherwise we attempt to compute the path to the class.
    // we spin through the registered directories and attempt
    // to locate and load the class file into the script.
    foreach ($directories as $directory) {
      $file = __DIR__
        . DIRECTORY_SEPARATOR
        . $directory
        . DIRECTORY_SEPARATOR
        . str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';

      if (file_exists($file)) {
        return require $file;
      }
    }

    return false;
  }
);
