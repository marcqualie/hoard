<?php

class TrackController extends PageController
{
	
	public $formats = array('json');
	
	public function req_post ()
	{
		
		$params = array_merge($_GET, $_POST);
		$event = $this->uri[1];
		$format = $params['format'];
		$params['data'] = urldecode($params['data']);
		$data = json_decode($params['data'], true);
		
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
		if ($event)
		{
			$data['event'] = $event;
		}
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
	
	public function req_get ()
	{
		
		echo 'Hoard tracker only supports post data right now';
		
	}
	
}

/*


header('Content-type: text/plain');
error_reporting(E_ALL);

// Only accept certain fields
$accept_fields = array('devkey', 'uri', 'action', 'host', 'type', 'text', 'message', 'query', 'object', 'server', 'referer', 'file', 'line', 'trace');
$data = array();
foreach ($_REQUEST as $k => $v)
{
	if (in_array($k, $accept_fields))
	{
		$data[$k] = $v;
	}
}
if (!$data['text']) $data['text'] = $data['message'];

// Save Data to log
if ($data['type'] && $data['host'] && $data['text'] && $data['action'] === 'log')
{
	
	$data['time'] = time();
	$data['sig'] = md5($data['host'] . ':' . $data['type'] . ':' . $data['text']);
	$mongo = new Mongo();
	$db = $mongo->selectDB('logger');
	$collection = $db->selectCollection('error');
	$collection->insert($data);
	echo "{\"ok\":1}";
	
}
else
{
	
	echo "{\"ok\":0}";
	
}

*/