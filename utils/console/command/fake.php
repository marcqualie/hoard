<?php

/**
 * Pipe in fake data
 */
if ($action === 'fake')
{

	// Assert Fake Bucket
	$name = 'Demo Bucket';
	$appkey = '50e8d81e17466';
	$secret = sha1($appkey . 'hoard');
	MongoX::selectCollection('app')->save(array(
		'_id' => $appkey,
		'name' => $name,
		'appkey' => (String) $appkey,
		'secret' => $secret,
		'roles' => array(
			'all' => 'owner'
		),
		'created' => new \MongoDate(),
		'updated' => new \MongoDate()
	));

	// Events
	$events = array('test1', 'test2', 'test3');

	// Now pump data in
	$run = true;
	$count = 0;
	while ($run)
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
		$ch = curl_init('http://dev.hoard.marcqualie.com/track/' . $event);
		curl_setopt_array($ch, array(
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => $post,
		));
		curl_exec($ch);
		$count++;
		echo "\rCount: " . $count;
		usleep(500);
	}
		

}