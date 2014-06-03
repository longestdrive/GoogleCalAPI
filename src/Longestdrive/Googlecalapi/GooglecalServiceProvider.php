<?php namespace Longestdrive\Googlecalapi;

use Illuminate\Support\ServiceProvider;

class GooglecalServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('longestdrive/googlecalapi');
		$loader = \Illuminate\Foundation\AliasLoader::getInstance();
  		$loader->alias('Googlecal', 'Longestdrive\Googlecalapi\Facades\Googlecal');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app['googlecalapi'] = $this->app->share(function($app) {

			if(!\File::exists($app['config']->get('googlecalapi::certificate_path')))
			{
				throw new \Exception("Can't find the .p12 certificate in: " . $app['config']->get('googlecalapi::certificate_path'));
			}

			$config = array(
				'oauth2_client_id' => $app['config']->get('googlecalapi::client_id'),
				'use_objects' => $app['config']->get('googlecalapi::use_objects'),
			);

			
			$client = new \Google_Client($config);

			$client->setAccessType('offline');

			$client->setAssertionCredentials(
				new \Google_Auth_AssertionCredentials(
					$app['config']->get('googlecalapi::service_email'),
					array(
						'https://www.googleapis.com/auth/calendar',
						'https://www.googleapis.com/auth/calendar.readonly'
						),
					file_get_contents($app['config']->get('googlecalapi::certificate_path'))
				)
			);

			return new Googlecal($client);
		  });
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('googlecal');
	}

}