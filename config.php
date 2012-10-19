<?php
/*
|--------------------------------------------------------------------------
| PIMF Configuration
|--------------------------------------------------------------------------
|
| The PIMF configuration is responsible for returning an array
| of configuration options. By default, we use the variable $config provided 
| with PIMF - however, you are free to use your own storage mechanism for 
| configuration arrays.
|
*/

$config = array(

  /*
  |------------------------------------------------------------------------
  | The default environment modus for your application
  |------------------------------------------------------------------------
  */
  
  'environment' => 'testing',

  /*
  |------------------------------------------------------------------------
  | The default character encoding used by your application.
  |------------------------------------------------------------------------
  */
  
  'encoding' => 'UTF-8',
  
  /*
  |------------------------------------------------------------------------
  | The default timezone of your application.
  | Supported timezones list: http://www.php.net/manual/en/timezones.php
  |------------------------------------------------------------------------
  */
  
  'timezone' => 'UTC',
  
  /*
  |------------------------------------------------------------------------
  | Application
  |------------------------------------------------------------------------
  */
  
  'app' => array(
    'name' => 'MyFirstBlog',
  ),

  /*
  |------------------------------------------------------------------------
  | Testing environment
  |------------------------------------------------------------------------
  */
  
  'testing' => array(
    'db' => array(
      'driver' => 'sqlite',
      'database' => ':memory:'
    ),
  ),

  /*
  |------------------------------------------------------------------------
  | Production environment
  |------------------------------------------------------------------------
  */
  
  'production' => array(
    'db' => array(
      'driver' => 'sqlite',
      'database' => 'app/MyFirstBlog/_database/blog-production.db'
    ),
  ),

  /*
  |------------------------------------------------------------------------
  | Bootstrapping and dependencies to php-version and extensions
  |------------------------------------------------------------------------
  */
  
  'bootstrap' => array(
    'expected' => array(
      'php_version' => 5.3,
      'extensions' => array('pdo', 'pdo_sqlite', 'date', 'reflection', 'session', 'json'),
    ),
    'local_temp_directory' => '/tmp/'
  ),

);

