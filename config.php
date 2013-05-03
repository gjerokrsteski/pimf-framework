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
  | The default environment modus for your application [testing|production]
  |------------------------------------------------------------------------
  */
  'environment' => 'production',

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
  |--------------------------------------------------------------------------
  | Is it regular HTTP or secure HTTPS
  |--------------------------------------------------------------------------
  */
  'ssl' => false,
  
  /*
  |------------------------------------------------------------------------
  | Application meta
  |------------------------------------------------------------------------
  */
  'app' => array(
    'name' => 'MyFirstBlog',
    'key' => 'some5secret5key5here',
    'default_controller' => 'blog',
  ),

  /*
  |------------------------------------------------------------------------
  | Testing environment settings
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
  | Production environment settings
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
    'local_temp_directory' => 'c:\\xampp\\tmp\\'
  ),

  /*
  |------------------------------------------------------------------------
  | Settings for the error handling behavior
  |------------------------------------------------------------------------
  */
  'error' => array(
    'ignore_levels' => array(E_USER_DEPRECATED),
    'debug_info' => true,
  	'log' => true,
  ),


  /*
  |--------------------------------------------------------------------------
  | Session settings
  |--------------------------------------------------------------------------
  */
  'session' => array(

      // Session storage 'cookie', 'file', 'pdo', 'memcached', 'apc', 'redis', 'dba'
      'storage' => 'file',

      // If using file storage - default is null
      'storage_path' => 'app/MyFirstBlog/_session/',

      // If using the PDO (database) session storage
      'database' => array(
        'driver' => 'sqlite',
        'database' => 'app/MyFirstBlog/_session/blog-session.db',
      ),

      // Garbage collection has a 2% chance of occurring for any given request to
      // the application. Feel free to tune this to your requirements.
      'garbage_collection' => array(2, 100),

      // Session lifetime number of minutes
      'lifetime' => 60,

      // Session expiration on web browser close
      'expire_on_close' => false,

      // Session cookie name
      'cookie' => 'pimf_session',

      // Session cookie path
      'path' => '/',

      // Domain for which the session cookie is available.
      'domain' => null,

      // If the cookie should only be sent over HTTPS.
      'secure' => false,
  ),

  /*
  |--------------------------------------------------------------------------
  | Cache settings
  |--------------------------------------------------------------------------
  */
  'cache' => array(

      // Cache storage 'cookie', 'file', 'memcached', 'apc', 'redis', 'dba'
      'storage' => 'file',

      // If using file storage - default is null
      'storage_path' => 'app/MyFirstBlog/_cache/',

      // If using the PDO (database) cache storage
      'database' => array(
        'driver' => 'sqlite',
        'database' => 'app/MyFirstBlog/_cache/blog-cache.db',
      ),

      // If using Memcached and APC to prevent collisions with other applications on the server.
      'key' => 'pimfmaster',

      // Memcached servers - for more check out: http://memcached.org
      'memcached' => array(
        array('host' => '127.0.0.1', 'port' => 11211, 'weight' => 100),
      ),
   ),

  /*
  |------------------------------------------------------------------------
  | View engines configurations - using multiple engines is possible
  |------------------------------------------------------------------------
  */
  'view' => array(

      /*
      |------------------------------------------------------------------------
      | Twig view environment configurations - optional
      |------------------------------------------------------------------------
      */
      'twig' => array(
        'cache'      => true,
        'debug'      => false,
        'auto_reload' => true,
      ),

      /*
      |------------------------------------------------------------------------
      | Haanga view environment configurations - optional
      |------------------------------------------------------------------------
      */
      'haanga' => array(
        'cache'      => true,
        'debug'      => false,
        'auto_reload' => true,
      ),
  ),

);
