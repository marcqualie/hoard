<?php

class AppsController extends PageController
{
	
	public function before ()
	{
		if ( ! Auth::$id)
		{
			header('Location: /login');
			exit;
		}
		$this->set('title', 'Hoard - My Apps');
	}
		
	public function req_post ()
	{
		
		if ($this->uri[1] === 'new')
		{
			$name = preg_replace('/[^a-zA-Z0-9\.\-_ \(\)\'"]/', '', $_POST['app-name']);
			if ($name)
			{
				$appkey = uniqid();
				$secret = sha1($appkey . uniqid() . $_SERVER['REMOTE_ADDR'] . rand(0, 999999));
				MongoX::selectCollection('app')->insert(array(
					'name' => $name,
					'appkey' => $appkey,
					'secret' => $secret,
					'roles' => array(
						Auth::$id => 'owner'
					),
					'created' => new MongoDate(),
					'updated' => new MongoDate()
				));
				$this->alert('Your app was created');
			}
			else
			{
				$this->alert('You need to specify a name');
			}
		}
		
		// Fallback to get
		$cursor = MongoX::selectCollection('app')->find(array('roles.' . Auth::$id => array('$exists' => 1)));
		Auth::$apps = iterator_to_array($cursor);
		return $this->req_get();
		
	}
	
	public function req_get ()
	{
		
		$collection = MongoX::selectCollection('app');
		
		$apps = Auth::$apps;
		foreach ($apps as &$app)
		{
			$app['records'] = MongoX::selectCollection('event_' . $app['appkey'])->find()->count();
			$app['rps'] = MongoX::selectCollection('event_' . $app['appkey'])->find(array('t' => array('$gte' => new MongoDate(time() - 60))))->count() / 60;
		}
		array_sort($apps, '!records');
		$this->set('apps', $apps);
		
	}
	
}