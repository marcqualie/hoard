<?php

date_default_timezone_set('UTC');

// Rescriptions
if (!in_array($_SERVER['REQUEST_METHOD'], array('GET', 'POST')))
{
	exit('Invalid Request Method');
}

// Constants
define('DOCROOT', __DIR__);
define('LIBROOT', DOCROOT . '/lib');
define('APPROOT', DOCROOT . '/app');

// Cookies
//print_r($_SERVER);
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
Hoard::$env = getenv('APP_ENV') ?: 'development';
$config = Config::load('default');

// Connect to MongoDB
if (getenv('HOARD_MONGO_URI'))
{
    $config['mongo_uri'] = getenv('HOARD_MONGO_URI');
}
MongoX::init($config['mongo_uri'], array('connect' => true));
if ( ! MongoX::$connected)
{
    echo 'Could not connect to Database' . PHP_EOL;
    exit;
}

// Authentication
Auth::init();
