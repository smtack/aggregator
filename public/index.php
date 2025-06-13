<?php
require_once "../app/config.php";

spl_autoload_register(function($class) {
  require_once "../app/classes/" . $class . ".php";
});

include_once "../app/functions.php";

// Error Reporting
ini_set('display_errors', 'on');
ini_set('display_startup_errors', 'on');
ini_set('log_errors', 'on');

error_reporting(E_ALL);
// set_error_handler('errorHandler');

session_start();

$app = new Controller();