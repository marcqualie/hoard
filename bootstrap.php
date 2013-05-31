<?php

date_default_timezone_set('UTC');

// Include Dependencies
include __DIR__ . '/vendor/autoload.php';

// Environment
$app = new Hoard\Application();
$app->env = getenv('APP_ENV') ?: 'development';
$app->config = Hoard\Config::load('default');

// Error Handling
$app->error(function ($e, $code) use ($app) {

    if (php_sapi_name() === 'cli')
    {
        echo $e->getMessage();
        exit;
    }
    $app->router->render($app, 'error', array(
        'code' => $code,
        'message' => $e->getMessage()
    ));
});

// Cookies
define('COOKIE_DOMAIN', isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '.');
define('COOKIE_SECURE', false);
define('COOKIE_HTTP', true);

// Connect to MongoDB
$mongo_client = new MongoMinify\Client(
    $app->config['mongo.server'],
    $app->config['mongo.options']
);
$app->mongo = $mongo_client->currentDb();

// Return App Instance
return $app;
