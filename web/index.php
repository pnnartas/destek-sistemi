<?php


use Silex\Application;


ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__.'/../vendor/autoload.php';

$application = new Application();

require __DIR__.'/../app/config/config_prod.php';
require __DIR__.'/../app/application.php';
require __DIR__.'/../app/config/routes.php';

$application->run();