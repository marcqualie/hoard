#!/usr/bin/env php
<?php

define('DOCROOT', __DIR__);
include DOCROOT . '/lib/mongox.php';
include DOCROOT . '/lib/auth.php';
$config = include DOCROOT . '/app/config/default.php';
MongoX::init($config['mongo_uri']);

/**
 * Make sure this script can't be run outside web interface
 */
if (php_sapi_name() !== 'cli' || isset($_SERVER['REMOTE_ADDR']))
{
	exit('Must be run via CLI' . PHP_EOL);
}

/**
 * Variables
 */
$action = isset($argv[1]) ? preg_replace('/[^a-zA-Z0-9-]/', '', $argv[1]) : '';
if ( ! $action)
{
	echo 'No action specified' . PHP_EOL;
	exit;
}

/**
 * Lookup action in command directory
 */
$command_file = DOCROOT . '/utils/console/command/' . $action . '.php';
if (file_exists($command_file))
{
	$response = include $command_file;
	echo PHP_EOL;
}
else
{
	echo 'Command [' . $action . '] not found' . PHP_EOL;
	exit;
}
