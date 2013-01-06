<?php

class TrackController extends PageController
{
	
	public $formats = array('json');
	
	public function req_get ()
	{
		
		header('Content-Type: text/plain');

		$params = array_merge($_GET, $_POST);
		$format = isset($params['format']) ? $params['format'] : 'json';
		$dataload = isset($params['data']) ? json_decode(urldecode($params['data']), true) : array();
		$payload = isset($params['payload']) ? json_decode(urldecode($params['payload']), true) : array();
		$data = array_merge($dataload, $payload);

		// Special data types
		$event = isset($data['event']) ? $data['event'] : $this->uri[1];
		unset($data['event']);
		$appkey = array_key_exists('appkey', $params)
			? $params['appkey'] : (
				array_key_exists('appkey', $data)
					? $data['appkey']
					: false
				);
		unset($data['appkey']);

		// Verify Appkey
		if ( ! $appkey)
		{
			echo '401 Invalid Application Key' . PHP_EOL;
			exit;
		}
		// TODO: Validate app key here
		unset($data['sig'], $data['hash']);

		
		// Sometimes local machines send no post data, or it's corrupt
//		if ( ! $data)
//		{
//			echo '500 No Data' . PHP_EOL;
//			exit;
//		}
				
		// Append to data
		$insert = array();
		$insert['t'] = new MongoDate();
		$insert['e'] = $event;
		$insert['d'] = $data;
		
		// Save Data to log
		try
		{

			$collection = MongoX::selectCollection('event_' . $appkey);
			$collection->insert($insert);
			echo $insert['_id'];
			// removed end of line code

			// Save data in temporary table for very fast real-time querying

			// Track in stats engine TODO: rewrite this basic tracking in Redis or something..
			// this is fucking awful.. fix
			/*
			Cache::instance()->increment('e' . date('ymdHis'), 1, 3600);
			Cache::instance()->increment('e' . date('ymdHi'), 1, 86400);
			Cache::instance()->increment('e' . date('ymdH'), 1, 86400 * 48);
			Cache::instance()->increment('e' . date('ymd'), 1, 86400 * 10);
			*/

			exit;
		}
		
		// Could not connect
		catch (MongoConnectionException $e)
		{
			echo '503 Database Exception' . PHP_EOL;
			exit;
		}
		exit;
		
	}
	
}