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
      'extensions' => array(
        'pdo' => 'Please navigate to "http://php.net/manual/pdo.installation.php" to find out how to install "PDO" on your system!',
        'pdo_sqlite' => 'Please navigate to "http://php.net/manual/ref.pdo-sqlite.php" to find out how to install "PDO_SQLITE" on your system!',
        'pdo_mysql' => 'Please navigate to "http://php.net/manual/ref.pdo-mysql.php" to find out how to install "PDO_MYSQL" on your system!',
        'mysql' => 'Please navigate to "http://php.net/manual/mysql.installation.php" to find out how to install "MySQL" on your system!',
        'sqlite' => 'Please navigate to "http://php.net/manual/sqlite.installation.php" to find out how to install "SQLite" on your system!',
        'date' => 'Please navigate to "http://php.net/manual/datetime.installation.php" to find out how to install "Date/Time" on your system!',
      ),
    ),
    'local_temp_directory' => '/tmp/'
  ),
);
