<?php

ignore_user_abort(true);
include dirname(__FILE__) . '/lib/core.php';

// Restrict IP
if (!in_array($_SERVER['REMOTE_ADDR'], $config['allow_ips']))
{
	echo "Hoard is only available from your whitelisted IPs. Contact <a href='http://www.marcqualie.com/contact/'>Marc</a> for help";
	exit;
}

// Connect to MongoDB
MongoX::init($config['mongo_uri']);

// Authentication
Auth::init();

// Detect Page Method
$request_uri_base = '';
$request_uri = str_replace($request_uri_base, '', $_SERVER['REQUEST_URI']);
/*
echo 'server: ' . $_SERVER['DOCUMENT_ROOT'] . '<br/>';
echo 'doc: ' . DOCROOT . '<br/>';
echo 'base: ' . $request_uri_base . '<br/>';
echo 'uri: ' . $request_uri . '<br/>';
*/
define('URIBASE', $request_uri_base);
$request_method = strtolower($_SERVER['REQUEST_METHOD']);
list ($_uri, $_query) = explode('?', $request_uri);
$uri = explode('/', $_uri);
array_shift($uri);
$method = preg_replace('/[^a-z0-9]/', '', strtolower($uri[0]));
if (!$method) $method = 'home';
define('PAGE', $method);

// Get Controller
$file = new File(DOCROOT . '/controller/' . $method . '.php');
if (!$file->exists())
{
	$method = 'error';
	$file = new File(DOCROOT . '/controller/' . $method . '.php');	
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
$file = new File(DOCROOT . '/view/' . $method . '.tpl');
if ($file->exists())
{
	extract($page->var);
	include $file->location;
}

// Ouput HTML
$html = ob_get_contents();
ob_end_clean();
include DOCROOT . '/view/layout.tpl';
