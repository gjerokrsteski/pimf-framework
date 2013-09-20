<?php
/*
|--------------------------------------------------------------------------
| PIMF bootstrap
|--------------------------------------------------------------------------
*/
require_once 'autoloader.php';
require_once 'config.php';
require_once 'utils.php';

Pimf_Application::bootstrap($config, $_SERVER);
