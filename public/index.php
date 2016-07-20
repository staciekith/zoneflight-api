<?php

require_once __DIR__ . "/../vendor/autoload.php";

$app             = new Silex\Application();
$application_env = getenv("APPLICATION_ENV") ? : null;

$app->register(new ZoneFlight\Config($application_env));

if (php_sapi_name() !== 'cli') {
    $app->run();
} else {
    return $app;
}

