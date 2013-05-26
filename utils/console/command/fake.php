<?php

/**
 * Pipe in fake data
 */
if ($action === 'fake')
{

	echo PHP_EOL;
	$hoard_host = prompt('    Hoard Host', 'hoard.dev');
	$request_count = prompt('    Request Count', 1000);

	// Assert Fake Bucket
	$name = 'Demo Bucket';
	$appkey = '50e8d81e17466';
	$secret = sha1($appkey . 'hoard');
	$data = array(
		'_id' => $appkey,
		'name' => $name,
		'appkey' => (String) $appkey,
		'secret' => $secret,
		'roles' => array(
			'all' => 'owner'
		),
		'created' => new \MongoDate(),
		'updated' => new \MongoDate()
	);
	$this->app->mongo->selectCollection('app')->save($data);

	// Events
	$events = array('test1', 'test2', 'test3');

	// Now pump data in
	$run = true;
	$count = 0;
	echo PHP_EOL;
	while ($run && $count < $request_count)
	{
		$event = $events[array_rand($events)];
		$post = array(
			'appkey' => $appkey,
			'format' => 'json',
			'data' => json_encode(array(
				'random1' => rand(0, 999999),
				'random2' => rand(0, 999999),
				'random3' => rand(0, 999999)
			)
		));
		$ch = curl_init('http://' . $hoard_host . '/track/' . $event);
		curl_setopt_array($ch, array(
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => $post,
		));
		curl_exec($ch);
		$count++;
		echo "\r    Count: " . number_format($count) . '  ';
//		usleep(500);
	}
	echo PHP_EOL;

}
