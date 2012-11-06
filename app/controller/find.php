<?php

class FindController extends PageController
{
	
	public function req_get ()
	{
		
		header('Content-Type: text/plain');
		$params = $_GET + $_POST;
		
		// Retrict Access to logged in users
//		if ( ! Auth::$id)
//		{
//			echo '{"error":"Authentication Required"}';
//			exit;
//		}

		// App Key Required, Secret too in future
		$appkey = array_key_exists('appkey', $params) ? $params['appkey'] : false;
		if (empty($appkey))
		{
			echo '{"error":"Application Key is required"}';
			exit;
		}
		
		// Vars
		$event = isset($this->uri[1]) ? $this->uri[1] : false;
		$limit = (int) $params['limit'];
		if ($limit < 1) $limit = 10;
		
		// Where
		$where = array();
		if ($event)
		{
			$where['e'] = $event;
		}
		/*
		if ($appkey)
		{
			$where['appkey'] = $appkey;
		}
		else
		{
			$app_keys = array();
			foreach (Auth::$apps as $k => $app)
			{
				$app_keys[] = $app['appkey'];
			}
			$where['appkey'] = array('$in' => $app_keys);
		}
		*/
		if ($params['query'])
		{
			$json = $this->json2array($params['query'], true);
			foreach ($json as $k => $v)
			{
				// Specials
				if ($k === '$e')
				{
					$where['e'] = $v;
					continue;
				}
				if (is_array($v))
				{
					foreach ($v as $_k1 => $_v1)
					{
						if ($_k1 === '$regex')
						{
							$v[$_k1] = new MongoRegex($_v1);
						}
					}
				}
				$where['d.' . $k] = $v;
			}
		}
//		print_r($where); exit;

		if ($where['_id'])
		{
			$where['_id'] = new MongoId($where['_id']);
		}
		
		// Fields
		$fields = array();
		/*
		if ($params['fields'])
		{
			$json = $this->json2array($params['fields'], true);
			$json['date'] = 1;
//			if (!$where['event'])
//			{
				$json['event'] = 1;
//			}
			foreach ($json as $k => $v)
			{
				$fields[$k] = $v;
			}
		}
		*/
		
		// Sort
		$sort = array();
		$sort['t'] = -1;
		if ($params['sort'])
		{
			$json = $this->json2array($params['sort'], true);
			foreach ($json as $k => $v)
			{
				$sort[$k] = $v;
			}
		}
		
		// Find Data
		// Save Data to log
		try
		{
			$collection = MongoX::selectCollection('event_' . $appkey);
			try
			{
				$cursor = $collection
					->find($where, $fields)
					->sort($sort)
					->limit($limit);
				$data = array();
				foreach ($cursor as $row)
				{
					$row['_id'] = (String) $row['_id'];
					$row['date'] = (array) $row['date'];
					$data[] = $row;
				}
				echo json_encode($data);
			}
			catch (MongoCursorException $e)
			{
				echo '{"error":"Cursor Exception"}';
				exit;
			}
			exit;
		}
		
		// Could not connect
		catch (MongoConnectionException $e)
		{
			echo '{"error":"Connection Exception"}';
			exit;
		}
		
		// Output
		exit;
		
	}
	
	
	public function json2array ($str)
	{
		try
		{
			$json = json_decode($str, true);
			if ($json)
			{
				return $json;
			}
		}
		catch (Exception $e)
		{
		}
		return array();
	}
	
}