<?php
require_once 'bootstrap.php';

$cliParams = array();

if (Pimf_Environment::isCli()) {
  parse_str(implode('&', array_slice($_SERVER['argv'], 1)), $cliParams);
}

try{

  $resolver = new Pimf_Resolver(

    new Pimf_Request(($_GET + $cliParams), $_POST, $_COOKIE),

    dirname(__FILE__) . '/' . 'app' . '/' . $registry->conf['app']['name'] . '/' . 'Controller',

    Pimf_Util_String::ensureTrailing('_', $registry->conf['app']['name'])

  );

  $resolver->process()->render();

} catch (Pimf_Controller_Exception $e){
  echo $e->getMessage().PHP_EOL;
} catch (Exception $e) {
  $registry->logger->error($e->getMessage() . $e->getTraceAsString());
}