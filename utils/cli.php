#!/usr/bin/env php
<?php

define('DOCROOT', realpath(dirname(__FILE__) . '/..'));
include DOCROOT . '/lib/mongox.php';
include DOCROOT . '/lib/auth.php';
$config = include DOCROOT . '/app/config/default.php';
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

/**
 * Upgrade
 */
if ($action === 'upgrade')
{

	$stats = array(
		'ok' => 0,
		'corrupt' => 0,
		'skipped' => 0
	);
	$old_collection = MongoX::selectCollection('event');
	$events = $old_collection->find();
	foreach ($events as $event)
	{
		if ( ! $event['appkey'])
		{
			$stats['corrupt']++;
			return;
		}
		$new_collection = MongoX::selectCollection('event_' . $event['appkey']);
		$found = $new_collection->findOne(array('_id' => $event['_id']));
		if ($found)
		{
			$stats['skipped']++;
		}
		else
		{
			$d = array();
			$d['_id'] = $event['_id'];
			$d['t'] = $event['date'];
			$d['d'] = $event;
			$d['e'] = $event['event'];
			unset($d['d']['_id']);
			unset($d['d']['date']);
			unset($d['d']['appkey']);
			unset($d['d']['event']);
//			print_r($d);
			$saved = $new_collection->save($d, array(
//				'safe' => true,
//				'fsync' => true
			));
//			if ($saved['ok'])
//			{
//			}
//			print_r($saved);
			$stats['ok']++;
		}
		$old_collection->remove(array('_id' => $event['_id']));
	}
	print_r($stats);

}
