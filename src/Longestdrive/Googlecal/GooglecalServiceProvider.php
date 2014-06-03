<?php namespace Longestdrive\Googlecal;

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
		$this->package('longestdrive/googlecal');
		$loader = \Illuminate\Foundation\AliasLoader::getInstance();
  		$loader->alias('Googlecal', 'Longestdrive\Googlecal\Facades\Googlecal');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app['googlecal'] = $this->app->share(function($app) {

			if(!\File::exists($app['config']->get('googlecal::certificate_path')))
			{
				throw new \Exception("Can't find the .p12 certificate in: " . $app['config']->get('googlecal::certificate_path'));
			}

			$config = array(
				'oauth2_client_id' => $app['config']->get('googlecal::client_id'),
				'use_objects' => $app['config']->get('googlecal::use_objects'),
			);

			
			$client = new \Google_Client($config);

			$client->setAccessType('offline');

			$client->setAssertionCredentials(
				new \Google_Auth_AssertionCredentials(
					$app['config']->get('googlecal::service_email'),
					array(
						'https://www.googleapis.com/auth/calendar',
						'https://www.googleapis.com/auth/calendar.readonly'
						),
					file_get_contents($app['config']->get('googlecal::certificate_path'))
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