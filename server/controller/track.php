<?php

class TrackController extends PageController
{
	
	public $formats = array('json');
	
	public function req_get ()
	{
		
		$format = $params['format']? $params['format'] : 'json';
		$params = array_merge($_GET, $_POST);
		$dataload = $params['data'] ? json_decode(urldecode($params['data']), true) : array();
		$payload = $params['payload'] ? json_decode(urldecode($params['payload']), true) : array();
		$data = array_merge($dataload, $payload);

		// Special data types
		$data['event'] = $this->uri[1] ? $this->uri[1] : $data['event'];
		$data['appkey'] = $params['appkey'] ? $params['appkey'] : $data['appkey'];
		
		// Sometimes local machines send no post data, or it's corrupt
		if (!$data)
		{
			echo '500';
			exit;
		}
		
		// Verify Appkey
		if (!$data['appkey'])
		{
			echo '401';
			exit;
		}
		unset($data['sig'], $data['hash']);
		
		// Append to data
		$data['date'] = new MongoDate();
		
		// Save Data to log
		try
		{
			$collection = MongoX::selectCollection('event');
			$collection->insert($data);
			echo $data['_id'];
			exit;
		}
		
		// Could not connect
		catch (MongoConnectionException $e)
		{
			echo '503';
			exit;
		}
		exit;
		
	}
	
}