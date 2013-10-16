<?php
/*
|--------------------------------------------------------------------------
| PIMF Framework Configuration
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
  | The default environment mode for your application [testing|production]
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
  |--------------------------------------------------------------------------
  | Is it regular HTTP or secure HTTPS
  |--------------------------------------------------------------------------
  */
  'ssl' => false,

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

  /*
  |------------------------------------------------------------------------
  | Settings for the error handling behavior
  |------------------------------------------------------------------------
  */
  'error' => array(
    'ignore_levels' => array(E_USER_DEPRECATED),
    'debug_info' => false,
  	'log' => false,
  ),

);

if(is_file('config.app.php')) require 'config.app.php';
