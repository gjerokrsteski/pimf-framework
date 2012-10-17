<?php
$config = array(

  'environment' => 'testing',

  'app' => array(
    'name' => 'MyFirstBlog'
  ),

  'testing' => array(
    'db' => array(
      'driver' => 'sqlite',
      'database' => ':memory:'
    ),
  ),

  'production' => array(
    'db' => array(
      'driver' => 'sqlite',
      'database' => 'app/MyFirstBlog/_database/blog-production.db'
    ),
  ),

  'bootstrap' => array(
    'expected' => array(
      'php_version' => 5.3,
      'extensions' => array('pdo', 'pdo_sqlite', 'date', 'reflection', 'session', 'json'),
    ),
    'local_temp_directory' => '/tmp/'
  ),

);

