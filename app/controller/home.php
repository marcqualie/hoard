<?php

class HomeController extends PageController
{
	
	public function before ()
	{
		if ( ! Auth::$id)
		{
			header('Location: /login/');
			exit;
		}
		$this->set('title', 'Buckets - Hoard');
	}
		
	public function req_post ()
	{
		
		if ($_POST['action'] === 'create_bucket')
		{
			$name = preg_replace('/[^a-zA-Z0-9\.\-_ \(\)\'"]/', '', $_POST['app-name']);
			if ($name)
			{
				$appkey = uniqid();
				$secret = sha1($appkey . uniqid() . $_SERVER['REMOTE_ADDR'] . rand(0, 999999));
				$data = array(
					'name' => $name,
					'appkey' => $appkey,
					'secret' => $secret,
					'roles' => array(
						Auth::$id => 'owner'
					),
					'created' => new MongoDate(),
					'updated' => new MongoDate()
				);
				App::$mongo->selectCollection('app')->insert($data);
				$this->alert('Your app was created');
			}
			else
			{
				$this->alert('You need to specify a name');
			}
		}
		
		// Fallback to get
		$cursor = App::$mongo->selectCollection('app')->find(array(
			'$or' => array(
				array(
					'roles.' . Auth::$id => array(
						'$exists' => 1
					)
				),
				array(
					'roles.all' => array('$exists' => 1)
				)
			)
		));
		Auth::$buckets = iterator_to_array($cursor);
		return $this->req_get();
		
	}
	
	public function req_get ()
	{
		
		$collection = App::$mongo->selectCollection('app');
		
		$apps = Auth::$buckets;
		$totals = array('records' => 0, 'rps' => 0, 'storage' => 0, 'storage_index' => 0);
		foreach ($apps as &$app)
		{

			// Get stats from raw collection data
			$stats_raw = App::$mongo->command(array(
				'collStats' => 'event_' . $app['appkey']
			));

			// Force index creation if not already there (temp hack, should be done on creation)
//			print_r($stats_raw);
			if ( ! isset($stats_raw['indexSizes']['t_-1']))
			{
				App::$mongo->selectCollection('event_' . $app['appkey'])->ensureIndex(array('t' => -1));
			}

			$stats = array();
			$app['records'] = isset ($stats_raw['count']) ? (int) $stats_raw['count'] : 0;
			$app['rps'] = 0;
			$app['rps'] = (float) App::$mongo->selectCollection('event_' . $app['appkey'])
				->find(
					array('t' => array('$gte' => new MongoDate(time() - 300)))
				,	array('_id' => 0, 't' => 1)
					)
				->count() / 300;
			$app['storage'] = isset ($stats_raw['size']) ? (int) $stats_raw['size'] : 0;
			$app['storage_index'] = isset ($stats_raw['totalIndexSize']) ? (int) $stats_raw['totalIndexSize'] : 0;
			$app['storage_avg'] = isset($stats_raw['avgObjSize']) ? (int) $stats_raw['avgObjSize'] : 0;

			// Calculate totals
			$totals['records'] += $app['records'];
			$totals['rps'] += $app['rps'];
			$totals['storage'] += $app['storage'];
			$totals['storage_index'] += $app['storage_index'];
		}
		array_sort($apps, '!records');
		$this->set('totals', $totals);
		$this->set('apps', $apps);
		
	}
	
}
