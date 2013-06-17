<?php

namespace Silex\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;

class HoardServiceProvider implements ServiceProviderInterface
{
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

			$instance = new \Hoard\Client(array(
				'server' => $app['hoard.server'],
				'apikey' => $app['hoard.apikey']
			));

			if (!isset($app['hoard.bucket']))
			{
				throw new Exception('A valid bucket ID must be suplied.');
			}

			return $instance->getBucket($app['hoard.bucket']);
		});
	}

	public function boot(Application $app) {}
}