<?php
/**
 * Pop Webmail Application
 */

$autoloader = include __DIR__ . '/../vendor/autoload.php';

try {
    $app = new Popcorn\Pop($autoloader, include __DIR__ . '/../app/config/app.http.php');
    $app->register(new PopWebmail\Module());
    $app->run();
} catch (\Exception $exception) {
    $app = new PopWebmail\Module();
    $app->error($exception);
}