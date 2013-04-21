<?php

date_default_timezone_set('UTC');

// Restrict HTTP Method
if ( ! array_key_exists($_SERVER['REQUEST_METHOD'], array('GET' => 1, 'POST' => 1)))
{
	exit('Invalid Request Method');
}

// Include composer autoloader
include __DIR__ . '/vendor/autoload.php';

// Constants
define('DOCROOT', __DIR__);
define('LIBROOT', DOCROOT . '/lib');
define('APPROOT', DOCROOT . '/app');
define('WEBROOT', DOCROOT . '/public');

// Cookies
define('COOKIE_DOMAIN', str_replace(':' . $_SERVER['SERVER_PORT'], '', $_SERVER['HTTP_HOST']));
define('COOKIE_SECURE', false);
define('COOKIE_HTTP', true);

// Include env
$iterator = new DirectoryIterator(LIBROOT);
foreach ($iterator as $file)
{
	if ( ! $file->isDot())
	{
		include $file->getPathname();
	}
}

// Environment
App::$env = getenv('APP_ENV') ?: 'development';
$config = Config::load('default');

// Connect to MongoDB
if (getenv('HOARD_MONGO_URI'))
{
    $config['mongo.server'] = getenv('HOARD_MONGO_URI');
}
$mongo_client = new MongoMinify\Client($config['mongo.server'], $config['mongo.options']);
App::$mongo = $mongo_client->currentDb();

// Authentication
Auth::init();
