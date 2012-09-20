<?php
require_once 'bootstrap.php';

$cliParams = array();

if (PHP_SAPI === 'cli') {
  parse_str(implode('&', array_slice($_SERVER['argv'], 1)), $cliParams);
}

try{

  $resolver = new Pimf_Resolver(
    new Pimf_Request(
      ($_GET + $cliParams), $_POST, $_COOKIE
    ),
    dirname(__FILE__).'/app/'.$registry->conf->app->name.'/Controller',
    $registry->conf->app->name.'_'
  );

  $resolver->process()->render();

} catch (Pimf_Controller_Exception $e){
  echo $e->getMessage();
} catch (Exception $e) {
  $registry->logger->error($e->getMessage());
  $registry->logger->error($e->getTraceAsString());
}