<?php

/**
 * Shell install script
 *
 * @param mongodburi string					MongoDB Connection URI
 * @param email string						Email which will be notified of errors in the script
 * @param deleteinstall boolean				If set to true /install will be deleted when installtion succeeds
 */

// Install composer dependencies
exec('php composer.phar install --dev');

$docroot = prompt('Install Directory', dirname(dirname(dirname(__DIR__))));
if ( ! is_dir($docroot) || ! file_exists($docroot . '/bootstrap.php'))
{
	echo '[ERROR] Invalid Hoard installation at ' . $docroot . PHP_EOL;
	exit;
}

// Check dependencies
if ( ! class_exists('MongoClient'))
{
	error('Mongo PHP driver is missing.');
}

// Authentication
$email = prompt('Admin Email', 'admin@example.com');
$password = null;
while ( ! $password)
{
	$password = prompt('Admin Password');
}

// Get database info and test connection
$mongodb_connected = false;
while ( ! $mongodb_connected)
{
	$mongodb_host = prompt('MongoDB Host', '127.0.0.1');
	$mongodb_port = prompt('MongoDB Port', '27017');
	$mongodb_database = prompt('MongoDB Database', 'hoard');
	$mongodb_uri = 'mongodb://' . $mongodb_host . ':' . $mongodb_port . '/' . $mongodb_database;
	try {
		$mongodb_client = new MongoMinify\Client($mongodb_uri, array(
			'connect' => true
		));
		$mongodb_connected = true;
	}
	catch (Exception $e)
	{
		echo '[ERROR] Cannot connect using those credentials, try again' . PHP_EOL;
		echo '        ' . $mongodb_uri . PHP_EOL;
	}
}

// Create config file from variables
$config_file = $docroot . '/config/default.php';
if (file_exists($config_file))
{
	echo '[NOTICE] Config file exists, overwriting' . PHP_EOL;
}
$date = date('c');
$content = <<< EOT
<?php

/**
 * Auto generated: $date
 */

return array(
    'timezone'         => 'UTC',
    'email'            => '$email',
    'mongo.server'     => '$mongodb_uri',
    'mongo.options'    => array()
);

EOT;
if ( ! is_dir(dirname($config_file)))
{
	echo '[NOTICE] Creating config firectory' . PHP_EOL;
	mkdir(dirname($config_file), 0775, true);
}
$fh = fopen($config_file, 'w');
  fwrite($fh, $content);
  fclose($fh);
echo '[NOTICE] Config file saved to ' . $config_file . PHP_EOL;

// Create admin user in Mongo
$collection = $mongodb_client->selectDb($mongodb_database)->selectCollection('user');
$user = $collection->findOne(array('email' => $email));
if (isset($user['_id']))
{
	echo '[NOTICE] User (' . $email . ') already exists - promoting to admin and updating password' . PHP_EOL;
	$collection->update(array('email' => $email), array(
		'$set' => array(
			'admin' => 1,
			'password' => \Hoard\Auth::password($password),
			'updated' => new \MongoDate()
		)
	));
}
else
{
	$collection->ensureIndex(array('email' => 1), array('unique' => true));
	$token = uniqid();
	echo '[NOTICE] Created admin user [ email=' . $email . ', password=' . $password . ', token=' . $token . ' ]' . PHP_EOL;
	$user = array(
		'email' => $email,
		'password' => \Hoard\Auth::password($password),
		'token' => $token,
		'admin' => 1,
		'created' => new \MongoDate(),
		'updated' => new \MongoDate()
	);
	$collection->insert($user);
}

// Delete install directory if set
echo '[NOTICE] Instalation Complete';
