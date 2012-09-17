<?php
/**
 * The global configuration.
 */
require_once 'autoloader.php';

$iniParser = new  Pimf_Util_IniParser(dirname(__FILE__).'/config.ini');
$config = $iniParser->parse();

if ($config->environment == 'testing') {

  error_reporting(E_ALL | E_STRICT);
  ini_set('display_errors', 'on');

  $dbDsn = $config->testing->database->dsn;

} else {

  error_reporting(E_ALL & ~E_NOTICE);
  ini_set('display_errors', 'off');

  $dbDsn = $config->production->database->dsn;
}

date_default_timezone_set('Europe/Berlin');

// start checking the dependencies.
$problems = array();

// check php-version.
if (version_compare(PHP_VERSION, $config->bootstrap->expected->php_version, '<')) {
  $problems[] = 'You have PHP '.PHP_VERSION
               .' and you need PHP '.$config->bootstrap->expected->php_version.' or higher!';
}

// check expected extensions.
foreach ($config->bootstrap->expected->extensions as $extension) {
  if (false === extension_loaded(strtolower($extension))) {
    $problems[] = 'No '.$extension.' extension loaded!';
  }
}

// configure necessary things for the application.
$registry = new Pimf_Registry();

try {

  $db = new Pimf_PDO($dbDsn);
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $db->exec(file_get_contents(dirname(__FILE__).'/'.$config->bootstrap->create_table_file));

  $registry->em = new Pimf_EntityManager($db, $config->app->name);
  $registry->logger = new Pimf_Logger($config->bootstrap->local_temp_directory);
  $registry->logger->init();
  $registry->env = new Pimf_Environment($_SERVER);
  $registry->conf = $config;

} catch (PDOException $e) {
  $problems[] = $e->getMessage();
}

if (!empty($problems)) {
  die(print_r($problems, true));
}

unset($dbDsn, $dbUser, $dbPwd, $db, $extension, $problems, $iniParser, $config);
