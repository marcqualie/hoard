#!/app/php/bin/php -c /app/www
<?php

/**
 * Shell install script
 *
 * @param email string						Email which will be notified of errors in the script
 * @param deleteinstall boolean				If set to true /install will be deleted when installtion succeeds
 */

header('Content-Type: text/plain');
date_default_timezone_set('UTC');

// Get params from Shell
parse_str(implode('&', array_slice($argv, 1)), $params);

// Create default admin user
$mongodburi = getenv('MONGO_URI');
if ( ! $mongodburi)
{
	echo '[ERROR] You need to configure MongoDB before you can run the installer' . PHP_EOL;
	exit;
}
echo 'mongodburi: ' . $mongodburi . PHP_EOL;
include __DIR__ . '/../lib/mongox.php';
MongoX::init($mongodburi, array('connect' => true));
if ( ! MongoX::$connected)
{
	echo '[ERROR] Invalid Mongo Config, cannot connect to server' . PHP_EOL;
	exit;
}

// Create admin user in Mongo
$email = 'admin@example.com';
$password = 'password';
$collection = MongoX::selectCollection('user');
$user = $collection->findOne(array('email' => $email));
if ($user['_id'])
{
	echo '[WARNING] Admin user (' . $email . ') already exists' . PHP_EOL;
}
else
{
	$collection->ensureIndex(array('email' => 1), array('unique' => true));
	include __DIR__ . '/../lib/auth.php';
	$token = uniqid();
	echo 'Created admin user [email=' . $email . ', password=' . $password . ', token=' . $token . ']' . PHP_EOL;
	$collection->insert(array(
		'email' => $email,
		'password' => Auth::password($password),
		'token' => $token,
		'admin' => 1,
		'created' => new MongoDate(),
		'updated' => new MongoDate()
	));
}

// Completion message
echo 'Instalation complete' . PHP_EOL;
