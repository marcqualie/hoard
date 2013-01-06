<?php

date_default_timezone_set('UTC');

// Rescriptions
if (!in_array($_SERVER['REQUEST_METHOD'], array('GET', 'POST')))
{
	exit('Invalid Request Method');
}

// Constants
define('DOCROOT', realpath(__DIR__ . '/..'));
define('LIBROOT', DOCROOT . '/lib');
define('APPROOT', DOCROOT . '/app');

// Cookies
define('COOKIE_DOMAIN', $_SERVER['HTTP_HOST']);
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
Hoard::$env = 'development';
$config = Config::load('default');