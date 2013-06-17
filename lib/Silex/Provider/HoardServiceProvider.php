<?php

use Silex\Application;
use Silex\ServiceProviderInterface;

class HoardServiceProvider implements ServiceProviderInterface
{
	public $apikey;
	public $server;

	private $instance;

	public function register(Application $app)
	{
		$app['hoard'] = $app->share(function() use ($app) {

			if (!isset($app['hoard.server']))
			{
				throw new Exception('Invalid server configuration.');
			}

			if (!isset($app['hoard.apikey']))
			{
				throw new Exception('A valid API Key must be provided.');
			}

			$this->apikey = $app['hoard.apikey'];
			$this->server = $app['hoard.server'];

			$this->instance = new \Hoard\Client(array(
				'server' => $this->server,
				'apikey' => $this->apikey
			));

			if (!isset($app['hoard.bucket']))
			{
				throw new Exception('A valid bucket ID must be suplied.');
			}

			return $this->instance->getBucket($app['hoard.bucket']);
		});
	}

	public function boot(Application $app) {}
}