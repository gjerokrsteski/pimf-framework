<?php
/*
|--------------------------------------------------------------------------
| PIMF Application gateway/runner
|--------------------------------------------------------------------------
*/

require_once 'bootstrap.php';

try{

  Pimf_Application::run($_GET, $_POST, $_COOKIE);

}
/**
 * thrown by the user requests resolver
 */
catch (Pimf_Resolver_Exception $pre) {

  Pimf_Registry::get('logger')->error($pre->getMessage() . $pre->getTraceAsString());
  Pimf_Util_Header::sendNotFound($pre->getMessage());

}
/**
 * thrown by the controllers
 */
catch (Pimf_Controller_Exception $pce) {

  Pimf_Registry::get('logger')->warn($pce->getMessage() . $pce->getTraceAsString());
  Pimf_Util_Header::sendNotFound($pce->getMessage());

}
/**
 * finally fetch the remaining exceptions
 */
catch (Exception $e) {

  Pimf_Registry::get('logger')->error($e->getMessage() . $e->getTraceAsString());
  Pimf_Util_Header::sendInternalServerError($e->getMessage());
}
