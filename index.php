<?php
/*
|--------------------------------------------------------------------------
| PIMF Application gateway/runner
|--------------------------------------------------------------------------
*/

require_once 'bootstrap.php';

try{

  Pimf_Application::run($_GET, $_POST, $_COOKIE);

} catch (Pimf_Resolver_Exception $e){ // thrown by the user requests resolver

  Pimf_Util_Header::sendNotFound($e->getMessage());

} catch (Pimf_Controller_Exception $e){ // thrown by the controller

  Pimf_Util_Header::sendNotFound($e->getMessage());

} catch (Exception $e) { // finally fetch the remaining exceptions

  Pimf_Util_Header::sendInternalServerError($e->getMessage());
  Pimf_Registry::get('logger')->error($e->getMessage() . $e->getTraceAsString());
}
