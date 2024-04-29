<?php
require_once "../app/config.php";

spl_autoload_register(function($class) {
  require_once "../app/classes/" . $class . ".php";
});

include_once "../app/functions.php";

// set_error_handler('errorHandler');
ini_set('display_errors', 'on');
error_reporting(E_ALL);

session_start();

$app = new Controller();