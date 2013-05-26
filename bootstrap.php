<?php

date_default_timezone_set('UTC');

// Restrict HTTP Method
if ( ! array_key_exists($_SERVER['REQUEST_METHOD'], array('GET' => 1, 'POST' => 1)))
{
	exit('Invalid Request Method');
}

// Include Dependencies
include __DIR__ . '/vendor/autoload.php';

// Environment
$app = new Hoard\Application();
$app->env = getenv('APP_ENV') ?: 'development';
$app->config = Hoard\Config::load('default');

// Constants
define('DOCROOT', __DIR__);
define('LIBROOT', DOCROOT . '/lib');
define('APPROOT', DOCROOT . '/app');
define('WEBROOT', DOCROOT . '/public');

// Cookies
define('COOKIE_DOMAIN', str_replace(':' . $_SERVER['SERVER_PORT'], '', $_SERVER['HTTP_HOST']));
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
