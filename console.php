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

// Set variables and include environment
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['SERVER_PORT'] = 80;
$_SERVER['REQUEST_METHOD'] = 'GET';
include __DIR__ . '/bootstrap.php';


/**
 * Helpers
 */
function prompt ($msg = '$', $default = null)
{
    if ($default)
    {
        $msg = $msg . ' [' . $default . ']';
    }
    echo $msg . ': ';
    $in = trim(fgets(STDIN));
    if ( ! $in)
    {
        $in = $default;
    }
    return $in;
}
function error ($msg)
{
    exit('[ERROR] ' . $msg . PHP_EOL);
}


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
