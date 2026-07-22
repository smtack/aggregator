<?php

use Core\Router;
use Core\Session;

const BASE_PATH = __DIR__ . '/../';

require BASE_PATH . '/vendor/autoload.php';
require BASE_PATH . '/config/config.php';
require BASE_PATH . '/src/functions.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('log_errors', 1);

$routes = require base_path('src/routes.php');

$router = new Router($routes);

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = trim($uri);

$router->route($uri);

$session = new Session();

$session->unflash();