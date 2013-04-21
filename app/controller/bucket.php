<?php

class BucketController extends PageController
{
	
	public $app;
	public $appkey;

	public function before ()
	{
		if ( ! Auth::$id)
		{
			header('Location: /login/');
			exit;
		}

		// Get app key
		$this->appkey = isset($this->uri[1]) ? $this->uri[1] : '';
		if ( ! $this->appkey)
		{
			header('Location: /apps/');
			exit;
		}
		$collection = App::$mongo->selectCollection('app');
		$this->app = $collection->findOne(array('appkey' => $this->appkey));
		if ( ! isset($this->app['_id']))
		{
			header('Location: /buckets/');
			exit;
		}

		// Check action
		$app_action = isset($this->uri[2]) ? $this->uri[2] : '';
		switch ($app_action)
		{
			case 'delete':
				App::$mongo->selectCollection('app')->remove(array('appkey' => $this->appkey));
				App::$mongo->selectCollection('event_' . $this->appkey)->drop();
				header('Location: /buckets/');
				exit;
				break;
			case 'empty':
				App::$mongo->selectCollection('event_' . $this->appkey)->remove();
				header('Location: /bucket/' . $this->appkey);
				exit;
				break;
		}

	}
	
	public function req_get ()
	{

		$this->app['records_1minute'] = App::$mongo->selectCollection('event_' . $this->app['appkey'])->find(array('t' => array('$gte' => new MongoDate(time() - 60))))->count();
		$this->app['records_1hour'] = App::$mongo->selectCollection('event_' . $this->app['appkey'])->find(array('t' => array('$gte' => new MongoDate(time() - 3600))))->count();
		$this->app['records_1day'] = App::$mongo->selectCollection('event_' . $this->app['appkey'])->find(array('t' => array('$gte' => new MongoDate(time() - 3600 * 24))))->count();
		$this->app['records_all'] = App::$mongo->selectCollection('event_' . $this->app['appkey'])->find()->count();
		$this->app['rps'] = App::$mongo->selectCollection('event_' . $this->app['appkey'])->find(array('t' => array('$gte' => new MongoDate(time() - 60))))->count() / 60;

		$this->set('app', $this->app);
		$this->set('title', 'Hoard - ' . $this->app['name']);
		
	}
	
}
