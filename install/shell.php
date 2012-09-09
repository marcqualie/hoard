<?php

/**
 * Shell install script
 *
 * @param mongodburi string					MongoDB Connection URI
 * @param email string						Email which will be notified of errors in the script
 * @param deleteinstall boolean				If set to true /install will be deleted when installtion succeeds
 */

header('Content-Type: text/plain');
date_default_timezone_set('UTC');

// Get params from Shell
parse_str(implode('&', array_slice($argv, 1)), $params);

// Define constants
$docroot = $params['docroot'];
if (!$params['docroot'])
{
	$docroot = dirname(__FILE__) . '/..';
}
while (preg_match('/\/\w+\/\.\./', $docroot))
{
    $docroot = preg_replace('/\/\w+\/\.\./', '', $docroot);
}
if (!file_exists($docroot . '/router.php'))
{
	echo '[Error] Invalid docroot [' . $docroot . ']' . PHP_EOL;
	exit;
}
define('DOCROOT', $docroot);
echo 'docroot: ' . $docroot . PHP_EOL;

// Validate email data
$admin = $params['admin'];
list($email, $password) = explode(':', $admin);
if (!$email)
{
	$email = 'admin@' . ($_SERVER['SERVER_NAME'] ? $_SERVER['SERVER_NAME'] : 'example.com');
}
if (!$password)
{
	$password = 'password';
}
echo 'email: ' . $email . PHP_EOL;

// Get database info and test connection
$mongodburi = $params['mongodburi'];
if (!$mongodburi)
{
	$mongodburi = 'mongodb://admin:admin@127.0.0.1:27017/hoard';
}
echo 'mongodburi: ' . $mongodburi . PHP_EOL;
include DOCROOT . '/lib/mongox.php';
MongoX::init($mongodburi, array('connect' => true));
if (!MongoX::$connected)
{
	echo '[Error] Invalid Mongo Config, cannot connect';
	exit;
}

// Create config file from variables
$config_file = DOCROOT . '/config.php';
if (0 && file_exists($config_file))
{
	echo 'config file exists, will overwrite' . PHP_EOL;
}
$data = date('c');
$content = <<< EOT
<?php

/**
 * Auto generated ($date)
 */

\$config = array();
\$config['email'] = '$email';

// Database
\$config['mongo_uri'] = '$mongodburi';

// Access Control
\$config['allow_ips'] = array('*'); // allow global access by default

EOT;
$fh = fopen($config_file, 'w');
  fwrite($fh, $content);
  fclose($fh);
echo 'config file saved [' . $config_file . ']' . PHP_EOL;

// Create admin user in Mongo
$collection = MongoX::selectCollection('user');
$user = $collection->findOne(array('email' => $email));
if ($user['_id'])
{
	echo 'admin user (' . $email . ') already exists' . PHP_EOL;
}
else
{
	$collection->ensureIndex(array('email' => 1), array('unique' => true));
	include DOCROOT . '/lib/auth.php';
	$token = uniqid();
	echo 'created admin user [email=' . $email . ', password=' . $password . ', token=' . $token . ']' . PHP_EOL;
	$collection->insert(array(
		'email' => $email,
		'password' => Auth::password($password),
		'token' => $token,
		'admin' => 1,
		'created' => new MongoDate(),
		'updated' => new MongoDate()
	));
}

// Delete install directory if set
echo 'please delete /install directory manually' . PHP_EOL;

// Email instalation details

// Completion message
echo 'instalation complete' . PHP_EOL;
