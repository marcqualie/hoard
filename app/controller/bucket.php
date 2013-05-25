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

//		$this->app['records_1minute'] = App::$mongo->selectCollection('event_' . $this->app['appkey'])->find(array('t' => array('$gte' => new MongoDate(time() - 60))))->count();
//		$this->app['records_1hour'] = App::$mongo->selectCollection('event_' . $this->app['appkey'])->find(array('t' => array('$gte' => new MongoDate(time() - 3600))))->count();
//		$this->app['records_1day'] = App::$mongo->selectCollection('event_' . $this->app['appkey'])->find(array('t' => array('$gte' => new MongoDate(time() - 3600 * 24))))->count();
//		$this->app['records_1week'] = App::$mongo->selectCollection('event_' . $this->app['appkey'])->find(array('t' => array('$gte' => new MongoDate(time() - 3600 * 24 * 7))))->count();
//		$this->app['records_1month'] = App::$mongo->selectCollection('event_' . $this->app['appkey'])->find(array('t' => array('$gte' => new MongoDate(time() - 3600 * 24 * 30))))->count();
		$this->app['latest_event'] = App::$mongo->selectCollection('event_' . $this->app['appkey'])->find()->sort(array('t' => -1))->limit(1)->next()->current();
//		$this->app['records_all'] = App::$mongo->selectCollection('event_' . $this->app['appkey'])->count();
		$this->app['stats'] = App::$mongo->command(array('collStats' => 'event_' . $this->app['appkey']));
		$this->app['rps'] = App::$mongo->selectCollection('event_' . $this->app['appkey'])->find(array('t' => array('$gte' => new MongoDate(time() - 86400))))->count() / 86400;

		$this->set('app', $this->app);
		$this->set('title', 'Hoard - ' . $this->app['name']);
		
	}
	
}
