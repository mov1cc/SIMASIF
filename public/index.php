<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

use Dotenv\Dotenv;
use App\Exceptions\Handler;

require __DIR__ . '/../vendor/autoload.php';

define(
    'BASE_PATH',
    dirname(__DIR__)
);

$dotenv = Dotenv::createImmutable(
    BASE_PATH
);

$dotenv->load();

$appConfig = require BASE_PATH . '/app/Config/app.php';

$dbConfig = require BASE_PATH . '/app/Config/database.php';

$mailConfig = require BASE_PATH . '/app/Config/mail.php';

require BASE_PATH . '/app/Config/constants.php';

date_default_timezone_set(
    $appConfig['timezone']
);

session_name(
    $appConfig['session']['name']
);

session_start();

Handler::register();

$router = require BASE_PATH . '/routes/web.php';

$router->dispatch();