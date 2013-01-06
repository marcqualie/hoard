<?php

$email = isset($argv[2]) ? $argv[2] : '';
if ( ! $email)
{
	echo 'Please enter the user\'s email address';
	return false;
}
$user = MongoX::selectCollection('user')->findOne(array('email' => $email));
if ( ! $user['_id'])
{
	echo 'Could not find ' . $email;
	return false;
}

// Get new password
$pass1 = '';
while ( ! $pass1)
{
	echo 'Password: ';
	$pass1 = trim(fgets(STDIN));
}

// Confirm passwords match
echo "\r" . 'Confirm Password: ';
$pass2 = trim(fgets(STDIN));
if ($pass1 !== $pass2)
{
	echo "\r" . 'Passwords need to match';
	return false;
}

// Update password in database
$user['password'] = Auth::password($pass1);
MongoX::selectCollection('user')->save($user);
echo "\r" . 'Password Updated';