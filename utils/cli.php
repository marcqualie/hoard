#!/usr/bin/env php
<?php

include dirname(__FILE__) . '/../lib/mongox.php';
include dirname(__FILE__) . '/../lib/auth.php';
include dirname(__FILE__) . '/../config.php';
MongoX::init($config['mongo_uri']);

/**
 * Make sure this script can't be run outside web interface
 */
if (php_sapi_name() !== 'cli' || $_SERVER['REMOTE_ADDR'])
{
	exit('Must be run via CLI');
}

/**
 * Variables
 */
$action = $argv[1];

/**
 * Change User Password
 */
if ($action === 'password')
{
	$email = $argv[2];
	$user = MongoX::selectCollection('user')->findOne(array('email' => $email));
	if ( ! $user['_id'])
	{
		echo 'No such user';
	}
	else
	{
		while ( ! $pass1)
		{
			echo 'Password: ';
			$pass1 = trim(fgets(STDIN));
		}
		echo 'Confirm Password: ';
		$pass2 = trim(fgets(STDIN));
		if ($pass1 === $pass2)
		{
			$user['password'] = Auth::password($pass1);
			MongoX::selectCollection('user')->save($user);
			echo 'Password Updated';
		}
		else
		{
			echo 'Passwords need to match';
		}
	}
	echo PHP_EOL;
}