<?php
/*
|--------------------------------------------------------------------------
| PIMF Application gateway/runner
|--------------------------------------------------------------------------
*/

require_once 'bootstrap.php';

try{

  Pimf_Application::run($_GET, $_POST, $_COOKIE, $_SERVER);

} catch (Pimf_Controller_Exception $e){
  echo $e->getMessage().PHP_EOL;
} catch (Exception $e) {
  Pimf_Registry::get('logger')->error($e->getMessage() . $e->getTraceAsString());
}


