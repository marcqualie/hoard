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
			$data = MongoX::selectCollection('event_' . $app['appkey'])->find()->count();
			$app['records'] = $data;
		}
		array_sort($apps, 'name');
		$this->set('apps', $apps);
		
	}
	
}