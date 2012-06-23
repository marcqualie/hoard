<?php

// Rescriptions
if (!in_array($_SERVER['REQUEST_METHOD'], array('GET', 'POST'))) exit('Invalid Request Method');

// Constants
define('LIBROOT', dirname(__FILE__));
define('DOCROOT', realpath(LIBROOT . '/../'));
define('CONTROLLERROOT', DOCROOT . '/controller');
define('VIEWROOT', DOCROOT . '/view');

// Cookies
define('COOKIE_DOMAIN', $_SERVER['HTTP_HOST']);
define('COOKIE_SECURE', false);
define('COOKIE_HTTP', true);

// Include env
include LIBROOT . '/functions.php';
include LIBROOT . '/file.php';
include LIBROOT . '/auth.php';
include LIBROOT . '/pagecontroller.php';
include LIBROOT . '/mongox.php';

// Environment
if (file_exists(DOCROOT . '/config.php'))
{
	include DOCROOT . '/config.php';
}
else
{
	echo 'You need to create a config.php file';
	exit;
}