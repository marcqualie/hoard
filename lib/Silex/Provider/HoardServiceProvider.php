<?php

use Silex\Application;
use Silex\ServiceProviderInterface;

class HoardServiceProvider implements ServiceProviderInterface
{
	public $apikey;
	public $server;
	public $bucket;

	private $instance;

	public function register(Application $app)
	{
		$app['hoard'] = $app->share(function() use ($app) {

			if (isset($app['hoard.server']))
			{
				$this->server = $app['hoard.server'];
			}

			if (isset($app['hoard.apikey']))
			{
				$this->apikey = $app['hoard.apikey'];
			}

			$this->instance = new \Hoard\Client(array(
				'server' => $this->server,
				'apikey' => $this->apikey
			));

			if (isset($app['hoard.bucket']))
			{
				$this->bucket = $this->instance->getBucket($app['hoard.bucket']);
			}

			return $this->instance;
		});
	}

	public function boot(Application $app) {}
}