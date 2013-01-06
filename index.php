<?php

ignore_user_abort(true);
include __DIR__ . '/app/bootstrap.php';

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

// Detect Page Method
$request_uri_base = '';
$request_uri = str_replace($request_uri_base, '', $_SERVER['REQUEST_URI']);
define('URIBASE', $request_uri_base);
$request_method = strtolower($_SERVER['REQUEST_METHOD']);
$_uri = $request_uri;
if (strpos($_uri, '?') !== false)
{
	list ($_uri, $_query) = explode('?', $request_uri);
}
$uri = explode('/', $_uri);
array_shift($uri);
$method = preg_replace('/[^a-z0-9]/', '', strtolower($uri[0]));
if ( ! $method)
{
	$method = 'home';
}
define('PAGE', $method);

// Get Controller
$file = new File(APPROOT . '/controller/' . $method . '.php');
if ( ! $file->exists())
{
	$method = 'error';
	$file = new File(APPROOT . '/controller/' . $method . '.php');	
}

// Start buffer
ob_start();
	
// Initialize class
include $file->location;
$class = ucfirst($method) . 'Controller';
$page = new $class();
$page->uri = $uri;
$page->config = $config;
$page->before();
$page->{ 'req_' . $request_method }();
$page->after();

// Display View, if there is one
$file = new File(APPROOT . '/view/' . $method . '.tpl');
if ($file->exists())
{
	extract($page->var);
	include $file->location;
}

// Ouput HTML
$html = ob_get_contents();
ob_end_clean();
include APPROOT . '/view/layout.tpl';
