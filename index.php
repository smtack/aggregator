<?php
require_once "src/config.php";

spl_autoload_register(function($class) {
  require_once "src/classes/" . $class . ".php";
});

include_once "src/functions.php";

// set_error_handler('errorHandler');
ini_set('display_errors', 'on');
error_reporting(E_ALL);

session_start();

$aggregator = new Controller();