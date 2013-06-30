<?php

date_default_timezone_set('UTC');

// Include Dependencies
defined('DOCROOT') || define('DOCROOT', __DIR__);
include_once DOCROOT . '/vendor/autoload.php';

// Environment
$app = new Hoard\Application();
$app->env = getenv('APP_ENV') ?: 'development';
$app->config = Hoard\Config::instance()->load($app->env);

// Error Handling
$app->error(function ($e, $code) use ($app) {

    if (php_sapi_name() === 'cli')
    {
        echo $e->getMessage();
        exit;
    }
    $app->router->render($app, 'error', array(
        'code' => $code,
        'message' => $e->getMessage() . ($app->env === 'development' ? ' [' . $e->getFile() . ':' . $e->getLine() . ']' : '')
    ));
});

// Cookies
defined('COOKIE_DOMAIN') || define('COOKIE_DOMAIN', $app->request->getHost());
defined('COOKIE_SECURE') || define('COOKIE_SECURE', false);
defined('COOKIE_HTTP') || define('COOKIE_HTTP', true);

// Connect to MongoDB
$mongo_client = new MongoMinify\Client(
    $app->config['mongo.server'],
    $app->config['mongo.options']
);
$app->mongo = $mongo_client->currentDb();

// Return App Instance
return $app;
