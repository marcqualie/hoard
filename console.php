#!/usr/bin/env php
<?php

// Check for composer dependencies
if ( ! file_exists(__DIR__ . '/composer.phar'))
{
    echo PHP_EOL;
    echo '    You will need to install composer. Please try the collowing command, then try again:' . PHP_EOL;
    echo '    curl -s https://getcomposer.org/installer | php' . PHP_EOL;
    echo PHP_EOL;
    exit;
}
if ( ! is_dir(__DIR__ . '/vendor'))
{
    passthru('php composer.phar install --dev');
    echo PHP_EOL;
}

define('DOCROOT', __DIR__);
include DOCROOT . '/vendor/autoload.php';
include DOCROOT . '/lib/auth.php';
$config_file = DOCROOT . '/app/config/default.php';
$config = file_exists($config_file) ? include $config_file : array();

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
